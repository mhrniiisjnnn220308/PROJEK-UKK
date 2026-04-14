<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Table;
use App\Models\Booking;

class KasirDashboardController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
                           ->where('status', 'aktif')
                           ->orderBy('nama_produk')
                           ->get();

        $categories = Category::where('status', 'aktif')
                               ->withCount(['products' => function ($q) {
                                   $q->where('status', 'aktif');
                               }])
                               ->get();

        $tables = Table::whereIn('status', ['tersedia', 'reserved'])
                       ->orderBy('nomor_meja')
                       ->get();

        
        $bookings = Booking::with('meja')
                           ->where('status', 'konfirmasi')
                           ->orderBy('tanggal_booking')
                           ->orderBy('jam_kedatangan')
                           ->get();

        return view('kasir.transactions.dashboard',
                    compact('products', 'categories', 'tables', 'bookings'));
    }
}