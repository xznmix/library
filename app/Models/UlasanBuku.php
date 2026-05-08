<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UlasanBuku extends Model
{
    protected $table = 'ulasan_buku';
    
    protected $fillable = [
        'user_id',
        'buku_id',
        'rating',
        'ulasan',
        'is_approved'
    ];
    
    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Relasi ke Buku
     */
    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class);
    }
    
    /**
     * Scope untuk ulasan yang sudah disetujui
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }
    
    /**
     * Hitung rata-rata rating buku
     */
    public static function getAverageRating($bukuId)
    {
        return self::where('buku_id', $bukuId)
            ->approved()
            ->avg('rating') ?? 0;
    }
    
    /**
     * Hitung total rating
     */
    public static function getTotalRatings($bukuId)
    {
        return self::where('buku_id', $bukuId)
            ->approved()
            ->count();
    }
    
    /**
     * Get rating distribution
     */
    public static function getRatingDistribution($bukuId)
    {
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = self::where('buku_id', $bukuId)
                ->approved()
                ->where('rating', $i)
                ->count();
        }
        return $distribution;
    }
    
    /**
     * Cek apakah user sudah memberikan rating
     */
    public static function hasUserRated($userId, $bukuId)
    {
        return self::where('user_id', $userId)
            ->where('buku_id', $bukuId)
            ->exists();
    }
    
    /**
     * Get user's rating for a book
     */
    public static function getUserRating($userId, $bukuId)
    {
        $rating = self::where('user_id', $userId)
            ->where('buku_id', $bukuId)
            ->first();
            
        return $rating ? $rating->rating : null;
    }
}