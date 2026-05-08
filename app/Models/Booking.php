<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';

    protected $fillable = [
        'kode_booking',
        'user_id',
        'buku_id',
        'tanggal_booking',
        'tanggal_ambil',
        'batas_ambil',
        'status',
        'catatan_penolakan',
        'petugas_id',
        'diproses_menjadi_peminjaman_id',
    ];

    protected $casts = [
        'tanggal_booking' => 'date',
        'tanggal_ambil' => 'date',
        'batas_ambil' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot method untuk generate kode booking otomatis
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->kode_booking)) {
                $booking->kode_booking = self::generateKodeBooking();
            }
        });
    }

    /**
     * Generate kode booking unik
     */
    public static function generateKodeBooking()
    {
        $prefix = 'BK';
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(4));
        $kode = $prefix . $date . $random;
        
        // Cek uniqueness
        while (self::where('kode_booking', $kode)->exists()) {
            $random = strtoupper(Str::random(4));
            $kode = $prefix . $date . $random;
        }
        
        return $kode;
    }

    /**
     * Relasi ke User (anggota)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Buku
     */
    public function buku()
    {
        return $this->belongsTo(Buku::class, 'buku_id');
    }

    /**
     * Relasi ke Petugas yang memproses
     */
    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    /**
     * Relasi ke Peminjaman (setelah diambil)
     */
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'diproses_menjadi_peminjaman_id');
    }

    /**
     * Accessor: Label status dengan badge
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'menunggu' => ['label' => 'Menunggu Konfirmasi', 'color' => 'yellow', 'icon' => '⏳'],
            'disetujui' => ['label' => 'Disetujui - Ambil Buku', 'color' => 'green', 'icon' => '✅'],
            'ditolak' => ['label' => 'Ditolak', 'color' => 'red', 'icon' => '❌'],
            'diambil' => ['label' => 'Sudah Diambil', 'color' => 'blue', 'icon' => '📖'],
            'hangus' => ['label' => 'Hangus', 'color' => 'gray', 'icon' => '⏰'],
        ];
        
        return $labels[$this->status] ?? ['label' => $this->status, 'color' => 'gray', 'icon' => '❓'];
    }

    /**
     * Cek apakah booking masih bisa diambil
     */
    public function isStillValid()
    {
        return $this->status === 'disetujui' && now()->lessThan($this->batas_ambil);
    }

    /**
     * Cek apakah booking sudah hangus
     */
    public function isExpired()
    {
        return $this->status === 'disetujui' && now()->greaterThan($this->batas_ambil);
    }
}