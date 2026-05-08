<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kunjungan extends Model
{
    use HasFactory;
    protected $table = 'kunjungan';

    protected $fillable = [
        'user_id', 
        'nama', 
        'jenis', 
        'kelas', 
        'no_hp',      // TAMBAHKAN
        'alamat',     // TAMBAHKAN
        'tanggal', 
        'jam_masuk',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_masuk' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}