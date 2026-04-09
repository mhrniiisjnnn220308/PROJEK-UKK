<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Models\Booking;

class KasirTableController extends Controller
{
    public function index()
    {
        $tables = Table::orderBy('nomor_meja')->get();
        return view('kasir.tables.index', compact('tables'));
    }

    public function bebaskan($id)
    {
        $table = Table::findOrFail($id);
        $table->update(['status' => 'tersedia']);

        return redirect()->back()->with('success', 'Meja ' . $table->nomor_meja . ' berhasil dibebaskan.');
    }

    public function selesai($id)
    {
        $table = Table::findOrFail($id);

        Booking::where('id_meja', $table->id)
            ->where('status', 'konfirmasi')
            ->update(['status' => 'selesai']);

        $table->update(['status' => 'tersedia']);

        return redirect()->back()->with('success', 'Meja ' . $table->nomor_meja . ' selesai dan tersedia kembali.');
    }
}