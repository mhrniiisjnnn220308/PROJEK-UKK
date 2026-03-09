<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['product', 'user'])
                                  ->where('id_user', Auth::id())
                                  ->orderBy('created_at', 'desc')
                                  ->paginate(20);
        
        return view('kasir.transactions.index', compact('transactions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id_produk' => 'required|exists:products,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'nama_pelanggan' => 'required|max:45',
            'uang_bayar' => 'required|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            $totalHarga = 0;
            $productNames = [];

            // Validasi stok dan hitung total
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['id_produk']);
                
                if ($product->stok < $item['jumlah']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stok {$product->nama_produk} tidak mencukupi! Tersedia: {$product->stok}"
                    ], 400);
                }

                $subtotal = $product->harga_produk * $item['jumlah'];
                $totalHarga += $subtotal;
                $productNames[] = $product->nama_produk . " x" . $item['jumlah'];
            }

            // Validasi uang bayar
            if ($request->uang_bayar < $totalHarga) {
                return response()->json([
                    'success' => false,
                    'message' => 'Uang bayar kurang dari total harga!'
                ], 400);
            }

            $uangKembali = $request->uang_bayar - $totalHarga;

            // Generate nomor unik transaksi dengan microtime untuk uniqueness
            $nomorUnik = 'TX' . date('ymd') . substr(microtime(true) * 10000, -8);
            
            // Log untuk debugging
            \Log::info('Generated Nomor Unik: ' . $nomorUnik);
            \Log::info('Total Items: ' . count($request->items));

            // Simpan transaksi untuk setiap item
            $transactionIds = [];
            $itemCounter = 1;
            
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['id_produk']);
                
                // Buat nomor unik berbeda untuk setiap item
                $itemNomorUnik = $nomorUnik . str_pad($itemCounter, 2, '0', STR_PAD_LEFT);
                
                \Log::info("Creating transaction {$itemCounter}: {$itemNomorUnik}");
                
                $transaction = Transaction::create([
                    'id_produk' => $item['id_produk'],
                    'id_user' => Auth::id(),
                    'nama_pelanggan' => $request->nama_pelanggan,
                    'nomor_unik' => $itemNomorUnik, // Unique untuk setiap item
                    'jumlah' => $item['jumlah'],
                    'total_harga' => $product->harga_produk * $item['jumlah'],
                    'uang_bayar' => $request->uang_bayar,
                    'uang_kembali' => $uangKembali,
                ]);

                $transactionIds[] = $transaction->id;

                // Kurangi stok
                $product->decrement('stok', $item['jumlah']);
                
                $itemCounter++;
            }

            // Log aktivitas dengan nomor transaksi utama
            Log::create([
                'id_user' => Auth::id(),
                'activity' => "Melakukan transaksi {$nomorUnik}: " . implode(', ', $productNames) . " - Total: Rp " . number_format($totalHarga, 0, ',', '.')
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil!',
                'data' => [
                    'nomor_unik' => $nomorUnik,
                    'total_harga' => $totalHarga,
                    'uang_bayar' => $request->uang_bayar,
                    'uang_kembali' => $uangKembali,
                    'transaction_ids' => $transactionIds
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Transaction Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function print($nomorUnik)
    {
        // Cari berdasarkan prefix nomor unik (tanpa suffix)
        $transactions = Transaction::with(['product', 'user'])
                                  ->where('nomor_unik', 'LIKE', $nomorUnik . '%')
                                  ->get();
        
        if ($transactions->isEmpty()) {
            abort(404, 'Transaksi tidak ditemukan');
        }

        $firstTransaction = $transactions->first();
        
        return view('kasir.transactions.print', compact('transactions', 'firstTransaction'));
    }
}