<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\KategoriBuku;
use Illuminate\Support\Facades\Storage;
use App\Services\AISearchService;

class OPACController extends Controller
{
    public function index(Request $request)
    {
        $query = Buku::with('kategori')
            ->where('status', 'tersedia'); // Hanya tampilkan yang tersedia
        
        // Pencarian
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'LIKE', "%{$search}%")
                  ->orWhere('pengarang', 'LIKE', "%{$search}%")
                  ->orWhere('penerbit', 'LIKE', "%{$search}%")
                  ->orWhere('isbn', 'LIKE', "%{$search}%")
                  ->orWhere('deskripsi', 'LIKE', "%{$search}%");
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
        switch ($request->get('sort', 'terbaru')) {
            case 'terbaru':
                $query->latest();
                break;
            case 'populer':
                $query->orderBy('total_dipinjam', 'desc');
                break;
            case 'judul':
                $query->orderBy('judul', 'asc');
                break;
            default:
                $query->latest();
        }
        
        $buku = $query->paginate(12)->withQueryString();
        
        return view('opac.index', compact('buku'));
    }

    public function show($id)
    {
        $buku = Buku::with(['kategori' => function($q) {
                $q->with(['buku' => function($buku) {
                    $buku->where('status', 'tersedia')->limit(6);
                }]);
            }])
            ->where('status', 'tersedia')
            ->findOrFail($id);
        
        return view('opac.show', compact('buku'));
    }

    /**
     * Download file digital publik (untuk soal/modul/dokumen yang download bebas)
     */
    public function download($id)
    {
        $buku = Buku::where('tipe', 'digital')->findOrFail($id);
        
        // Cek apakah file bisa di-download langsung (tanpa login)
        if (!$buku->bisa_langsung_download) {
            return redirect()->route('opac.show', $buku->id)
                ->with('error', 'File ini tidak bisa di-download langsung. Silakan login untuk meminjam.');
        }
        
        // Cek apakah file ada
        if (!$buku->file_path || !Storage::disk('public')->exists($buku->file_path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }
        
        // Log download (opsional, bisa disimpan atau tidak)
        // Karena publik, kita bisa skip log atau simpan dengan user_id = null
        
        $filePath = storage_path('app/public/' . $buku->file_path);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $filename = $buku->judul . '.' . $extension;
        
        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Pencarian dengan AI (Gemini)
     */
    public function searchWithAI(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:500',
        ]);
        
        $query = $request->q;
        $limit = $request->limit ?? 10;
        
        $aiService = new AISearchService();
        $result = $aiService->search($query, $limit);
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($result);
        }
        
        // Untuk request biasa, tampilkan view dengan hasil
        return view('opac.index', [
            'ai_results' => $result,
            'search_query' => $query,
            'is_ai_search' => true,
            'buku_populer' => Buku::populer(6)->get(),
            'kategori_list' => KategoriBuku::withCount('buku')->having('buku_count', '>', 0)->get(),
        ]);
    }
}