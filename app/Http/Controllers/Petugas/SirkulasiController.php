<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\User;
use App\Models\Buku;
use App\Models\Denda;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

// Service imports - tetap dipertahankan
use App\Services\WhatsAppService;
use App\Services\MidtransService;
use App\Services\NotificationService;

class SirkulasiController extends Controller
{
    // Constants for business rules
    const MAX_LOANS_PER_USER = 3;
    const EXTEND_DAYS = 7;
    const DEFAULT_FINE_PER_DAY = 1000;
    const CACHE_TTL = 3600; // 1 hour

    protected $whatsappService;
    protected $midtransService;
    protected $notificationService;

    public function __construct(
        WhatsAppService $whatsappService = null, 
        MidtransService $midtransService = null,
        NotificationService $notificationService = null
    ) {
        // Optional dependencies - tidak akan error jika service tidak ada
        $this->whatsappService = $whatsappService;
        $this->midtransService = $midtransService;
        $this->notificationService = $notificationService;
    }

    /**
     * ==============================================
     * PEMINJAMAN
     * ==============================================
     */

    /**
     * Display list of active loans
     */
    public function indexPeminjaman(Request $request)
    {
        try {
            $query = Peminjaman::with(['user', 'buku'])
                ->whereIn('status_pinjam', ['dipinjam', 'terlambat']);
            
            // Apply filters
            $this->applyLoanFilters($query, $request);
            
            $peminjaman = $query->latest()->paginate(15)->withQueryString();
            
            // Cache statistics for better performance
            $statistik = Cache::remember('peminjaman_statistik', self::CACHE_TTL, function () {
                return $this->calculateLoanStatistics();
            });
            
            return view('petugas.pages.sirkulasi.peminjaman.index', compact('peminjaman', 'statistik'));
            
        } catch (\Exception $e) {
            Log::error('Error loading peminjaman index: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat data peminjaman.');
        }
    }

    /**
     * Show create loan form
     */
    public function createPeminjaman()
    {
        try {
            $anggota = User::whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])
                ->where('status', 'active')
                ->orderBy('name', 'asc')
                ->get(['id', 'name', 'role', 'nisn_nik', 'no_anggota', 'phone', 'email']);
            
            $buku = Buku::where('status', 'tersedia')
                ->where('stok_tersedia', '>', 0)
                ->orderBy('judul', 'asc')
                ->get(['id', 'judul', 'pengarang', 'penerbit', 'stok_tersedia', 'tahun_terbit', 'isbn']);
            
            return view('petugas.pages.sirkulasi.peminjaman.create', compact('anggota', 'buku'));
            
        } catch (\Exception $e) {
            Log::error('Error loading create peminjaman form: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat form peminjaman.');
        }
    }

    /**
     * Search anggota via AJAX
     */
    public function cariAnggota(Request $request)
    {
        try {
            $request->validate([
                'q' => 'required|string|min:2'
            ]);
            
            $search = $request->q;
            
            $anggota = User::whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])
                ->where('status', 'active')
                ->where(function($query) use ($search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('nisn_nik', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%")
                        ->orWhere('no_anggota', 'LIKE', "%{$search}%")
                        ->orWhere('phone', 'LIKE', "%{$search}%");
                })
                ->select('id', 'name', 'role', 'nisn_nik', 'email', 'no_anggota', 'phone')
                ->limit(10)
                ->get();
            
            // Add active loans count
            foreach ($anggota as $a) {
                $a->active_loans = Peminjaman::where('user_id', $a->id)
                    ->whereIn('status_pinjam', ['dipinjam', 'terlambat'])
                    ->count();
                $a->can_borrow = $a->active_loans < self::MAX_LOANS_PER_USER;
            }
            
            return response()->json([
                'success' => true,
                'data' => $anggota
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error searching anggota: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencari anggota'
            ], 500);
        }
    }

    /**
     * Search buku via AJAX
     */
    public function cariBuku(Request $request)
    {
        try {
            $request->validate([
                'q' => 'required|string|min:2'
            ]);
            
            $search = $request->q;
            
            $buku = Buku::where('status', 'tersedia')
                ->where('stok_tersedia', '>', 0)
                ->where(function($query) use ($search) {
                    $query->where('judul', 'LIKE', "%{$search}%")
                        ->orWhere('pengarang', 'LIKE', "%{$search}%")
                        ->orWhere('penerbit', 'LIKE', "%{$search}%")
                        ->orWhere('isbn', 'LIKE', "%{$search}%");
                })
                ->select('id', 'judul', 'pengarang', 'penerbit', 'stok_tersedia', 'tahun_terbit', 'isbn', 'rak')
                ->limit(10)
                ->get();
            
            // Tambahkan informasi rak
            foreach ($buku as $item) {
                $item->lokasi_rak = $item->rak ?? '-';
            }
            
            return response()->json([
                'success' => true,
                'data' => $buku
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error searching buku: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencari buku'
            ], 500);
        }
    }

    /**
     * Store new loan
     */
    public function storePeminjaman(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'buku_id' => 'required|exists:buku,id',
                'kode_eksemplar' => 'required|string|unique:peminjaman,kode_eksemplar',
                'tanggal_pinjam' => 'required|date',
                'tgl_jatuh_tempo' => 'required|date|after_or_equal:tanggal_pinjam',
                'keterangan' => 'nullable|string|max:500',
            ]);
            
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            
            DB::beginTransaction();
            
            // Validate book availability
            $buku = Buku::lockForUpdate()->find($request->buku_id);
            if (!$buku || $buku->stok_tersedia < 1) {
                throw new \Exception('Stok buku tidak tersedia.');
            }
            
            // Validate member status
            $user = User::find($request->user_id);
            if (!$user || $user->status !== 'active') {
                throw new \Exception('Anggota tidak aktif.');
            }
            
            // Check active loans limit
            $activeLoans = Peminjaman::where('user_id', $request->user_id)
                ->whereIn('status_pinjam', ['dipinjam', 'terlambat'])
                ->count();
            
            if ($activeLoans >= self::MAX_LOANS_PER_USER) {
                throw new \Exception('Anggota sudah mencapai batas maksimal peminjaman (' . self::MAX_LOANS_PER_USER . ' buku).');
            }
            
            // Check if member has unpaid fines
            // Check if member has unpaid fines - melalui relasi peminjaman
            $unpaidFines = Denda::whereHas('peminjaman', function($query) use ($request) {
                    $query->where('user_id', $request->user_id);
                })
                ->where('payment_status', '!=', 'paid')
                ->exists();
            
            if ($unpaidFines) {
                throw new \Exception('Anggota memiliki denda yang belum dibayar. Harap selesaikan denda terlebih dahulu.');
            }
            
            // Update book stock
            $buku->stok_tersedia -= 1;
            $buku->stok_dipinjam += 1;
            $buku->save();
            
            // Create loan record
            $peminjaman = Peminjaman::create([
                'user_id' => $request->user_id,
                'buku_id' => $request->buku_id,
                'kode_eksemplar' => $request->kode_eksemplar,
                'tanggal_pinjam' => $request->tanggal_pinjam,
                'tgl_jatuh_tempo' => $request->tgl_jatuh_tempo,
                'status_pinjam' => 'dipinjam',
                'keterangan' => $request->keterangan,
                'petugas_id' => Auth::id(),
            ]);
            
            DB::commit();
            
            // Clear cache
            Cache::forget('peminjaman_statistik');
            
            // Send notifications (non-blocking)
            $this->sendBorrowNotifications($peminjaman);
            
            return redirect()
                ->route('petugas.sirkulasi.peminjaman.index')
                ->with('success', '✅ Peminjaman berhasil dicatat. Notifikasi telah dikirim ke anggota.');
                
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Gagal menyimpan peminjaman: ' . $e->getMessage(), [
                'request_data' => $request->except('_token')
            ]);
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Get all anggota for dropdown (AJAX)
     */
    public function getAllAnggota()
    {
        try {
            $anggota = User::whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])
                ->where('status', 'active')
                ->orderBy('name', 'asc')
                ->select('id', 'name', 'role', 'nisn_nik', 'email', 'no_anggota')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $anggota
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting all anggota: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data anggota'
            ], 500);
        }
    }

    /**
     * Get all buku for dropdown (AJAX)
     */
    public function getAllBuku()
    {
        try {
            $buku = Buku::where('status', 'tersedia')
                ->where('stok_tersedia', '>', 0)
                ->orderBy('judul', 'asc')
                ->select('id', 'judul', 'pengarang', 'penerbit', 'stok_tersedia', 'tahun_terbit')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $buku
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting all buku: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data buku'
            ], 500);
        }
    }

    /**
     * Get single anggota by ID (AJAX)
     */
    public function getAnggota($id)
    {
        try {
            $anggota = User::whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])
                ->where('status', 'active')
                ->select('id', 'name', 'role', 'nisn_nik', 'email', 'no_anggota', 'phone', 'alamat')
                ->findOrFail($id);
            
            $activeLoans = Peminjaman::where('user_id', $anggota->id)
                ->whereIn('status_pinjam', ['dipinjam', 'terlambat'])
                ->count();
            
            $anggota->active_loans = $activeLoans;
            $anggota->can_borrow = $activeLoans < self::MAX_LOANS_PER_USER;
            $anggota->remaining_quota = self::MAX_LOANS_PER_USER - $activeLoans;
            
            return response()->json([
                'success' => true,
                'data' => $anggota
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting anggota: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Anggota tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Get single buku by ID (AJAX)
     */
    public function getBuku($id)
    {
        try {
            $buku = Buku::select(
                'id', 
                'judul', 
                'pengarang', 
                'penerbit', 
                'stok_tersedia', 
                'tahun_terbit', 
                'isbn', 
                'rak',
                'lokasi',
                'denda_per_hari', 
                'sinopsis'
            )->findOrFail($id);
            
            // Tambahkan alias untuk frontend
            $buku->lokasi_rak = $buku->rak ?? '-';
            
            return response()->json([
                'success' => true,
                'data' => $buku
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting buku: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Buku tidak ditemukan'
            ], 500);
        }
    }

    /**
     * Show loan detail
     */
    public function showPeminjaman($id)
    {
        try {
            $peminjaman = Peminjaman::with(['user', 'buku', 'petugas'])->findOrFail($id);
            return view('petugas.pages.sirkulasi.peminjaman.show', compact('peminjaman'));
            
        } catch (\Exception $e) {
            Log::error('Error showing peminjaman: ' . $e->getMessage());
            return back()->with('error', 'Data peminjaman tidak ditemukan');
        }
    }

    /**
     * Get loan data as JSON - VERSI DIPERBAIKI
     */
    public function getPeminjamanJson($id)
    {
        try {
            $peminjaman = Peminjaman::with(['user', 'buku'])->findOrFail($id);
            
            $today = Carbon::now();
            $jatuhTempo = Carbon::parse($peminjaman->tgl_jatuh_tempo);
            $hariTerlambat = 0;
            $dendaTerlambat = 0;
            
            if ($today->gt($jatuhTempo) && $peminjaman->status_pinjam !== 'dikembalikan') {
                $hariTerlambat = (int) $jatuhTempo->diffInDays($today);
                $dendaPerHari = (int) ($peminjaman->buku->denda_per_hari ?? 1000);
                $dendaTerlambat = (int) ($hariTerlambat * $dendaPerHari);
            }
            
            // Pastikan harga dalam bentuk integer
            $hargaBuku = $peminjaman->buku->harga;
            if (is_string($hargaBuku)) {
                $hargaBuku = (int) preg_replace('/[^0-9]/', '', $hargaBuku);
            }
            if ($hargaBuku <= 0) {
                $hargaBuku = 50000;
            }
            
            return response()->json([
                'success' => true,
                'id' => $peminjaman->id,
                'kode_eksemplar' => $peminjaman->kode_eksemplar,
                'tanggal_pinjam' => $peminjaman->tanggal_pinjam ? $peminjaman->tanggal_pinjam->format('Y-m-d') : now()->format('Y-m-d'),
                'tgl_jatuh_tempo' => $peminjaman->tgl_jatuh_tempo ? $peminjaman->tgl_jatuh_tempo->format('Y-m-d') : now()->addDays(7)->format('Y-m-d'),
                'hari_terlambat' => $hariTerlambat,
                'denda_terlambat' => $dendaTerlambat,
                'can_extend' => false,
                'user' => [
                    'id' => $peminjaman->user->id,
                    'name' => $peminjaman->user->name,
                    'nisn_nik' => $peminjaman->user->nisn_nik ?? '-',
                    'no_anggota' => $peminjaman->user->no_anggota ?? '-',
                    'kelas' => $peminjaman->user->kelas ?? '-',
                    'phone' => $peminjaman->user->phone ?? '-',
                    'role' => $peminjaman->user->role,
                ],
                'buku' => [
                    'id' => $peminjaman->buku->id,
                    'judul' => $peminjaman->buku->judul ?? 'Tidak diketahui',
                    'pengarang' => $peminjaman->buku->pengarang ?? '-',
                    'isbn' => $peminjaman->buku->isbn ?? '-',
                    'denda_per_hari' => (int) ($peminjaman->buku->denda_per_hari ?? 1000),
                    'harga' => (int) $hargaBuku,
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting peminjaman JSON: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'Data tidak ditemukan: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * ==============================================
     * PENGEMBALIAN
     * ==============================================
     */

    /**
     * Display return page
     */
    public function indexPengembalian(Request $request)
    {
        try {
            $query = Peminjaman::with(['user', 'buku'])
                ->whereIn('status_pinjam', ['dipinjam', 'terlambat']);
            
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('kode_eksemplar', 'LIKE', "%{$search}%")
                        ->orWhereHas('user', function($user) use ($search) {
                            $user->where('name', 'LIKE', "%{$search}%")
                                ->orWhere('no_anggota', 'LIKE', "%{$search}%");
                        });
                });
            }
            
            $peminjamanAktif = $query->latest()->paginate(15)->withQueryString();
            
            $statistik = $this->calculateReturnStatistics();
            
            return view('petugas.pages.sirkulasi.pengembalian.index', compact('peminjamanAktif', 'statistik'));
            
        } catch (\Exception $e) {
            Log::error('Error loading pengembalian index: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat data pengembalian.');
        }
    }

    /**
     * Search loan by barcode for return
     */
    public function cariPeminjaman(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'kode_eksemplar' => 'required|string'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode eksemplar harus diisi'
                ], 422);
            }
            
            $peminjaman = Peminjaman::with(['user', 'buku'])
                ->where('kode_eksemplar', $request->kode_eksemplar)
                ->whereIn('status_pinjam', ['dipinjam', 'terlambat'])
                ->first();
            
            if (!$peminjaman) {
                return response()->json([
                    'success' => false,
                    'message' => 'Peminjaman tidak ditemukan atau sudah dikembalikan.'
                ], 404);
            }
            
            $today = Carbon::now();
            $jatuhTempo = Carbon::parse($peminjaman->tgl_jatuh_tempo);
            $dendaTerlambat = 0;
            $hariTerlambat = 0;
            
            if ($today->gt($jatuhTempo)) {
                $hariTerlambat = (int) $jatuhTempo->diffInDays($today);
                $dendaPerHari = $peminjaman->buku->denda_per_hari ?? self::DEFAULT_FINE_PER_DAY;
                $dendaTerlambat = (int) ($hariTerlambat * $dendaPerHari);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $peminjaman->id,
                    'kode_eksemplar' => $peminjaman->kode_eksemplar,
                    'tanggal_pinjam' => $peminjaman->tanggal_pinjam->format('Y-m-d'),
                    'tgl_jatuh_tempo' => $peminjaman->tgl_jatuh_tempo->format('Y-m-d'),
                    'hari_terlambat' => $hariTerlambat,
                    'denda_terlambat' => $dendaTerlambat,
                    'user' => [
                        'id' => $peminjaman->user->id,
                        'name' => $peminjaman->user->name,
                        'nisn_nik' => $peminjaman->user->nisn_nik,
                        'role' => $peminjaman->user->role,
                        'phone' => $peminjaman->user->phone,
                    ],
                    'buku' => [
                        'id' => $peminjaman->buku->id,
                        'judul' => $peminjaman->buku->judul,
                        'pengarang' => $peminjaman->buku->pengarang,
                        'isbn' => $peminjaman->buku->isbn,
                        'denda_per_hari' => (int) ($peminjaman->buku->denda_per_hari ?? self::DEFAULT_FINE_PER_DAY),
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error searching peminjaman: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari data'
            ], 500);
        }
    }

    /**
     * Process book return - VERSI DIPERBAIKI
     */
    public function prosesPengembalian(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'peminjaman_id' => 'required|exists:peminjaman,id',
                'tanggal_pengembalian' => 'required|date',
                'kondisi_kembali' => 'required|in:baik,rusak_ringan,rusak_berat,hilang',
                'catatan_kondisi' => 'nullable|string|max:500',
                'payment_method' => 'required|in:qris,tunai',
                'denda_terlambat' => 'required|integer|min:0',
                'denda_rusak' => 'required|integer|min:0',
            ]);
            
            if ($validator->fails()) {
                return redirect()
                    ->route('petugas.sirkulasi.pengembalian.index')
                    ->with('error', 'Validasi gagal: ' . implode(', ', $validator->errors()->all()));
            }
            
            DB::beginTransaction();
            
            $peminjaman = Peminjaman::with(['buku', 'user'])->lockForUpdate()->findOrFail($request->peminjaman_id);
            
            if ($peminjaman->status_pinjam === 'dikembalikan') {
                throw new \Exception('Peminjaman ini sudah dikembalikan sebelumnya.');
            }
            
            $dendaTerlambat = (int) $request->denda_terlambat;
            $dendaRusak = (int) $request->denda_rusak;
            $dendaTotal = $dendaTerlambat + $dendaRusak;
            
            $jatuhTempo = Carbon::parse($peminjaman->tgl_jatuh_tempo);
            $tanggalKembali = Carbon::parse($request->tanggal_pengembalian);
            $hariTerlambat = $tanggalKembali->gt($jatuhTempo) ? $jatuhTempo->diffInDays($tanggalKembali) : 0;
            
            $this->updateBookStock($peminjaman->buku, $request->kondisi_kembali);
            
            $peminjaman->tanggal_pengembalian = $request->tanggal_pengembalian;
            $peminjaman->status_pinjam = 'dikembalikan';
            $peminjaman->kondisi_kembali = $request->kondisi_kembali;
            $peminjaman->catatan_kondisi = $request->catatan_kondisi;
            $peminjaman->updated_by = Auth::id();
            $peminjaman->petugas_id = Auth::id();
            
            $peminjaman->updateDendaTotal($dendaTerlambat, $dendaRusak, $hariTerlambat);
            
            if ($dendaTotal > 0 && $request->payment_method === 'tunai') {
                $peminjaman->status_verifikasi = 'disetujui';
            } elseif ($dendaTotal > 0 && $request->payment_method === 'qris') {
                $peminjaman->status_verifikasi = 'pending';
            } else {
                $peminjaman->status_verifikasi = 'disetujui';
            }
            
            $peminjaman->save();
            
            // Update atau create denda record
            if ($dendaTotal > 0) {
                $denda = Denda::updateOrCreate(
                    ['peminjaman_id' => $peminjaman->id],
                    [
                        'jumlah_denda' => $dendaTotal,
                        'denda_terlambat' => $dendaTerlambat,
                        'denda_kerusakan' => $dendaRusak,
                        'hari_terlambat' => $hariTerlambat,
                        'keterangan' => $request->catatan_kondisi,
                        'status' => $request->payment_method === 'tunai' ? 'lunas' : 'pending',
                        'payment_status' => $request->payment_method === 'tunai' ? 'paid' : 'pending',
                        'payment_method' => $request->payment_method,
                        'confirmed_by' => $request->payment_method === 'tunai' ? Auth::id() : null,
                        'paid_at' => $request->payment_method === 'tunai' ? now() : null,
                    ]
                );
                
                // ✅ QRIS - Redirect ke halaman pembayaran
                if ($request->payment_method === 'qris') {
                    DB::commit();
                    $dendaId = $denda->id_denda ?? $denda->id;  // ← AMBIL ID YANG BENAR
                    return redirect()->route('petugas.sirkulasi.pembayaran.show', $dendaId)
                        ->with('success', '✅ Buku berhasil dikembalikan. Silakan lakukan pembayaran QRIS.');
                }
                
                // ✅ TUNAI - Redirect ke daftar denda
                if ($request->payment_method === 'tunai') {
                    DB::commit();
                    return redirect()
                        ->route('petugas.sirkulasi.denda.index')
                        ->with('success', '✅ Buku berhasil dikembalikan. Pembayaran denda tunai telah dicatat.');
                }
            }
            
            DB::commit();
            
            return redirect()
                ->route('petugas.sirkulasi.pengembalian.index')
                ->with('success', '✅ Buku berhasil dikembalikan. Terima kasih!');
                
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Gagal memproses pengembalian: ' . $e->getMessage());
            
            return redirect()
                ->route('petugas.sirkulasi.pengembalian.index')
                ->with('error', 'Gagal memproses pengembalian: ' . $e->getMessage());
        }
    }

    /**
     * ==============================================
     * PERPANJANGAN
     * ==============================================
     */

    /**
     * Extend loan period
     */
    public function perpanjangPeminjaman(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'extend_days' => 'nullable|integer|min:1|max:14'
            ]);
            
            DB::beginTransaction();
            
            $peminjamanLama = Peminjaman::with(['buku', 'user'])->lockForUpdate()->findOrFail($id);
            
            // Validate extension eligibility
            $this->validateExtension($peminjamanLama);
            
            $extendDays = $request->extend_days ?? self::EXTEND_DAYS;
            $newDueDate = Carbon::now()->addDays($extendDays);
            
            // Mark old loan as extended
            $peminjamanLama->update([
                'status_pinjam' => 'diperpanjang',
                'is_perpanjangan' => true,
                'updated_by' => Auth::id(),
            ]);
            
            // Create new loan record
            $peminjamanBaru = Peminjaman::create([
                'user_id' => $peminjamanLama->user_id,
                'buku_id' => $peminjamanLama->buku_id,
                'kode_eksemplar' => $peminjamanLama->kode_eksemplar,
                'tanggal_pinjam' => now(),
                'tgl_jatuh_tempo' => $newDueDate,
                'status_pinjam' => 'dipinjam',
                'is_perpanjangan' => false,
                'parent_peminjaman_id' => $peminjamanLama->id,
                'keterangan' => 'Perpanjangan dari peminjaman #' . $peminjamanLama->id,
                'petugas_id' => Auth::id(),
            ]);
            
            DB::commit();
            
            // Clear cache
            Cache::forget('peminjaman_statistik');
            
            // Send WhatsApp notification
            if ($this->whatsappService && method_exists($this->whatsappService, 'sendExtendNotification')) {
                try {
                    $this->whatsappService->sendExtendNotification($peminjamanBaru);
                } catch (\Exception $e) {
                    Log::warning('Gagal kirim WA perpanjangan: ' . $e->getMessage());
                }
            }
            
            $tanggalBaru = $newDueDate->format('d/m/Y');
            
            return response()->json([
                'success' => true,
                'message' => '✅ Peminjaman berhasil diperpanjang hingga ' . $tanggalBaru,
                'id_baru' => $peminjamanBaru->id,
                'tanggal_baru' => $tanggalBaru
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Gagal perpanjang peminjaman: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * ==============================================
     * DENDA & PEMBAYARAN
     * ==============================================
     */

    /**
     * Display pending fines index
     */
    public function indexDenda(Request $request)
    {
        try {
            $query = Denda::with(['anggota', 'peminjaman.buku'])
                ->where('payment_status', 'pending');
            
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('anggota', function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('no_anggota', 'LIKE', "%{$search}%");
                });
            }
            
            $denda = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
            $totalPending = Denda::where('payment_status', 'pending')->sum('jumlah_denda');
            
            return view('petugas.pages.sirkulasi.denda.index', compact('denda', 'totalPending'));
            
        } catch (\Exception $e) {
            Log::error('Error loading denda index: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat data denda.');
        }
    }

    /**
     * Display paid fines index
     */
    public function indexDendaLunas(Request $request)
    {
        try {
            $query = Denda::with(['anggota', 'peminjaman.buku'])
                ->where('payment_status', 'paid');
            
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('anggota', function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                });
            }
            
            $dendaLunas = $query->orderBy('paid_at', 'desc')->paginate(10)->withQueryString();
            $totalLunas = Denda::where('payment_status', 'paid')->sum('jumlah_denda');
            
            return view('petugas.pages.sirkulasi.denda.lunas', compact('dendaLunas', 'totalLunas'));
            
        } catch (\Exception $e) {
            Log::error('Error loading denda lunas: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat data denda lunas.');
        }
    }

    /**
     * Show payment page for fine
     */
    public function showPayment($id)
    {
        try {
            $denda = Denda::with(['peminjaman.buku', 'anggota'])->findOrFail($id);
            
            if ($denda->isPaid()) {
                return redirect()->route('petugas.sirkulasi.denda.index')
                    ->with('error', 'Denda ini sudah dibayar.');
            }
            
            return view('petugas.pages.sirkulasi.payment', compact('denda'));
            
        } catch (\Exception $e) {
            Log::error('Error showing payment: ' . $e->getMessage());
            return back()->with('error', 'Data denda tidak ditemukan.');
        }
    }

    /**
     * Process QRIS payment for fine
     */
    public function processQrisPayment($id)
    {
        try {
            $denda = Denda::with(['peminjaman.buku', 'anggota'])->findOrFail($id);
            
            if ($denda->isPaid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Denda sudah dibayar'
                ]);
            }
            
            if ($this->midtransService && method_exists($this->midtransService, 'createQrisPayment')) {
                $result = $this->midtransService->createQrisPayment($denda, $denda->anggota);
                return response()->json($result);
            }
            
            // Fallback: generate QR code manual
            $qrCode = $this->generateSimpleQrCode($denda);
            return response()->json([
                'success' => true,
                'qr_code' => $qrCode,
                'order_id' => 'QR-' . $denda->id_denda . '-' . time()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error processing QRIS payment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran QRIS: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check payment status for fine
     */
    public function checkPaymentStatus($id)
    {
        try {
            $denda = Denda::findOrFail($id);
            
            if ($this->midtransService && method_exists($this->midtransService, 'checkPaymentStatus')) {
                $result = $this->midtransService->checkPaymentStatus($denda);
                
                if (in_array($result['status'], ['settlement', 'capture'])) {
                    // Update denda status
                    $denda->update([
                        'payment_status' => 'paid',
                        'paid_at' => now(),
                        'status' => 'lunas'
                    ]);
                    
                    // Update related peminjaman
                    if ($denda->peminjaman) {
                        $denda->peminjaman->update([
                            'status_verifikasi' => 'selesai'
                        ]);
                    }
                    
                    return response()->json([
                        'success' => true,
                        'paid' => true
                    ]);
                }
                
                return response()->json([
                    'success' => true,
                    'paid' => false,
                    'status' => $result['status']
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Service pembayaran tidak tersedia'
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('Error checking payment status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengecek status pembayaran'
            ], 500);
        }
    }

    /**
     * Show payment page with QR Code
     */
    public function showPembayaran($id)
    {
        try {
            $denda = Denda::with(['peminjaman.buku', 'anggota'])->findOrFail($id);
            
            if ($denda->isPaid()) {
                return redirect()->route('petugas.sirkulasi.denda.index')
                    ->with('error', 'Denda ini sudah dibayar.');
            }
            
            return view('petugas.pages.sirkulasi.pembayaran', compact('denda'));
            
        } catch (\Exception $e) {
            Log::error('Error showing payment: ' . $e->getMessage());
            return back()->with('error', 'Data denda tidak ditemukan.');
        }
    }

    /**
     * Confirm payment manually
     */
    public function confirmPembayaran(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'metode' => 'required|in:tunai,transfer,qris'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Metode pembayaran tidak valid'
                ], 422);
            }
            
            $denda = Denda::findOrFail($id);
            
            if ($denda->isPaid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Denda sudah dibayar'
                ]);
            }
            
            DB::beginTransaction();
            
            $denda->update([
                'payment_status' => 'paid',
                'status' => 'lunas',
                'paid_at' => now(),
                'confirmed_by' => Auth::id(),
                'payment_method' => $request->metode
                // 'tanggal_bayar' => now()
            ]);
            
            // Update peminjaman terkait
            if ($denda->peminjaman) {
                $denda->peminjaman->update([
                    'status_verifikasi' => 'disetujui'
                ]);
            }
            
            DB::commit();
            
            // Kirim notifikasi WhatsApp (opsional)
            try {
                if ($this->whatsappService && method_exists($this->whatsappService, 'sendDendaPaidNotification')) {
                    $this->whatsappService->sendDendaPaidNotification($denda->peminjaman, $denda->jumlah_denda);
                }
            } catch (\Exception $e) {
                Log::warning('Gagal kirim WA: ' . $e->getMessage());
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dikonfirmasi'
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error confirming payment: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal konfirmasi pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ==============================================
     * RIWAYAT & LAPORAN
     * ==============================================
     */

    /**
     * Display loan history
     */
    public function riwayat(Request $request)
    {
        try {
            $query = Peminjaman::with(['user', 'buku']);
            
            // Apply date filters
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            
            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }
            
            // Apply status filter
            if ($request->filled('status')) {
                if ($request->status === 'all') {
                    // No filter
                } elseif ($request->status === 'active') {
                    $query->whereIn('status_pinjam', ['dipinjam', 'terlambat']);
                } elseif ($request->status === 'completed') {
                    $query->where('status_pinjam', 'dikembalikan');
                } else {
                    $query->where('status_pinjam', $request->status);
                }
            }
            
            // Apply search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->whereHas('user', function($user) use ($search) {
                        $user->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('buku', function($buku) use ($search) {
                        $buku->where('judul', 'LIKE', "%{$search}%")
                            ->orWhere('isbn', 'LIKE', "%{$search}%");
                    })
                    ->orWhere('kode_eksemplar', 'LIKE', "%{$search}%");
                });
            }
            
            $riwayat = $query->latest()->paginate(20)->withQueryString();
            
            // Calculate summary
            $summary = [
                'total_peminjaman' => Peminjaman::count(),
                'total_dikembalikan' => Peminjaman::where('status_pinjam', 'dikembalikan')->count(),
                'total_aktif' => Peminjaman::whereIn('status_pinjam', ['dipinjam', 'terlambat'])->count(),
                'total_denda' => Peminjaman::sum('denda_total')
            ];
            
            return view('petugas.pages.sirkulasi.riwayat', compact('riwayat', 'summary'));
            
        } catch (\Exception $e) {
            Log::error('Error loading riwayat: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat data riwayat.');
        }
    }

    /**
     * ==============================================
     * PRIVATE HELPER METHODS
     * ==============================================
     */

    /**
     * Apply loan filters to query
     */
    private function applyLoanFilters($query, Request $request)
    {
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($user) use ($search) {
                    $user->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('no_anggota', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('buku', function($buku) use ($search) {
                    $buku->where('judul', 'LIKE', "%{$search}%")
                        ->orWhere('isbn', 'LIKE', "%{$search}%");
                })
                ->orWhere('kode_eksemplar', 'LIKE', "%{$search}%");
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status_pinjam', $request->status);
        }
    }

    /**
     * Calculate loan statistics
     */
    private function calculateLoanStatistics()
    {
        return [
            'total' => Peminjaman::whereIn('status_pinjam', ['dipinjam', 'terlambat'])->count(),
            'tepat_waktu' => Peminjaman::where('status_pinjam', 'dipinjam')
                ->whereDate('tgl_jatuh_tempo', '>=', now())
                ->count(),
            'terlambat' => Peminjaman::where('status_pinjam', 'terlambat')->count(),
            'hari_ini' => Peminjaman::whereDate('tanggal_pinjam', now())->count(),
            'jatuh_tempo_hari_ini' => Peminjaman::whereIn('status_pinjam', ['dipinjam', 'terlambat'])
                ->whereDate('tgl_jatuh_tempo', today())
                ->count(),
        ];
    }

    /**
     * Calculate return statistics
     */
    private function calculateReturnStatistics()
    {
        return [
            'total' => Peminjaman::whereIn('status_pinjam', ['dipinjam', 'terlambat'])->count(),
            'tepat_waktu' => Peminjaman::where('status_pinjam', 'dipinjam')
                ->whereDate('tgl_jatuh_tempo', '>=', now())
                ->count(),
            'terlambat' => Peminjaman::where('status_pinjam', 'terlambat')->count(),
            'jatuh_tempo_hari_ini' => Peminjaman::whereIn('status_pinjam', ['dipinjam', 'terlambat'])
                ->whereDate('tgl_jatuh_tempo', today())
                ->count(),
        ];
    }

    /**
     * Check if loan can be extended
     */
    private function canExtendLoan($peminjaman)
    {
        // Cannot extend returned loans
        if ($peminjaman->status_pinjam === 'dikembalikan') {
            return false;
        }
        
        // Cannot extend if already extended
        if ($peminjaman->is_perpanjangan) {
            return false;
        }
        
        // Cannot extend if has existing extension
        $existingExtension = Peminjaman::where('parent_peminjaman_id', $peminjaman->id)
            ->whereIn('status_pinjam', ['dipinjam', 'terlambat'])
            ->exists();
        
        if ($existingExtension) {
            return false;
        }
        
        // Cannot extend if has unpaid fines
        if ($peminjaman->denda_total > 0 && $peminjaman->status_verifikasi !== 'selesai') {
            return false;
        }
        
        // Cannot extend if already past due date
        $jatuhTempo = Carbon::parse($peminjaman->tgl_jatuh_tempo);
        if (now()->gt($jatuhTempo)) {
            return false;
        }
        
        return true;
    }

    /**
     * Validate loan extension eligibility
     */
    private function validateExtension($peminjaman)
    {
        if ($peminjaman->status_pinjam === 'dikembalikan') {
            throw new \Exception('❌ Peminjaman sudah dikembalikan, tidak bisa diperpanjang.');
        }
        
        if ($peminjaman->is_perpanjangan) {
            throw new \Exception('❌ Peminjaman sudah pernah diperpanjang. Maksimal 1x perpanjangan.');
        }
        
        $existingPerpanjangan = Peminjaman::where('parent_peminjaman_id', $peminjaman->id)
            ->whereIn('status_pinjam', ['dipinjam', 'terlambat'])
            ->exists();
        
        if ($existingPerpanjangan) {
            throw new \Exception('❌ Sudah ada perpanjangan aktif untuk peminjaman ini.');
        }
        
        $hasUnpaidFines = Denda::where('peminjaman_id', $peminjaman->id)
            ->where('payment_status', '!=', 'paid')
            ->exists();
        
        if ($hasUnpaidFines) {
            throw new \Exception('❌ Masih ada denda yang harus dibayar. Selesaikan denda terlebih dahulu.');
        }
        
        $jatuhTempo = Carbon::parse($peminjaman->tgl_jatuh_tempo);
        if (now()->gt($jatuhTempo)) {
            throw new \Exception('❌ Peminjaman sudah terlambat. Tidak bisa diperpanjang, silakan kembalikan buku dan bayar denda.');
        }
    }

    /**
     * Update book stock based on return condition
     */
    private function updateBookStock($buku, $kondisi)
    {
        if (in_array($kondisi, ['baik', 'rusak_ringan'])) {
            $buku->stok_tersedia += 1;
            $buku->stok_dipinjam -= 1;
        } elseif ($kondisi === 'rusak_berat') {
            $buku->stok_rusak = ($buku->stok_rusak ?? 0) + 1;
            $buku->stok_dipinjam -= 1;
        } elseif ($kondisi === 'hilang') {
            $buku->stok_hilang = ($buku->stok_hilang ?? 0) + 1;
            $buku->stok_dipinjam -= 1;
        }
        
        // Update buku status
        if ($buku->stok_tersedia > 0) {
            $buku->status = 'tersedia';
        } elseif ($buku->stok_dipinjam > 0) {
            $buku->status = 'dipinjam';
        } else {
            $buku->status = 'habis';
        }
        
        $buku->save();
    }

    /**
     * Create fine record
     */
    private function createFineRecord($peminjaman, $request, $dendaTotal)
    {
        $paymentStatus = ($request->payment_method === 'tunai') ? 'paid' : 'pending';
        $statusOld = ($request->payment_method === 'tunai') ? 'lunas' : 'pending';
        $kodePembayaran = Denda::generateKodePembayaran();
        
        $keterangan = sprintf(
            'Denda keterlambatan: Rp %s, Denda kerusakan: Rp %s',
            number_format($request->denda_terlambat, 0, ',', '.'),
            number_format($request->denda_rusak, 0, ',', '.')
        );
        
        return Denda::create([
            'peminjaman_id' => $peminjaman->id,
            'id_anggota' => $peminjaman->user_id,
            'jumlah_denda' => $dendaTotal,
            'keterangan' => $keterangan,
            'status' => $statusOld,
            'payment_status' => $paymentStatus,
            'payment_method' => $request->payment_method,
            'kode_pembayaran' => $kodePembayaran,
            'paid_at' => ($request->payment_method === 'tunai') ? now() : null,
        ]);
    }

    /**
     * Generate QR Code for denda
     */
    private function generateQrCodeForDenda($denda)
    {
        try {
            // Simple QR code generation without package
            $qrContent = json_encode([
                'id' => $denda->id_denda,
                'amount' => $denda->jumlah_denda,
                'kode' => $denda->kode_pembayaran,
                'timestamp' => time()
            ]);
            
            $denda->update([
                'qr_code_path' => 'data:application/json,' . urlencode($qrContent)
            ]);
            
        } catch (\Exception $e) {
            Log::warning('Failed to generate QR code: ' . $e->getMessage());
        }
    }

    /**
     * Generate simple QR code fallback
     */
    private function generateSimpleQrCode($denda)
    {
        $data = [
            'type' => 'denda',
            'id' => $denda->id_denda,
            'amount' => $denda->jumlah_denda,
            'kode' => $denda->kode_pembayaran
        ];
        
        return 'https://quickchart.io/qr?text=' . urlencode(json_encode($data)) . '&size=200';
    }

    /**
     * Send borrow notifications
     */
    private function sendBorrowNotifications($peminjaman)
    {
        try {
            // WhatsApp notification
            if ($this->whatsappService && method_exists($this->whatsappService, 'sendSuccessBorrowNotification')) {
                $this->whatsappService->sendSuccessBorrowNotification($peminjaman);
            }
            
            // In-app notification
            if ($this->notificationService && method_exists($this->notificationService, 'createMemberNotification')) {
                $this->notificationService->createMemberNotification(
                    $peminjaman->user_id,
                    'Peminjaman Buku',
                    'Anda meminjam buku "' . $peminjaman->buku->judul . '". Batas pengembalian: ' . $peminjaman->tgl_jatuh_tempo->format('d/m/Y'),
                    'info',
                    route('anggota.peminjaman.riwayat')
                );
            }
        } catch (\Exception $e) {
            Log::warning('Failed to send borrow notifications: ' . $e->getMessage());
        }
    }

    /**
     * Send return notifications
     */
    private function sendReturnNotifications($peminjaman, $dendaTotal)
    {
        try {
            // WhatsApp notification
            if ($this->whatsappService && method_exists($this->whatsappService, 'sendReturnNotification')) {
                $this->whatsappService->sendReturnNotification($peminjaman, $dendaTotal);
                
                if ($dendaTotal > 0 && method_exists($this->whatsappService, 'sendDendaNotification')) {
                    $this->whatsappService->sendDendaNotification($peminjaman, $dendaTotal);
                }
                
                $hariTerlambat = Carbon::parse($peminjaman->tanggal_pengembalian)
                    ->diffInDays($peminjaman->tgl_jatuh_tempo);
                
                if ($hariTerlambat > 0 && method_exists($this->whatsappService, 'sendLateReturnNotification')) {
                    $this->whatsappService->sendLateReturnNotification($peminjaman, $hariTerlambat, $peminjaman->denda);
                }
            }
            
            // Notifikasi ke kepala pustaka jika ada denda
            if ($dendaTotal > 0) {
                $this->kirimNotifikasiKeKepalaPustaka($peminjaman, $dendaTotal);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to send return notifications: ' . $e->getMessage());
        }
    }

    /**
     * Send notification to kepala pustaka
     */
    private function kirimNotifikasiKeKepalaPustaka($peminjaman, $dendaTotal)
    {
        try {
            $kepalaPustaka = User::where('role', 'kepala_pustaka')->first();
            
            if ($kepalaPustaka && $this->notificationService && method_exists($this->notificationService, 'createKepalaPustakaNotification')) {
                $this->notificationService->createKepalaPustakaNotification(
                    'Denda Baru Perlu Verifikasi',
                    'Petugas ' . Auth::user()->name . ' mencatat denda Rp ' . 
                    number_format($dendaTotal, 0, ',', '.') . ' untuk ' . 
                    $peminjaman->user->name . ' (' . $peminjaman->buku->judul . ')',
                    'warning',
                    route('kepala-pustaka.verifikasi.detail', $peminjaman->id)
                );
            }
        } catch (\Exception $e) {
            Log::warning('Failed to send notification to kepala pustaka: ' . $e->getMessage());
        }
    }
}