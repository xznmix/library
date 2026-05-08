<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeminjamanLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'peminjaman_id', 'user_id', 'aktivitas', 'keterangan'
    ];

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}