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

    // ✅ TAMBAHKAN 'id' untuk fallback jika perlu
    protected $guarded = []; // Lebih aman daripada fillable jika banyak field
    
    // Atau tetap pakai fillable tapi lengkapi:
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
        'id_denda' => 'integer',  // ✅ TAMBAHKAN
        'jumlah_denda' => 'integer',
        'denda_terlambat' => 'integer',
        'denda_kerusakan' => 'integer',
        'hari_terlambat' => 'integer',
        'tanggal_bayar' => 'datetime',
        'paid_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',  // ✅ TAMBAHKAN
    ];

    // ✅ TAMBAHKAN: Attribute untuk akses mudah
    public function getAmountFormattedAttribute()
    {
        return 'Rp ' . number_format($this->jumlah_denda, 0, ',', '.');
    }

    // ✅ PERBAIKI: Method static untuk generate kode
    public static function generateKodePembayaran()
    {
        $prefix = 'DND';
        $date = date('Ymd');
        $random = strtoupper(substr(uniqid(), -6));
        return $prefix . '-' . $date . '-' . $random;
    }

    // ✅ PERBAIKI: Method cek status lebih lengkap
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid' || $this->status === 'lunas';
    }

    public function isPending(): bool
    {
        return $this->payment_status === 'pending' && $this->status !== 'lunas';
    }

    public function isFailed(): bool
    {
        return $this->payment_status === 'failed' || $this->status === 'failed';
    }

    // ✅ TAMBAHKAN: Method untuk mark as paid
    public function markAsPaid(string $method = 'qris', int $confirmedBy = null): bool
    {
        return $this->update([
            'payment_status' => 'paid',
            'status' => 'lunas',
            'paid_at' => now(),
            'payment_method' => $method,
            'confirmed_by' => $confirmedBy ?? Auth::id(),
        ]);
    }

    // ✅ TAMBAHKAN: Method untuk get order id (fallback)
    public function getOrderId(): string
    {
        return $this->midtrans_order_id ?? 'DENDA-' . ($this->id_denda ?? $this->id) . '-' . time();
    }

    // ✅ TAMBAHKAN: Accessor untuk formatted amount (pakai nama sama seperti di view)
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->jumlah_denda, 0, ',', '.');
    }

    // Relasi
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }

    public function anggota()
    {
        return $this->belongsTo(User::class, 'id_anggota');
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ✅ TAMBAHKAN: Scope untuk filter
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    // Boot method
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
            // ✅ TAMBAHKAN: Default status
            if (empty($denda->payment_status)) {
                $denda->payment_status = 'pending';
            }
            if (empty($denda->status)) {
                $denda->status = 'pending';
            }
        });
    }
}