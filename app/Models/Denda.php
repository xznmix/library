<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class Denda extends Model
{
    use HasFactory;

    protected $table = 'denda';

    /*
    |--------------------------------------------------------------------------
    | PRIMARY KEY
    |--------------------------------------------------------------------------
    | Migration Anda: $table->id() → kolom "id" (bukan "id_denda")
    | Jika Anda sudah terlanjur pakai id_denda di banyak tempat,
    | accessor getIdDendaAttribute() di bawah akan menjadi bridge-nya.
    */
    protected $primaryKey = 'id';
    public    $incrementing = true;
    protected $keyType      = 'int';

    /*
    |--------------------------------------------------------------------------
    | FILLABLE
    |--------------------------------------------------------------------------
    */
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
        'payment_verified_by',
    ];

    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'jumlah_denda'    => 'integer',
        'denda_terlambat' => 'integer',
        'denda_kerusakan' => 'integer',
        'hari_terlambat'  => 'integer',
        'tanggal_bayar'   => 'datetime',
        'paid_at'         => 'datetime',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    /*
    |=========================================================================
    | BOOT — auto-generate kode_pembayaran & default status
    |=========================================================================
    */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Denda $denda) {
            if (empty($denda->kode_pembayaran)) {
                $denda->kode_pembayaran = self::generateKodePembayaran();
            }
            if (empty($denda->payment_status)) {
                $denda->payment_status = 'pending';
            }
            if (empty($denda->status)) {
                $denda->status = 'pending';
            }
        });
    }

    /*
    |=========================================================================
    | RELASI
    |=========================================================================
    */

    /**
     * Peminjaman terkait
     */
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id', 'id');
    }

    /**
     * Anggota / user yang kena denda
     * (foreign key: id_anggota → users.id)
     */
    public function anggota()
    {
        return $this->belongsTo(User::class, 'id_anggota', 'id');
    }

    /**
     * Petugas yang konfirmasi pembayaran
     */
    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by', 'id');
    }

    /*
    |=========================================================================
    | ACCESSORS
    |=========================================================================
    */

    /**
     * Alias: $denda->id_denda → supaya kode lama yang pakai id_denda tetap jalan.
     * Contoh pemakaian: redirect()->route('...', $denda->id_denda)
     */
    public function getIdDendaAttribute(): int
    {
        return (int) $this->id;
    }

    /**
     * Format rupiah untuk tampil di view.
     * Pemakaian: {{ $denda->formatted_amount }}
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format((int) $this->jumlah_denda, 0, ',', '.');
    }

    /**
     * Format rupiah versi dua (alias agar tidak breaking jika ada yang pakai ini).
     * Pemakaian: {{ $denda->amount_formatted }}
     */
    public function getAmountFormattedAttribute(): string
    {
        return $this->formatted_amount;
    }

    /*
    |=========================================================================
    | QUERY SCOPES
    |=========================================================================
    */

    /** Denda yang belum dibayar */
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    /** Denda yang sudah lunas */
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    /** Denda yang gagal */
    public function scopeFailed($query)
    {
        return $query->where('payment_status', 'failed');
    }

    /*
    |=========================================================================
    | STATUS METHODS
    |=========================================================================
    */

    /** Cek apakah denda sudah lunas */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid'
            || $this->status === 'lunas';
    }

    /** Cek apakah denda masih pending */
    public function isPending(): bool
    {
        return $this->payment_status === 'pending'
            && $this->status !== 'lunas';
    }

    /** Cek apakah pembayaran gagal */
    public function isFailed(): bool
    {
        return $this->payment_status === 'failed'
            || $this->status === 'failed';
    }

    /*
    |=========================================================================
    | BUSINESS LOGIC METHODS
    |=========================================================================
    */

    /**
     * Tandai denda sebagai lunas.
     * Dipakai di PaymentController::markAsPaid() dan confirmPembayaran().
     */
    public function markAsPaid(string $method = 'qris', ?int $confirmedBy = null): bool
    {
        return $this->update([
            'payment_status' => 'paid',
            'status'         => 'lunas',
            'paid_at'        => now(),
            'payment_method' => $method,
            'confirmed_by'   => $confirmedBy ?? Auth::id(),
        ]);
    }

    /**
     * Ambil order_id Midtrans, fallback ke format manual jika belum ada.
     * Dipakai di PaymentController::generateQRIS()
     */
    public function getOrderId(): string
    {
        return $this->midtrans_order_id
            ?? ('DENDA-' . $this->id . '-' . time());
    }

    /**
     * Generate kode pembayaran unik.
     * Format: DND-20250515-AB12C3
     */
    public static function generateKodePembayaran(): string
    {
        $prefix = 'DND';
        $date   = date('Ymd');
        $random = strtoupper(substr(uniqid(), -6));
        return "{$prefix}-{$date}-{$random}";
    }
}