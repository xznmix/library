<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        // ========== STATISTIK UTAMA ==========
        $totalUsers = User::count();
        $totalAnggota = User::anggota()->count();
        $totalPetugas = User::petugas()->count();
        $totalAdmin = User::where('role', 'admin')->count();
        
        // ========== STATISTIK SISTEM REAL ==========
        $totalBuku = Buku::count();
        $peminjamanAktif = Peminjaman::whereIn('status_pinjam', ['dipinjam', 'terlambat'])->count();
        
        // PERBAIKAN: Query data terlambat yang benar - menggunakan kolom 'tanggal_pengembalian', bukan 'tanggal_kembali'
        $peminjamanTerlambat = Peminjaman::where(function($query) {
                $query->where('status_pinjam', 'terlambat')
                      ->orWhere(function($q) {
                          $q->where('status_pinjam', 'dipinjam')
                            ->where('tgl_jatuh_tempo', '<', now());  // PERBAIKAN: gunakan 'tgl_jatuh_tempo' bukan 'tanggal_kembali'
                      });
            })
            ->count();
        
        $aktivitasHariIni = ActivityLog::whereDate('created_at', today())->count();
        
        // ========== DATA GRAFIK BULAN INI ==========
        $chartData = $this->getChartData();
        
        // ========== AKTIVITAS TERBARU DARI ACTIVITY LOG ==========
        $recentActivities = ActivityLog::with('user')
            ->whereHas('user', function($query) {
                $query->whereIn('role', ['admin', 'petugas']);
            })
            ->latest()
            ->limit(10)
            ->get();
        
        // ========== STATUS SISTEM ==========
        $systemStatus = [
            'server' => 'Online',
            'database' => 'Connected',
            'last_update' => now()->diffForHumans(),
            'total_aktivitas' => ActivityLog::count(),
        ];
        
        return view('admin.dashboard', compact(
            'totalUsers',
            'totalAnggota',
            'totalPetugas',
            'totalAdmin',
            'totalBuku',
            'peminjamanAktif',
            'peminjamanTerlambat',
            'aktivitasHariIni',
            'chartData',
            'recentActivities',
            'systemStatus'
        ));
    }

    private function getChartData()
    {
        $labels = [];
        $data = [];
        
        // Data untuk 5 bulan terakhir
        for ($i = 4; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->translatedFormat('M Y');
            
            $count = User::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $data[] = $count;
        }
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
    
    /**
     * API endpoint untuk mendapatkan aktivitas terbaru
     */
    public function getActivityLogs(Request $request)
    {
        $activities = ActivityLog::with('user')
            ->whereHas('user', function($query) {
                $query->whereIn('role', ['admin', 'petugas']);
            })
            ->latest()
            ->limit(10)
            ->get()
            ->map(function($activity) {
                return [
                    'id' => $activity->id,
                    'user_name' => $activity->user->name ?? 'System',
                    'user_role' => $activity->user->role ?? 'system',
                    'action' => $activity->action,
                    'description' => $activity->description,
                    'model' => $activity->model,
                    'ip_address' => $activity->ip_address,
                    'time_ago' => $activity->created_at ? $activity->created_at->diffForHumans() : 'Baru saja',
                    'created_at' => $activity->created_at ? $activity->created_at->toISOString() : null
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => $activities
        ]);
    }
    
    /**
     * API endpoint untuk mendapatkan data chart
     */
    public function getChartDataApi()
    {
        return response()->json([
            'success' => true,
            'data' => $this->getChartData()
        ]);
    }
}