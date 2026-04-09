<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Booking;
use App\Models\Product;
use App\Models\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function dashboard()
    {
        $products = Product::with('category')
            ->where('status', 'aktif')
            ->where('stok', '>', 0)
            ->orderBy('nama_produk')
            ->get();

        $categories = \App\Models\Category::withCount([
            'products' => fn($q) => $q->where('status', 'aktif')->where('stok', '>', 0)
        ])->orderBy('nama_kategori')->get();

        $bookings = Booking::with('meja')
            ->where('status', 'konfirmasi')
            ->orderBy('tanggal_booking')
            ->get();

        $tables = Table::where('status', 'tersedia')
            ->orderBy('nomor_meja')
            ->get();

        return view('kasir.transactions.dashboard', compact(
            'products',
            'categories',
            'bookings',
            'tables'
        ));
    }

    public function index()
    {
        $allTransactions = Transaction::with(['product', 'table', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $grouped = $allTransactions->groupBy('nomor_unik');

        $totalTransaksi  = $grouped->count();
        $totalPendapatan = $grouped->sum(fn($items) => $items->sum('total_harga'));
        $totalDineIn     = $grouped->filter(fn($items) => $items->first()->jenis_pemesanan === 'dine_in')->count();
        $totalTakeAway   = $grouped->filter(fn($items) => $items->first()->jenis_pemesanan === 'take_away')->count();

        $currentPage  = request()->get('page', 1);
        $perPage      = 15;
        $pagedGrouped = $grouped->slice(($currentPage - 1) * $perPage, $perPage);

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedGrouped,
            $grouped->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('kasir.transactions.index', compact(
            'paginator',
            'totalTransaksi',
            'totalPendapatan',
            'totalDineIn',
            'totalTakeAway'
        ));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'items'             => 'required|array|min:1',
                'items.*.id_produk' => 'required|exists:products,id',
                'items.*.jumlah'    => 'required|integer|min:1',
                'nama_pelanggan'    => 'required|string|max:255',
                'jenis_pemesanan'   => 'required|in:dine_in,take_away',
                'id_meja'           => 'nullable|exists:tables,id',
                'uang_bayar'        => 'required|integer|min:0',
            ]);

            $totalHarga = 0;
            $itemsData  = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['id_produk']);

                if ($product->stok < $item['jumlah']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Stok {$product->nama_produk} tidak mencukupi! Tersedia: {$product->stok}"
                    ], 400);
                }

                $subtotal    = $product->harga_produk * $item['jumlah'];
                $totalHarga += $subtotal;
                $itemsData[] = [
                    'product'  => $product,
                    'jumlah'   => $item['jumlah'],
                    'subtotal' => $subtotal,
                ];
            }

            if ($request->uang_bayar < $totalHarga) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Uang bayar kurang dari total harga!'
                ], 400);
            }

            $uangKembali = $request->uang_bayar - $totalHarga;
            $nomorUnik   = 'TX' . date('ymdHis') . rand(100, 999);

            if ($request->jenis_pemesanan === 'dine_in' && $request->id_meja) {
                Table::where('id', $request->id_meja)->update(['status' => 'terisi']);
            }

            foreach ($itemsData as $item) {
                Transaction::create([
                    'booking_id'        => null,   // transaksi biasa, tidak dari booking
                    'nomor_unik'        => $nomorUnik,
                    'id_produk'         => $item['product']->id,
                    'id_user'           => Auth::id(),
                    'id_meja'           => $request->jenis_pemesanan === 'dine_in' ? $request->id_meja : null,
                    'nama_pelanggan'    => $request->nama_pelanggan,
                    'jenis_pemesanan'   => $request->jenis_pemesanan,
                    'metode_pembayaran' => 'lunas',
                    'status_pembayaran' => 'lunas',
                    'jumlah'            => $item['jumlah'],
                    'total_harga'       => $item['subtotal'],
                    'dp_dibayar'        => 0,
                    'sisa_pembayaran'   => 0,
                    'uang_bayar'        => $request->uang_bayar,
                    'uang_kembali'      => $uangKembali,
                    'tanggal_lunas'     => now(),
                ]);

                $item['product']->decrement('stok', $item['jumlah']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil!',
                'data'    => [
                    'nomor_unik'   => $nomorUnik,
                    'total_harga'  => $totalHarga,
                    'uang_bayar'   => $request->uang_bayar,
                    'uang_kembali' => $uangKembali,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeFromBooking(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'booking_id'        => 'required|exists:bookings,id',
                'items'             => 'required|array|min:1',
                'items.*.id_produk' => 'required|exists:products,id',
                'items.*.jumlah'    => 'required|integer|min:1',
                'uang_bayar'        => 'required|integer|min:0',
            ]);

            $booking = Booking::with('meja')->findOrFail($request->booking_id);

            if ($booking->status !== 'konfirmasi') {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Booking belum dikonfirmasi admin!'
                ], 400);
            }

            $totalHarga = 0;
            $itemsData  = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['id_produk']);

                if ($product->stok < $item['jumlah']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Stok {$product->nama_produk} tidak mencukupi! Tersedia: {$product->stok}"
                    ], 400);
                }

                $subtotal    = $product->harga_produk * $item['jumlah'];
                $totalHarga += $subtotal;
                $itemsData[] = [
                    'product'  => $product,
                    'jumlah'   => $item['jumlah'],
                    'subtotal' => $subtotal,
                ];
            }

            $dpDibayar   = $booking->jumlah_dp;
            $sisaTagihan = max(0, $totalHarga - $dpDibayar);

            if ($request->uang_bayar < $sisaTagihan) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Uang bayar kurang! Sisa tagihan: Rp ' . number_format($sisaTagihan, 0, ',', '.')
                ], 400);
            }

            $uangKembali = $request->uang_bayar - $sisaTagihan;
            $nomorUnik   = 'TX' . date('ymdHis') . rand(100, 999);

            foreach ($itemsData as $index => $item) {
                Transaction::create([
                    'booking_id'        => $booking->id,  // ← INI KUNCINYA, simpan booking_id
                    'nomor_unik'        => $nomorUnik,
                    'id_produk'         => $item['product']->id,
                    'id_user'           => Auth::id(),
                    'id_meja'           => $booking->id_meja,
                    'nama_pelanggan'    => $booking->nama_pelanggan,
                    'jenis_pemesanan'   => 'dine_in',
                    'metode_pembayaran' => 'lunas',
                    'status_pembayaran' => 'lunas',
                    'jumlah'            => $item['jumlah'],
                    'total_harga'       => $item['subtotal'],
                    'dp_dibayar'        => $index === 0 ? $dpDibayar : 0,
                    'sisa_pembayaran'   => $index === 0 ? $sisaTagihan : 0,
                    'uang_bayar'        => $index === 0 ? $request->uang_bayar : 0,
                    'uang_kembali'      => $index === 0 ? $uangKembali : 0,
                    'tanggal_lunas'     => now(),
                ]);

                $item['product']->decrement('stok', $item['jumlah']);
            }

            $booking->update(['status' => 'selesai']);

            if ($booking->meja) {
                $booking->meja->update(['status' => 'tersedia']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi booking berhasil!',
                'data'    => [
                    'nomor_unik'   => $nomorUnik,
                    'total_harga'  => $totalHarga,
                    'jumlah_dp'    => $dpDibayar,
                    'sisa_tagihan' => $sisaTagihan,
                    'uang_bayar'   => $request->uang_bayar,
                    'uang_kembali' => $uangKembali,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function print($nomorUnik)
    {
        $transactions = Transaction::with(['product', 'user', 'table'])
            ->where('nomor_unik', $nomorUnik)
            ->get();

        if ($transactions->isEmpty()) {
            abort(404, 'Transaksi tidak ditemukan');
        }

        $firstTransaction = $transactions->first();
        $totalHarga       = $transactions->sum('total_harga');

        return view('kasir.transactions.print', compact(
            'transactions',
            'firstTransaction',
            'totalHarga'
        ));
    }
}