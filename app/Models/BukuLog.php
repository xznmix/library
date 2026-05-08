<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BukuLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'buku_id',
        'aktivitas',
        'jumlah',
        'keterangan',
        'user_id'
    ];

    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}