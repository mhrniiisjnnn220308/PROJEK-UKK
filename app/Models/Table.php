<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = [
        'nomor_meja',
        'kapasitas',
        'status',
        'keterangan',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'id_meja');
    }

    public function bookings()
    {
        return $this->hasMany(\App\Models\Booking::class, 'id_meja');
    }
}