<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeminjamanDigital extends Model
{
    use HasFactory;

    protected $table = 'peminjaman_digital';

    protected $fillable = [
        'user_id',
        'buku_id',
        'petugas_id',
        'tanggal_pinjam',
        'tanggal_expired',
        'tanggal_dikembalikan',
        'token_akses',
        'status',
        'ip_address',
        'user_agent',
        'jumlah_akses',
        'terakhir_akses',
        'catatan'
    ];

    protected $casts = [
        'tanggal_pinjam' => 'datetime',
        'tanggal_expired' => 'datetime',
        'tanggal_dikembalikan' => 'datetime',
        'terakhir_akses' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function logs()
    {
        return $this->hasMany(DigitalAccessLog::class, 'peminjaman_digital_id');
    }

    public function isExpired()
    {
        return now()->greaterThan($this->tanggal_expired);
    }

    public function isActive()
    {
        return $this->status === 'aktif' && !$this->isExpired();
    }

    public function sisaWaktu()
    {
        if ($this->isExpired()) {
            return 0;
        }
        return now()->diffInHours($this->tanggal_expired, false);
    }

    public function sisaWaktuFormatted()
    {
        $sisa = $this->sisaWaktu();
        
        if ($sisa <= 0) {
            return '<span class="text-red-600 font-bold">Expired</span>';
        }
        
        $jam = floor($sisa);
        $menit = floor(($sisa - $jam) * 60);
        
        if ($jam >= 24) {
            $hari = floor($jam / 24);
            $jamSisa = $jam % 24;
            return "{$hari} hari {$jamSisa} jam";
        }
        
        return "{$jam} jam {$menit} menit";
    }
}