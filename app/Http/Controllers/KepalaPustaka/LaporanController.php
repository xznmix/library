<?php

namespace App\Http\Controllers\KepalaPustaka;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Buku;
use App\Models\User;
use App\Models\Kunjungan;
use App\Models\ActivityLog;
use App\Models\KategoriBuku;
use App\Models\Notifikasi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LaporanController extends Controller
{
    /**
     * Laporan Denda (FULL VERSION - FIXED)
     */
    public function denda(Request $request)
    {
        try {
            // Set periode default
            $periode = $request->periode ?? 'bulan_ini';
            $startDate = $this->getStartDateFromPeriode($periode, $request);
            $endDate = $this->getEndDateFromPeriode($periode, $request);
            
            // Base query
            $query = Peminjaman::with(['user', 'buku', 'petugas', 'diverifikasiOleh'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('denda_total', '>', 0);
            
            // Filter status
            if ($request->filled('status')) {
                $query->where('status_verifikasi', $request->status);
            }
            
            // Filter petugas
            if ($request->filled('petugas_id')) {
                $query->where('petugas_id', $request->petugas_id);
            }
            
            // Get data dengan pagination
            $dendas = $query->orderBy('created_at', 'desc')
                ->paginate(20)
                ->withQueryString();
            
            // ========== AMBIL SEMUA DATA TANPA PAGINATION UNTUK STATISTIK ==========
            $allData = (clone $query)->get();
            
            // ========== STATISTIK REAL ==========
            $totalDenda = $allData->sum('denda_total');
            $totalTransaksi = $allData->count();
            $rataDenda = $totalTransaksi > 0 ? $totalDenda / $totalTransaksi : 0;
            $dendaTertinggi = $allData->max('denda_total');
            
            // Komposisi denda
            $totalDendaTerlambat = $allData->sum('denda');
            $totalDendaRusak = $allData->sum('denda_rusak');
            
            // ========== GRAFIK HARIAN (30 hari terakhir) ==========
            $grafikHarian = $this->getGrafikDendaHarian($startDate, $endDate);
            
            // ========== GRAFIK PER PETUGAS ==========
            $grafikPetugas = $this->getGrafikDendaPerPetugas($startDate, $endDate);
            
            // ========== STATISTIK PER BULAN ==========
            $statistikBulanan = $this->getStatistikBulanan($startDate, $endDate);
            
            // ========== 10 DENDA TERBESAR ==========
            $dendaTerbesar = Peminjaman::with(['user', 'buku', 'petugas'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('denda_total', '>', 0)
                ->orderBy('denda_total', 'desc')
                ->limit(10)
                ->get();
            
            // ========== STATISTIK VERIFIKASI ==========
            $verifikasi = [
                'pending' => Peminjaman::whereBetween('created_at', [$startDate, $endDate])
                    ->where('status_verifikasi', 'pending')
                    ->where('denda_total', '>', 0)
                    ->count(),
                'disetujui' => Peminjaman::whereBetween('created_at', [$startDate, $endDate])
                    ->where('status_verifikasi', 'disetujui')
                    ->where('denda_total', '>', 0)
                    ->count(),
                'ditolak' => Peminjaman::whereBetween('created_at', [$startDate, $endDate])
                    ->where('status_verifikasi', 'ditolak')
                    ->where('denda_total', '>', 0)
                    ->count(),
            ];
            
            // Daftar petugas untuk filter
            $petugas = User::where('role', 'petugas')->get(['id', 'name']);
            
            return view('kepala-pustaka.pages.laporan.denda', compact(
                'dendas',
                'totalDenda',
                'totalTransaksi',
                'rataDenda',
                'dendaTertinggi',
                'totalDendaTerlambat',
                'totalDendaRusak',
                'grafikHarian',
                'grafikPetugas',
                'statistikBulanan',
                'dendaTerbesar',
                'verifikasi',
                'startDate',
                'endDate',
                'petugas'
            ));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Laporan Aktivitas (FULL VERSION)
     */
    public function aktivitas(Request $request)
    {
        try {
            $query = ActivityLog::with('user');
            
            // Filter role
            if ($request->filled('role')) {
                $query->where('role', $request->role);
            }
            
            // Filter aksi
            if ($request->filled('action')) {
                $query->where('action', $request->action);
            }
            
            // Filter user
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
            
            // Filter tanggal
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            
            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }
            
            $aktivitas = $query->latest()->paginate(30)->withQueryString();
            
            // Statistik aktivitas
            $statistik = [
                'hari_ini' => ActivityLog::whereDate('created_at', today())->count(),
                'minggu_ini' => ActivityLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'bulan_ini' => ActivityLog::whereMonth('created_at', now()->month)->count(),
                'total' => ActivityLog::count(),
            ];
            
            // Top 5 user paling aktif
            $userAktif = ActivityLog::select('user_id', DB::raw('count(*) as total'))
                ->with('user')
                ->groupBy('user_id')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get();
            
            // Daftar user untuk filter
            $users = User::whereIn('role', ['admin', 'petugas', 'kepala_pustaka'])->get(['id', 'name', 'role']);
            
            // Daftar aksi unik
            $aksiList = ActivityLog::distinct()->pluck('action');
            
            return view('kepala-pustaka.pages.laporan.aktivitas', compact(
                'aktivitas',
                'statistik',
                'userAktif',
                'users',
                'aksiList'
            ));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Laporan Peminjaman (FULL VERSION)
     */
    public function peminjaman(Request $request)
    {
        try {
            // Filter periode
            $tahun = $request->tahun ?? now()->year;
            $bulan = $request->bulan ?? null;
            $status = $request->status ?? null;
            $jenis = $request->jenis ?? null;
            
            // Validasi tahun
            if ($tahun < 2000 || $tahun > now()->year + 1) {
                $tahun = now()->year;
            }
            
            // Base query untuk statistik
            $baseQuery = Peminjaman::whereYear('created_at', $tahun)
                ->when($bulan, function($q) use ($bulan) {
                    $q->whereMonth('created_at', $bulan);
                })
                ->when($status, function($q) use ($status) {
                    $q->where('status_pinjam', $status);
                })
                ->when($jenis, function($q) use ($jenis) {
                    $q->whereHas('user', function($user) use ($jenis) {
                        $user->where('jenis', $jenis);
                    });
                });
            
            // ========== STATISTIK UTAMA ==========
            $totalPeminjaman = (clone $baseQuery)->count();
            $sedangDipinjam = Peminjaman::whereIn('status_pinjam', ['dipinjam', 'terlambat'])->count();
            
            $tepatWaktu = (clone $baseQuery)
                ->where('status_pinjam', 'dikembalikan')
                ->where('denda', 0)
                ->count();
            
            $terlambat = (clone $baseQuery)
                ->where('status_pinjam', 'terlambat')
                ->count();
            
            // ========== GRAFIK PER BULAN ==========
            $grafikBulanan = $this->getGrafikPeminjamanBulanan($tahun, $status, $jenis);
            
            // ========== BUKU TERPOPULER (CACHED) ==========
            $cacheKey = "buku_populer_{$tahun}_{$bulan}_{$status}_{$jenis}";
            $bukuPopuler = Cache::remember($cacheKey, 3600, function() use ($tahun, $bulan, $status, $jenis) {
                $query = Buku::withCount(['peminjaman' => function ($q) use ($tahun, $bulan, $status, $jenis) {
                    $q->whereYear('created_at', $tahun);
                    if ($bulan) {
                        $q->whereMonth('created_at', $bulan);
                    }
                    if ($status) {
                        $q->where('status_pinjam', $status);
                    }
                    if ($jenis) {
                        $q->whereHas('user', function($user) use ($jenis) {
                            $user->where('jenis', $jenis);
                        });
                    }
                }])
                ->orderBy('peminjaman_count', 'desc')
                ->limit(10)
                ->get();
                
                return $query;
            });
            
            // ========== ANGGOTA TERAKTIF ==========
            $anggotaAktif = User::whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])
                ->withCount(['peminjaman' => function ($q) use ($tahun, $bulan, $status) {
                    $q->whereYear('created_at', $tahun);
                    if ($bulan) {
                        $q->whereMonth('created_at', $bulan);
                    }
                    if ($status) {
                        $q->where('status_pinjam', $status);
                    }
                }])
                ->orderBy('peminjaman_count', 'desc')
                ->limit(10)
                ->get();
            
            // ========== STATISTIK PER KATEGORI ==========
            $statistikKategori = KategoriBuku::withCount(['buku'])
                ->withCount(['buku as peminjaman_count' => function($q) use ($tahun, $bulan, $status, $jenis) {
                    $q->join('peminjaman', 'buku.id', '=', 'peminjaman.buku_id')
                      ->whereYear('peminjaman.created_at', $tahun);
                    if ($bulan) {
                        $q->whereMonth('peminjaman.created_at', $bulan);
                    }
                    if ($status) {
                        $q->where('peminjaman.status_pinjam', $status);
                    }
                    if ($jenis) {
                        $q->whereHas('peminjaman.user', function($user) use ($jenis) {
                            $user->where('jenis', $jenis);
                        });
                    }
                }])
                ->get();
            
            // ========== STATISTIK PER JENIS ANGGOTA ==========
            $statistikJenis = [
                'siswa' => (clone $baseQuery)->whereHas('user', fn($q) => $q->where('jenis', 'siswa'))->count(),
                'guru' => (clone $baseQuery)->whereHas('user', fn($q) => $q->where('jenis', 'guru'))->count(),
                'pegawai' => (clone $baseQuery)->whereHas('user', fn($q) => $q->where('jenis', 'pegawai'))->count(),
                'umum' => (clone $baseQuery)->whereHas('user', fn($q) => $q->where('jenis', 'umum'))->count(),
            ];
            
            // ========== DATA PEMINJAMAN ==========
            $peminjaman = Peminjaman::with(['user', 'buku', 'petugas'])
                ->whereYear('created_at', $tahun)
                ->when($bulan, function($q) use ($bulan) {
                    $q->whereMonth('created_at', $bulan);
                })
                ->when($status, function($q) use ($status) {
                    $q->where('status_pinjam', $status);
                })
                ->when($jenis, function($q) use ($jenis) {
                    $q->whereHas('user', function($user) use ($jenis) {
                        $user->where('jenis', $jenis);
                    });
                })
                ->orderBy('created_at', 'desc')
                ->paginate(20)
                ->withQueryString();
            
            return view('kepala-pustaka.pages.laporan.peminjaman', compact(
                'totalPeminjaman',
                'sedangDipinjam',
                'tepatWaktu',
                'terlambat',
                'grafikBulanan',
                'bukuPopuler',
                'anggotaAktif',
                'statistikKategori',
                'statistikJenis',
                'peminjaman',
                'tahun',
                'bulan',
                'status',
                'jenis'
            ));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Laporan Kunjungan (FULL VERSION)
     */
    public function kunjungan(Request $request)
    {
        try {
            $tahun = $request->tahun ?? now()->year;
            
            // ========== GRAFIK KUNJUNGAN PER BULAN ==========
            $kunjunganBulanan = Kunjungan::select(
                    DB::raw('MONTH(tanggal) as bulan'),
                    DB::raw('COUNT(*) as total')
                )
                ->whereYear('tanggal', $tahun)
                ->groupBy('bulan')
                ->orderBy('bulan')
                ->get()
                ->map(function($item) use ($tahun) {
                    $bulan = [
                        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
                        5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
                        9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
                    ];
                    $item->nama_bulan = $bulan[$item->bulan] ?? $item->bulan;
                    $item->nama_bulan_full = Carbon::createFromDate($tahun, $item->bulan, 1)->format('F');
                    return $item;
                });
            
            // ========== STATISTIK ==========
            $totalKunjungan = $kunjunganBulanan->sum('total');
            $rataPerBulan = $kunjunganBulanan->avg('total');
            $bulanTertinggi = $kunjunganBulanan->sortByDesc('total')->first();
            $bulanTerendah = $kunjunganBulanan->sortBy('total')->first();
            
            // ========== KUNJUNGAN PER HARI (7 HARI TERAKHIR) ==========
            $kunjunganHarian = [];
            for ($i = 6; $i >= 0; $i--) {
                $tanggal = now()->subDays($i);
                $kunjunganHarian[] = [
                    'tanggal' => $tanggal->format('d/m'),
                    'tanggal_full' => $tanggal->format('Y-m-d'),
                    'hari' => $tanggal->isoFormat('dddd'),
                    'total' => Kunjungan::whereDate('tanggal', $tanggal)->count()
                ];
            }
            
            // ========== STATISTIK PER JENIS ANGGOTA ==========
            $kunjunganPerJenis = Kunjungan::select('jenis_anggota', DB::raw('COUNT(*) as total'))
                ->whereYear('tanggal', $tahun)
                ->groupBy('jenis_anggota')
                ->get()
                ->mapWithKeys(function($item) {
                    return [$item->jenis_anggota ?? 'umum' => $item->total];
                });
            
            // ========== TREN KUNJUNGAN (12 BULAN) ==========
            $trenKunjungan = [
                'labels' => $kunjunganBulanan->pluck('nama_bulan'),
                'data' => $kunjunganBulanan->pluck('total')
            ];
            
            // ========== STATISTIK PER HARI (RATA-RATA) ==========
            $rataPerHari = [];
            for ($i = 0; $i <= 6; $i++) {
                $hari = now()->startOfWeek()->addDays($i);
                $namaHari = $hari->isoFormat('dddd');
                $rataPerHari[$namaHari] = Kunjungan::whereRaw('DAYOFWEEK(tanggal) = ?', [$i + 1])
                    ->whereYear('tanggal', $tahun)
                    ->count() / 52; // Rata-rata per minggu
            }
            
            return view('kepala-pustaka.pages.laporan.kunjungan', compact(
                'kunjunganBulanan',
                'totalKunjungan',
                'rataPerBulan',
                'bulanTertinggi',
                'bulanTerendah',
                'kunjunganHarian',
                'kunjunganPerJenis',
                'trenKunjungan',
                'rataPerHari',
                'tahun'
            ));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * ========== EXPORT FUNCTIONS ==========
     */

    /**
     * Export Laporan Denda ke Excel
     */
    public function exportDendaExcel(Request $request)
    {
        try {
            $startDate = $this->getStartDateFromRequest($request);
            $endDate = $this->getEndDateFromRequest($request);
            
            $query = Peminjaman::with(['user', 'buku', 'petugas'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('denda_total', '>', 0);
            
            if ($request->filled('status')) {
                $query->where('status_verifikasi', $request->status);
            }
            
            $data = $query->orderBy('created_at', 'desc')->get();
            
            // TODO: Implement Excel export dengan Maatwebsite\Excel
            // return Excel::download(new DendaExport($data), "laporan-denda-{$startDate->format('Ymd')}-{$endDate->format('Ymd')}.xlsx");
            
            return redirect()->back()->with('info', 'Fitur export Excel sedang dalam pengembangan');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export: ' . $e->getMessage());
        }
    }

    /**
     * Export Laporan Denda ke PDF
     */
    public function exportDendaPdf(Request $request)
    {
        try {
            $startDate = $this->getStartDateFromRequest($request);
            $endDate = $this->getEndDateFromRequest($request);
            
            $query = Peminjaman::with(['user', 'buku', 'petugas'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('denda_total', '>', 0);
            
            if ($request->filled('status')) {
                $query->where('status_verifikasi', $request->status);
            }
            
            $data = $query->orderBy('created_at', 'desc')->get();
            
            $totalDenda = $data->sum('denda_total');
            $totalTransaksi = $data->count();
            
            // TODO: Implement PDF export dengan Barryvdh\DomPDF
            // $pdf = PDF::loadView('kepala-pustaka.exports.pdf.denda', compact('data', 'totalDenda', 'totalTransaksi', 'startDate', 'endDate'));
            // return $pdf->download("laporan-denda-{$startDate->format('Ymd')}-{$endDate->format('Ymd')}.pdf");
            
            return redirect()->back()->with('info', 'Fitur export PDF sedang dalam pengembangan');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export: ' . $e->getMessage());
        }
    }

    /**
     * Export Laporan Peminjaman ke Excel
     */
    public function exportPeminjamanExcel(Request $request)
    {
        try {
            $tahun = $request->tahun ?? now()->year;
            $bulan = $request->bulan ?? null;
            $status = $request->status ?? null;
            $jenis = $request->jenis ?? null;
            
            $query = Peminjaman::with(['user', 'buku', 'petugas'])
                ->whereYear('created_at', $tahun)
                ->when($bulan, fn($q) => $q->whereMonth('created_at', $bulan))
                ->when($status, fn($q) => $q->where('status_pinjam', $status))
                ->when($jenis, fn($q) => $q->whereHas('user', fn($u) => $u->where('jenis', $jenis)));
            
            $data = $query->orderBy('created_at', 'desc')->get();
            
            // TODO: Implement Excel export
            return redirect()->back()->with('info', 'Fitur export Excel sedang dalam pengembangan');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export: ' . $e->getMessage());
        }
    }

    /**
     * Export Laporan Peminjaman ke PDF
     */
    public function exportPeminjamanPdf(Request $request)
    {
        try {
            $tahun = $request->tahun ?? now()->year;
            $bulan = $request->bulan ?? null;
            
            $query = Peminjaman::with(['user', 'buku', 'petugas'])
                ->whereYear('created_at', $tahun)
                ->when($bulan, fn($q) => $q->whereMonth('created_at', $bulan));
            
            $data = $query->orderBy('created_at', 'desc')->get();
            
            // TODO: Implement PDF export
            return redirect()->back()->with('info', 'Fitur export PDF sedang dalam pengembangan');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export: ' . $e->getMessage());
        }
    }

    /**
     * Export Laporan Kunjungan ke Excel
     */
    public function exportKunjunganExcel(Request $request)
    {
        try {
            $tahun = $request->tahun ?? now()->year;
            
            $data = Kunjungan::whereYear('tanggal', $tahun)
                ->orderBy('tanggal', 'desc')
                ->get();
            
            // TODO: Implement Excel export
            return redirect()->back()->with('info', 'Fitur export Excel sedang dalam pengembangan');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export: ' . $e->getMessage());
        }
    }

    /**
     * Export Laporan Aktivitas ke Excel
     */
    public function exportAktivitasExcel(Request $request)
    {
        try {
            $query = ActivityLog::with('user');
            
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            
            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }
            
            if ($request->filled('role')) {
                $query->where('role', $request->role);
            }
            
            $data = $query->orderBy('created_at', 'desc')->get();
            
            // TODO: Implement Excel export
            return redirect()->back()->with('info', 'Fitur export Excel sedang dalam pengembangan');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export: ' . $e->getMessage());
        }
    }

    /**
     * ========== PRIVATE HELPER FUNCTIONS ==========
     */

    /**
     * Get start date berdasarkan periode
     */
    private function getStartDateFromPeriode($periode, $request)
    {
        if ($periode == 'kustom' && $request->filled('start_date')) {
            return Carbon::parse($request->start_date)->startOfDay();
        }
        
        return match($periode) {
            'hari_ini' => now()->startOfDay(),
            'minggu_ini' => now()->startOfWeek(),
            'bulan_ini' => now()->startOfMonth(),
            'tahun_ini' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };
    }

    /**
     * Get end date berdasarkan periode
     */
    private function getEndDateFromPeriode($periode, $request)
    {
        if ($periode == 'kustom' && $request->filled('end_date')) {
            return Carbon::parse($request->end_date)->endOfDay();
        }
        
        return match($periode) {
            'hari_ini' => now()->endOfDay(),
            'minggu_ini' => now()->endOfWeek(),
            'bulan_ini' => now()->endOfMonth(),
            'tahun_ini' => now()->endOfYear(),
            default => now()->endOfDay(),
        };
    }

    /**
     * Get start date dari request (untuk export)
     */
    private function getStartDateFromRequest($request)
    {
        if ($request->filled('start_date')) {
            return Carbon::parse($request->start_date)->startOfDay();
        }
        
        if ($request->filled('periode')) {
            return $this->getStartDateFromPeriode($request->periode, $request);
        }
        
        return now()->startOfMonth();
    }

    /**
     * Get end date dari request (untuk export)
     */
    private function getEndDateFromRequest($request)
    {
        if ($request->filled('end_date')) {
            return Carbon::parse($request->end_date)->endOfDay();
        }
        
        if ($request->filled('periode')) {
            return $this->getEndDateFromPeriode($request->periode, $request);
        }
        
        return now()->endOfDay();
    }

    /**
     * Grafik denda harian (30 hari terakhir)
     */
    private function getGrafikDendaHarian($startDate, $endDate)
    {
        $labels = [];
        $data = [];
        
        // Hitung jumlah hari
        $days = $startDate->diffInDays($endDate);
        $days = min($days, 30); // Maksimal 30 hari
        
        // Ambil data dari database
        $dailyTotals = Peminjaman::whereBetween('created_at', [$startDate, $endDate])
            ->where('denda_total', '>', 0)
            ->select(DB::raw('DATE(created_at) as tanggal'), DB::raw('SUM(denda_total) as total'))
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal')
            ->toArray();
        
        for ($i = $days; $i >= 0; $i--) {
            $tanggal = Carbon::parse($endDate)->subDays($i);
            $labels[] = $tanggal->format('d/m');
            $key = $tanggal->format('Y-m-d');
            $data[] = $dailyTotals[$key] ?? 0;
        }
        
        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Grafik denda per petugas
     */
    private function getGrafikDendaPerPetugas($startDate, $endDate)
    {
        $petugas = User::where('role', 'petugas')->get(['id', 'name']);
        
        $labels = [];
        $data = [];
        
        foreach ($petugas as $p) {
            $labels[] = $p->name;
            $data[] = Peminjaman::where('petugas_id', $p->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('denda_total', '>', 0)
                ->where('status_verifikasi', 'disetujui')
                ->sum('denda_total');
        }
        
        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Statistik bulanan
     */
    private function getStatistikBulanan($startDate, $endDate)
    {
        $statistik = [];
        
        $current = clone $startDate->startOfMonth();
        while ($current <= $endDate) {
            $bulanKey = $current->format('Y-m');
            $bulanName = $current->format('M Y');
            
            // Gunakan query sekali untuk efisiensi
            $data = Peminjaman::whereYear('created_at', $current->year)
                ->whereMonth('created_at', $current->month)
                ->where('denda_total', '>', 0)
                ->select(
                    DB::raw('COUNT(*) as total_transaksi'),
                    DB::raw('SUM(denda_total) as total_nominal')
                )
                ->first();
            
            $statistik[$bulanKey] = [
                'bulan' => $bulanName,
                'total' => $data->total_transaksi ?? 0,
                'nominal' => $data->total_nominal ?? 0
            ];
            
            $current->addMonth();
        }
        
        return $statistik;
    }

    /**
     * Grafik peminjaman per bulan
     */
    private function getGrafikPeminjamanBulanan($tahun, $status = null, $jenis = null)
    {
        $labels = [];
        $data = [];
        
        $monthlyTotals = Peminjaman::whereYear('created_at', $tahun)
            ->when($status, fn($q) => $q->where('status_pinjam', $status))
            ->when($jenis, function($q) use ($jenis) {
                $q->whereHas('user', fn($u) => $u->where('jenis', $jenis));
            })
            ->select(DB::raw('MONTH(created_at) as bulan'), DB::raw('COUNT(*) as total'))
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();
        
        for ($i = 1; $i <= 12; $i++) {
            $labels[] = Carbon::createFromDate($tahun, $i, 1)->format('M');
            $data[] = $monthlyTotals[$i] ?? 0;
        }
        
        return compact('labels', 'data');
    }
}