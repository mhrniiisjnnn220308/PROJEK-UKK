<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    
    public function index()
    {
        $categories = Category::with('products')->orderBy('created_at', 'desc')->get();
        return view('admin.categories.index', compact('categories'));
    }

    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required|string|max:255|unique:categories,nama_kategori',
            'deskripsi' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Validasi gagal: ' . $validator->errors()->first());
        }

        try {
            $category = new Category();
            $category->nama_kategori = $request->nama_kategori;
            $category->deskripsi = $request->deskripsi;
            $category->status = 'aktif';
            $category->save();

            return redirect()->route('admin.categories.index')
                ->with('success', 'Kategori berhasil ditambahkan!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan kategori: ' . $e->getMessage())
                ->withInput();
        }
    }

    
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required|string|max:255|unique:categories,nama_kategori,' . $id,
            'deskripsi' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Validasi gagal: ' . $validator->errors()->first());
        }

        try {
            $category = Category::findOrFail($id);
            $category->nama_kategori = $request->nama_kategori;
            $category->deskripsi = $request->deskripsi;
            $category->save();

            return redirect()->route('admin.categories.index')
                ->with('success', 'Kategori berhasil diperbarui!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui kategori: ' . $e->getMessage())
                ->withInput();
        }
    }

    
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            
            // Cek apakah kategori memiliki produk
            if ($category->products()->count() > 0) {
                return redirect()->route('admin.categories.index')
                    ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki ' . $category->products()->count() . ' produk!');
            }
            
            $category->delete();
            
            return redirect()->route('admin.categories.index')
                ->with('success', 'Kategori berhasil dihapus!');
                
        } catch (\Exception $e) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }

    
    public function toggle($id)
    {
        try {
            $category = Category::findOrFail($id);
            
            // Ubah status
            $category->status = $category->status == 'aktif' ? 'nonaktif' : 'aktif';
            $category->save();
            
            $statusText = $category->status == 'aktif' ? 'diaktifkan' : 'dinonaktifkan';
            
            return redirect()->route('admin.categories.index')
                ->with('success', "Kategori berhasil {$statusText}!");
                
        } catch (\Exception $e) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Gagal mengubah status kategori: ' . $e->getMessage());
        }
    }
}