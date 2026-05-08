<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\PeminjamanDigital;
use App\Models\DigitalAccessLog;
use App\Services\DigitalBorrowService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class KoleksiDigitalController extends Controller
{
    protected $digitalService;

    public function __construct(DigitalBorrowService $digitalService)
    {
        $this->digitalService = $digitalService;
    }

    /**
     * Daftar koleksi digital untuk anggota
     */
    public function index(Request $request)
    {
        $query = Buku::where('tipe', 'digital')
            ->where('file_path', '!=', null);
        
        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'LIKE', '%' . $search . '%')
                  ->orWhere('pengarang', 'LIKE', '%' . $search . '%')
                  ->orWhere('penerbit', 'LIKE', '%' . $search . '%');
            });
        }
        
        // Filter jenis koleksi
        if ($request->filled('jenis')) {
            $query->where('jenis_koleksi', $request->jenis);
        }
        
        $koleksi = $query->latest()->paginate(15);
        
        // Ambil peminjaman aktif user
        $peminjamanAktif = PeminjamanDigital::with('buku')
            ->where('user_id', Auth::id())
            ->where('status', 'aktif')
            ->where('tanggal_expired', '>', now())
            ->get();
        
        // Statistik
        $statistik = [
            'total' => Buku::where('tipe', 'digital')->count(),
            'ebook' => Buku::where('tipe', 'digital')->where('jenis_koleksi', 'ebook')->count(),
            'soal' => Buku::where('tipe', 'digital')->where('jenis_koleksi', 'soal')->count(),
            'modul' => Buku::where('tipe', 'digital')->where('jenis_koleksi', 'modul')->count(),
            'dokumen' => Buku::where('tipe', 'digital')->where('jenis_koleksi', 'dokumen')->count(),
        ];
        
        return view('anggota.pages.koleksi-digital.index', compact('koleksi', 'peminjamanAktif', 'statistik'));
    }

    /**
     * Detail koleksi digital
     */
    public function show($id)
    {
        $buku = Buku::where('tipe', 'digital')
            ->where('file_path', '!=', null)
            ->findOrFail($id);
        
        // Cek apakah user sedang meminjam buku ini (hanya untuk ebook)
        $sedangDipinjam = null;
        if ($buku->perlu_pinjam) {
            $sedangDipinjam = PeminjamanDigital::where('user_id', Auth::id())
                ->where('buku_id', $buku->id)
                ->where('status', 'aktif')
                ->where('tanggal_expired', '>', now())
                ->first();
        }
        
        $ketersediaan = $buku->cekKetersediaanDigital();
        
        return view('anggota.pages.koleksi-digital.show', compact('buku', 'sedangDipinjam', 'ketersediaan'));
    }

    /**
     * Pinjam buku digital (hanya untuk ebook)
     */
    public function pinjam($id)
    {
        $buku = Buku::where('tipe', 'digital')->findOrFail($id);
        $user = Auth::user();
        
        // Validasi: hanya ebook yang perlu dipinjam
        if (!$buku->perlu_pinjam) {
            return redirect()->back()
                ->with('error', 'Koleksi ini tidak perlu dipinjam. Silakan langsung download atau baca.');
        }
        
        // Cek ketersediaan lisensi
        $tersedia = $buku->jumlah_lisensi - $buku->lisensi_dipinjam;
        if ($tersedia <= 0) {
            return redirect()->back()
                ->with('error', 'Maaf, semua lisensi sedang dipinjam. Silakan coba lagi nanti.');
        }
        
        // Cek apakah user sudah meminjam buku ini
        $sudahMeminjam = PeminjamanDigital::where('user_id', $user->id)
            ->where('buku_id', $buku->id)
            ->where('status', 'aktif')
            ->where('tanggal_expired', '>', now())
            ->exists();
        
        if ($sudahMeminjam) {
            return redirect()->back()
                ->with('error', 'Anda masih memiliki peminjaman aktif untuk buku ini.');
        }
        
        // Generate token akses unik
        $tokenAkses = Str::random(64);
        
        // Hitung tanggal expired (durasi dalam jam)
        $durasiJam = $buku->durasi_pinjam_hari ?? 24;
        $tanggalExpired = Carbon::now()->addHours($durasiJam);
        
        // Buat peminjaman digital
        $peminjaman = PeminjamanDigital::create([
            'user_id' => $user->id,
            'buku_id' => $buku->id,
            'tanggal_pinjam' => now(),
            'tanggal_expired' => $tanggalExpired,
            'token_akses' => $tokenAkses,
            'status' => 'aktif',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        
        // Kurangi lisensi yang tersedia
        $buku->increment('lisensi_dipinjam');
        
        // Log aktivitas
        DigitalAccessLog::create([
            'peminjaman_digital_id' => $peminjaman->id,
            'user_id' => $user->id,
            'buku_id' => $buku->id,
            'aksi' => 'pinjam',
            'status' => 'berhasil',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'keterangan' => "Meminjam e-book: {$buku->judul}"
        ]);
        
        return redirect()->route('anggota.koleksi-digital.show', $buku->id)
            ->with('success', "Berhasil meminjam e-book! Masa pinjam: {$durasiJam} jam.");
    }

    /**
     * Download koleksi (untuk soal/modul/dokumen)
     */
    public function download($id)
    {
        $buku = Buku::where('tipe', 'digital')->findOrFail($id);
        $user = Auth::user();
        
        // Cek apakah koleksi bisa di-download langsung
        if (!$buku->bisa_langsung_download) {
            // Jika ebook, cek apakah user punya peminjaman aktif
            if ($buku->jenis_koleksi === 'ebook') {
                $peminjamanAktif = PeminjamanDigital::where('user_id', $user->id)
                    ->where('buku_id', $buku->id)
                    ->where('status', 'aktif')
                    ->where('tanggal_expired', '>', now())
                    ->first();
                
                if (!$peminjamanAktif) {
                    return redirect()->back()
                        ->with('error', 'Anda harus meminjam e-book ini terlebih dahulu.');
                }
            } else {
                abort(403, 'File ini tidak bisa di-download.');
            }
        }
        
        $filePath = storage_path('app/public/' . $buku->file_path);
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }
        
        // Log download
        DigitalAccessLog::create([
            'user_id' => $user->id,
            'buku_id' => $buku->id,
            'aksi' => 'download',
            'status' => 'berhasil',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'keterangan' => "Download: {$buku->judul}"
        ]);
        
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $filename = Str::slug($buku->judul) . '.' . $extension;
        
        return response()->download($filePath, $filename);
    }

    /**
     * Baca online (redirect ke secure reader)
     */
    public function baca($id)
    {
        $buku = Buku::where('tipe', 'digital')->findOrFail($id);
        $user = Auth::user();
        
        // Untuk koleksi download bebas, langsung bisa baca
        if ($buku->bisa_langsung_download) {
            // Log akses
            DigitalAccessLog::create([
                'user_id' => $user->id,
                'buku_id' => $buku->id,
                'aksi' => 'baca',
                'status' => 'berhasil',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'keterangan' => "Baca online: {$buku->judul}"
            ]);
            
            return view('digital.secure-reader', [
                'buku' => $buku,
                'maxSessionTime' => 7200 // 2 jam
            ]);
        }
        
        // Untuk ebook, cek peminjaman aktif
        $peminjamanAktif = PeminjamanDigital::where('user_id', $user->id)
            ->where('buku_id', $buku->id)
            ->where('status', 'aktif')
            ->where('tanggal_expired', '>', now())
            ->first();
        
        if (!$peminjamanAktif) {
            return redirect()->route('anggota.koleksi-digital.show', $buku->id)
                ->with('error', 'Anda harus meminjam e-book ini terlebih dahulu.');
        }
        
        // Update terakhir akses
        $peminjamanAktif->update(['terakhir_akses' => now()]);
        $peminjamanAktif->increment('jumlah_akses');
        
        // Log akses
        DigitalAccessLog::create([
            'peminjaman_digital_id' => $peminjamanAktif->id,
            'user_id' => $user->id,
            'buku_id' => $buku->id,
            'aksi' => 'baca',
            'status' => 'berhasil',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
        
        return view('digital.secure-reader', [
            'buku' => $buku,
            'maxSessionTime' => $buku->durasi_pinjam_hari * 3600 // Konversi jam ke detik
        ]);
    }
}