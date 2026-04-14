<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Table;
use App\Models\Log;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['meja', 'transaksi'])
                           ->orderBy('id', 'desc')
                           ->get();

        $tables = Table::whereIn('status', ['tersedia', 'reserved'])
                       ->orderBy('nomor_meja')
                       ->get();

        return view('admin.bookings.index', compact('bookings', 'tables'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_meja'         => 'required|exists:tables,id',
            'nama_pelanggan'  => 'required|max:100',
            'no_hp'           => 'required|max:20',
            'tanggal_booking' => 'required|date',
            'jam_kedatangan'  => 'required',
            'jumlah_dp'       => 'required|integer|min:0',
            'catatan_pesanan' => 'nullable|string',
            'bukti_dp'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $buktiPath  = null;
        $dpVerified = false;

        if ($request->hasFile('bukti_dp')) {
            $file      = $request->file('bukti_dp');
            $filename  = 'dp_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/bukti_dp'), $filename);
            $buktiPath  = $filename;
            $dpVerified = true;
        }

        $booking = Booking::create([
            'id_meja'         => $request->id_meja,
            'nama_pelanggan'  => $request->nama_pelanggan,
            'no_hp'           => $request->no_hp,
            'tanggal_booking' => $request->tanggal_booking,
            'jam_kedatangan'  => $request->jam_kedatangan,
            'jumlah_dp'       => $request->jumlah_dp,
            'catatan_pesanan' => $request->catatan_pesanan,
            'bukti_dp'        => $buktiPath,
            'dp_verified'     => $dpVerified,
            'status'          => 'pending',
        ]);

        $table = Table::find($request->id_meja);
        if ($table) {
            $table->update(['status' => 'reserved']);
        }

        Log::create([
            'id_user'  => Auth::id(),
            'activity' => "Menambahkan booking untuk {$request->nama_pelanggan} pada {$request->tanggal_booking} jam {$request->jam_kedatangan} - Meja {$table->nomor_meja}"
        ]);

        return redirect()->back()->with('success', 'Booking berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $request->validate([
            'id_meja'         => 'required|exists:tables,id',
            'nama_pelanggan'  => 'required|max:100',
            'no_hp'           => 'required|max:20',
            'tanggal_booking' => 'required|date',
            'jam_kedatangan'  => 'required',
            'jumlah_dp'       => 'required|integer|min:0',
            'catatan_pesanan' => 'nullable|string',
            'bukti_dp'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'hapus_bukti_dp'  => 'nullable',
        ]);

        if ($booking->id_meja != $request->id_meja) {
            $oldTable = Table::find($booking->id_meja);
            if ($oldTable && $oldTable->status === 'reserved') {
                $oldTable->update(['status' => 'tersedia']);
            }
            $newTable = Table::find($request->id_meja);
            if ($newTable) {
                $newTable->update(['status' => 'reserved']);
            }
        }

        $buktiPath  = $booking->bukti_dp;
        $dpVerified = $booking->dp_verified;

        if ($request->hapus_bukti_dp) {
            if ($buktiPath && file_exists(public_path('uploads/bukti_dp/' . $buktiPath))) {
                unlink(public_path('uploads/bukti_dp/' . $buktiPath));
            }
            $buktiPath  = null;
            $dpVerified = false;
        }

        if ($request->hasFile('bukti_dp')) {
            if ($buktiPath && file_exists(public_path('uploads/bukti_dp/' . $buktiPath))) {
                unlink(public_path('uploads/bukti_dp/' . $buktiPath));
            }
            $file      = $request->file('bukti_dp');
            $filename  = 'dp_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/bukti_dp'), $filename);
            $buktiPath  = $filename;
            $dpVerified = true;
        }

        $booking->update([
            'id_meja'         => $request->id_meja,
            'nama_pelanggan'  => $request->nama_pelanggan,
            'no_hp'           => $request->no_hp,
            'tanggal_booking' => $request->tanggal_booking,
            'jam_kedatangan'  => $request->jam_kedatangan,
            'jumlah_dp'       => $request->jumlah_dp,
            'catatan_pesanan' => $request->catatan_pesanan,
            'bukti_dp'        => $buktiPath,
            'dp_verified'     => $dpVerified,
        ]);

        Log::create([
            'id_user'  => Auth::id(),
            'activity' => "Mengupdate booking #{$booking->id} - {$booking->nama_pelanggan}"
        ]);

        return redirect()->back()->with('success', 'Booking berhasil diupdate!');
    }

    public function uploadBukti(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $request->validate([
            'bukti_dp' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($booking->bukti_dp && file_exists(public_path('uploads/bukti_dp/' . $booking->bukti_dp))) {
            unlink(public_path('uploads/bukti_dp/' . $booking->bukti_dp));
        }

        $file     = $request->file('bukti_dp');
        $filename = 'dp_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/bukti_dp'), $filename);

        $booking->update([
            'bukti_dp'    => $filename,
            'dp_verified' => false,
        ]);

        Log::create([
            'id_user'  => Auth::id(),
            'activity' => "Upload bukti DP booking #{$booking->id} - {$booking->nama_pelanggan}"
        ]);

        return redirect()->back()->with('success', 'Bukti transfer berhasil diupload! Silakan verifikasi.');
    }

    public function verifikasiDp($id)
    {
        $booking = Booking::findOrFail($id);

        if (!$booking->bukti_dp) {
            return redirect()->back()->with('error', 'Tidak ada bukti transfer untuk diverifikasi!');
        }

        $booking->update(['dp_verified' => true]);

        Log::create([
            'id_user'  => Auth::id(),
            'activity' => "Verifikasi DP booking #{$booking->id} - {$booking->nama_pelanggan} sebesar Rp " . number_format($booking->jumlah_dp, 0, ',', '.')
        ]);

        return redirect()->back()->with('success', 'DP berhasil diverifikasi!');
    }

    public function konfirmasi($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'konfirmasi']);

        Log::create([
            'id_user'  => Auth::id(),
            'activity' => "Mengkonfirmasi booking #{$booking->id} - {$booking->nama_pelanggan}"
        ]);

        return redirect()->back()->with('success', 'Booking berhasil dikonfirmasi!');
    }

    public function selesai($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'selesai']);

        if ($booking->meja) {
            $booking->meja->update(['status' => 'tersedia']);
        }

        Log::create([
            'id_user'  => Auth::id(),
            'activity' => "Menandai booking #{$booking->id} selesai - {$booking->nama_pelanggan}"
        ]);

        return redirect()->back()->with('success', 'Booking berhasil ditandai selesai!');
    }

    public function batal($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'batal']);

        if ($booking->meja) {
            $booking->meja->update(['status' => 'tersedia']);
        }

        Log::create([
            'id_user'  => Auth::id(),
            'activity' => "Membatalkan booking #{$booking->id} - {$booking->nama_pelanggan}"
        ]);

        return redirect()->back()->with('success', 'Booking berhasil dibatalkan!');
    }

    public function destroy($id)
    {
        $booking     = Booking::findOrFail($id);
        $namaBooking = $booking->nama_pelanggan;

        if ($booking->bukti_dp && file_exists(public_path('uploads/bukti_dp/' . $booking->bukti_dp))) {
            unlink(public_path('uploads/bukti_dp/' . $booking->bukti_dp));
        }

        if ($booking->meja && $booking->meja->status === 'reserved') {
            $booking->meja->update(['status' => 'tersedia']);
        }

        $booking->delete();

        Log::create([
            'id_user'  => Auth::id(),
            'activity' => "Menghapus booking {$namaBooking}"
        ]);

        return redirect()->back()->with('success', 'Booking berhasil dihapus!');
    }


    public function cariTransaksi($id)
    {
        
        $transaksi = Transaction::where('booking_id', $id)
                                ->whereNotNull('nomor_unik')
                                ->first();

       
        if (!$transaksi) {
            $booking = Booking::find($id);

            if ($booking) {
                $transaksi = Transaction::where('nama_pelanggan', $booking->nama_pelanggan)
                                        ->where('id_meja', $booking->id_meja)
                                        ->where('jenis_pemesanan', 'dine_in')
                                        ->whereNull('booking_id')  
                                        ->whereNotNull('nomor_unik')
                                        ->orderBy('created_at', 'desc')
                                        ->first();

                
                if ($transaksi) {
                    Transaction::where('nomor_unik', $transaksi->nomor_unik)
                               ->update(['booking_id' => $id]);
                }
            }
        }

        if ($transaksi && $transaksi->nomor_unik) {
            return response()->json([
                'url' => route('kasir.transactions.print', $transaksi->nomor_unik)
            ]);
        }

        return response()->json([
            'message' => 'Transaksi untuk booking ini belum tercatat di sistem kasir.'
        ], 404);
    }
}