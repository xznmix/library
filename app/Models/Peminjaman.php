<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
    {
        use HasFactory;

        protected $table = 'peminjaman';

        protected $fillable = [
        'user_id',
        'buku_id',
        'petugas_id', // <-- TAMBAHKAN
        'kode_eksemplar',
        'tanggal_pinjam',
        'tgl_jatuh_tempo',
        'tanggal_pengembalian',
        'status_pinjam',
        'is_perpanjangan',           // ✅ TAMBAHKAN
        'parent_peminjaman_id',      // ✅ TAMBAHKAN
        'status_verifikasi', // <-- TAMBAHKAN
        'diverifikasi_oleh', // <-- TAMBAHKAN
        'diverifikasi_at', // <-- TAMBAHKAN
        'catatan_verifikasi', // <-- TAMBAHKAN
        'denda',
        'denda_rusak', // <-- TAMBAHKAN
        'denda_total', // <-- TAMBAHKAN
        'denda_asli', // <-- TAMBAHKAN
        'kondisi_kembali', // <-- TAMBAHKAN
        'catatan_kondisi', // <-- TAMBAHKAN
        'keterangan',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'datetime',
        'tgl_jatuh_tempo' => 'datetime',
        'tanggal_pengembalian' => 'datetime',
        'diverifikasi_at' => 'datetime',
        'is_perpanjangan' => 'boolean',  // ✅ TAMBAHKAN
    ];

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
     * Relasi ke Petugas yang membuat
     */
    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    // ========== TAMBAHKAN RELASI INI ==========
    /**
     * Relasi ke user yang memverifikasi (kepala pustaka)
     */
    public function diverifikasiOleh()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    /**
     * Relasi ke Log Peminjaman
     */
    public function logs()
    {
        return $this->hasMany(PeminjamanLog::class, 'peminjaman_id');
    }

    /**
     * Accessor untuk status dengan label
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'dipinjam' => ['label' => 'Dipinjam', 'color' => 'blue', 'bg' => 'bg-blue-100 text-blue-800'],
            'terlambat' => ['label' => 'Terlambat', 'color' => 'red', 'bg' => 'bg-red-100 text-red-800'],
            'dikembalikan' => ['label' => 'Dikembalikan', 'color' => 'green', 'bg' => 'bg-green-100 text-green-800'],
        ];
        
        return $labels[$this->status_pinjam] ?? ['label' => 'Unknown', 'color' => 'gray', 'bg' => 'bg-gray-100 text-gray-800'];
    }

    /**
     * Get status verifikasi label
     */
    public function getStatusVerifikasiLabelAttribute()
    {
        $labels = [
            'pending' => ['label' => 'Menunggu Verifikasi', 'color' => 'yellow'],
            'disetujui' => ['label' => 'Disetujui', 'color' => 'green'],
            'ditolak' => ['label' => 'Ditolak', 'color' => 'red'],
            'selesai' => ['label' => 'Selesai', 'color' => 'blue'],
        ];
        
        return $labels[$this->status_verifikasi] ?? ['label' => $this->status_verifikasi, 'color' => 'gray'];
    }

    /**
     * Hitung denda otomatis
     */
    public function hitungDenda()
    {
        if ($this->status_pinjam == 'dikembalikan') {
            return $this->denda;
        }
        
        $jatuhTempo = \Carbon\Carbon::parse($this->tgl_jatuh_tempo);
        $hariIni = \Carbon\Carbon::now();
        
        if ($hariIni <= $jatuhTempo) {
            return 0;
        }
        
        $hariTerlambat = $jatuhTempo->diffInDays($hariIni);
        $dendaPerHari = $this->buku->denda_per_hari ?? 1000;
        
        return $hariTerlambat * $dendaPerHari;
    }

    /**
     * Update denda total dengan sinkronisasi ke tabel denda
     */
    public function updateDendaTotal($dendaTerlambat, $dendaRusak = 0, $hariTerlambat = 0)
    {
        $dendaTotal = (int)$dendaTerlambat + (int)$dendaRusak;
        
        $this->denda_total = $dendaTotal;
        $this->extra_attributes = array_merge($this->extra_attributes ?? [], [
            'denda_terlambat' => $dendaTerlambat,
            'denda_rusak' => $dendaRusak,
            'hari_terlambat' => $hariTerlambat
        ]);
        $this->save();
        
        // Sinkronkan ke tabel denda
        if ($dendaTotal > 0) {
            Denda::updateOrCreate(
                ['peminjaman_id' => $this->id],
                [
                    'id_anggota' => $this->user_id,
                    'jumlah_denda' => $dendaTotal,
                    'denda_terlambat' => $dendaTerlambat,
                    'denda_kerusakan' => $dendaRusak,
                    'hari_terlambat' => $hariTerlambat,
                    'keterangan' => $this->catatan_kondisi,
                    'status' => 'pending',
                    'payment_status' => 'pending'
                ]
            );
        }
        
        return $dendaTotal;
    }

    // Relasi ke peminjaman asal (jika ini hasil perpanjangan)
    public function parentPeminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'parent_peminjaman_id');
    }

    // Relasi ke perpanjangan (jika ini peminjaman asal)
    public function perpanjangan()
    {
        return $this->hasOne(Peminjaman::class, 'parent_peminjaman_id');
    }
}