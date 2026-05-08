<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\User;
use App\Models\Kunjungan;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ========== STATISTIK UTAMA ==========
        $totalBuku = Buku::count();
        $totalDigital = Buku::where('tipe', 'digital')->count();
        
        $totalAnggota = User::whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])->where('status', 'active')->count();
        
        $peminjamanAktif = Peminjaman::whereIn('status_pinjam', ['dipinjam', 'terlambat'])->count();
        
        $kunjunganHariIni = Kunjungan::whereDate('tanggal', today())->count();
        $pengembalianHariIni = Peminjaman::whereDate('tanggal_pengembalian', today())
            ->where('status_pinjam', 'dikembalikan')
            ->count();
        
        $jatuhTempoHariIni = Peminjaman::whereDate('tgl_jatuh_tempo', today())
            ->whereIn('status_pinjam', ['dipinjam', 'terlambat'])
            ->count();
        
        $totalDendaBulanIni = Peminjaman::whereMonth('tanggal_pengembalian', now()->month)
            ->whereYear('tanggal_pengembalian', now()->year)
            ->sum('denda');
        
        $totalTerlambat = Peminjaman::where('status_pinjam', 'terlambat')
            ->whereMonth('tgl_jatuh_tempo', now()->month)
            ->count();

        // ========== GRAFIK PEMINJAMAN HARI INI (PER JAM) ==========
        $jamLabels = ['08', '09', '10', '11', '12', '13', '14', '15'];
        $peminjamanPerJam = [];
        
        foreach ($jamLabels as $jam) {
            $startTime = date('Y-m-d ' . $jam . ':00:00');
            $endTime = date('Y-m-d ' . $jam . ':59:59');
            $count = Peminjaman::whereDate('created_at', today())
                ->whereBetween('created_at', [$startTime, $endTime])
                ->count();
            $peminjamanPerJam[] = $count;
        }
        
        // ========== GRAFIK 7 HARI TERAKHIR (Peminjaman & Kunjungan) ==========
        $grafikPeminjaman = [
            'labels' => [],
            'data' => []
        ];
        $grafikKunjungan = [
            'labels' => [],
            'data' => []
        ];

        for ($i = 6; $i >= 0; $i--) {
            $tanggal = now()->subDays($i);
            $grafikPeminjaman['labels'][] = $tanggal->format('d M');
            $grafikPeminjaman['data'][] = Peminjaman::whereDate('created_at', $tanggal)->count();
            $grafikKunjungan['labels'][] = $tanggal->format('d M');
            $grafikKunjungan['data'][] = Kunjungan::whereDate('tanggal', $tanggal)->count();
        }

        // ========== BUKU TERPOPULER (KONEK DATABASE) ==========
        $bukuTerpopuler = Buku::withCount(['peminjaman' => function($query) {
                $query->whereYear('created_at', now()->year);
            }])
            ->orderBy('peminjaman_count', 'desc')
            ->limit(5)
            ->get();

        // ========== PEMBACA TERAKTIF (KONEK DATABASE) ==========
        $pembacaTeraktif = User::whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])
            ->where('status', 'active')
            ->withCount(['peminjaman' => function($query) {
                $query->whereMonth('created_at', now()->month);
            }])
            ->orderBy('peminjaman_count', 'desc')
            ->limit(5)
            ->get();

        // ========== AKTIVITAS TERKINI ==========
        $aktivitas = Peminjaman::with(['user', 'buku'])
            ->latest()
            ->limit(10)
            ->get();

        return view('petugas.dashboard', compact(
            'totalBuku',
            'totalDigital',
            'totalAnggota',
            'peminjamanAktif',
            'kunjunganHariIni',
            'pengembalianHariIni',
            'jatuhTempoHariIni',
            'totalDendaBulanIni',
            'totalTerlambat',
            'peminjamanPerJam',
            'jamLabels',
            'grafikPeminjaman',
            'grafikKunjungan',
            'bukuTerpopuler',
            'pembacaTeraktif',
            'aktivitas'
        ));
    }

    public function getRealtimeData()
    {
        return response()->json([
            'kunjungan_hari_ini' => Kunjungan::whereDate('tanggal', today())->count(),
            'peminjaman_aktif' => Peminjaman::whereIn('status_pinjam', ['dipinjam', 'terlambat'])->count(),
            'jatuh_tempo' => Peminjaman::whereDate('tgl_jatuh_tempo', today())
                ->whereIn('status_pinjam', ['dipinjam', 'terlambat'])
                ->count(),
            'pengembalian_hari_ini' => Peminjaman::whereDate('tanggal_pengembalian', today())->count(),
        ]);
    }
}