<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kunjungan;
use App\Models\User;

class KunjunganController extends Controller
{
    /**
     * Daftar kunjungan hari ini
     */
    public function index(Request $request)
    {
        $query = Kunjungan::with('user')
            ->whereDate('tanggal', today());

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($user) use ($search) {
                      $user->where('nisn_nik', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter jenis
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        $kunjunganHariIni = $query->orderBy('jam_masuk', 'desc')
            ->paginate(20)
            ->withQueryString();

        $totalHariIni = Kunjungan::whereDate('tanggal', today())->count();

        return view('petugas.pages.kunjungan.index', compact('kunjunganHariIni', 'totalHariIni'));
    }

    /**
     * Detail kunjungan
     */
    public function show($id)
    {
        $kunjungan = Kunjungan::with('user')->findOrFail($id);
        return view('petugas.pages.kunjungan.show', compact('kunjungan'));
    }

    /**
     * Hapus kunjungan
     */
    public function destroy($id)
    {
        try {
            $kunjungan = Kunjungan::findOrFail($id);
            $kunjungan->delete();
            
            return redirect()->route('petugas.kunjungan.index')
                ->with('success', 'Data kunjungan berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('petugas.kunjungan.index')
                ->with('error', 'Gagal menghapus data kunjungan');
        }
    }

    /**
     * Rekap kunjungan (untuk laporan)
     */
    public function rekap(Request $request)
    {
        $query = Kunjungan::with('user');

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        $rekap = $query->orderBy('tanggal', 'desc')
            ->orderBy('jam_masuk', 'desc')
            ->paginate(20);

        return view('petugas.pages.kunjungan.rekap', compact('rekap'));
    }
}