<?php

namespace App\Http\Controllers\KepalaPustaka;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Kunjungan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ========== STATISTIK UTAMA ==========
        $totalBuku = Buku::count();
        $totalAnggota = User::whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])->count();
        
        // Denda pending (belum diverifikasi)
        $dendaPending = Peminjaman::where('status_verifikasi', 'pending')
            ->where('denda_total', '>', 0)
            ->count();
            
        // Total denda bulan ini (yang sudah disetujui)
        $totalDendaBulanIni = Peminjaman::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status_verifikasi', 'disetujui')
            ->sum('denda_total');
        
        // Kunjungan hari ini
        $kunjunganHariIni = Kunjungan::whereDate('tanggal', today())->count();
        
        // Peminjaman hari ini
        $peminjamanHariIni = Peminjaman::whereDate('created_at', today())->count();
        
        // Pengembalian hari ini
        $pengembalianHariIni = Peminjaman::whereDate('tanggal_pengembalian', today())->count();
        
        // Buku sedang dipinjam
        $bukuDipinjam = Peminjaman::whereIn('status_pinjam', ['dipinjam', 'terlambat'])->count();
        
        // Anggota aktif
        $anggotaAktif = User::where('status_anggota', 'active')->whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])->count();
        
        // Anggota baru bulan ini
        $anggotaBaruBulanIni = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])
            ->count();
        
        // ========== GRAFIK KUNJUNGAN 7 HARI (LINE CHART) ==========
        $grafikKunjungan = $this->getGrafikKunjungan();
        
        // ========== DETEKSI ANOMALI DENDA ==========
        $anomaliDenda = $this->deteksiAnomaliDenda();
        
        // ========== AKTIVITAS TERBARU ==========
        $aktivitas = ActivityLog::with('user')
            ->latest()
            ->limit(10)
            ->get();
        
        // ========== BUKU POPULER ==========
        $bukuPopuler = Buku::withCount(['peminjaman' => function($query) {
                $query->whereYear('created_at', now()->year);
            }])
            ->orderBy('peminjaman_count', 'desc')
            ->limit(5)
            ->get();
        
        // ========== STATISTIK TAMBAHAN ==========
        $statistik = [
            'peminjaman_hari_ini' => $peminjamanHariIni,
            'pengembalian_hari_ini' => $pengembalianHariIni,
            'buku_dipinjam' => $bukuDipinjam,
            'buku_tersedia' => Buku::sum('stok_tersedia'),
            'anggota_aktif' => $anggotaAktif,
            'anggota_baru_bulan_ini' => $anggotaBaruBulanIni,
        ];
        
        // ========== 5 DENDA PENDING TERBARU ==========
        $dendaPendingList = Peminjaman::with(['user', 'buku', 'petugas'])
            ->where('status_verifikasi', 'pending')
            ->where('denda_total', '>', 0)
            ->latest()
            ->limit(5)
            ->get();
        
        // ========== STOK MENIPIS ==========
        $stokMenipis = Buku::where('stok_tersedia', '<=', 3)
            ->where('stok_tersedia', '>', 0)
            ->orderBy('stok_tersedia', 'asc')
            ->limit(5)
            ->get();
        
        // ========== BUKU HABIS ==========
        $bukuHabis = Buku::where('stok_tersedia', 0)->count();
        
        // ========== DATA UNTUK SIDEBAR ==========
        view()->share([
            'dendaPending' => $dendaPending,
            'kunjunganHariIni' => $kunjunganHariIni
        ]);
        
        return view('kepala-pustaka.dashboard', compact(
            'totalBuku',
            'totalAnggota',
            'dendaPending',
            'totalDendaBulanIni',
            'kunjunganHariIni',
            'grafikKunjungan',
            'anomaliDenda',
            'aktivitas',
            'bukuPopuler',
            'statistik',
            'dendaPendingList',
            'stokMenipis',
            'bukuHabis'
        ));
    }

    /**
     * Grafik Kunjungan 7 Hari Terakhir (LINE CHART)
     */
    private function getGrafikKunjungan()
    {
        $labels = [];
        $data = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = now()->subDays($i);
            $labels[] = $tanggal->isoFormat('dddd');
            $data[] = Kunjungan::whereDate('tanggal', $tanggal)->count();
        }
        
        // Hitung total, rata-rata, dan prediksi
        $total = array_sum($data);
        $rataRata = $total > 0 ? round($total / 7, 1) : 0;
        $trend = $data[6] - $data[0]; // Selisih hari ini vs 7 hari lalu
        
        return [
            'labels' => $labels,
            'data' => $data,
            'total' => $total,
            'rata_rata' => $rataRata,
            'trend' => $trend,
            'trend_text' => $trend > 0 ? "↑ {$trend}" : ($trend < 0 ? "↓ " . abs($trend) : "→ 0"),
            'trend_color' => $trend > 0 ? 'text-green-600' : ($trend < 0 ? 'text-red-600' : 'text-gray-500')
        ];
    }

    /**
     * Deteksi anomali denda per petugas
     */
    private function deteksiAnomaliDenda()
    {
        $anomali = [];
        
        $petugas = User::where('role', 'petugas')->get();
        
        $rataGlobal = Peminjaman::where('status_verifikasi', 'disetujui')
            ->where('denda_total', '>', 0)
            ->avg('denda_total') ?? 0;
        
        foreach ($petugas as $p) {
            $jumlahTransaksi = Peminjaman::where('petugas_id', $p->id)
                ->where('denda_total', '>', 0)
                ->count();
            
            if ($jumlahTransaksi < 5) {
                continue;
            }
            
            $rataPetugas = Peminjaman::where('petugas_id', $p->id)
                ->where('status_verifikasi', 'disetujui')
                ->where('denda_total', '>', 0)
                ->avg('denda_total') ?? 0;
            
            $totalDendaDiajukan = Peminjaman::where('petugas_id', $p->id)
                ->where('denda_total', '>', 0)
                ->sum('denda_total');
            
            $totalDendaDisetujui = Peminjaman::where('petugas_id', $p->id)
                ->where('status_verifikasi', 'disetujui')
                ->where('denda_total', '>', 0)
                ->sum('denda_total');
            
            $rasioPenolakan = $totalDendaDiajukan > 0 
                ? (($totalDendaDiajukan - $totalDendaDisetujui) / $totalDendaDiajukan) * 100 
                : 0;
            
            if ($rataPetugas > 0 && $rataPetugas < $rataGlobal * 0.7) {
                $anomali[] = [
                    'petugas' => $p->name,
                    'rata_denda' => round($rataPetugas),
                    'rata_global' => round($rataGlobal),
                    'selisih' => round((1 - $rataPetugas / $rataGlobal) * 100) . '% lebih rendah',
                    'level' => $rataPetugas < $rataGlobal * 0.5 ? 'danger' : 'warning',
                    'jumlah_transaksi' => $jumlahTransaksi,
                    'rasio_penolakan' => round($rasioPenolakan, 1)
                ];
            }
        }
        
        return $anomali;
    }

    /**
     * API endpoint untuk data realtime
     */
    public function getRealtimeData()
    {
        $data = [
            'peminjaman_hari_ini' => Peminjaman::whereDate('created_at', today())->count(),
            'pengembalian_hari_ini' => Peminjaman::whereDate('tanggal_pengembalian', today())->count(),
            'kunjungan_hari_ini' => Kunjungan::whereDate('tanggal', today())->count(),
            'denda_pending' => Peminjaman::where('status_verifikasi', 'pending')
                ->where('denda_total', '>', 0)
                ->count(),
            'updated_at' => now()->format('H:i:s')
        ];
        
        return response()->json($data);
    }
}