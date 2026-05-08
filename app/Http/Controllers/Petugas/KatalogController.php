<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\KategoriBuku;
use Barryvdh\DomPDF\Facade\Pdf;

class KatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Buku::with('kategori');
        
        // Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'LIKE', "%{$search}%")
                  ->orWhere('pengarang', 'LIKE', "%{$search}%")
                  ->orWhere('penerbit', 'LIKE', "%{$search}%")
                  ->orWhere('isbn', 'LIKE', "%{$search}%")
                  ->orWhere('no_ddc', 'LIKE', "%{$search}%")
                  ->orWhere('nomor_panggil', 'LIKE', "%{$search}%");
            });
        }
        
        // Filter kategori
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }
        
        // Filter tipe
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }
        
        // Sorting
        $sort = $request->get('sort', 'terbaru');
        switch ($sort) {
            case 'terbaru': $query->latest(); break;
            case 'terlama': $query->oldest(); break;
            case 'judul-asc': $query->orderBy('judul', 'asc'); break;
            case 'judul-desc': $query->orderBy('judul', 'desc'); break;
            default: $query->latest();
        }
        
        $buku = $query->paginate(12)->withQueryString();
        $kategoriList = KategoriBuku::all();
        
        // Statistik
        $totalJudul = Buku::count();
        $totalEksemplar = Buku::sum('stok');
        $totalTersedia = Buku::sum('stok_tersedia');
        
        return view('petugas.pages.katalog.index', compact(
            'buku', 'kategoriList', 'totalJudul', 'totalEksemplar', 'totalTersedia'
        ));
    }
    
    /**
     * Cetak katalog dalam format PDF
     */
    public function print(Request $request)
    {
        $query = Buku::with('kategori');
        
        // Filter yang sama seperti index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'LIKE', "%{$search}%")
                  ->orWhere('pengarang', 'LIKE', "%{$search}%")
                  ->orWhere('penerbit', 'LIKE', "%{$search}%");
            });
        }
        
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }
        
        $buku = $query->orderBy('judul', 'asc')->get();
        
        $pdf = Pdf::loadView('petugas.pages.katalog.print', [
            'buku' => $buku,
            'tanggal_cetak' => now()->format('d/m/Y H:i:s'),
            'total_buku' => $buku->count()
        ]);
        
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download('katalog-perpustakaan-' . date('Y-m-d') . '.pdf');
    }
    
    /**
     * Cetak kartu katalog per buku (format seperti yang diminta sekolah)
     */
    public function printCard($id)
    {
        $buku = Buku::with('kategori')->findOrFail($id);
        
        $pdf = Pdf::loadView('petugas.pages.katalog.card', [
            'buku' => $buku
        ]);
        
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download('kartu-katalog-' . $buku->judul . '.pdf');
    }
    
    /**
     * Cetak banyak kartu katalog (format per halaman 2-4 kartu)
     */
    public function printMultiple(Request $request)
    {
        $ids = explode(',', $request->ids);
        $buku = Buku::whereIn('id', $ids)->orderBy('judul', 'asc')->get();
        
        $pdf = Pdf::loadView('petugas.pages.katalog.cards', [
            'buku' => $buku
        ]);
        
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download('katalog-bulk-' . date('Y-m-d') . '.pdf');
    }
}