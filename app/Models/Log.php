<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = [
        'id_user',
        'activity',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}