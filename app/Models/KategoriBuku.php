<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriBuku extends Model
{
    use HasFactory;
    protected $table = 'kategori_buku';

    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
        'parent_id',
        'icon',
        'urutan'
    ];

    public function buku()
    {
        return $this->hasMany(Buku::class, 'kategori_id');
    }

    public function parent()
    {
        return $this->belongsTo(KategoriBuku::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(KategoriBuku::class, 'parent_id');
    }
}