<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\PeminjamanDigital;
use App\Models\DigitalAccessLog;
use App\Services\DigitalBorrowService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KoleksiDigitalController extends Controller
{
    protected $digitalService;

    public function __construct(DigitalBorrowService $digitalService)
    {
        $this->digitalService = $digitalService;
    }

    /**
     * Daftar koleksi digital
     */
    public function index(Request $request)
    {
        $query = Buku::withCount(['peminjamanDigital' => function($q) {
                $q->where('status', 'aktif');
            }])
            ->where('tipe', 'digital')
            ->where('file_path', '!=', null);
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('judul', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('pengarang', 'LIKE', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            if ($request->status == 'tersedia') {
                $query->where(function($q) {
                    $q->whereRaw('jumlah_lisensi > lisensi_dipinjam')
                      ->orWhereIn('jenis_koleksi', ['soal', 'modul', 'dokumen']);
                });
            } elseif ($request->status == 'habis') {
                $query->where('jenis_koleksi', 'ebook')
                      ->whereRaw('jumlah_lisensi <= lisensi_dipinjam');
            }
        }
        
        if ($request->filled('jenis')) {
            $query->where('jenis_koleksi', $request->jenis);
        }
        
        $koleksi = $query->latest()->paginate(12);
        
        $statistik = [
            'total' => Buku::where('tipe', 'digital')->count(),
            'tersedia' => Buku::where('tipe', 'digital')
                              ->where(function($q) {
                                  $q->whereRaw('jumlah_lisensi > lisensi_dipinjam')
                                    ->orWhereIn('jenis_koleksi', ['soal', 'modul', 'dokumen']);
                              })
                              ->count(),
            'dipinjam' => PeminjamanDigital::where('status', 'aktif')->count(),
            'ebook' => Buku::where('tipe', 'digital')->where('jenis_koleksi', 'ebook')->count(),
            'soal' => Buku::where('tipe', 'digital')->where('jenis_koleksi', 'soal')->count(),
            'modul' => Buku::where('tipe', 'digital')->where('jenis_koleksi', 'modul')->count(),
            'dokumen' => Buku::where('tipe', 'digital')->where('jenis_koleksi', 'dokumen')->count(),
        ];
        
        return view('petugas.pages.koleksi-digital.index', compact('koleksi', 'statistik'));
    }
    
    /**
     * Detail koleksi digital
     */
    public function show($id)
    {
        $buku = Buku::with(['kategori', 'peminjamanDigital' => function($q) {
                $q->with('user')->latest()->limit(10);
            }])
            ->where('tipe', 'digital')
            ->where('file_path', '!=', null)
            ->findOrFail($id);

        $ketersediaan = $buku->cekKetersediaanDigital();
        $peminjamanAktif = $buku->peminjamanDigital()
                               ->where('status', 'aktif')
                               ->with('user')
                               ->get();
        
        return view('petugas.pages.koleksi-digital.show', compact('buku', 'ketersediaan', 'peminjamanAktif'));
    }

    /**
     * Form tambah koleksi digital
     */
    public function create()
    {
        $kategori = \App\Models\KategoriBuku::all();
        return view('petugas.pages.koleksi-digital.create', compact('kategori'));
    }

    /**
     * Simpan koleksi digital baru
     */
    public function store(Request $request)
    {
        // Validasi dasar
        $rules = [
            'judul' => 'required|string|max:255',
            'jenis_koleksi' => 'required|in:ebook,soal,modul,dokumen',
            'pengarang' => 'nullable|string|max:255',
            'penerbit' => 'nullable|string|max:255',
            'tahun_terbit' => 'nullable|integer|min:1900|max:' . date('Y'),
            'isbn' => 'nullable|string|max:20|unique:buku,isbn',
            'kategori_id' => 'required|exists:kategori_buku,id',
            'deskripsi' => 'nullable|string',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'penerbit_lisensi' => 'nullable|string|max:255',
            'tanggal_berlaku_lisensi' => 'nullable|date',
            'tanggal_kadaluarsa_lisensi' => 'nullable|date|after:tanggal_berlaku_lisensi',
            'catatan_lisensi' => 'nullable|string'
        ];

        // Validasi conditional untuk file
        if ($request->jenis_koleksi === 'ebook') {
            $rules['file_ebook'] = 'required|file|mimes:pdf,epub|max:20480';
            $rules['jumlah_lisensi'] = 'required|integer|min:1';
            // durasi_pinjam_hari TIDAK divisualisasi karena akan di-set manual ke 7
        } else {
            $rules['file_ebook'] = 'nullable|file|mimes:pdf,epub|max:20480';
            $rules['jumlah_lisensi'] = 'nullable|integer|min:1';
        }

        $validated = $request->validate($rules);
        
        DB::beginTransaction();
        
        try {
            // Upload file ebook (jika ada)
            $filePath = null;
            if ($request->hasFile('file_ebook')) {
                $filePath = $request->file('file_ebook')->store('ebooks', 'public');
            }
            
            // Upload cover (jika ada)
            $coverPath = null;
            if ($request->hasFile('cover')) {
                $coverPath = $request->file('cover')->store('covers', 'public');
            }

            // Tentukan lisensi berdasarkan jenis
            $jenisKoleksi = $request->jenis_koleksi;
            $bisaDownload = in_array($jenisKoleksi, ['soal', 'modul', 'dokumen']);
            
            if ($bisaDownload) {
                // Untuk Soal, Modul, Dokumen
                $jumlahLisensi = 999;
                $durasiPinjam = 0;
                $aksesDigital = 'full_access';
                $accessLevel = 'public';
                $drmEnabled = false;
            } else {
                // Untuk E-Book
                $jumlahLisensi = $request->jumlah_lisensi ?? 3;
                $durasiPinjam = 7; // TETAP 7 HARI (dalam hari)
                $aksesDigital = $request->akses_digital ?? 'online_only';
                $accessLevel = 'member_only';
                $drmEnabled = true;
            }

            // Simpan data buku
            $buku = Buku::create([
                'judul' => $request->judul,
                'pengarang' => $request->pengarang,
                'penerbit' => $request->penerbit,
                'tahun_terbit' => $request->tahun_terbit,
                'isbn' => $request->isbn,
                'kategori_id' => $request->kategori_id,
                'deskripsi' => $request->deskripsi,

                'tipe' => 'digital',
                'jenis_koleksi' => $jenisKoleksi,
                'bisa_download' => $bisaDownload,
                'status' => 'tersedia',

                'stok' => 0,
                'stok_tersedia' => 0,
                'stok_dipinjam' => 0,
                'stok_rusak' => 0,
                'stok_hilang' => 0,

                'format' => $filePath ? pathinfo($filePath, PATHINFO_EXTENSION) : null,
                'file_path' => $filePath,
                'cover_path' => $coverPath,
                'sampul' => $coverPath,
                'file_size' => $request->hasFile('file_ebook') ? $request->file('file_ebook')->getSize() : null,
                'file_type' => $request->hasFile('file_ebook') ? $request->file('file_ebook')->getMimeType() : null,

                'jumlah_lisensi' => $jumlahLisensi,
                'lisensi_dipinjam' => 0,
                'akses_digital' => $aksesDigital,
                'durasi_pinjam_hari' => $durasiPinjam, // 7 HARI UNTUK E-BOOK

                'penerbit_lisensi' => $request->penerbit_lisensi,
                'tanggal_berlaku_lisensi' => $request->tanggal_berlaku_lisensi,
                'tanggal_kadaluarsa_lisensi' => $request->tanggal_kadaluarsa_lisensi,
                'catatan_lisensi' => $request->catatan_lisensi,

                'access_level' => $accessLevel,
                'drm_enabled' => $drmEnabled,

                'created_by' => Auth::id(),
            ]);
            
            DB::commit();

            return redirect()
                ->route('petugas.koleksi-digital.show', $buku->id)
                ->with('success', 'Koleksi digital berhasil ditambahkan.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Hapus file yang sudah diupload jika terjadi error
            if (isset($filePath) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            if (isset($coverPath) && Storage::disk('public')->exists($coverPath)) {
                Storage::disk('public')->delete($coverPath);
            }
            
            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    /**
     * Form edit koleksi digital
     */
    public function edit($id)
    {
        $buku = Buku::where('tipe', 'digital')->findOrFail($id);
        $kategori = \App\Models\KategoriBuku::all();
        return view('petugas.pages.koleksi-digital.edit', compact('buku', 'kategori'));
    }

    /**
     * Update koleksi digital
     */
    public function update(Request $request, $id)
    {
        $buku = Buku::where('tipe', 'digital')->findOrFail($id);

        $rules = [
            'judul' => 'required|string|max:255',
            'jenis_koleksi' => 'required|in:ebook,soal,modul,dokumen',
            'pengarang' => 'nullable|string|max:255',
            'penerbit' => 'nullable|string|max:255',
            'tahun_terbit' => 'nullable|integer|min:1900|max:' . date('Y'),
            'isbn' => 'nullable|string|max:20|unique:buku,isbn,' . $buku->id,
            'kategori_id' => 'required|exists:kategori_buku,id',
            'deskripsi' => 'nullable|string',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'penerbit_lisensi' => 'nullable|string|max:255',
            'tanggal_berlaku_lisensi' => 'nullable|date',
            'tanggal_kadaluarsa_lisensi' => 'nullable|date|after:tanggal_berlaku_lisensi',
            'catatan_lisensi' => 'nullable|string'
        ];

        if ($request->jenis_koleksi === 'ebook') {
            $rules['file_ebook'] = 'nullable|file|mimes:pdf,epub|max:20480';
            $rules['jumlah_lisensi'] = 'required|integer|min:1';
        } else {
            $rules['file_ebook'] = 'nullable|file|mimes:pdf,epub|max:20480';
            $rules['jumlah_lisensi'] = 'nullable|integer|min:1';
        }

        $validated = $request->validate($rules);

        $data = $request->except(['file_ebook', 'cover', '_token', '_method']);

        DB::beginTransaction();
        
        try {
            // Upload file ebook baru jika ada
            if ($request->hasFile('file_ebook')) {
                if ($buku->file_path && Storage::disk('public')->exists($buku->file_path)) {
                    Storage::disk('public')->delete($buku->file_path);
                }
                
                $filePath = $request->file('file_ebook')->store('ebooks', 'public');
                $data['file_path'] = $filePath;
                $data['file_size'] = $request->file('file_ebook')->getSize();
                $data['file_type'] = $request->file('file_ebook')->getMimeType();
                $data['format'] = $request->file('file_ebook')->getClientOriginalExtension();
            }

            // Upload cover baru jika ada
            if ($request->hasFile('cover')) {
                if ($buku->cover_path && Storage::disk('public')->exists($buku->cover_path)) {
                    Storage::disk('public')->delete($buku->cover_path);
                }
                if ($buku->sampul && Storage::disk('public')->exists($buku->sampul) && $buku->sampul !== $buku->cover_path) {
                    Storage::disk('public')->delete($buku->sampul);
                }
                
                $coverPath = $request->file('cover')->store('covers', 'public');
                $data['cover_path'] = $coverPath;
                $data['sampul'] = $coverPath;
            }

            // Update berdasarkan jenis koleksi
            $jenisKoleksi = $request->jenis_koleksi;
            $bisaDownload = in_array($jenisKoleksi, ['soal', 'modul', 'dokumen']);
            
            $data['jenis_koleksi'] = $jenisKoleksi;
            $data['bisa_download'] = $bisaDownload;
            
            if ($bisaDownload) {
                $data['jumlah_lisensi'] = 999;
                $data['durasi_pinjam_hari'] = 0;
                $data['akses_digital'] = 'full_access';
                $data['access_level'] = 'public';
                $data['drm_enabled'] = false;
            } else {
                $data['jumlah_lisensi'] = $request->jumlah_lisensi ?? $buku->jumlah_lisensi;
                $data['durasi_pinjam_hari'] = 7; // TETAP 7 HARI
                $data['akses_digital'] = $request->akses_digital ?? $buku->akses_digital;
                $data['access_level'] = 'member_only';
                $data['drm_enabled'] = true;
            }

            $buku->update($data);
            
            DB::commit();

            return redirect()
                ->route('petugas.koleksi-digital.show', $buku->id)
                ->with('success', 'Koleksi digital berhasil diperbarui.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui: ' . $e->getMessage());
        }
    }

    /**
     * Hapus koleksi digital
     */
    public function destroy($id)
    {
        $buku = Buku::where('tipe', 'digital')->findOrFail($id);

        // Cek apakah sedang dipinjam (hanya untuk ebook)
        if ($buku->jenis_koleksi === 'ebook' && $buku->lisensi_dipinjam > 0) {
            return back()->with('error', 'Tidak dapat menghapus karena masih ada ' . 
                                $buku->lisensi_dipinjam . ' lisensi sedang dipinjam.');
        }

        // Hapus file
        if ($buku->file_path && Storage::disk('public')->exists($buku->file_path)) {
            Storage::disk('public')->delete($buku->file_path);
        }
        if ($buku->cover_path && Storage::disk('public')->exists($buku->cover_path)) {
            Storage::disk('public')->delete($buku->cover_path);
        }
        if ($buku->sampul && $buku->sampul !== $buku->cover_path && Storage::disk('public')->exists($buku->sampul)) {
            Storage::disk('public')->delete($buku->sampul);
        }
        
        $buku->delete();

        return redirect()
            ->route('petugas.koleksi-digital.index')
            ->with('success', 'Koleksi digital berhasil dihapus.');
    }

    /**
     * Method untuk membaca buku digital dengan secure viewer
     */
    public function baca($id)
    {
        $buku = Buku::where('tipe', 'digital')->findOrFail($id);
        
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }
        
        $user = Auth::user();
        $isPetugas = in_array($user->role, ['petugas', 'admin', 'kepala_pustaka', 'pimpinan']);
        
        if (!$isPetugas) {
            if ($buku->bisa_langsung_download) {
                DigitalAccessLog::create([
                    'user_id' => $user->id,
                    'buku_id' => $buku->id,
                    'aksi' => 'baca',
                    'status' => 'berhasil',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]);
            } else {
                $peminjamanAktif = PeminjamanDigital::where('user_id', $user->id)
                    ->where('buku_id', $buku->id)
                    ->where('status', 'aktif')
                    ->where('tanggal_expired', '>', now())
                    ->first();
                
                if (!$peminjamanAktif) {
                    return redirect()->route('home')
                        ->with('error', 'Anda tidak memiliki akses ke buku ini. Silakan pinjam terlebih dahulu.');
                }
                
                $peminjamanAktif->update(['terakhir_akses' => now()]);
                $peminjamanAktif->increment('jumlah_akses');
            }
        }
        
        if (!$buku->file_path || !Storage::disk('public')->exists($buku->file_path)) {
            return back()->with('error', 'File buku tidak ditemukan.');
        }
        
        return view('digital.secure-reader', [
            'buku' => $buku,
            'maxSessionTime' => $buku->bisa_langsung_download ? 7200 : ($buku->durasi_pinjam_hari * 3600)
        ]);
    }
    
    /**
     * Streaming file PDF (tidak bisa di-download)
     */
    public function stream($id)
    {
        $buku = Buku::where('tipe', 'digital')->findOrFail($id);
        
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }
        
        $user = Auth::user();
        $isPetugas = in_array($user->role, ['petugas', 'admin', 'kepala_pustaka', 'pimpinan']);
        
        if (!$isPetugas) {
            if ($buku->bisa_langsung_download) {
                // Boleh akses
            } else {
                $peminjamanAktif = PeminjamanDigital::where('user_id', $user->id)
                    ->where('buku_id', $buku->id)
                    ->where('status', 'aktif')
                    ->where('tanggal_expired', '>', now())
                    ->exists();
                
                if (!$peminjamanAktif) {
                    abort(403, 'Tidak memiliki akses ke buku ini.');
                }
            }
        }
        
        $filePath = storage_path('app/public/' . $buku->file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan.');
        }
        
        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $buku->judul . '.pdf"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => 'Sat, 01 Jan 2000 00:00:00 GMT',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-Robots-Tag' => 'noindex, nofollow, noarchive',
        ]);
    }

    /**
     * Download file untuk soal/modul/dokumen
     */
    public function download($id)
    {
        $buku = Buku::where('tipe', 'digital')->findOrFail($id);
        
        if (!$buku->bisa_langsung_download) {
            abort(403, 'File ini tidak bisa di-download langsung. Silakan pinjam terlebih dahulu.');
        }
        
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }
        
        $filePath = storage_path('app/public/' . $buku->file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan.');
        }
        
        DigitalAccessLog::create([
            'user_id' => Auth::id(),
            'buku_id' => $buku->id,
            'aksi' => 'download',
            'status' => 'berhasil',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
        
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $filename = $buku->judul . '.' . $extension;
        
        return response()->download($filePath, $filename);
    }
}