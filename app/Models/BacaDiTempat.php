<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BacaDiTempat extends Model
{
    protected $table = 'baca_di_tempat';
    
    protected $fillable = [
        'anggota_id',           // ← menggunakan anggota_id (sesuai database)
        'buku_id', 
        'barcode_buku', 
        'no_anggota',
        'waktu_mulai', 
        'waktu_selesai', 
        'durasi_menit', 
        'poin_didapat',
        'lokasi', 
        'status', 
        'catatan',
        'petugas_id',
        'updated_by'
    ];
    
    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];
    
    /**
     * Relasi ke User (anggota)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'anggota_id');  // ← foreign key: anggota_id
    }
    
    /**
     * Relasi ke Buku
     */
    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class, 'buku_id');
    }
    
    /**
     * Relasi ke Petugas yang mencatat
     */
    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
    
    /**
     * Accessor untuk status dengan label
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'sedang_baca' => ['label' => 'Sedang Baca', 'color' => 'yellow', 'bg' => 'bg-yellow-100 text-yellow-800'],
            'selesai' => ['label' => 'Selesai', 'color' => 'green', 'bg' => 'bg-green-100 text-green-800'],
        ];
        
        return $labels[$this->status] ?? ['label' => 'Unknown', 'color' => 'gray', 'bg' => 'bg-gray-100 text-gray-800'];
    }
    
    /**
     * Hitung poin berdasarkan durasi
     */
    public function hitungPoin()
    {
        if (!$this->durasi_menit || $this->durasi_menit <= 0) {
            return 0;
        }
        
        $poinDasar = 5;
        $poinBonus = 0;
        if ($this->durasi_menit >= 30) $poinBonus += 5;
        if ($this->durasi_menit >= 60) $poinBonus += 5;
        
        return $poinDasar + $poinBonus;
    }

    // Tambahkan accessor untuk format durasi yang benar
    public function getDurasiFormattedAttribute()
    {
        if (!$this->durasi_menit || $this->durasi_menit <= 0) {
            return '0 menit';
        }
        
        $jam = floor($this->durasi_menit / 60);
        $menit = $this->durasi_menit % 60;
        
        if ($jam > 0) {
            return $jam . ' jam ' . $menit . ' menit';
        }
        return $menit . ' menit';
    }
}