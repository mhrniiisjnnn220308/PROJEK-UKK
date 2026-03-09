<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('created_at', 'desc')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|max:45',
            'deskripsi' => 'nullable',
        ]);

        Category::create($request->all());

        Log::create([
            'id_user' => Auth::id(),
            'activity' => "Menambahkan kategori: {$request->nama_kategori}"
        ]);

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kategori' => 'required|max:45',
            'deskripsi' => 'nullable',
        ]);

        $category = Category::findOrFail($id);
        $category->update($request->all());

        Log::create([
            'id_user' => Auth::id(),
            'activity' => "Mengupdate kategori: {$request->nama_kategori}"
        ]);

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Kategori berhasil diupdate!');
    }

    public function toggleStatus($id)
    {
        $category = Category::findOrFail($id);
        $newStatus = $category->status == 'aktif' ? 'nonaktif' : 'aktif';
        $category->update(['status' => $newStatus]);

        Log::create([
            'id_user' => Auth::id(),
            'activity' => "Mengubah status kategori '{$category->nama_kategori}' menjadi {$newStatus}"
        ]);

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Status kategori berhasil diubah!');
    }
}