<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Buku extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel di database
     */
    protected $table = 'buku';

    /**
     * Kolom yang bisa diisi (mass assignment)
     */
    protected $fillable = [
        // Identitas Buku
        'judul',
        'sub_judul',
        'pernyataan_tanggungjawab',
        'pengarang',
        'pengarang_badan',
        'pengarang_tambahan',
        'penerbit',
        'kota_terbit',
        'tahun_terbit',
        'isbn',
        'issn',
        'edisi',
        'cetakan',
        'jumlah_halaman',
        'bahasa',
        
        // Klasifikasi
        'kategori_id',
        'sub_kategori',
        'klasifikasi',
        'no_ddc',
        'nomor_panggil',
        'nomor_panggil_katalog',
        'rak',
        'baris',
        
        // Fisik Buku
        'tipe',
        'jenis_koleksi',
        'bisa_download',
        'format',
        'sampul',
        'ukuran',
        'berat',
        'warna',
        'kategori_koleksi',
        'lokasi',
        'ketersediaan',
        'jumlah_eksemplar',
        
        // Status dan Stok
        'stok',
        'stok_tersedia',
        'stok_direservasi',
        'stok_dipinjam',
        'stok_rusak',
        'stok_hilang',
        'status',
        
        // Statistik
        'total_dipinjam',
        'total_denda',
        'rating',
        'jumlah_ulasan',
        'views',
        
        // Digital
        'file_path',
        'file_size',
        'file_type',
        'cover_path',
        'drm_enabled',
        'drm_key',
        'access_level',
        
        // FIELD LISENSI DIGITAL
        'jumlah_lisensi',
        'lisensi_dipinjam',
        'akses_digital',
        'durasi_pinjam_hari',
        'tanggal_berlaku_lisensi',
        'tanggal_kadaluarsa_lisensi',
        'penerbit_lisensi',
        'catatan_lisensi',
        
        // Data Eksemplar
        'barcode',
        'rfid',
        'sumber_jenis',
        'sumber_nama',
        'kode_lokasi_perpus',
        'kode_lokasi_ruang',
        
        // Data Terbitan Berkala
        'edisi_serial',
        'tanggal_terbit_serial',
        'bahan_sertaan',
        
        // Kata Kunci
        'kata_kunci',
        
        // Tanggal
        'tanggal_pengadaan',
        'tanggal_terbit',
        'terakhir_dipinjam',
        
        // Keterangan
        'deskripsi',
        'sinopsis',
        'daftar_isi',
        'catatan',
        
        // Sumber
        'sumber_id',
        'harga',
        'harga_sewa',
        'denda_per_hari',
        
        // Metadata
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    /**
     * Kolom yang disembunyikan dalam array/JSON
     */
    protected $hidden = [
        'drm_key',
        'deleted_by'
    ];

    /**
     * Casting tipe data
     */
    protected $casts = [
        'tahun_terbit' => 'integer',
        'jumlah_halaman' => 'integer',
        'stok' => 'integer',
        'stok_tersedia' => 'integer',
        'stok_direservasi' => 'integer',
        'stok_dipinjam' => 'integer',
        'stok_rusak' => 'integer',
        'stok_hilang' => 'integer',
        'total_dipinjam' => 'integer',
        'total_denda' => 'decimal:2',
        'rating' => 'decimal:2',
        'jumlah_ulasan' => 'integer',
        'views' => 'integer',
        'file_size' => 'integer',
        'harga' => 'decimal:2',
        'harga_sewa' => 'decimal:2',
        'denda_per_hari' => 'decimal:2',
        'drm_enabled' => 'boolean',
        'bisa_download' => 'boolean',
        
        // CAST UNTUK FIELD LISENSI
        'jumlah_lisensi' => 'integer',
        'lisensi_dipinjam' => 'integer',
        'durasi_pinjam_hari' => 'integer',
        'tanggal_berlaku_lisensi' => 'date',
        'tanggal_kadaluarsa_lisensi' => 'date',
        
        'tanggal_pengadaan' => 'date',
        'tanggal_terbit' => 'date',
        'tanggal_terbit_serial' => 'date',
        'terakhir_dipinjam' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Nilai default untuk atribut tertentu
     */
    protected $attributes = [
        'stok' => 0,
        'stok_tersedia' => 0,
        'stok_dipinjam' => 0,
        'stok_rusak' => 0,
        'stok_hilang' => 0,
        'total_dipinjam' => 0,
        'total_denda' => 0,
        'rating' => 0,
        'jumlah_ulasan' => 0,
        'views' => 0,
        'drm_enabled' => false,
        'harga' => 0,
        'harga_sewa' => 0,
        'denda_per_hari' => 500,
        'lokasi' => 'Ruang Baca Umum Perpustakaan Tambang Ilmu',
        'status' => 'tersedia',
        'tipe' => 'fisik',
        'bahasa' => 'Indonesia',
        'access_level' => 'member_only',
        'jenis_koleksi' => 'ebook',
        'bisa_download' => false,
        
        // DEFAULT UNTUK FIELD LISENSI
        'jumlah_lisensi' => 1,
        'lisensi_dipinjam' => 0,
        'akses_digital' => 'online_only',
        'durasi_pinjam_hari' => 7
    ];

    /**
     * ===========================
     * RELASI
     * ===========================
     */

    /**
     * Relasi ke Kategori
     */
    public function kategori()
    {
        return $this->belongsTo(KategoriBuku::class, 'kategori_id');
    }

    /**
     * Relasi ke Peminjaman (Fisik)
     */
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'buku_id');
    }

    /**
     * RELASI KE PEMINJAMAN DIGITAL
     */
    public function peminjamanDigital()
    {
        return $this->hasMany(PeminjamanDigital::class, 'buku_id');
    }

    /**
     * Relasi ke Booking
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'buku_id');
    }

    /**
     * Booking aktif untuk buku ini
     */
    public function bookingAktif()
    {
        return $this->hasMany(Booking::class, 'buku_id')
            ->whereIn('status', ['menunggu', 'disetujui']);
    }

    /**
     * Relasi ke Ulasan
     */
    public function ulasan()
    {
        return $this->hasMany(UlasanBuku::class, 'buku_id');
    }

    /**
     * Relasi ke Ulasan yang sudah disetujui
     */
    public function ulasanDisetujui()
    {
        return $this->hasMany(UlasanBuku::class, 'buku_id')->where('is_approved', true);
    }

    /**
     * Relasi ke Favorit
     */
    public function favorit()
    {
        return $this->hasMany(FavoritBuku::class, 'buku_id');
    }

    /**
     * Relasi ke Log
     */
    public function logs()
    {
        return $this->hasMany(BukuLog::class, 'buku_id');
    }

    /**
     * Relasi ke Penulis (Many-to-Many)
     */
    public function penulis()
    {
        return $this->belongsToMany(Penulis::class, 'buku_penulis')
                    ->withTimestamps();
    }

    /**
     * Relasi ke User yang membuat
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke User yang mengupdate
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relasi ke User yang menghapus
     */
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * ===========================
     * SCOPES (Query Filters)
     * ===========================
     */

    /**
     * Scope untuk buku fisik
     */
    public function scopeFisik($query)
    {
        return $query->where('tipe', 'fisik');
    }

    /**
     * Scope untuk buku digital
     */
    public function scopeDigital($query)
    {
        return $query->where('tipe', 'digital');
    }

    /**
     * Scope untuk buku tersedia
     */
    public function scopeTersedia($query)
    {
        return $query->where('status', 'tersedia')
                     ->where('stok_tersedia', '>', 0);
    }

    /**
     * Scope untuk buku sedang dipinjam
     */
    public function scopeDipinjam($query)
    {
        return $query->where('status', 'dipinjam');
    }

    /**
     * Scope untuk buku rusak
     */
    public function scopeRusak($query)
    {
        return $query->where('status', 'rusak');
    }

    /**
     * Scope untuk buku hilang
     */
    public function scopeHilang($query)
    {
        return $query->where('status', 'hilang');
    }

    /**
     * SCOPE UNTUK BUKU DIGITAL TERSEDIA
     */
    public function scopeDigitalTersedia($query)
    {
        return $query->where('tipe', 'digital')
                     ->whereRaw('jumlah_lisensi > lisensi_dipinjam');
    }

    /**
     * Scope untuk buku populer
     */
    public function scopePopuler($query, $limit = 10)
    {
        return $query->orderBy('total_dipinjam', 'desc')
                     ->limit($limit);
    }

    /**
     * Scope untuk buku baru
     */
    public function scopeBaru($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope pencarian
     */
    public function scopeCari($query, $keyword)
    {
        return $query->where(function($q) use ($keyword) {
            $q->where('judul', 'LIKE', "%{$keyword}%")
              ->orWhere('pengarang', 'LIKE', "%{$keyword}%")
              ->orWhere('penerbit', 'LIKE', "%{$keyword}%")
              ->orWhere('isbn', 'LIKE', "%{$keyword}%")
              ->orWhere('barcode', 'LIKE', "%{$keyword}%")
              ->orWhere('kata_kunci', 'LIKE', "%{$keyword}%");
        });
    }

    /**
     * Scope berdasarkan kategori
     */
    public function scopeKategori($query, $kategoriId)
    {
        return $query->where('kategori_id', $kategoriId);
    }

    /**
     * Scope untuk jenis koleksi tertentu
     */
    public function scopeJenisKoleksi($query, $jenis)
    {
        return $query->where('jenis_koleksi', $jenis);
    }
    
    /**
     * Scope untuk ebook (perlu pinjam)
     */
    public function scopeEbook($query)
    {
        return $query->where('jenis_koleksi', 'ebook');
    }
    
    /**
     * Scope untuk koleksi yang bisa di-download langsung
     */
    public function scopeBisaDownload($query)
    {
        return $query->where('bisa_download', true)
                     ->orWhereIn('jenis_koleksi', ['soal', 'modul', 'dokumen']);
    }

    // Tambahkan di dalam class Buku, di area RELATIONS

    public function stockOpnameLogs()
    {
        return $this->hasMany(StockOpnameLog::class, 'buku_id');
    }

    public function auditSchedules()
    {
        return $this->hasMany(AuditSchedule::class, 'buku_id');
    }

    /**
     * ===========================
     * ACCESSORS (GETTERS)
     * ===========================
     */

    /**
     * Get sampul URL
     */
    public function getSampulUrlAttribute()
    {
        if ($this->sampul && Storage::disk('public')->exists($this->sampul)) {
            return asset('storage/' . $this->sampul);
        }
        return asset('img/default-book-cover.jpg');
    }

    /**
     * Get cover URL untuk digital
     */
    public function getCoverUrlAttribute()
    {
        if ($this->cover_path && Storage::disk('public')->exists($this->cover_path)) {
            return asset('storage/' . $this->cover_path);
        }
        if ($this->sampul && Storage::disk('public')->exists($this->sampul)) {
            return asset('storage/' . $this->sampul);
        }
        return asset('img/default-digital-cover.jpg');
    }

    /**
     * Get file URL untuk digital
     */
    public function getFileUrlAttribute()
    {
        if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
            return asset('storage/' . $this->file_path);
        }
        return null;
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) {
            return '0 B';
        }
        
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get status badge
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'tersedia' => 'bg-green-100 text-green-800',
            'dipinjam' => 'bg-yellow-100 text-yellow-800',
            'rusak' => 'bg-red-100 text-red-800',
            'hilang' => 'bg-gray-100 text-gray-800',
            'dipesan' => 'bg-blue-100 text-blue-800',
            'habis' => 'bg-orange-100 text-orange-800'
        ];
        
        $class = $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $class . '">' . ucfirst($this->status) . '</span>';
    }

    // Tambahkan accessor untuk label kategori koleksi
    public function getKategoriKoleksiLabelAttribute()
    {
        $labels = [
            'buku_paket' => '📚 Buku Paket',
            'fisik' => '📖 Koleksi Fisik',
            'referensi' => '📕 Koleksi Referensi',
            'non_fiksi' => '📗 Koleksi Non Fiksi',
            'umum' => '📘 Koleksi Umum',
            'paket' => '📙 Koleksi Paket',
        ];
        
        return $labels[$this->kategori_koleksi] ?? '📚 Umum';
    }

    /**
     * Get stok info
     */
    public function getStokInfoAttribute()
    {
        return $this->stok_tersedia . '/' . $this->stok . ' tersedia';
    }

    /**
     * Get ketersediaan
     */
    public function getKetersediaanAttribute()
    {
        if ($this->tipe == 'digital') {
            return $this->ketersediaan_digital ?? 'Selalu Tersedia';
        }
        
        if ($this->stok_tersedia > 0) {
            return 'Tersedia (' . $this->stok_tersedia . ')';
        }
        
        if ($this->stok_dipinjam > 0) {
            return 'Sedang Dipinjam';
        }
        
        return 'Tidak Tersedia';
    }

    /**
     * GET KETERSEDIAAN LISENSI DIGITAL
     */
    public function getKetersediaanDigitalAttribute()
    {
        if ($this->tipe != 'digital') {
            return null;
        }
        
        // Untuk soal/modul/dokumen selalu tersedia
        if ($this->bisa_langsung_download) {
            return "Selalu Tersedia (Download Bebas)";
        }
        
        $tersedia = $this->jumlah_lisensi - $this->lisensi_dipinjam;
        
        if ($tersedia > 0) {
            return "Tersedia {$tersedia} dari {$this->jumlah_lisensi} lisensi";
        }
        
        return "Semua lisensi sedang dipinjam";
    }

    /**
     * Get label tipe buku
     */
    public function getTipeLabelAttribute()
    {
        return $this->tipe == 'fisik' ? '📖 Fisik' : '💻 Digital';
    }

    /**
     * Get format harga
     */
    public function getHargaFormattedAttribute()
    {
        $harga = $this->harga ?? 0;
        return 'Rp ' . number_format((float) $harga, 0, ',', '.');
    }

    /**
     * Get format denda
     */
    public function getDendaFormattedAttribute()
    {
        $denda = $this->denda_per_hari ?? 0;
        return 'Rp ' . number_format((float) $denda, 0, ',', '.') . '/hari';
    }

    /**
     * ===========================
     * ACCESSORS JENIS KOLEKSI (TAMBAHAN)
     * ===========================
     */

    /**
     * Cek apakah buku perlu dipinjam (ebook biasa)
     */
    public function getPerluPinjamAttribute()
    {
        return $this->jenis_koleksi === 'ebook' && !$this->bisa_download;
    }

    /**
     * Cek apakah buku bisa langsung di-download (soal/modul/dokumen)
     */
    public function getBisaLangsungDownloadAttribute()
    {
        return in_array($this->jenis_koleksi, ['soal', 'modul', 'dokumen']) || $this->bisa_download;
    }

    /**
     * Get label jenis koleksi
     */
    public function getJenisKoleksiLabelAttribute()
    {
        $labels = [
            'ebook' => ['label' => 'E-Book', 'icon' => '📚', 'color' => 'bg-blue-100 text-blue-800'],
            'soal' => ['label' => 'Bank Soal', 'icon' => '📝', 'color' => 'bg-green-100 text-green-800'],
            'modul' => ['label' => 'Modul', 'icon' => '📖', 'color' => 'bg-purple-100 text-purple-800'],
            'dokumen' => ['label' => 'Dokumen', 'icon' => '📄', 'color' => 'bg-orange-100 text-orange-800'],
        ];
        
        // Fallback untuk data lama
        if (empty($this->jenis_koleksi)) {
            return $labels['ebook'];
        }
        
        return $labels[$this->jenis_koleksi] ?? $labels['ebook'];
    }

    /**
     * Get badge HTML untuk jenis koleksi
     */
    public function getJenisKoleksiBadgeAttribute()
    {
        $info = $this->jenis_koleksi_label;
        return '<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium ' . $info['color'] . '">' . $info['icon'] . ' ' . $info['label'] . '</span>';
    }

    /**
     * Get label durasi pinjam
     */
    public function getDurasiPinjamLabelAttribute()
    {
        if ($this->bisa_langsung_download) {
            return 'Unlimited';
        }
        
        if ($this->durasi_pinjam_hari >= 24) {
            $hari = floor($this->durasi_pinjam_hari / 24);
            return $hari . ' Hari';
        }
        
        return $this->durasi_pinjam_hari . ' Jam';
    }

    /**
     * ===========================
     * METHODS (TAMBAHAN UNTUK DIGITAL)
     * ===========================
     */

    /**
     * Cek ketersediaan lisensi digital
     */
    public function cekKetersediaanDigital()
    {
        // Untuk soal/modul/dokumen selalu tersedia
        if ($this->bisa_langsung_download) {
            return [
                'total_lisensi' => 'Unlimited',
                'sedang_dipinjam' => 0,
                'tersedia' => 'Unlimited',
                'bisa_dipinjam' => true,
                'bisa_download' => true,
                'durasi_pinjam' => 'Unlimited'
            ];
        }
        
        $tersedia = $this->jumlah_lisensi - $this->lisensi_dipinjam;
        
        return [
            'total_lisensi' => $this->jumlah_lisensi,
            'sedang_dipinjam' => $this->lisensi_dipinjam,
            'tersedia' => max(0, $tersedia),
            'bisa_dipinjam' => $tersedia > 0,
            'bisa_download' => false,
            'durasi_pinjam' => $this->durasi_pinjam_label
        ];
    }

    /**
     * Cek apakah user sedang meminjam buku ini
     */
    public function sedangDipinjamOleh(User $user)
    {
        return $this->peminjamanDigital()
            ->where('user_id', $user->id)
            ->where('status', 'aktif')
            ->where('tanggal_expired', '>', now())
            ->exists();
    }

    /**
     * Ambil peminjaman aktif user untuk buku ini
     */
    public function getPeminjamanAktifUser(User $user)
    {
        return $this->peminjamanDigital()
            ->where('user_id', $user->id)
            ->where('status', 'aktif')
            ->where('tanggal_expired', '>', now())
            ->first();
    }

    /**
     * ===========================
     * METHODS (EXISTING)
     * ===========================
     */

    /**
     * Tambah stok
     */
    public function tambahStok($jumlah, $keterangan = null)
    {
        $this->stok += $jumlah;
        $this->stok_tersedia += $jumlah;
        $this->status = $this->stok_tersedia > 0 ? 'tersedia' : $this->status;
        $this->save();
        
        // Catat log
        $this->logs()->create([
            'aktivitas' => 'tambah_stok',
            'jumlah' => $jumlah,
            'keterangan' => $keterangan,
            'user_id' => Auth::id()
        ]);
        
        return true;
    }

    /**
     * Kurangi stok
     */
    public function kurangiStok($jumlah, $alasan, $keterangan = null)
    {
        if ($this->stok_tersedia < $jumlah) {
            return false;
        }
        
        $this->stok -= $jumlah;
        $this->stok_tersedia -= $jumlah;
        
        if ($alasan == 'rusak') {
            $this->stok_rusak += $jumlah;
        } elseif ($alasan == 'hilang') {
            $this->stok_hilang += $jumlah;
        }
        
        $this->status = $this->stok_tersedia > 0 ? 'tersedia' : ($this->stok_dipinjam > 0 ? 'dipinjam' : 'habis');
        $this->save();
        
        // Catat log
        $this->logs()->create([
            'aktivitas' => 'kurangi_stok_' . $alasan,
            'jumlah' => $jumlah,
            'keterangan' => $keterangan,
            'user_id' => Auth::id()
        ]);
        
        return true;
    }

    /**
     * Pinjam buku
     */
    public function pinjam()
    {
        if ($this->tipe == 'fisik' && $this->stok_tersedia > 0) {
            $this->stok_tersedia -= 1;
            $this->stok_dipinjam += 1;
            $this->total_dipinjam += 1;
            $this->terakhir_dipinjam = now();
            
            if ($this->stok_tersedia == 0 && $this->stok_dipinjam > 0) {
                $this->status = 'dipinjam';
            }
            
            return $this->save();
        }
        
        return true;
    }

    /**
     * Kembalikan buku
     */
    public function kembali($denda = 0)
    {
        if ($this->tipe == 'fisik') {
            $this->stok_tersedia += 1;
            $this->stok_dipinjam -= 1;
            $this->total_denda += $denda;
            
            if ($this->stok_tersedia > 0) {
                $this->status = 'tersedia';
            }
            
            return $this->save();
        }
        
        return true;
    }

    /**
     * Tambah view
     */
    public function tambahView()
    {
        $this->increment('views');
        return true;
    }

    /**
     * Update rating
     */
    public function updateRating()
    {
        $this->rating = $this->ulasan()->avg('rating') ?? 0;
        $this->jumlah_ulasan = $this->ulasan()->count();
        return $this->save();
    }

    /**
     * Cek ketersediaan
     */
    public function isTersedia()
    {
        if ($this->tipe == 'digital') {
            // Untuk digital, cek berdasarkan jenis
            if ($this->bisa_langsung_download) {
                return true;
            }
            return $this->jumlah_lisensi > $this->lisensi_dipinjam;
        }
        
        return $this->stok_tersedia > 0 && $this->status == 'tersedia';
    }

    /**
     * Cek apakah buku bisa di-booking
     */
    public function canBook()
    {
        if ($this->tipe == 'digital') {
            return false; // Digital tidak bisa di-booking
        }
        
        // Stok tersedia setelah dikurangi reservasi
        $tersediaUntukBooking = $this->stok_tersedia - $this->stok_direservasi;
        return $tersediaUntukBooking > 0 && $this->status == 'tersedia';
    }

    /**
     * Hitung stok yang benar-benar tersedia (untuk peminjaman)
     */
    public function getStokSiapPinjamAttribute()
    {
        return $this->stok_tersedia - $this->stok_direservasi;
    }

    /**
     * Cek apakah bisa dipinjam
     */
    public function canBorrow()
    {
        if ($this->tipe == 'digital') {
            if ($this->bisa_langsung_download) {
                return true;
            }
            return $this->jumlah_lisensi > $this->lisensi_dipinjam;
        }
        
        return $this->stok_tersedia > 0 && in_array($this->status, ['tersedia', 'dipesan']);
    }

    /**
     * ===========================
     * RATING & REVIEW METHODS
     * ===========================
     */

    /**
     * Get average rating
     */
    public function getAverageRatingAttribute()
    {
        return round($this->ulasan()->where('is_approved', true)->avg('rating') ?? 0, 1);
    }
    
    /**
     * Get total ratings count
     */
    public function getTotalRatingsAttribute()
    {
        return $this->ulasan()->where('is_approved', true)->count();
    }
    
    /**
     * Get rating as stars (1-5)
     */
    public function getRatingStarsAttribute()
    {
        $rating = round($this->average_rating);
        return $rating;
    }
    
    /**
     * Get rating percentage for display
     */
    public function getRatingPercentageAttribute()
    {
        return ($this->average_rating / 5) * 100;
    }
    
    /**
     * Get rating distribution
     */
    public function getRatingDistributionAttribute()
    {
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = $this->ulasan()
                ->where('is_approved', true)
                ->where('rating', $i)
                ->count();
        }
        return $distribution;
    }
    
    /**
     * Update rating stats ke database
     */
    public function updateRatingStats()
    {
        $this->rating = $this->average_rating;
        $this->jumlah_ulasan = $this->total_ratings;
        return $this->saveQuietly();
    }

    /**
     * ===========================
     * BOOT METHOD
     * ===========================
     */
    protected static function boot()
    {
        parent::boot();

        /**
         * Saat membuat buku baru
         */
        static::creating(function ($buku) {

            // Simpan siapa yang membuat
            if (Auth::check()) {
                $buku->created_by = Auth::id();
            }

            // Untuk buku digital
            if ($buku->tipe === 'digital') {

                // buku digital tidak pakai stok fisik
                $buku->stok = 0;
                $buku->stok_tersedia = 0;
                $buku->stok_dipinjam = 0;

                // status selalu tersedia
                $buku->status = 'tersedia';

                // lisensi digital
                $buku->lisensi_dipinjam = 0;
                
                // Set default jenis_koleksi jika belum di-set
                if (empty($buku->jenis_koleksi)) {
                    $buku->jenis_koleksi = 'ebook';
                }
            }

            // Untuk buku fisik
            if ($buku->tipe === 'fisik') {

                $buku->stok_tersedia = $buku->stok ?? 0;

                if ($buku->stok_tersedia > 0) {
                    $buku->status = 'tersedia';
                } else {
                    $buku->status = 'dipinjam';
                }
            }
        });

        /**
         * Saat update buku
         */
        static::updating(function ($buku) {

            if (Auth::check()) {
                $buku->updated_by = Auth::id();
            }

            // Jangan ubah status buku digital
            if ($buku->tipe === 'digital') {
                return;
            }

            // Update status buku fisik otomatis
            if (!$buku->isDirty('status')) {

                if ($buku->stok_tersedia > 0) {
                    $buku->status = 'tersedia';
                } elseif ($buku->stok_dipinjam > 0) {
                    $buku->status = 'dipinjam';
                } else {
                    $buku->status = 'rusak';
                }
            }
        });

        /**
         * Saat soft delete
         */
        static::deleting(function ($buku) {

            if (Auth::check() && !$buku->isForceDeleting()) {
                $buku->deleted_by = Auth::id();
                $buku->save();
            }
        });

        /**
         * Saat restore
         */
        static::restoring(function ($buku) {
            $buku->deleted_by = null;
        });
    }
}