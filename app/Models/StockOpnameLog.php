<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpnameLog extends Model
{
    use HasFactory;

    protected $table = 'stock_opname_logs';

    protected $fillable = [
        'buku_id',
        'user_id',
        'stok_sistem',
        'stok_fisik',
        'selisih',
        'keterangan'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}