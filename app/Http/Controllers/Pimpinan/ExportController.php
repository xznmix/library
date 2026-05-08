<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Peminjaman;
use App\Models\Kunjungan;
use App\Models\Denda;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PeminjamanExport;
use App\Exports\KunjunganExport;
use App\Exports\KeuanganExport;
use App\Exports\KinerjaExport;
use Carbon\Carbon;

class ExportController extends Controller
{
    public function index()
    {
        return view('pimpinan.pages.export.index');
    }

    public function download($jenis, $format)
    {
        try {
            switch ($jenis) {
                case 'peminjaman':
                    return $this->exportPeminjaman($format);
                case 'kunjungan':
                    return $this->exportKunjungan($format);
                case 'keuangan':
                    return $this->exportKeuangan($format);
                case 'kinerja':
                    return $this->exportKinerja($format);
                default:
                    return redirect()->route('pimpinan.export.index')->with('error', 'Jenis laporan tidak ditemukan');
            }
        } catch (\Exception $e) {
            return redirect()->route('pimpinan.export.index')->with('error', 'Gagal mengexport data: ' . $e->getMessage());
        }
    }

    private function exportPeminjaman($format)
    {
        $data = Peminjaman::with(['user', 'buku', 'petugas'])
            ->orderBy('created_at', 'desc')
            ->get();

        $title = 'Laporan Peminjaman Buku';
        $periode = 'Periode: ' . Carbon::now()->format('F Y');
        $total = $data->count();
        $totalDenda = $data->sum('denda_total');

        if ($format == 'pdf') {
            $pdf = Pdf::loadView('pimpinan.pages.export.peminjaman-pdf', compact('data', 'title', 'periode', 'total', 'totalDenda'));
            return $pdf->download('laporan_peminjaman_' . date('Ymd_His') . '.pdf');
        } else {
            return Excel::download(new PeminjamanExport($data), 'laporan_peminjaman_' . date('Ymd_His') . '.xlsx');
        }
    }

    private function exportKunjungan($format)
    {
        $data = Kunjungan::with(['user'])
            ->orderBy('tanggal', 'desc')
            ->get();

        $title = 'Laporan Kunjungan Perpustakaan';
        $periode = 'Periode: ' . Carbon::now()->format('F Y');
        $total = $data->count();
        
        $statistik = [
            'siswa' => $data->where('jenis', 'siswa')->count(),
            'guru' => $data->where('jenis', 'guru')->count(),
            'pegawai' => $data->where('jenis', 'pegawai')->count(),
            'umum' => $data->where('jenis', 'umum')->count(),
        ];

        if ($format == 'pdf') {
            $pdf = Pdf::loadView('pimpinan.pages.export.kunjungan-pdf', compact('data', 'title', 'periode', 'total', 'statistik'));
            return $pdf->download('laporan_kunjungan_' . date('Ymd_His') . '.pdf');
        } else {
            return Excel::download(new KunjunganExport($data), 'laporan_kunjungan_' . date('Ymd_His') . '.xlsx');
        }
    }

    private function exportKeuangan($format)
    {
        $data = Peminjaman::with(['user', 'buku'])
            ->where('status_verifikasi', 'disetujui')
            ->where('denda_total', '>', 0)
            ->orderBy('created_at', 'desc')
            ->get();

        $title = 'Laporan Keuangan Denda';
        $periode = 'Periode: ' . Carbon::now()->format('F Y');
        
        $totalDenda = $data->sum('denda_total');
        $totalTerbayar = $data->sum('denda_total'); // Karena sudah disetujui
        $totalBelumBayar = 0;
        $persentaseTerbayar = $totalDenda > 0 ? 100 : 0;

        if ($format == 'pdf') {
            $pdf = Pdf::loadView('pimpinan.pages.export.keuangan-pdf', compact('data', 'title', 'periode', 'totalDenda', 'totalTerbayar', 'totalBelumBayar', 'persentaseTerbayar'));
            return $pdf->download('laporan_keuangan_' . date('Ymd_His') . '.pdf');
        } else {
            return Excel::download(new KeuanganExport($data), 'laporan_keuangan_' . date('Ymd_His') . '.xlsx');
        }
    }

    private function exportKinerja($format)
    {
        $tahunIni = date('Y');
        $bulanIni = date('m');
        
        $petugasStats = User::where('role', 'petugas')
            ->withCount(['peminjaman as total_peminjaman' => function($q) use ($tahunIni) {
                $q->whereYear('created_at', $tahunIni);
            }])
            ->withCount(['peminjaman as peminjaman_bulan_ini' => function($q) use ($tahunIni, $bulanIni) {
                $q->whereYear('created_at', $tahunIni)
                  ->whereMonth('created_at', $bulanIni);
            }])
            ->get();

        $totalAnggota = User::whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])->count();
        $totalPeminjaman = Peminjaman::whereYear('created_at', $tahunIni)->count();
        $totalKunjungan = Kunjungan::whereYear('tanggal', $tahunIni)->count();
        $totalDendaTerbayar = Peminjaman::whereYear('created_at', $tahunIni)
            ->where('status_verifikasi', 'disetujui')
            ->sum('denda_total');
        
        $peminjamanPerBulan = [];
        for ($i = 1; $i <= 12; $i++) {
            $peminjamanPerBulan[$i] = Peminjaman::whereYear('created_at', $tahunIni)
                ->whereMonth('created_at', $i)
                ->count();
        }
        
        $kunjunganPerBulan = [];
        for ($i = 1; $i <= 12; $i++) {
            $kunjunganPerBulan[$i] = Kunjungan::whereYear('tanggal', $tahunIni)
                ->whereMonth('tanggal', $i)
                ->count();
        }

        $title = 'Laporan Kinerja Perpustakaan';
        $periode = 'Tahun: ' . $tahunIni;

        if ($format == 'pdf') {
            $pdf = Pdf::loadView('pimpinan.pages.export.kinerja-pdf', compact(
                'petugasStats', 'totalAnggota', 'totalPeminjaman', 
                'totalKunjungan', 'totalDendaTerbayar', 'peminjamanPerBulan',
                'kunjunganPerBulan', 'title', 'periode', 'tahunIni'
            ));
            return $pdf->download('laporan_kinerja_' . date('Ymd_His') . '.pdf');
        } else {
            return Excel::download(new KinerjaExport($petugasStats, $totalAnggota, $totalPeminjaman, $totalKunjungan, $totalDendaTerbayar, $peminjamanPerBulan, $kunjunganPerBulan), 'laporan_kinerja_' . date('Ymd_His') . '.xlsx');
        }
    }
}