<?php

namespace App\Services;

use App\Models\Buku;
use App\Models\Kunjungan;
use App\Models\Peminjaman;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getDashboardData()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        return [
            // Statistik Utama
            'totalBuku' => Buku::count(),
            'totalDigital' => Buku::where('tipe', 'digital')->count(),
            'totalEksemplar' => Buku::sum('stok'),
            'totalAnggota' => User::whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])->count(),
            
            // Kunjungan Hari Ini
            'kunjunganHariIni' => Kunjungan::whereDate('tanggal', $today)->count(),
            'kunjunganAktif' => Kunjungan::whereDate('tanggal', $today)->where('status', 'aktif')->count(),
            
            // Statistik Peminjaman
            'peminjamanAktif' => Peminjaman::where('status_pinjam', 'dipinjam')->count(),
            'pengembalianHariIni' => Peminjaman::whereDate('tanggal_pengembalian', $today)
                ->where('status_pinjam', 'dikembalikan')->count(),
            'jatuhTempoHariIni' => Peminjaman::whereDate('tgl_jatuh_tempo', $today)
                ->where('status_pinjam', 'dipinjam')->count(),
            
            // Statistik Keterlambatan Bulan Ini
            'totalTerlambat' => Peminjaman::whereMonth('tanggal_pengembalian', Carbon::now()->month)
                ->whereYear('tanggal_pengembalian', Carbon::now()->year)
                ->where('status_pinjam', 'terlambat')
                ->count(),

            'totalDendaBulanIni' => Peminjaman::whereMonth('tanggal_pengembalian', Carbon::now()->month)
                ->whereYear('tanggal_pengembalian', Carbon::now()->year)
                ->sum('denda'),
        ];
    }

    public function getGrafikKunjungan($bulan = 6)
    {
        $data = [];
        $labels = [];
        
        for ($i = $bulan - 1; $i >= 0; $i--) {
            $bulan = Carbon::now()->subMonths($i);
            $labels[] = $bulan->format('M Y');
            
            $jumlah = Kunjungan::whereYear('tanggal', $bulan->year)
                ->whereMonth('tanggal', $bulan->month)
                ->count();
            
            $data[] = $jumlah;
        }
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    public function getBukuTerpopuler($limit = 5)
    {
        return Buku::orderBy('total_dipinjam', 'desc')
            ->limit($limit)
            ->get(['judul', 'total_dipinjam', 'pengarang']);
    }

    public function getPembacaTeraktif($limit = 5)
    {
        return User::whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])
            ->withCount(['peminjaman as total_pinjam' => function($query) {
                $query->whereMonth('created_at', Carbon::now()->month);
            }])
            ->orderBy('total_pinjam', 'desc')
            ->limit($limit)
            ->get(['id', 'name', 'role', 'total_tepat_waktu', 'total_terlambat']);
    }

    public function getStatistikKetepatanWaktu()
    {
        $totalTransaksi = Peminjaman::whereMonth('created_at', Carbon::now()->month)->count();
        
        if ($totalTransaksi == 0) {
            return [
                'tepat_waktu' => 0,
                'terlambat' => 0,
                'persentase_tepat' => 0,
                'persentase_terlambat' => 0
            ];
        }
        
        $tepatWaktu = Peminjaman::whereMonth('created_at', Carbon::now()->month)
            ->where('status', 'tepat_waktu')
            ->count();
        
        $terlambat = Peminjaman::whereMonth('created_at', Carbon::now()->month)
            ->where('status', 'terlambat')
            ->count();
        
        return [
            'tepat_waktu' => $tepatWaktu,
            'terlambat' => $terlambat,
            'persentase_tepat' => round(($tepatWaktu / $totalTransaksi) * 100, 1),
            'persentase_terlambat' => round(($terlambat / $totalTransaksi) * 100, 1)
        ];
    }

    public function getDataKeterlambatanHarian($hari = 7)
    {
        $data = [];
        $labels = [];
        
        for ($i = $hari - 1; $i >= 0; $i--) {
            $tanggal = Carbon::today()->subDays($i);
            $labels[] = $tanggal->format('d M');
            
            $jumlah = Peminjaman::whereDate('tanggal_pengembalian', $tanggal)
                ->where('status_pinjam', 'terlambat')
                ->count();
            
            $data[] = $jumlah;
        }
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
}