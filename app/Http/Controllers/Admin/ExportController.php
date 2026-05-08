<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Exports\ActivitiesExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    /**
     * Export data pengguna ke Excel
     */
    public function usersExcel()
    {
        return Excel::download(new UsersExport, 'data-pengguna-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Export data pengguna ke PDF
     */
    public function usersPdf()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        $pdf = Pdf::loadView('admin.exports.users-pdf', compact('users'));
        return $pdf->download('data-pengguna-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export log aktivitas ke Excel
     */
    public function activitiesExcel(Request $request)
    {
        return Excel::download(new ActivitiesExport($request), 'log-aktivitas-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Export log aktivitas ke PDF
     */
    public function activitiesPdf(Request $request)
    {
        $query = ActivityLog::with('user');
        
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        $activities = $query->latest()->get();
        $pdf = Pdf::loadView('admin.exports.activities-pdf', compact('activities'));
        return $pdf->download('log-aktivitas-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export ringkasan laporan ke PDF
     */
    public function reportPdf()
    {
        $totalUsers = User::count();
        $totalAnggota = User::anggota()->count();
        $totalPetugas = User::petugas()->count();
        $totalAdmin = User::where('role', 'admin')->count();
        $totalBuku = \App\Models\Buku::count();
        $peminjamanAktif = \App\Models\Peminjaman::whereIn('status_pinjam', ['dipinjam', 'terlambat'])->count();
        
        $pdf = Pdf::loadView('admin.exports.report-pdf', compact(
            'totalUsers', 'totalAnggota', 'totalPetugas', 'totalAdmin',
            'totalBuku', 'peminjamanAktif'
        ));
        return $pdf->download('laporan-perpustakaan-' . date('Y-m-d') . '.pdf');
    }
}