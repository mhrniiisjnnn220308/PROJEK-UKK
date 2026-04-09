<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        $tables = Table::all();
        return view('admin.tables.index', compact('tables'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_meja' => 'required|unique:tables,nomor_meja',
            'kapasitas'  => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        Table::create([
            'nomor_meja' => $request->nomor_meja,
            'kapasitas'  => $request->kapasitas,
            'keterangan' => $request->keterangan,
            'status'     => 'tersedia',
        ]);

        return redirect()->back()->with('success', 'Meja berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $table = Table::findOrFail($id);

        $request->validate([
            'nomor_meja' => 'required|unique:tables,nomor_meja,' . $id,
            'kapasitas'  => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $table->update([
            'nomor_meja' => $request->nomor_meja,
            'kapasitas'  => $request->kapasitas,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->back()->with('success', 'Meja berhasil diupdate');
    }

    public function destroy($id)
    {
        $table = Table::findOrFail($id);

        if ($table->status == 'terisi') {
            return redirect()->back()->with('error', 'Meja sedang terisi, tidak bisa dihapus');
        }

        $table->delete();
        return redirect()->back()->with('success', 'Meja berhasil dihapus');
    }

    public function toggleStatus($id)
    {
        $table = Table::findOrFail($id);
        $table->status = $table->status == 'tersedia' ? 'reserved' : 'tersedia';
        $table->save();

        return redirect()->back()->with('success', 'Status meja berhasil diubah');
    }

    public function setTersedia($id)
    {
        $table = Table::findOrFail($id);
        $table->status = 'tersedia';
        $table->save();

        return redirect()->back()->with('success', 'Meja sudah tersedia kembali');
    }


    public function reserve($id)
    {
        $table = Table::findOrFail($id);
        $table->status = 'booking';
        $table->save();

        return redirect()->back()->with('success', 'Meja berhasil direservasi');
    }
}