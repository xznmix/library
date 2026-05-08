<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoinAnggota extends Model
{
    protected $table = 'poin_anggota';
    
    protected $fillable = [
        'user_id',
        'poin',
        'keterangan',
        'jenis',
        'referensi'
    ];
    
    protected $casts = [
        'poin' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Scope untuk poin tambah
     */
    public function scopeTambah($query)
    {
        return $query->where('jenis', 'tambah');
    }
    
    /**
     * Scope untuk poin kurang
     */
    public function scopeKurang($query)
    {
        return $query->where('jenis', 'kurang');
    }
    
    /**
     * Get total poin user
     */
    public static function getTotalPoin($userId)
    {
        return self::where('user_id', $userId)
            ->selectRaw('SUM(CASE WHEN jenis = "tambah" THEN poin ELSE -poin END) as total')
            ->value('total') ?? 0;
    }
    
    /**
     * Tambah poin
     */
    public static function tambahPoin($userId, $poin, $keterangan, $referensi = null)
    {
        return self::create([
            'user_id' => $userId,
            'poin' => $poin,
            'keterangan' => $keterangan,
            'jenis' => 'tambah',
            'referensi' => $referensi
        ]);
    }
    
    /**
     * Kurang poin
     */
    public static function kurangiPoin($userId, $poin, $keterangan, $referensi = null)
    {
        return self::create([
            'user_id' => $userId,
            'poin' => $poin,
            'keterangan' => $keterangan,
            'jenis' => 'kurang',
            'referensi' => $referensi
        ]);
    }

    public static function tambahPoinBacaDitempat($userId, $bukuId, $durasiMenit = 0)
    {
        $poin = 5; // poin dasar
        
        if ($durasiMenit >= 30) $poin += 5;
        if ($durasiMenit >= 60) $poin += 5;
        
        $buku = Buku::find($bukuId);
        
        return self::tambahPoin(
            $userId,
            $poin,
            "Baca di tempat: " . ($buku ? $buku->judul : 'Buku') . " selama {$durasiMenit} menit",
            'baca_ditempat'
        );
    }
}