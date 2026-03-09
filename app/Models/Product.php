<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'kategori_id',
        'nama_produk',
        'foto',
        'deskripsi',
        'harga_produk',
        'stok',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'kategori_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'id_produk');
    }
}