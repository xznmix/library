<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        // Data Dasar
        'name',
        'email',
        'password',
        'role',
        
        // Data Identitas
        'nisn_nik',
        'jenis',
        'kelas',
        'jurusan',
        'phone',
        'pekerjaan',
        'address',
        'last_login_at',
        
        // Data Keanggotaan
        'status_anggota',
        'no_anggota',
        'tanggal_daftar',
        'masa_berlaku',
        'catatan_penolakan',
        'rejection_reason',
        'approved_at',
        'approved_by',
        
        // Data Verifikasi
        'submitted_at',
        'verification_token',
        'processed_at',
        'processed_by',
        
        // Data Upload
        'foto_ktp',
        'foto_kartu',
        
        // Status Sistem
        'status',
        'force_password_change',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'verification_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'submitted_at' => 'datetime',
        'processed_at' => 'datetime',
        'approved_at' => 'datetime',
        'tanggal_daftar' => 'date',
        'masa_berlaku' => 'date',
        'force_password_change' => 'boolean',
    ];

    protected $attributes = [
        'status' => 'active',
        'force_password_change' => true,
    ];

    /**
     * ===========================
     * RELASI
     * ===========================
     */
    public function dataAnggota()
    {
        return $this->hasOne(Anggota::class, 'user_id');
    }

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'user_id');
    }

    public function peminjamanDigital()
    {
        return $this->hasMany(PeminjamanDigital::class, 'user_id');
    }

    public function digitalAccessLogs()
    {
        return $this->hasMany(DigitalAccessLog::class, 'user_id');
    }

    public function kunjungan()
    {
        return $this->hasMany(Kunjungan::class, 'user_id');
    }

    public function favoritBuku()
    {
        return $this->hasMany(FavoritBuku::class, 'user_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * ✅ RELASI KE TABEL DENDA (DITAMBAHKAN UNTUK VERIFIKASI)
     */
    
    /**
     * Relasi ke Denda (sebagai verifikator/penyetuju)
     * Digunakan untuk statistik verifikasi per petugas di Kepala Pustaka
     */
    public function denda()
    {
        return $this->hasMany(Denda::class, 'confirmed_by');
    }

    /**
     * Relasi ke Denda (sebagai petugas yang mencatat denda)
     * Digunakan untuk melihat denda yang dicatat oleh petugas tertentu
     */
    public function dendaSebagaiPetugas()
    {
        return $this->hasMany(Denda::class, 'created_by');
    }

    /**
     * Relasi ke Denda (sebagai anggota yang kena denda)
     * Digunakan untuk melihat riwayat denda anggota
     */
    public function dendas()
    {
        return $this->hasMany(Denda::class, 'id_anggota');
    }

    /**
     * ===========================
     * SCOPES (FILTERS)
     * ===========================
     */
    public function scopeAnggota($query)
    {
        return $query->whereIn('role', ['siswa', 'guru', 'pegawai', 'umum']);
    }

    public function scopePetugas($query)
    {
        return $query->where('role', 'petugas');
    }

    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeKepalaPustaka($query)
    {
        return $query->where('role', 'kepala_pustaka');
    }

    public function scopePimpinan($query)
    {
        return $query->where('role', 'pimpinan');
    }

    public function scopePending($query)
    {
        return $query->where('status_anggota', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->where('status_anggota', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status_anggota', 'inactive');
    }

    public function scopeRejected($query)
    {
        return $query->where('status_anggota', 'rejected');
    }

    public function scopeUnverified($query)
    {
        return $query->whereNull('email_verified_at');
    }

    /**
     * Scope untuk user yang sudah aktif (verified)
     */
    public function scopeVerified($query)
    {
        return $query->where(function($q) {
            $q->whereIn('role', ['admin', 'petugas', 'kepala_pustaka', 'pimpinan'])
            ->orWhere('status_anggota', 'active');
        });
    }

    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis', $jenis);
    }

    /**
     * ===========================
     * VERIFIKASI EMAIL
     * ===========================
     */
    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }

    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
            'verification_token' => null,
        ])->save();
    }

    public function generateVerificationToken()
    {
        $this->verification_token = Str::random(60);
        $this->save();
        
        return $this->verification_token;
    }

    /**
     * ===========================
     * ACCESSORS (GETTERS)
     * ===========================
     */
    public function getStatusAnggotaLabelAttribute()
    {
        $labels = [
            'pending' => ['label' => 'Menunggu Verifikasi', 'color' => 'yellow', 'icon' => '⏳'],
            'active' => ['label' => 'Aktif', 'color' => 'green', 'icon' => '✅'],
            'inactive' => ['label' => 'Nonaktif', 'color' => 'red', 'icon' => '❌'],
            'rejected' => ['label' => 'Ditolak', 'color' => 'gray', 'icon' => '⛔'],
        ];
        
        return $labels[$this->status_anggota] ?? ['label' => 'Unknown', 'color' => 'gray', 'icon' => '❓'];
    }

    public function getJenisLabelAttribute()
    {
        $labels = [
            'siswa' => ['label' => 'Siswa', 'icon' => '🎓', 'color' => 'blue'],
            'guru' => ['label' => 'Guru', 'icon' => '👨‍🏫', 'color' => 'green'],
            'pegawai' => ['label' => 'Pegawai', 'icon' => '💼', 'color' => 'purple'],
            'umum' => ['label' => 'Umum', 'icon' => '👤', 'color' => 'gray'],
        ];
        
        return $labels[$this->jenis] ?? ['label' => 'Umum', 'icon' => '👤', 'color' => 'gray'];
    }

    /**
     * Get verification status for display
     */
    public function getVerificationStatusAttribute()
    {
        if ($this->status_anggota === 'active') {
            return 'approved';
        } elseif ($this->status_anggota === 'rejected') {
            return 'rejected';
        }
        return 'pending';
    }

    public function getFotoKtpUrlAttribute()
    {
        if ($this->foto_ktp && file_exists(public_path('storage/' . $this->foto_ktp))) {
            return asset('storage/' . $this->foto_ktp);
        }
        return asset('img/default-ktp.jpg');
    }

    public function getFotoKartuUrlAttribute()
    {
        if ($this->foto_kartu && file_exists(public_path('storage/' . $this->foto_kartu))) {
            return asset('storage/' . $this->foto_kartu);
        }
        return asset('img/default-card.jpg');
    }

    /**
     * ===========================
     * METHODS
     * ===========================
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isPetugas()
    {
        return $this->role === 'petugas';
    }

    public function isAnggota()
    {
        return in_array($this->role, ['siswa', 'guru', 'pegawai', 'umum']);
    }

    public function isKepalaPustaka()
    {
        return $this->role === 'kepala_pustaka';
    }

    public function isPimpinan()
    {
        return $this->role === 'pimpinan';
    }

    public function canBorrow()
    {
        return $this->status_anggota === 'active' && 
               $this->status === 'active' && 
               $this->hasVerifiedEmail() &&
               !$this->hasExpired();
    }

    public function hasExpired()
    {
        if (!$this->masa_berlaku) {
            return false;
        }
        return now()->greaterThan($this->masa_berlaku);
    }

    public function extendMembership($tahun = 1)
    {
        if (!$this->masa_berlaku) {
            $this->masa_berlaku = now()->addYears($tahun);
        } else {
            $this->masa_berlaku = $this->masa_berlaku->addYears($tahun);
        }
        $this->status_anggota = 'active';
        return $this->save();
    }

    public function sedangMeminjamDigital($bukuId)
    {
        return $this->peminjamanDigital()
            ->where('buku_id', $bukuId)
            ->where('status', 'aktif')
            ->where('tanggal_expired', '>', now())
            ->exists();
    }

    public function totalPinjamanDigitalAktif()
    {
        return $this->peminjamanDigital()
            ->where('status', 'aktif')
            ->where('tanggal_expired', '>', now())
            ->count();
    }

    public function getPinjamanDigitalAktif()
    {
        return $this->peminjamanDigital()
            ->with('buku')
            ->where('status', 'aktif')
            ->where('tanggal_expired', '>', now())
            ->get();
    }

    /**
     * ===========================
     * BOOT METHOD
     * ===========================
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // Set default jenis berdasarkan role jika tidak diisi
            if (in_array($user->role, ['siswa', 'guru', 'pegawai', 'umum']) && !$user->jenis) {
                $user->jenis = $user->role;
            }
            
            // Set tanggal daftar
            $user->tanggal_daftar = now();
            
            // Set submitted_at untuk pendaftaran baru
            if (!$user->submitted_at && $user->status_anggota === 'pending') {
                $user->submitted_at = now();
            }
        });

        static::updating(function ($user) {
            // Jika status diubah menjadi active, set masa berlaku jika belum ada
            if ($user->isDirty('status_anggota') && $user->status_anggota === 'active' && !$user->masa_berlaku) {
                $user->masa_berlaku = now()->addYear();
                $user->processed_at = now();
            }
        });
    }

    /**
     * Relasi ke poin anggota
     */
    public function poinAnggota()
    {
        return $this->hasMany(PoinAnggota::class, 'user_id');
    }
    
    /**
     * Get total poin user
     */
    public function getTotalPoinAttribute()
    {
        return PoinAnggota::getTotalPoin($this->id);
    }
    
    /**
     * Get peringkat user
     */
    public function getPeringkatAttribute()
    {
        $totalPoin = $this->total_poin;
        
        $peringkat = PoinAnggota::select('user_id')
            ->groupBy('user_id')
            ->selectRaw('SUM(CASE WHEN jenis = "tambah" THEN poin ELSE -poin END) as total')
            ->having('total', '>', $totalPoin)
            ->count();
            
        return $peringkat + 1;
    }
}