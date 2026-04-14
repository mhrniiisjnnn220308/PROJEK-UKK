<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'id_meja',
        'nama_pelanggan',
        'no_hp',
        'tanggal_booking',
        'jam_kedatangan',
        'catatan_pesanan',
        'jumlah_dp',
        'bukti_dp',
        'dp_verified',
        'status_pembayaran',
        'status',
    ];

    protected $casts = [
        'tanggal_booking' => 'date',
        'jumlah_dp'       => 'integer',
        'dp_verified'     => 'boolean',
    ];

    public function meja()
    {
        return $this->belongsTo(Table::class, 'id_meja');
    }

    public function transaksi()
    {
        return $this->hasOne(Transaction::class, 'booking_id');
    }
}