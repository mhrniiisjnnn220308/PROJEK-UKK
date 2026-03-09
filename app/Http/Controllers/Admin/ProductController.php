<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->orderBy('created_at', 'desc')->get();
        $categories = Category::where('status', 'aktif')->get();
        return view('admin.products.index', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:categories,id',
            'nama_produk' => 'required|max:45',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'deskripsi' => 'nullable',
            'harga_produk' => 'required|integer',
            'stok' => 'required|integer',
        ]);

        $data = $request->all();

        // Upload foto jika ada
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/products'), $filename);
            $data['foto'] = $filename;
        }

        Product::create($data);

        Log::create([
            'id_user' => Auth::id(),
            'activity' => "Menambahkan produk: {$request->nama_produk}"
        ]);

        return redirect()->route('admin.products.index')
                         ->with('success', 'Produk berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori_id' => 'required|exists:categories,id',
            'nama_produk' => 'required|max:45',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'deskripsi' => 'nullable',
            'harga_produk' => 'required|integer',
            'stok' => 'required|integer',
        ]);

        $product = Product::findOrFail($id);
        $data = $request->all();

        // Upload foto baru jika ada
        if ($request->hasFile('foto')) {
            // Hapus foto lama
            if ($product->foto && file_exists(public_path('uploads/products/' . $product->foto))) {
                unlink(public_path('uploads/products/' . $product->foto));
            }

            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/products'), $filename);
            $data['foto'] = $filename;
        }

        $product->update($data);

        Log::create([
            'id_user' => Auth::id(),
            'activity' => "Mengupdate produk: {$request->nama_produk}"
        ]);

        return redirect()->route('admin.products.index')
                         ->with('success', 'Produk berhasil diupdate!');
    }

    public function toggleStatus($id)
    {
        $product = Product::findOrFail($id);
        $newStatus = $product->status == 'aktif' ? 'nonaktif' : 'aktif';
        $product->update(['status' => $newStatus]);

        Log::create([
            'id_user' => Auth::id(),
            'activity' => "Mengubah status produk '{$product->nama_produk}' menjadi {$newStatus}"
        ]);

        return redirect()->route('admin.products.index')
                         ->with('success', 'Status produk berhasil diubah!');
    }
}