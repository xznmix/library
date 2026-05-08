<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\PeminjamanDigital;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RiwayatController extends Controller
{
    /**
     * Daftar riwayat peminjaman
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Riwayat peminjaman fisik
        $query = Peminjaman::with('buku')
            ->where('user_id', $user->id);
        
        // Filter status
        if ($request->filled('status')) {
            if ($request->status == 'dipinjam') {
                $query->whereIn('status_pinjam', ['dipinjam', 'terlambat']);
            } elseif ($request->status == 'jatuh_tempo') {
                $query->whereIn('status_pinjam', ['dipinjam', 'terlambat'])
                    ->whereDate('tgl_jatuh_tempo', '<=', Carbon::now()->addDays(3))
                    ->whereDate('tgl_jatuh_tempo', '>=', Carbon::now());
            } elseif ($request->status == 'selesai') {
                $query->where('status_pinjam', 'dikembalikan');
            }
        }
        
        $riwayatFisik = $query->latest()->paginate(10);
        
        // Riwayat peminjaman digital
        $riwayatDigital = PeminjamanDigital::with('buku')
            ->where('user_id', $user->id)
            ->latest()
            ->get();
        
        return view('anggota.pages.riwayat.index', compact('riwayatFisik', 'riwayatDigital'));
    }

    /**
     * Detail peminjaman
     */
    public function show($id)
    {
        $peminjaman = Peminjaman::with(['buku', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);
        
        return view('anggota.pages.riwayat.detail', compact('peminjaman'));
    }
}