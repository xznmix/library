<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BukuDigital extends Model
{
    protected $table = 'buku_digital';

    protected $fillable = [
        'judul',
        'pengarang',
        'isbn',
        'file_path',
        'jumlah_lisensi',
        'lisensi_dipinjam',
        'harga'
    ];

    public function access()
    {
        return $this->hasMany(DigitalAccess::class, 'buku_id');
    }
}