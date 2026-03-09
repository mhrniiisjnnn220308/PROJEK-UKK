<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;

class KasirDashboardController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
                          ->where('status', 'aktif')
                          ->orderBy('nama_produk', 'asc')
                          ->get();
        
        $categories = Category::where('status', 'aktif')
                             ->withCount(['products' => function($query) {
                                 $query->where('status', 'aktif');
                             }])
                             ->get();
        
        return view('kasir.transactions.dashboard', compact('products', 'categories'));
    }
}