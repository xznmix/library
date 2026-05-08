<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Buku;
use App\Models\User;
use App\Models\PoinAnggota;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AnggotaController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // ========== STATISTIK DASAR ==========
        $sedang_dipinjam = Peminjaman::where('user_id', $user->id)
            ->whereIn('status_pinjam', ['dipinjam', 'terlambat'])
            ->count();
            
        $jatuh_tempo = Peminjaman::where('user_id', $user->id)
            ->whereIn('status_pinjam', ['dipinjam', 'terlambat'])
            ->whereDate('tgl_jatuh_tempo', '<=', Carbon::now()->addDays(3))
            ->whereDate('tgl_jatuh_tempo', '>=', Carbon::now())
            ->count();
            
        $total_peminjaman = Peminjaman::where('user_id', $user->id)->count();
        
        // ========== AKTIVITAS TERAKHIR ==========
        $aktivitas_terakhir = Peminjaman::with('buku')
            ->where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();
        
        // ========== REKOMENDASI BERDASARKAN PEMINJAMAN ==========
        // Ambil kategori yang sering dipinjam user
        $kategori_favorit = Peminjaman::where('user_id', $user->id)
            ->whereHas('buku.kategori')
            ->with('buku.kategori')
            ->get()
            ->pluck('buku.kategori_id')
            ->filter()
            ->unique()
            ->toArray();
        
        $rekomendasi_peminjaman = collect();
        if (!empty($kategori_favorit)) {
            // Ambil buku dari kategori favorit yang belum pernah dipinjam
            $rekomendasi_peminjaman = Buku::with('kategori')
                ->whereIn('kategori_id', $kategori_favorit)
                ->whereNotIn('id', function($query) use ($user) {
                    $query->select('buku_id')
                        ->from('peminjaman')
                        ->where('user_id', $user->id);
                })
                ->where('stok', '>', 0) // Hanya yang masih ada stok
                ->inRandomOrder()
                ->limit(4)
                ->get();
                
            // Jika kurang dari 4, tambahkan dari buku populer
            if ($rekomendasi_peminjaman->count() < 4) {
                $sudah_ada_ids = $rekomendasi_peminjaman->pluck('id')->toArray();
                $buku_tambahan = Buku::with('kategori')
                    ->whereNotIn('id', $sudah_ada_ids)
                    ->whereNotIn('id', function($query) use ($user) {
                        $query->select('buku_id')
                            ->from('peminjaman')
                            ->where('user_id', $user->id);
                    })
                    ->where('stok', '>', 0)
                    ->inRandomOrder()
                    ->limit(4 - $rekomendasi_peminjaman->count())
                    ->get();
                    
                $rekomendasi_peminjaman = $rekomendasi_peminjaman->merge($buku_tambahan);
            }
        } else {
            // Jika belum pernah pinjam, tampilkan rekomendasi random
            $rekomendasi_peminjaman = Buku::with('kategori')
                ->where('stok', '>', 0)
                ->inRandomOrder()
                ->limit(4)
                ->get();
        }
        
        // ========== REKOMENDASI BUKU TERPOPULER ==========
        $rekomendasi_populer = Buku::withCount('peminjaman')
            ->where('stok', '>', 0)
            ->orderBy('peminjaman_count', 'desc')
            ->limit(10)
            ->get();
        
        // ========== SISTEM POIN DAN PERINGKAT ==========
        // Hitung total poin user
        $poin_aktif = PoinAnggota::where('user_id', $user->id)->sum('poin');
        
        // Jika belum ada poin, inisialisasi dari total peminjaman
        if ($poin_aktif == 0 && $total_peminjaman > 0) {
            $poin_aktif = $total_peminjaman * 10;
            // Simpan ke tabel poin (opsional)
            // $this->inisialisasiPoin($user->id, $total_peminjaman);
        }
        
        // Hitung peringkat user
        $peringkat = $this->getPeringkatUser($user);
        $total_anggota = User::where('role', 'anggota')->count();
        
        // Hitung poin yang dibutuhkan untuk ke peringkat selanjutnya
        $poin_ke_peringkat_selanjutnya = $this->getPoinKePeringkatSelanjutnya($user, $poin_aktif);
        $peringkat_selanjutnya = $this->getPeringkatSelanjutnya($user, $peringkat);
        
        // ========== DATA UNTUK GRAFIK (Opsional) ==========
        $peminjaman_per_bulan = $this->getPeminjamanPerBulan($user);
        
        return view('anggota.dashboard', compact(
            'sedang_dipinjam',
            'jatuh_tempo',
            'total_peminjaman',
            'aktivitas_terakhir',
            'rekomendasi_peminjaman',
            'rekomendasi_populer',
            'poin_aktif',
            'peringkat',
            'total_anggota',
            'poin_ke_peringkat_selanjutnya',
            'peringkat_selanjutnya',
            'peminjaman_per_bulan'
        ));
    }
    
    /**
     * Get peringkat user berdasarkan poin
     */
    private function getPeringkatUser($user)
    {
        $poinUser = PoinAnggota::where('user_id', $user->id)->sum('poin');
        
        // Jika poin 0, cek dari total peminjaman
        if ($poinUser == 0) {
            $totalPinjam = Peminjaman::where('user_id', $user->id)->count();
            $poinUser = $totalPinjam * 10;
        }
        
        // Hitung jumlah user dengan poin lebih besar
        $peringkat = PoinAnggota::select('user_id')
            ->groupBy('user_id')
            ->selectRaw('SUM(poin) as total_poin')
            ->having('total_poin', '>', $poinUser)
            ->count();
        
        // Tambahkan user yang memiliki total peminjaman (tanpa poin di tabel)
        $userDenganPinjam = Peminjaman::select('user_id')
            ->groupBy('user_id')
            ->selectRaw('COUNT(*) as total_pinjam')
            ->havingRaw('(COUNT(*) * 10) > ?', [$poinUser])
            ->where('user_id', '!=', $user->id)
            ->count();
            
        return ($peringkat + $userDenganPinjam) + 1;
    }
    
    /**
     * Get poin yang dibutuhkan untuk naik ke peringkat selanjutnya
     */
    private function getPoinKePeringkatSelanjutnya($user, $poinSaatIni)
    {
        // Ambil poin user di peringkat atasnya
        $poinDiAtas = PoinAnggota::select('user_id')
            ->groupBy('user_id')
            ->selectRaw('SUM(poin) as total_poin')
            ->having('total_poin', '>', $poinSaatIni)
            ->orderBy('total_poin', 'asc')
            ->first();
            
        if ($poinDiAtas) {
            return $poinDiAtas->total_poin - $poinSaatIni;
        }
        
        // Cek dari user yang memiliki peminjaman
        $userDiatas = Peminjaman::select('user_id')
            ->groupBy('user_id')
            ->selectRaw('COUNT(*) * 10 as total_poin')
            ->havingRaw('(COUNT(*) * 10) > ?', [$poinSaatIni])
            ->orderBy('total_poin', 'asc')
            ->first();
            
        if ($userDiatas) {
            return $userDiatas->total_poin - $poinSaatIni;
        }
        
        return 0; // Sudah peringkat 1
    }
    
    /**
     * Get peringkat selanjutnya
     */
    private function getPeringkatSelanjutnya($user, $peringkatSaatIni)
    {
        if ($peringkatSaatIni <= 1) {
            return 1;
        }
        return $peringkatSaatIni - 1;
    }
    
    /**
     * Get data peminjaman per bulan untuk grafik
     */
    private function getPeminjamanPerBulan($user)
    {
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = Carbon::now()->subMonths($i);
            $jumlah = Peminjaman::where('user_id', $user->id)
                ->whereYear('created_at', $bulan->year)
                ->whereMonth('created_at', $bulan->month)
                ->count();
                
            $data[] = [
                'bulan' => $bulan->format('M'),
                'jumlah' => $jumlah
            ];
        }
        return $data;
    }
    
    /**
     * Inisialisasi poin dari total peminjaman (opsional)
     */
    private function inisialisasiPoin($userId, $totalPeminjaman)
    {
        // Cek apakah sudah ada poin
        $sudahAda = PoinAnggota::where('user_id', $userId)->exists();
        
        if (!$sudahAda && $totalPeminjaman > 0) {
            // Buat poin berdasarkan peminjaman
            PoinAnggota::create([
                'user_id' => $userId,
                'poin' => $totalPeminjaman * 10,
                'keterangan' => 'Poin awal dari ' . $totalPeminjaman . ' kali peminjaman'
            ]);
        }
    }
    
    /**
     * Method untuk menambah poin (dipanggil saat peminjaman atau pengembalian)
     */
    public function tambahPoin(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'poin' => 'required|integer|min:1',
            'keterangan' => 'required|string'
        ]);
        
        PoinAnggota::create([
            'user_id' => $request->user_id,
            'poin' => $request->poin,
            'keterangan' => $request->keterangan
        ]);
        
        return response()->json(['success' => true, 'message' => 'Poin berhasil ditambahkan']);
    }
    
    /**
     * Get leaderboard (opsional)
     */
    public function leaderboard()
    {
        $leaderboard = PoinAnggota::select('user_id')
            ->with('user')
            ->groupBy('user_id')
            ->selectRaw('SUM(poin) as total_poin')
            ->orderBy('total_poin', 'desc')
            ->limit(10)
            ->get();
            
        // Tambahkan user yang memiliki peminjaman tapi belum ada di tabel poin
        $userTanpaPoin = Peminjaman::select('user_id')
            ->with('user')
            ->groupBy('user_id')
            ->selectRaw('COUNT(*) * 10 as total_poin')
            ->whereNotIn('user_id', $leaderboard->pluck('user_id'))
            ->orderBy('total_poin', 'desc')
            ->limit(10 - $leaderboard->count())
            ->get();
            
        $leaderboard = $leaderboard->merge($userTanpaPoin)->sortByDesc('total_poin')->values();
        
        return view('anggota.leaderboard', compact('leaderboard'));
    }
    
    /**
     * Get detail poin user
     */
    public function detailPoin()
    {
        $user = Auth::user();
        
        $riwayatPoin = PoinAnggota::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        $totalPoin = $riwayatPoin->sum('poin');
        
        // Jika belum ada riwayat poin, hitung dari peminjaman
        if ($riwayatPoin->isEmpty()) {
            $totalPinjam = Peminjaman::where('user_id', $user->id)->count();
            $totalPoin = $totalPinjam * 10;
        }
        
        $peringkat = $this->getPeringkatUser($user);
        
        return view('anggota.poin', compact('riwayatPoin', 'totalPoin', 'peringkat'));
    }
}