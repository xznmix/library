<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    use HasFactory;

    protected $table = 'stock_opname';

    protected $fillable = [
        'buku_id',
        'stok_sistem',
        'stok_fisik',
        'selisih',
        'keterangan',
        'created_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relasi ke buku
     */
    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }

    /**
     * Relasi ke user yang melakukan opname
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Accessor: status selisih
     */
    public function getStatusSelisihAttribute()
    {
        if ($this->selisih == 0) {
            return ['label' => 'Sesuai', 'color' => 'green', 'icon' => '✓'];
        } elseif ($this->stok_sistem > $this->stok_fisik) {
            return ['label' => 'Kekurangan', 'color' => 'red', 'icon' => '⬇️'];
        } else {
            return ['label' => 'Kelebihan', 'color' => 'orange', 'icon' => '⬆️'];
        }
    }

    /**
     * Scope: filter berdasarkan periode
     */
    public function scopePeriode($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope: yang memiliki selisih
     */
    public function scopeMemilikiSelisih($query)
    {
        return $query->where('selisih', '>', 0);
    }
}