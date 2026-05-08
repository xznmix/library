<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BacaDiTempat;
use App\Models\Buku;
use App\Models\User;
use App\Models\PoinAnggota;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BacaDiTempatController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = BacaDiTempat::with(['user', 'buku', 'petugas']);
            
            if ($request->filled('tanggal')) {
                $query->whereDate('waktu_mulai', $request->tanggal);
            }
            
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->whereHas('user', function($sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%")
                            ->orWhere('no_anggota', 'like', "%{$search}%");
                    })->orWhereHas('buku', function($sub) use ($search) {
                        $sub->where('judul', 'like', "%{$search}%");
                    });
                });
            }
            
            $bacaDiTempat = $query->latest('waktu_mulai')->paginate(20);
            
            $statistik = [
                'hari_ini' => BacaDiTempat::whereDate('waktu_mulai', Carbon::today())->count(),
                'sedang_baca' => BacaDiTempat::where('status', 'sedang_baca')->count(),
                'total_poin' => BacaDiTempat::where('status', 'selesai')->sum('poin_didapat'),
                'total_baca' => BacaDiTempat::where('status', 'selesai')->count(),
            ];
            
            return view('petugas.pages.baca-ditempat.index', compact('bacaDiTempat', 'statistik'));
        } catch (\Exception $e) {
            Log::error('Error in BacaDiTempat index: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data.');
        }
    }
    
    public function create()
    {
        return view('petugas.pages.baca-ditempat.create');
    }
    
    public function store(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'buku_id' => 'required|exists:buku,id',
                'lokasi' => 'nullable|string',
                'catatan' => 'nullable|string',
            ]);
            
            DB::beginTransaction();
            
            // Ambil data user (anggota) dan buku
            $user = User::findOrFail($request->user_id);
            $buku = Buku::findOrFail($request->buku_id);
            
            // Validasi status anggota
            if ($user->status_anggota != 'active') {
                return back()->with('error', 'Anggota tidak aktif!')->withInput();
            }
            
            // Validasi role anggota
            if (!in_array($user->role, ['siswa', 'guru', 'pegawai', 'umum'])) {
                return back()->with('error', 'Role anggota tidak valid!')->withInput();
            }
            
            // Cek apakah sedang baca buku lain (gunakan anggota_id sesuai database)
            $sedangBaca = BacaDiTempat::where('anggota_id', $user->id)  // ← diubah ke anggota_id
                ->where('status', 'sedang_baca')
                ->first();
                
            if ($sedangBaca) {
                return back()->with('error', 'Anggota sedang membaca buku lain! Selesaikan dulu.')->withInput();
            }
            
            // Simpan data (gunakan anggota_id sesuai database)
            $baca = BacaDiTempat::create([
                'anggota_id' => $user->id,  // ← diubah ke anggota_id
                'buku_id' => $buku->id,
                'barcode_buku' => $buku->barcode,
                'no_anggota' => $user->no_anggota,
                'waktu_mulai' => now(),
                'lokasi' => $request->lokasi ?? 'Perpustakaan Tambang Ilmu - Ruang Baca Umum',
                'status' => 'sedang_baca',
                'catatan' => $request->catatan,
                'petugas_id' => Auth::id(),
                'updated_by' => Auth::user()->name,
            ]);
            
            DB::commit();
            
            return redirect()->route('petugas.baca-ditempat.show', $baca->id)
                ->with('success', 'Berhasil mencatat aktivitas baca!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing baca ditempat: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
    
    public function show($id)
    {
        try {
            $baca = BacaDiTempat::with(['user', 'buku', 'petugas'])->findOrFail($id);
            return view('petugas.pages.baca-ditempat.show', compact('baca'));
        } catch (\Exception $e) {
            Log::error('Error showing baca ditempat: ' . $e->getMessage());
            return redirect()->route('petugas.baca-ditempat.index')
                ->with('error', 'Data tidak ditemukan.');
        }
    }
    
    public function selesai($id)
    {
        try {
            DB::beginTransaction();
            
            $baca = BacaDiTempat::with(['user', 'buku'])->findOrFail($id);
            
            if ($baca->status === 'selesai') {
                return back()->with('error', 'Aktivitas baca sudah selesai!');
            }
            
            $waktuSelesai = now();
            $waktuMulai = Carbon::parse($baca->waktu_mulai);
            
            // Hitung durasi dengan aman
            if ($waktuSelesai->greaterThan($waktuMulai)) {
                $durasi = $waktuMulai->diffInMinutes($waktuSelesai);
            } else {
                // Jika waktu selesai lebih kecil, gunakan durasi default 1 menit
                $durasi = 1;
            }
            
            // Hitung poin berdasarkan durasi
            $poinDasar = 5;
            $poinBonus = 0;
            if ($durasi >= 30) $poinBonus += 5;
            if ($durasi >= 60) $poinBonus += 5;
            $totalPoin = $poinDasar + $poinBonus;
            
            $baca->update([
                'waktu_selesai' => $waktuSelesai,
                'durasi_menit' => $durasi,
                'poin_didapat' => $totalPoin,
                'status' => 'selesai',
                'updated_by' => Auth::user()->name,
            ]);
            
            // Tambah poin ke anggota
            if (class_exists(PoinAnggota::class)) {
                PoinAnggota::tambahPoin(
                    $baca->anggota_id,
                    $totalPoin,
                    "Baca di tempat: {$baca->buku->judul} selama {$durasi} menit",
                    'baca_ditempat_' . $baca->id
                );
            }
            
            DB::commit();
            
            return redirect()->route('petugas.baca-ditempat.index')
                ->with('success', "✓ Baca selesai! Durasi: " . floor($durasi / 60) . " jam " . ($durasi % 60) . " menit, Poin: +{$totalPoin}");
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error finishing baca ditempat: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyelesaikan baca.');
        }
    }
    
    public function cariAnggota(Request $request)
    {
        try {
            $search = $request->get('search');
            
            if (!$search || strlen($search) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Minimal 2 karakter untuk pencarian'
                ]);
            }
            
            $query = User::whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])
                ->where('status_anggota', 'active');
            
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('no_anggota', 'like', "%{$search}%")
                    ->orWhere('nisn_nik', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
            
            $anggota = $query->limit(10)->get();
            
            if ($anggota->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anggota tidak ditemukan'
                ]);
            }
            
            $results = [];
            foreach ($anggota as $a) {
                $totalPoin = 0;
                if (class_exists(PoinAnggota::class)) {
                    $totalPoin = PoinAnggota::where('user_id', $a->id)->sum('poin');
                }
                
                $results[] = [
                    'id' => $a->id,
                    'nama' => $a->name,
                    'no_anggota' => $a->no_anggota,
                    'nisn_nik' => $a->nisn_nik,
                    'kelas' => $a->kelas ?? '-',
                    'role' => $a->role,
                    'poin' => $totalPoin,
                ];
            }
            
            return response()->json([
                'success' => true,
                'data' => $results
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error searching anggota: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari anggota'
            ]);
        }
    }
    
    public function cariBuku(Request $request)
    {
        try {
            $search = $request->get('search');
            
            if (!$search || strlen($search) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Minimal 2 karakter untuk pencarian'
                ]);
            }
            
            $query = Buku::where('stok', '>', 0);
            
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('pengarang', 'like', "%{$search}%")
                    ->orWhere('penerbit', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
            
            $buku = $query->limit(10)->get();
            
            if ($buku->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buku tidak ditemukan'
                ]);
            }
            
            $results = [];
            foreach ($buku as $b) {
                $results[] = [
                    'id' => $b->id,
                    'judul' => $b->judul,
                    'barcode' => $b->barcode,
                    'pengarang' => $b->pengarang ?? '-',
                    'penerbit' => $b->penerbit ?? '-',
                    'tahun_terbit' => $b->tahun_terbit ?? '-',
                    'rak' => $b->rak ?? '-',
                    'stok' => $b->stok,
                ];
            }
            
            return response()->json([
                'success' => true,
                'data' => $results
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error searching buku: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari buku'
            ]);
        }
    }
}