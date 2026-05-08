<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penulis extends Model
{
    use HasFactory;

    protected $table = 'penulis';

    protected $fillable = [
        'nama',
        'slug',
        'bio',
        'tanggal_lahir',
        'tempat_lahir',
        'tanggal_wafat',
        'kewarganegaraan',
        'website',
        'email',
        'foto',
        'status'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_wafat' => 'date'
    ];

    /**
     * Relasi many-to-many dengan Buku
     */
    public function buku()
    {
        return $this->belongsToMany(Buku::class, 'buku_penulis')
                    ->withTimestamps();
    }

    /**
     * Get foto URL
     */
    public function getFotoUrlAttribute()
    {
        if ($this->foto && file_exists(public_path('storage/' . $this->foto))) {
            return asset('storage/' . $this->foto);
        }
        return asset('img/default-author.jpg');
    }
}