<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\PoinAnggota;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    /**
     * Store peminjaman E-BOOK SAJA (bukan buku fisik)
     * Anggota tidak bisa pinjam buku fisik langsung, harus booking
     */
    public function store(Request $request)
    {
        $request->validate([
            'buku_id' => 'required|exists:buku,id'
        ]);
        
        $user = Auth::user();
        $buku = Buku::findOrFail($request->buku_id);
        
        // ✅ HANYA E-BOOK yang bisa dipinjam langsung
        if ($buku->tipe !== 'digital') {
            return redirect()->back()->with('error', 'Buku fisik tidak bisa dipinjam langsung. Silakan gunakan fitur Booking.');
        }
        
        // Cek apakah ebook bisa di-download langsung atau perlu pinjam
        if ($buku->bisa_langsung_download) {
            return redirect()->route('anggota.koleksi-digital.download', $buku->id)
                ->with('info', 'Koleksi ini bisa langsung di-download.');
        }
        
        // Cek ketersediaan lisensi ebook
        $tersedia = $buku->jumlah_lisensi - $buku->lisensi_dipinjam;
        if ($tersedia <= 0) {
            return redirect()->back()->with('error', 'Maaf, semua lisensi e-book sedang dipinjam.');
        }
        
        // Redirect ke pinjam ebook
        return redirect()->route('anggota.koleksi-digital.pinjam', $buku->id);
    }
    
    /**
     * Riwayat peminjaman
     */
    public function riwayat(Request $request)
    {
        $user = Auth::user();
        $status = $request->get('status');
        
        $query = Peminjaman::with('buku')
            ->where('user_id', $user->id);
            
        if ($status == 'dipinjam') {
            $query->whereIn('status_pinjam', ['dipinjam', 'terlambat']);
        } elseif ($status == 'jatuh_tempo') {
            $query->whereIn('status_pinjam', ['dipinjam', 'terlambat'])
                  ->whereDate('tgl_jatuh_tempo', '<=', Carbon::now()->addDays(3));
        } elseif ($status == 'selesai') {
            $query->where('status_pinjam', 'dikembalikan');
        }
        
        $riwayat = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('anggota.pages.riwayat.index', compact('riwayat', 'status'));
    }
    
    /**
     * Detail peminjaman
     */
    public function detail($id)
    {
        $peminjaman = Peminjaman::with('buku')
            ->where('user_id', Auth::id())
            ->findOrFail($id);
            
        return view('anggota.pages.riwayat.detail', compact('peminjaman'));
    }
}