<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\User;
use App\Models\Kunjungan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $tahunSekarang = now()->year;
        $bulanSekarang = now()->month;
        $tahunLalu = $tahunSekarang - 1;
        
        // ========== STATISTIK UTAMA ==========
        $totalBuku = Buku::count();
        $totalAnggota = User::whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])->count();
        
        // Peminjaman tahun ini
        $peminjamanTahunIni = Peminjaman::whereYear('created_at', $tahunSekarang);
        $totalPeminjaman = $peminjamanTahunIni->count();
        
        // Peminjaman tahun lalu untuk perbandingan
        $peminjamanTahunLalu = Peminjaman::whereYear('created_at', $tahunLalu)->count();
        $persenPeminjaman = $peminjamanTahunLalu > 0 
            ? round((($totalPeminjaman - $peminjamanTahunLalu) / $peminjamanTahunLalu) * 100, 1)
            : ($totalPeminjaman > 0 ? 100 : 0);
        
        // Denda yang sudah disetujui (sudah dibayar/diproses)
        $totalDenda = Peminjaman::whereYear('created_at', $tahunSekarang)
            ->where('status_verifikasi', 'disetujui')
            ->where('denda_total', '>', 0)
            ->sum('denda_total');
        
        // Denda tahun lalu
        $totalDendaTahunLalu = Peminjaman::whereYear('created_at', $tahunLalu)
            ->where('status_verifikasi', 'disetujui')
            ->where('denda_total', '>', 0)
            ->sum('denda_total');
        $persenDenda = $totalDendaTahunLalu > 0 
            ? round((($totalDenda - $totalDendaTahunLalu) / $totalDendaTahunLalu) * 100, 1)
            : ($totalDenda > 0 ? 100 : 0);
        
        // Denda pending (menunggu verifikasi)
        $dendaPending = Peminjaman::where('status_verifikasi', 'pending')
            ->where('denda_total', '>', 0)
            ->count();
        
        // Statistik ketepatan waktu
        $pengembalianTahunIni = Peminjaman::whereYear('created_at', $tahunSekarang)
            ->where('status_pinjam', 'dikembalikan');
        
        $tepatWaktu = (clone $pengembalianTahunIni)->where('denda', 0)->count();
        $terlambat = (clone $pengembalianTahunIni)->where('denda', '>', 0)->count();
        
        $totalPengembalian = $tepatWaktu + $terlambat;
        $persenTepatWaktu = $totalPengembalian > 0 ? round(($tepatWaktu / $totalPengembalian) * 100, 1) : 0;
        
        // ========== DATA UNTUK GRAFIK TREN 6 BULAN TERAKHIR ==========
        $trenPeminjaman = [];
        $trenKunjungan = [];
        $bulanLabels = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);
            $bulanLabels[] = $bulan->format('M');
            
            $trenPeminjaman[] = Peminjaman::whereYear('created_at', $bulan->year)
                ->whereMonth('created_at', $bulan->month)
                ->count();
            
            $trenKunjungan[] = Kunjungan::whereYear('tanggal', $bulan->year)
                ->whereMonth('tanggal', $bulan->month)
                ->count();
        }
        
        // ========== BUKU POPULER ==========
        $bukuPopuler = Buku::withCount(['peminjaman' => function ($query) use ($tahunSekarang) {
                $query->whereYear('created_at', $tahunSekarang);
            }])
            ->orderBy('peminjaman_count', 'desc')
            ->limit(5)
            ->get();
        
        // ========== ANGGOTA AKTIF ==========
        $anggotaAktif = User::whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])
            ->withCount(['peminjaman' => function ($query) use ($tahunSekarang) {
                $query->whereYear('created_at', $tahunSekarang);
            }])
            ->orderBy('peminjaman_count', 'desc')
            ->limit(5)
            ->get();
        
        // ========== KPI DENGAN TARGET ==========
        // Target berdasarkan standar perpustakaan sekolah
        $targetKunjunganPerHari = 50;
        $targetPeminjamanPerBulan = 300;
        $targetKetepatanWaktu = 95;
        $targetKeanggotaanAktif = 80;
        
        // Realisasi
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $hariDalamBulan = now()->daysInMonth;
        $hariTerlewat = now()->day;
        
        $kunjunganBulanIni = Kunjungan::whereBetween('tanggal', [$startOfMonth, $endOfMonth])->count();
        $realisasiKunjunganPerHari = $hariTerlewat > 0 ? round($kunjunganBulanIni / $hariTerlewat, 1) : 0;
        
        $peminjamanBulanIni = Peminjaman::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        $realisasiPeminjamanPerBulan = $peminjamanBulanIni;
        
        // Anggota aktif (melakukan peminjaman dalam 3 bulan terakhir)
        $anggotaAktifCount = User::whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])
            ->whereHas('peminjaman', function ($query) {
                $query->where('created_at', '>=', Carbon::now()->subMonths(3));
            })
            ->count();
        $realisasiKeanggotaanAktif = $totalAnggota > 0 ? round(($anggotaAktifCount / $totalAnggota) * 100, 1) : 0;
        
        // ========== PERINGATAN PENTING ==========
        $peringatan = [];
        
        // Cek penurunan peminjaman
        $peminjamanBulanLalu = Peminjaman::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        if ($peminjamanBulanLalu > 0 && $peminjamanBulanIni < $peminjamanBulanLalu) {
            $penurunan = round((($peminjamanBulanLalu - $peminjamanBulanIni) / $peminjamanBulanLalu) * 100, 1);
            $peringatan[] = [
                'type' => 'warning',
                'title' => 'Peminjaman Bulan Ini Turun',
                'message' => "Peminjaman bulan ini turun {$penurunan}% dibanding bulan lalu"
            ];
        }
        
        // Cek denda pending
        if ($dendaPending > 0) {
            $peringatan[] = [
                'type' => 'danger',
                'title' => 'Denda Pending Tinggi',
                'message' => "{$dendaPending} denda belum diverifikasi"
            ];
        }
        
        // Cek peningkatan anggota baru
        $anggotaBaruBulanIni = User::whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])
            ->whereMonth('created_at', $bulanSekarang)
            ->whereYear('created_at', $tahunSekarang)
            ->count();
        $anggotaBaruBulanLalu = User::whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        
        if ($anggotaBaruBulanLalu > 0 && $anggotaBaruBulanIni > $anggotaBaruBulanLalu) {
            $kenaikan = round((($anggotaBaruBulanIni - $anggotaBaruBulanLalu) / $anggotaBaruBulanLalu) * 100, 1);
            $peringatan[] = [
                'type' => 'success',
                'title' => 'Anggota Baru Meningkat',
                'message' => "{$kenaikan}% peningkatan anggota baru bulan ini"
            ];
        }
        
        // ========== DATA TREN 5 TAHUN ==========
        $tren5TahunPeminjaman = [];
        $tren5TahunKunjungan = [];
        $tahunLabels = [];
        
        for ($i = 4; $i >= 0; $i--) {
            $tahun = $tahunSekarang - $i;
            $tahunLabels[] = $tahun;
            
            $tren5TahunPeminjaman[] = Peminjaman::whereYear('created_at', $tahun)->count();
            $tren5TahunKunjungan[] = Kunjungan::whereYear('tanggal', $tahun)->count();
        }
        
        return view('pimpinan.dashboard', compact(
            'totalBuku',
            'totalAnggota',
            'totalPeminjaman',
            'persenPeminjaman',
            'totalDenda',
            'persenDenda',
            'dendaPending',
            'tepatWaktu',
            'terlambat',
            'persenTepatWaktu',
            'trenPeminjaman',
            'trenKunjungan',
            'bulanLabels',
            'bukuPopuler',
            'anggotaAktif',
            'targetKunjunganPerHari',
            'targetPeminjamanPerBulan',
            'targetKetepatanWaktu',
            'targetKeanggotaanAktif',
            'realisasiKunjunganPerHari',
            'realisasiPeminjamanPerBulan',
            'realisasiKeanggotaanAktif',
            'peringatan',
            'tren5TahunPeminjaman',
            'tren5TahunKunjungan',
            'tahunLabels'
        ));
    }
}