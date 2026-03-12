<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'id_produk',
        'id_user',
        'nama_pelanggan',
        'nomor_unik',
        'jumlah',
        'total_harga',
        'uang_bayar',
        'uang_kembali',
    ];

    

    public function product()
    {
        return $this->belongsTo(Product::class, 'id_produk');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}