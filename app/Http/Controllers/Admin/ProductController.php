<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
   
    public function index()
    {
        $products = Product::with('category')->get();
        $categories = Category::where('status', 'aktif')->get();
        return view('admin.products.index', compact('products', 'categories'));
    }

    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_produk' => 'required|string|max:255',
            'kategori_id' => 'required|exists:categories,id',
            'harga_produk' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'deskripsi' => 'required|string',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Validasi gagal: ' . $validator->errors()->first());
        }

        try {
            $product = new Product();
            $product->nama_produk = $request->nama_produk;
            $product->kategori_id = $request->kategori_id;
            $product->harga_produk = $request->harga_produk;
            $product->stok = $request->stok;
            $product->deskripsi = $request->deskripsi;
            $product->status = 'aktif';

            // Upload foto
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('uploads/products', $filename, 'public');
                $product->foto = $filename;
            }

            $product->save();

            return redirect()->route('admin.products.index')
                ->with('success', 'Produk berhasil ditambahkan!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan produk: ' . $e->getMessage())
                ->withInput();
        }
    }

    
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_produk' => 'required|string|max:255',
            'kategori_id' => 'required|exists:categories,id',
            'harga_produk' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'deskripsi' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Validasi gagal: ' . $validator->errors()->first());
        }

        try {
            $product = Product::findOrFail($id);
            $product->nama_produk = $request->nama_produk;
            $product->kategori_id = $request->kategori_id;
            $product->harga_produk = $request->harga_produk;
            $product->stok = $request->stok;
            $product->deskripsi = $request->deskripsi;

            // Upload foto baru jika ada
            if ($request->hasFile('foto')) {
                // Hapus foto lama
                if ($product->foto && Storage::disk('public')->exists('uploads/products/' . $product->foto)) {
                    Storage::disk('public')->delete('uploads/products/' . $product->foto);
                }
                
                $file = $request->file('foto');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('uploads/products', $filename, 'public');
                $product->foto = $filename;
            }

            $product->save();

            return redirect()->route('admin.products.index')
                ->with('success', 'Produk berhasil diperbarui!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui produk: ' . $e->getMessage())
                ->withInput();
        }
    }

    
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            
            // Hapus file foto jika ada
            if ($product->foto && Storage::disk('public')->exists('uploads/products/' . $product->foto)) {
                Storage::disk('public')->delete('uploads/products/' . $product->foto);
            }
            
            $product->delete();
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Produk berhasil dihapus!');
                
        } catch (\Exception $e) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }

    
    public function toggle($id)
    {
        try {
            $product = Product::findOrFail($id);
            
            // Ubah status
            $product->status = $product->status == 'aktif' ? 'nonaktif' : 'aktif';
            $product->save();
            
            $statusText = $product->status == 'aktif' ? 'diaktifkan' : 'dinonaktifkan';
            
            return redirect()->route('admin.products.index')
                ->with('success', "Produk berhasil {$statusText}!");
                
        } catch (\Exception $e) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Gagal mengubah status produk: ' . $e->getMessage());
        }
    }
}