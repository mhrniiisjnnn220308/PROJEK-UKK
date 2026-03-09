<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

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
        
        $transactions = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Hitung total
        $totalPendapatan = $query->sum('total_harga');
        $totalTransaksi = $query->distinct('nomor_unik')->count(DB::raw('DISTINCT SUBSTRING(nomor_unik, 1, 10)'));
        $totalItem = $query->sum('jumlah');
        
        // Data untuk filter dropdown
        $kasirList = \App\Models\User::where('role', 'kasir')
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
    
    public function products()
    {
        $products = Product::with('category')
                          ->orderBy('nama_produk')
                          ->paginate(20);
        
        return view('owner.reports.products', compact('products'));
    }
}