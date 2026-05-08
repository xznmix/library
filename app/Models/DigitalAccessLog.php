<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigitalAccessLog extends Model
{
    use HasFactory;

    protected $table = 'digital_access_logs';

    protected $fillable = [
        'peminjaman_digital_id',
        'user_id',
        'buku_id',
        'ip_address',
        'user_agent',
        'lokasi_perkiraan',
        'aksi',
        'status',
        'keterangan'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    public function peminjaman()
    {
        return $this->belongsTo(PeminjamanDigital::class, 'peminjaman_digital_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }
}