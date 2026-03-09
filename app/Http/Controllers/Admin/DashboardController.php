<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProduk = Product::where('status', 'aktif')->count();
        $totalUser = User::where('status', 'aktif')->count();
        $totalTransaksi = Transaction::count();
        $produkStokRendah = Product::where('status', 'aktif')
                                   ->where('stok', '<', 10)
                                   ->count();
        
        $logs = Log::with('user')
                   ->orderBy('created_at', 'desc')
                   ->limit(10)
                   ->get();

        return view('admin.dashboard', compact(
            'totalProduk',
            'totalUser',
            'totalTransaksi',
            'produkStokRendah',
            'logs'
        ));
    }
}