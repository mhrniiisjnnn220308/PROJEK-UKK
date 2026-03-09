<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'username',
        'password',
        'nama',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    public function logs()
    {
        return $this->hasMany(Log::class, 'id_user');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'id_user');
    }
}