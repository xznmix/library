<?php
// app/Models/Denda.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Denda extends Model
{
    use HasFactory;

    protected $table = 'denda';
    protected $primaryKey = 'id_denda';

    protected $fillable = [
        'peminjaman_id',
        'id_anggota',
        'jumlah_denda',
        'denda_terlambat',
        'denda_kerusakan',
        'hari_terlambat',
        'keterangan',
        'status',
        'payment_status',
        'payment_method',
        'kode_pembayaran',
        'midtrans_order_id',
        'midtrans_token',
        'midtrans_transaction_id',
        'qr_code_path',
        'tanggal_bayar',
        'paid_at',
        'confirmed_by',
        'created_by'
    ];

    protected $casts = [
        'jumlah_denda' => 'integer',
        'denda_terlambat' => 'integer',
        'denda_kerusakan' => 'integer',
        'hari_terlambat' => 'integer',
        'tanggal_bayar' => 'datetime',
        'paid_at' => 'datetime',
        'created_at' => 'datetime'
    ];

    public static function generateKodePembayaran()
    {
        $prefix = 'DND';
        $date = date('Ymd');
        $random = strtoupper(substr(uniqid(), -6));
        return $prefix . '-' . $date . '-' . $random;
    }

    public function isPaid()
    {
        return $this->payment_status === 'paid' || $this->status === 'lunas';
    }

    public function isPending()
    {
        return $this->payment_status === 'pending' && $this->status !== 'lunas';
    }

    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->jumlah_denda, 0, ',', '.');
    }

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }

    public function anggota()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($denda) {
            if (empty($denda->kode_pembayaran)) {
                $denda->kode_pembayaran = self::generateKodePembayaran();
            }
            if (empty($denda->created_by) && Auth::check()) {
                $denda->created_by = Auth::id();
            }
        });
    }
}