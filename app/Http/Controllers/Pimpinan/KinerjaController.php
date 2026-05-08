<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Peminjaman;
use App\Models\Kunjungan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class KinerjaController extends Controller
{
    public function index()
    {
        $tahunSekarang = now()->year;
        
        // ========== DATA KPI UNTUK GRAFIK 6 BULAN TERAKHIR ==========
        $kpiKunjungan = [];
        $kpiPeminjaman = [];
        $kpiKetepatan = [];
        $bulanLabels = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);
            $bulanLabels[] = $bulan->format('M');
            
            // Kunjungan per bulan
            $kpiKunjungan[] = Kunjungan::whereYear('tanggal', $bulan->year)
                ->whereMonth('tanggal', $bulan->month)
                ->count();
            
            // Peminjaman per bulan
            $peminjamanBulan = Peminjaman::whereYear('created_at', $bulan->year)
                ->whereMonth('created_at', $bulan->month);
            $kpiPeminjaman[] = $peminjamanBulan->count();
            
            // Ketepatan waktu (persentase)
            $totalKembali = (clone $peminjamanBulan)->where('status_pinjam', 'dikembalikan')->count();
            $tepat = (clone $peminjamanBulan)->where('status_pinjam', 'dikembalikan')->where('denda', 0)->count();
            $kpiKetepatan[] = $totalKembali > 0 ? round(($tepat / $totalKembali) * 100, 1) : 0;
        }
        
        // ========== DATA KINERJA PETUGAS ==========
        $petugas = User::where('role', 'petugas')
            ->withCount([
                'peminjaman as total_transaksi' => function ($query) use ($tahunSekarang) {
                    $query->whereYear('created_at', $tahunSekarang);
                }
            ])
            ->get()
            ->map(function ($petugas) use ($tahunSekarang) {
                // Total denda yang diproses petugas ini
                $dendaDiproses = Peminjaman::where('petugas_id', $petugas->id)
                    ->whereYear('created_at', $tahunSekarang)
                    ->where('status_verifikasi', 'disetujui')
                    ->where('denda_total', '>', 0)
                    ->sum('denda_total');
                
                $petugas->total_denda = $dendaDiproses;
                
                // Persentase verifikasi denda
                $totalDendaCase = Peminjaman::where('petugas_id', $petugas->id)
                    ->whereYear('created_at', $tahunSekarang)
                    ->where('denda_total', '>', 0)
                    ->count();
                
                $verifikasiDisetujui = Peminjaman::where('petugas_id', $petugas->id)
                    ->whereYear('created_at', $tahunSekarang)
                    ->where('status_verifikasi', 'disetujui')
                    ->where('denda_total', '>', 0)
                    ->count();
                
                $petugas->persen_verifikasi = $totalDendaCase > 0 
                    ? round(($verifikasiDisetujui / $totalDendaCase) * 100, 1) 
                    : 100;
                
                // Hitung skor kinerja (0-100)
                $skorVerifikasi = $petugas->persen_verifikasi;
                $targetTransaksi = 200;
                $skorTransaksi = min(($petugas->total_transaksi / $targetTransaksi) * 100, 100);
                
                $petugas->skor = round(($skorVerifikasi * 0.4) + ($skorTransaksi * 0.6), 1);
                
                // Rating bintang
                if ($petugas->skor >= 90) {
                    $petugas->rating = 5;
                    $petugas->rating_star = '⭐⭐⭐⭐⭐';
                } elseif ($petugas->skor >= 75) {
                    $petugas->rating = 4;
                    $petugas->rating_star = '⭐⭐⭐⭐';
                } elseif ($petugas->skor >= 60) {
                    $petugas->rating = 3;
                    $petugas->rating_star = '⭐⭐⭐';
                } elseif ($petugas->skor >= 40) {
                    $petugas->rating = 2;
                    $petugas->rating_star = '⭐⭐';
                } else {
                    $petugas->rating = 1;
                    $petugas->rating_star = '⭐';
                }
                
                return $petugas;
            })
            ->sortByDesc('skor')
            ->values();
        
        // ========== TARGET DAN REALISASI KPI ==========
        $targetKunjunganPerHari = 50;
        $targetPeminjamanPerBulan = 300;
        $targetKetepatanWaktu = 95;
        
        // Realisasi terbaru (bulan ini)
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $hariTerlewat = now()->day;
        
        $kunjunganBulanIni = Kunjungan::whereBetween('tanggal', [$startOfMonth, $endOfMonth])->count();
        $realisasiKunjunganPerHari = $hariTerlewat > 0 ? round($kunjunganBulanIni / $hariTerlewat, 1) : 0;
        
        $realisasiPeminjamanPerBulan = Peminjaman::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        
        $pengembalianBulanIni = Peminjaman::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->where('status_pinjam', 'dikembalikan');
        $totalKembali = $pengembalianBulanIni->count();
        $tepat = (clone $pengembalianBulanIni)->where('denda', 0)->count();
        $realisasiKetepatan = $totalKembali > 0 ? round(($tepat / $totalKembali) * 100, 1) : 0;
        
        // ========== REKOMENDASI ==========
        $rekomendasi = [];
        
        if ($realisasiKetepatan < $targetKetepatanWaktu) {
            $rekomendasi[] = [
                'icon' => 'clock',
                'title' => 'Tingkatkan Ketepatan Waktu',
                'message' => 'Implementasikan reminder otomatis H-1 jatuh tempo melalui WhatsApp/SMS'
            ];
        }
        
        $petugasRendah = $petugas->filter(function ($p) {
            return $p->persen_verifikasi < 85;
        });
        
        if ($petugasRendah->count() > 0) {
            $rekomendasi[] = [
                'icon' => 'training',
                'title' => 'Pelatihan Petugas',
                'message' => "Adakan pelatihan untuk {$petugasRendah->count()} petugas dengan skor verifikasi di bawah 85%"
            ];
        }
        
        $rekomendasi[] = [
            'icon' => 'optimize',
            'title' => 'Optimalkan Jam Operasional',
            'message' => 'Berdasarkan data kunjungan, pertimbangkan penambahan petugas di jam sibuk (09:00-11:00 dan 13:00-15:00)'
        ];
        
        return view('pimpinan.pages.kinerja.index', compact(
            'kpiKunjungan',
            'kpiPeminjaman',
            'kpiKetepatan',
            'bulanLabels',
            'petugas',
            'targetKunjunganPerHari',
            'targetPeminjamanPerBulan',
            'targetKetepatanWaktu',
            'realisasiKunjunganPerHari',
            'realisasiPeminjamanPerBulan',  // <-- SEKARANG SUDAH ADA
            'realisasiKetepatan',
            'rekomendasi'
        ));
    }
}