<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'id_produk',
        'id_user',
        'id_meja',
        'nama_pelanggan',
        'jenis_pemesanan',
        'metode_pembayaran',      
        'status_pembayaran',      
        'nomor_unik',
        'jumlah',
        'total_harga',
        'dp_dibayar',             
        'sisa_pembayaran',        
        'uang_bayar',
        'uang_kembali',
        'tanggal_lunas',          
    ];

    protected $casts = [
        'tanggal_lunas' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'id_produk');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function table()
    {
        return $this->belongsTo(Table::class, 'id_meja');
    }
}