<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DigitalAccess extends Model
{
    protected $table = 'digital_access';

    protected $fillable = [
        'user_id',
        'buku_id',
        'token_akses',
        'tanggal_expired',
        'status'
    ];

    public function buku()
    {
        return $this->belongsTo(BukuDigital::class, 'buku_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}