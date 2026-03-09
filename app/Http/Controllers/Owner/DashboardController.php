<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Log;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik
        $totalProduk = Product::where('status', 'aktif')->count();
        $totalTransaksi = Transaction::count();
        $totalPendapatan = Transaction::sum('total_harga');
        $totalUser = User::where('status', 'aktif')->count();
        
        // Transaksi hari ini
        $transaksiHariIni = Transaction::whereDate('created_at', today())
                                      ->distinct('nomor_unik')
                                      ->count(DB::raw('DISTINCT SUBSTRING(nomor_unik, 1, 10)'));
        
        $pendapatanHariIni = Transaction::whereDate('created_at', today())
                                       ->sum('total_harga');
        
        // Produk terlaris (5 teratas)
        $produkTerlaris = Transaction::select('id_produk', DB::raw('SUM(jumlah) as total_terjual'))
                                    ->groupBy('id_produk')
                                    ->orderBy('total_terjual', 'desc')
                                    ->limit(5)
                                    ->with('product')
                                    ->get();
        
        // Transaksi terbaru (10 terakhir)
        $transaksiTerbaru = Transaction::with(['product', 'user'])
                                      ->orderBy('created_at', 'desc')
                                      ->limit(10)
                                      ->get()
                                      ->groupBy('nomor_unik')
                                      ->take(5);
        
        // Log aktivitas terbaru
        $logTerbaru = Log::with('user')
                        ->orderBy('created_at', 'desc')
                        ->limit(10)
                        ->get();
        
        return view('owner.dashboard', compact(
            'totalProduk',
            'totalTransaksi',
            'totalPendapatan',
            'totalUser',
            'transaksiHariIni',
            'pendapatanHariIni',
            'produkTerlaris',
            'transaksiTerbaru',
            'logTerbaru'
        ));
    }
}