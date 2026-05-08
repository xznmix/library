<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    use HasFactory;
    protected $table = 'anggota';

    protected $fillable = [
        'user_id',
        'jenis_anggota',
        'tanggal_daftar',
        'tanggal_berakhir',
        'status_keanggotaan',
        'catatan',
    ];

    protected $casts = [
        'tanggal_daftar' => 'date',
        'tanggal_berakhir' => 'date',
    ];

    // Relasi dengan User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}