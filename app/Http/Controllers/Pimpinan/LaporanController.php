<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Kunjungan;
use App\Models\Buku;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    /**
     * Laporan Peminjaman
     */
    public function peminjaman(Request $request)
    {
        $periode = $request->periode ?? 'bulan_ini';
        $startDate = $this->getStartDate($periode, $request);
        $endDate = $this->getEndDate($periode, $request);
        
        $query = Peminjaman::with(['user', 'buku', 'petugas'])
            ->whereBetween('created_at', [$startDate, $endDate]);
        
        $totalPeminjaman = $query->count();
        
        $tepatWaktu = (clone $query)->where('status_pinjam', 'dikembalikan')
            ->where('denda', 0)
            ->count();
        
        $terlambat = (clone $query)->where('status_pinjam', 'terlambat')
            ->orWhere(function ($q) {
                $q->where('status_pinjam', 'dikembalikan')->where('denda', '>', 0);
            })->count();
        
        $hariDalamPeriode = max($startDate->diffInDays($endDate) + 1, 1);
        $rataPerHari = round($totalPeminjaman / $hariDalamPeriode, 1);
        
        $peminjaman = $query->orderBy('created_at', 'desc')->limit(10)->get();
        
        $labelsHarian = [];
        $dataHarian = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = now()->subDays($i);
            $labelsHarian[] = $tanggal->format('d M');
            $dataHarian[] = Peminjaman::whereDate('created_at', $tanggal)->count();
        }
        
        $kategoriData = DB::table('peminjaman')
            ->join('buku', 'peminjaman.buku_id', '=', 'buku.id')
            ->join('kategori_buku', 'buku.kategori_id', '=', 'kategori_buku.id')
            ->whereBetween('peminjaman.created_at', [$startDate, $endDate])
            ->select('kategori_buku.nama', DB::raw('COUNT(*) as total'))
            ->groupBy('kategori_buku.nama')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
        
        $kategoriLabels = $kategoriData->pluck('nama')->toArray();
        $kategoriValues = $kategoriData->pluck('total')->toArray();
        
        return view('pimpinan.pages.laporan.peminjaman', compact(
            'totalPeminjaman',
            'tepatWaktu',
            'terlambat',
            'rataPerHari',
            'peminjaman',
            'labelsHarian',
            'dataHarian',
            'kategoriLabels',
            'kategoriValues',
            'startDate',
            'endDate',
            'periode'
        ));
    }

    /**
     * Laporan Kunjungan
     */
    public function kunjungan(Request $request)
    {
        $tahun = $request->tahun ?? now()->year;
        
        $totalKunjungan = Kunjungan::whereYear('tanggal', $tahun)->count();
        $kunjunganSiswa = Kunjungan::whereYear('tanggal', $tahun)
            ->where('jenis', 'siswa')
            ->count();
        $kunjunganGuru = Kunjungan::whereYear('tanggal', $tahun)
            ->whereIn('jenis', ['guru', 'pegawai', 'umum'])
            ->count();
        
        $hariDalamTahun = Carbon::create($tahun, 12, 31)->dayOfYear;
        $rataPerHari = $totalKunjungan > 0 ? round($totalKunjungan / $hariDalamTahun, 1) : 0;
        
        $labelsHarian = [];
        $dataHarian = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = now()->subDays($i);
            $labelsHarian[] = $tanggal->format('d M');
            $dataHarian[] = Kunjungan::whereDate('tanggal', $tanggal)->count();
        }
        
        $labelsJam = [];
        $dataJam = [];
        
        for ($jam = 7; $jam <= 16; $jam++) {
            $labelsJam[] = $jam . ':00';
            $dataJam[] = Kunjungan::whereYear('tanggal', $tahun)
                ->whereTime('jam_masuk', '>=', sprintf('%02d:00:00', $jam))
                ->whereTime('jam_masuk', '<', sprintf('%02d:00:00', $jam + 1))
                ->count();
        }
        
        $kunjunganHarian = Kunjungan::select(
                DB::raw('DATE(tanggal) as tanggal'),
                DB::raw('SUM(CASE WHEN jenis = "siswa" THEN 1 ELSE 0 END) as siswa'),
                DB::raw('SUM(CASE WHEN jenis = "guru" THEN 1 ELSE 0 END) as guru'),
                DB::raw('SUM(CASE WHEN jenis = "pegawai" THEN 1 ELSE 0 END) as pegawai'),
                DB::raw('SUM(CASE WHEN jenis = "umum" THEN 1 ELSE 0 END) as umum'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('tanggal', $tahun)
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'desc')
            ->limit(10)
            ->get();
        
        $heatmapData = $this->getHeatmapData($tahun);
        
        return view('pimpinan.pages.laporan.kunjungan', compact(
            'totalKunjungan',
            'kunjunganSiswa',
            'kunjunganGuru',
            'rataPerHari',
            'labelsHarian',
            'dataHarian',
            'labelsJam',
            'dataJam',
            'kunjunganHarian',
            'heatmapData',
            'tahun'
        ));
    }

    /**
     * Laporan Keuangan (Denda) - FIXED
     */
    public function keuangan(Request $request)
    {
        $tahun = $request->tahun ?? now()->year;
        $bulanLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        
        // Statistik keuangan
        $totalDendaTahun = Peminjaman::whereYear('created_at', $tahun)
            ->where('status_verifikasi', 'disetujui')
            ->where('denda_total', '>', 0)
            ->sum('denda_total');
        
        $dendaBulanIni = Peminjaman::whereMonth('created_at', now()->month)
            ->whereYear('created_at', $tahun)
            ->where('status_verifikasi', 'disetujui')
            ->where('denda_total', '>', 0)
            ->sum('denda_total');
        
        $transaksiDenda = Peminjaman::whereYear('created_at', $tahun)
            ->where('status_verifikasi', 'disetujui')
            ->where('denda_total', '>', 0)
            ->count();
        
        $rataDenda = $transaksiDenda > 0 ? round($totalDendaTahun / $transaksiDenda, 0) : 0;
        
        $dendaPendingTotal = Peminjaman::where('status_verifikasi', 'pending')
            ->where('denda_total', '>', 0)
            ->sum('denda_total');
        
        // Data untuk tabel bulanan
        $dendaBulanan = [];
        $totalDendaTerlambat = 0;
        $totalDendaRusak = 0;
        
        for ($i = 1; $i <= 12; $i++) {
            $dendaTerlambat = Peminjaman::whereMonth('created_at', $i)
                ->whereYear('created_at', $tahun)
                ->where('status_verifikasi', 'disetujui')
                ->sum('denda');
            
            $dendaRusakSum = Peminjaman::whereMonth('created_at', $i)
                ->whereYear('created_at', $tahun)
                ->where('status_verifikasi', 'disetujui')
                ->sum('denda_rusak');
            
            $total = $dendaTerlambat + $dendaRusakSum;
            
            $totalDendaTerlambat += $dendaTerlambat;
            $totalDendaRusak += $dendaRusakSum;
            
            $transaksi = Peminjaman::whereMonth('created_at', $i)
                ->whereYear('created_at', $tahun)
                ->where('denda_total', '>', 0)
                ->count();
            
            $verifikasi = $transaksi > 0 ? round(Peminjaman::whereMonth('created_at', $i)
                ->whereYear('created_at', $tahun)
                ->where('status_verifikasi', 'disetujui')
                ->where('denda_total', '>', 0)
                ->count() / $transaksi * 100, 1) : 0;
            
            $dendaBulanan[] = (object)[
                'bulan' => $bulanLabels[$i-1],
                'transaksi' => $transaksi,
                'denda_terlambat' => $dendaTerlambat,
                'denda_rusak' => $dendaRusakSum,
                'total' => $total,
                'verifikasi' => $verifikasi
            ];
        }
        
        // Data verifikasi
        $totalDisetujui = Peminjaman::whereYear('created_at', $tahun)
            ->where('status_verifikasi', 'disetujui')
            ->where('denda_total', '>', 0)
            ->sum('denda_total');
        
        $totalPending = $dendaPendingTotal;
        
        $totalDitolak = Peminjaman::whereYear('created_at', $tahun)
            ->where('status_verifikasi', 'ditolak')
            ->where('denda_total', '>', 0)
            ->sum('denda_total');
        
        $totalSemua = $totalDisetujui + $totalPending + $totalDitolak;
        
        $persenDisetujui = $totalSemua > 0 ? round(($totalDisetujui / $totalSemua) * 100, 1) : 0;
        $persenPending = $totalSemua > 0 ? round(($totalPending / $totalSemua) * 100, 1) : 0;
        $persenDitolak = $totalSemua > 0 ? round(($totalDitolak / $totalSemua) * 100, 1) : 0;
        
        // Komposisi denda
        $persenTerlambat = $totalDendaTahun > 0 ? round(($totalDendaTerlambat / $totalDendaTahun) * 100, 1) : 0;
        $persenRusak = $totalDendaTahun > 0 ? round(($totalDendaRusak / $totalDendaTahun) * 100, 1) : 0;
        
        return view('pimpinan.pages.laporan.keuangan', compact(
            'totalDendaTahun',
            'dendaBulanIni',
            'rataDenda',
            'dendaPendingTotal',
            'dendaBulanan',
            'totalDisetujui',
            'totalPending',
            'totalDitolak',
            'persenDisetujui',
            'persenPending',
            'persenDitolak',
            'persenTerlambat',
            'persenRusak',
            'totalDendaTerlambat',
            'totalDendaRusak',
            'tahun'
        ));
    }

    /**
     * Helper untuk mendapatkan start date
     */
    private function getStartDate($periode, $request)
    {
        switch ($periode) {
            case 'hari_ini':
                return Carbon::today();
            case 'minggu_ini':
                return Carbon::now()->startOfWeek();
            case 'bulan_ini':
                return Carbon::now()->startOfMonth();
            case 'tahun_ini':
                return Carbon::now()->startOfYear();
            case 'custom':
                return $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
            default:
                return Carbon::now()->startOfMonth();
        }
    }

    /**
     * Helper untuk mendapatkan end date
     */
    private function getEndDate($periode, $request)
    {
        switch ($periode) {
            case 'hari_ini':
                return Carbon::today()->endOfDay();
            case 'minggu_ini':
                return Carbon::now()->endOfWeek();
            case 'bulan_ini':
                return Carbon::now()->endOfMonth();
            case 'tahun_ini':
                return Carbon::now()->endOfYear();
            case 'custom':
                return $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now();
            default:
                return Carbon::now();
        }
    }
    
    /**
     * Get heatmap data
     */
    private function getHeatmapData($tahun)
    {
        $hariMapping = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        $heatmap = [];
        
        for ($hari = 0; $hari < 7; $hari++) {
            for ($jam = 7; $jam <= 16; $jam++) {
                $heatmap[$hari][$jam] = Kunjungan::whereYear('tanggal', $tahun)
                    ->whereRaw('DAYOFWEEK(tanggal) = ?', [$hari + 2])
                    ->whereTime('jam_masuk', '>=', sprintf('%02d:00:00', $jam))
                    ->whereTime('jam_masuk', '<', sprintf('%02d:00:00', $jam + 1))
                    ->count();
            }
        }
        
        return $heatmap;
    }
}