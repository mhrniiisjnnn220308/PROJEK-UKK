<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['product', 'user']);
        
        // Filter berdasarkan tanggal
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }
        
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_selesai);
        }
        
        // Filter berdasarkan kasir
        if ($request->filled('kasir')) {
            $query->where('id_user', $request->kasir);
        }
        
        // Filter berdasarkan produk
        if ($request->filled('produk')) {
            $query->where('id_produk', $request->produk);
        }
        
        // Clone query untuk perhitungan (tanpa pagination)
        $queryForStats = clone $query;
        
        // Pagination
        $transactions = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Hitung total menggunakan query tanpa pagination
        $totalPendapatan = $queryForStats->sum('total_harga');
        
        // Hitung total transaksi unik (berdasarkan prefix nomor_unik)
        $totalTransaksi = DB::table('transactions')
            ->selectRaw('SUBSTRING(nomor_unik, 1, 10) as unique_prefix')
            ->when($request->filled('tanggal_mulai'), function($q) use ($request) {
                return $q->whereDate('created_at', '>=', $request->tanggal_mulai);
            })
            ->when($request->filled('tanggal_selesai'), function($q) use ($request) {
                return $q->whereDate('created_at', '<=', $request->tanggal_selesai);
            })
            ->when($request->filled('kasir'), function($q) use ($request) {
                return $q->where('id_user', $request->kasir);
            })
            ->when($request->filled('produk'), function($q) use ($request) {
                return $q->where('id_produk', $request->produk);
            })
            ->groupBy('unique_prefix')
            ->get()
            ->count();
        
        $totalItem = $queryForStats->sum('jumlah');
        
        // Data untuk filter dropdown
        $kasirList = User::where('role', 'kasir')
                        ->where('status', 'aktif')
                        ->get();
        
        $produkList = Product::where('status', 'aktif')
                            ->orderBy('nama_produk')
                            ->get();
        
        return view('owner.reports.index', compact(
            'transactions',
            'totalPendapatan',
            'totalTransaksi',
            'totalItem',
            'kasirList',
            'produkList'
        ));
    }
    
    // Print PDF Laporan Transaksi
    public function printPdf(Request $request)
    {
        $query = Transaction::with(['product', 'user']);
        
        // Terapkan filter yang sama
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }
        
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_selesai);
        }
        
        if ($request->filled('kasir')) {
            $query->where('id_user', $request->kasir);
        }
        
        if ($request->filled('produk')) {
            $query->where('id_produk', $request->produk);
        }
        
        $transactions = $query->orderBy('created_at', 'desc')->get();
        
        // Hitung statistik
        $totalPendapatan = $transactions->sum('total_harga');
        $totalTransaksi = $transactions->groupBy(function($item) {
            return substr($item->nomor_unik, 0, 10);
        })->count();
        $totalItem = $transactions->sum('jumlah');
        
        // Data filter untuk ditampilkan di PDF
        $filters = [
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'kasir' => $request->kasir ? User::find($request->kasir)->nama : 'Semua Kasir',
            'produk' => $request->produk ? Product::find($request->produk)->nama_produk : 'Semua Produk',
        ];
        
        $pdf = Pdf::loadView('owner.reports.pdf', compact(
            'transactions',
            'totalPendapatan',
            'totalTransaksi',
            'totalItem',
            'filters'
        ));
        
        $filename = 'Laporan_Transaksi_' . date('Y-m-d_His') . '.pdf';
        
        return $pdf->download($filename);
    }
    
    public function products()
    {
        $products = Product::with('category')
                          ->orderBy('nama_produk')
                          ->paginate(20);
        
        return view('owner.reports.products', compact('products'));
    }
    
    // Print PDF Data Produk
    public function productsPdf()
    {
        $products = Product::with('category')
                          ->orderBy('nama_produk')
                          ->get();
        
        $totalProduk = $products->count();
        $totalAktif = $products->where('status', 'aktif')->count();
        $totalNonaktif = $products->where('status', 'nonaktif')->count();
        $stokMenipis = $products->where('stok', '<', 10)->where('status', 'aktif')->count();
        
        $pdf = Pdf::loadView('owner.reports.products-pdf', compact(
            'products',
            'totalProduk',
            'totalAktif',
            'totalNonaktif',
            'stokMenipis'
        ));
        
        $filename = 'Data_Produk_' . date('Y-m-d_His') . '.pdf';
        
        return $pdf->download($filename);
    }
}