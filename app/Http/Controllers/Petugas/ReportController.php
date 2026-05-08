<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\User;
use App\Models\Buku;
use App\Models\Kunjungan;
use App\Models\KategoriBuku;
use App\Models\Denda;  // ← TAMBAHKAN
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PeminjamanExport;
use App\Exports\AnggotaExport;
use App\Exports\BukuExport;
use App\Exports\KunjunganExport;
use App\Exports\DendaExport;
use App\Exports\LaporanGabunganExport;

class ReportController extends Controller
{
    /**
     * Halaman utama report
     */
    public function index()
    {
        $totalPinjam = Peminjaman::count();
        $totalAnggota = User::whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])->count();
        $totalBuku = Buku::count();
        $totalKunjungan = Kunjungan::count();
        
        // ✅ PERBAIKAN: Gunakan denda_total (bukan denda)
        $totalDenda = Peminjaman::sum('denda_total');
        
        return view('petugas.pages.report.index', compact(
            'totalPinjam', 'totalAnggota', 'totalBuku', 'totalKunjungan', 'totalDenda'
        ));
    }

    /**
     * Laporan Peminjaman
     */
    public function peminjaman(Request $request)
    {
        $query = Peminjaman::with(['user', 'buku']);
        
        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_pinjam', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_pinjam', '<=', $request->end_date);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status_pinjam', $request->status);
        }
        
        $peminjaman = $query->latest()->get();
        
        return view('petugas.pages.report.peminjaman', compact('peminjaman'));
    }

    /**
     * Laporan Anggota - HANYA YANG AKTIF
     */
    public function anggota(Request $request)
    {
        $query = User::whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])
            ->where('status_anggota', 'active')  // ✅ HANYA AKTIF
            ->withCount('peminjaman');
        
        // Filter by jenis
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        
        $anggota = $query->orderBy('name', 'asc')->get();
        
        // Hapus filter status dari request karena sudah fixed
        $status = 'active';  // Tetap active
        $jenis = $request->get('jenis', '');
        
        return view('petugas.pages.report.anggota', compact('anggota', 'status', 'jenis'));
    }

    /**
     * Laporan Buku
     */
    public function buku(Request $request)
    {
        $query = Buku::with('kategori')
            ->withCount('peminjaman');
        
        // Filter by kategori
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }
        
        // Filter by tipe
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }
        
        $buku = $query->orderBy('peminjaman_count', 'desc')->get();
        $kategoriList = KategoriBuku::all();
        
        return view('petugas.pages.report.buku', compact('buku', 'kategoriList'));
    }

    /**
     * Laporan Kunjungan
     */
    public function kunjungan(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        
        $kunjungan = Kunjungan::select(
                DB::raw('MONTH(tanggal) as bulan'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('tanggal', $year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->map(function ($item) {
                $bulan = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ];
                $item->nama_bulan = $bulan[$item->bulan] ?? $item->bulan;
                return $item;
            });
        
        return view('petugas.pages.report.kunjungan', compact('kunjungan', 'year'));
    }

    /**
     * Laporan Denda - DIPERBAIKI menggunakan denda_total dan Denda model
     */
    public function denda(Request $request)
    {
        // ✅ PERBAIKAN: Gunakan model Denda untuk laporan denda
        $query = Denda::with(['peminjaman.buku', 'anggota'])
            ->where('payment_status', '!=', 'pending')
            ->orderBy('paid_at', 'desc');
        
        // Filter by date range (tanggal bayar)
        if ($request->filled('start_date')) {
            $query->whereDate('paid_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('paid_at', '<=', $request->end_date);
        }
        
        $denda = $query->get();
        
        return view('petugas.pages.report.denda', compact('denda'));
    }

    /**
     * Export Peminjaman ke PDF
     */
    public function exportPeminjamanPdf(Request $request)
    {
        $query = Peminjaman::with(['user', 'buku']);
        
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_pinjam', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_pinjam', '<=', $request->end_date);
        }
        
        if ($request->filled('status')) {
            $query->where('status_pinjam', $request->status);
        }
        
        $peminjaman = $query->latest()->get();
        
        $data = [
            'peminjaman' => $peminjaman,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'tanggal_cetak' => Carbon::now()->format('d/m/Y H:i:s')
        ];
        
        $pdf = Pdf::loadView('petugas.exports.pdf.peminjaman', $data);
        return $pdf->download('laporan-peminjaman-'.Carbon::now()->format('Y-m-d').'.pdf');
    }

    /**
     * Export Peminjaman ke Excel
     */
    public function exportPeminjamanExcel(Request $request)
    {
        return Excel::download(new PeminjamanExport($request), 'laporan-peminjaman-'.Carbon::now()->format('Y-m-d').'.xlsx');
    }

    /**
     * Export Anggota ke PDF
     */
    public function exportAnggotaPdf(Request $request)
    {
        $query = User::whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])
            ->withCount('peminjaman');
        
        if ($request->filled('status')) {
            $query->where('status_anggota', $request->status);
        }
        
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        
        $anggota = $query->get();
        
        $data = [
            'anggota' => $anggota,
            'status' => $request->status,
            'jenis' => $request->jenis,
            'tanggal_cetak' => Carbon::now()->format('d/m/Y H:i:s')
        ];
        
        $pdf = Pdf::loadView('petugas.exports.pdf.anggota', $data);
        return $pdf->download('laporan-anggota-'.Carbon::now()->format('Y-m-d').'.pdf');
    }

    /**
     * Export Anggota ke Excel
     */
    public function exportAnggotaExcel(Request $request)
    {
        return Excel::download(new AnggotaExport($request), 'laporan-anggota-'.Carbon::now()->format('Y-m-d').'.xlsx');
    }

    /**
     * Export Buku ke PDF
     */
    public function exportBukuPdf(Request $request)
    {
        $query = Buku::with('kategori')
            ->withCount('peminjaman');
        
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }
        
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }
        
        $buku = $query->orderBy('peminjaman_count', 'desc')->get();
        
        $data = [
            'buku' => $buku,
            'kategori' => $request->kategori,
            'tipe' => $request->tipe,
            'tanggal_cetak' => Carbon::now()->format('d/m/Y H:i:s')
        ];
        
        $pdf = Pdf::loadView('petugas.exports.pdf.buku', $data);
        return $pdf->download('laporan-buku-'.Carbon::now()->format('Y-m-d').'.pdf');
    }

    /**
     * Export Buku ke Excel
     */
    public function exportBukuExcel(Request $request)
    {
        return Excel::download(new BukuExport($request), 'laporan-buku-'.Carbon::now()->format('Y-m-d').'.xlsx');
    }

    /**
     * Export Kunjungan ke PDF
     */
    public function exportKunjunganPdf(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        
        $kunjungan = Kunjungan::select(
                DB::raw('MONTH(tanggal) as bulan'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('tanggal', $year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->map(function ($item) {
                $bulan = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ];
                $item->nama_bulan = $bulan[$item->bulan] ?? $item->bulan;
                return $item;
            });
        
        $data = [
            'kunjungan' => $kunjungan,
            'year' => $year,
            'tanggal_cetak' => Carbon::now()->format('d/m/Y H:i:s')
        ];
        
        $pdf = Pdf::loadView('petugas.exports.pdf.kunjungan', $data);
        return $pdf->download('laporan-kunjungan-'.$year.'.pdf');
    }

    /**
     * Export Kunjungan ke Excel
     */
    public function exportKunjunganExcel(Request $request)
    {
        return Excel::download(new KunjunganExport($request), 'laporan-kunjungan-'.Carbon::now()->format('Y-m-d').'.xlsx');
    }

    /**
     * Export Denda ke PDF - DIPERBAIKI
     */
    public function exportDendaPdf(Request $request)
    {
        // ✅ PERBAIKAN: Gunakan model Denda
        $query = Denda::with(['peminjaman.buku', 'anggota']);
        
        if ($request->filled('start_date')) {
            $query->whereDate('paid_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('paid_at', '<=', $request->end_date);
        }
        
        $denda = $query->orderBy('paid_at', 'desc')->get();
        
        $data = [
            'denda' => $denda,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'tanggal_cetak' => Carbon::now()->format('d/m/Y H:i:s')
        ];
        
        $pdf = Pdf::loadView('petugas.exports.pdf.denda', $data);
        return $pdf->download('laporan-denda-'.Carbon::now()->format('Y-m-d').'.pdf');
    }

    /**
     * Export Denda ke Excel - DIPERBAIKI
     */
    public function exportDendaExcel(Request $request)
    {
        return Excel::download(new DendaExport($request), 'laporan-denda-'.Carbon::now()->format('Y-m-d').'.xlsx');
    }

    /**
     * Export Semua Laporan (Gabungan) ke PDF - DIPERBAIKI
     */
    public function exportAllPdf()
    {
        // ✅ PERBAIKAN: Gunakan denda_total dan model Denda
        $data = [
            'totalPinjam' => Peminjaman::count(),
            'totalAnggota' => User::whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])->count(),
            'totalBuku' => Buku::count(),
            'totalKunjungan' => Kunjungan::count(),
            'totalDenda' => Denda::where('payment_status', 'paid')->sum('jumlah_denda'), // Denda yang sudah dibayar
            'peminjamanTerbaru' => Peminjaman::with(['user', 'buku'])->latest()->take(10)->get(),
            'bukuPopuler' => Buku::withCount('peminjaman')->orderBy('peminjaman_count', 'desc')->take(10)->get(),
            'anggotaAktif' => User::whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])->withCount('peminjaman')->orderBy('peminjaman_count', 'desc')->take(10)->get(),
            'tanggal_cetak' => Carbon::now()->format('d/m/Y H:i:s')
        ];
        
        $pdf = Pdf::loadView('petugas.exports.pdf.all', $data);
        return $pdf->download('laporan-lengkap-'.Carbon::now()->format('Y-m-d').'.pdf');
    }

    /**
     * Export Semua Laporan (Gabungan) ke Excel
     */
    public function exportAllExcel()
    {
        return Excel::download(new LaporanGabunganExport(), 'laporan-lengkap-'.Carbon::now()->format('Y-m-d').'.xlsx');
    }
}