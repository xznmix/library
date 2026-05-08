<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\KategoriBuku;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BukuImport;

class BukuController extends Controller
{
    /**
     * Display a listing of the books.
     */
    public function index(Request $request)
    {
        $query = Buku::with('kategori');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', '%' . $search . '%')
                  ->orWhere('pengarang', 'like', '%' . $search . '%')
                  ->orWhere('penerbit', 'like', '%' . $search . '%')
                  ->orWhere('isbn', 'like', '%' . $search . '%')
                  ->orWhere('barcode', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        if ($request->filled('kategori_koleksi')) {
            $query->where('kategori_koleksi', $request->kategori_koleksi);
        }

        $buku = $query->latest()->paginate(10)->withQueryString();

        $totalBuku = Buku::count();
        $totalTersedia = Buku::where('status', 'tersedia')->sum('stok_tersedia');
        $totalDipinjam = Buku::sum('stok_dipinjam');
        $totalEbook = Buku::where('tipe', 'digital')->count();
        $totalKategori = KategoriBuku::count();
        $kategoriList = KategoriBuku::all();

        return view('petugas.pages.buku.index', compact(
            'buku', 'totalBuku', 'totalTersedia', 'totalDipinjam', 
            'totalKategori', 'totalEbook', 'kategoriList'
        ));
    }

    /**
     * Show the form for creating a new book.
     */
    public function create()
    {
        $kategori = KategoriBuku::all();
        return view('petugas.pages.buku.create', compact('kategori'));
    }

    /**
     * Store a newly created book in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'barcode' => 'nullable|string|max:50|unique:buku,barcode',
            'rfid' => 'nullable|string|max:50',
            'tipe' => 'required|in:fisik,digital',
            'stok' => 'required|integer|min:0',
            'sumber_jenis' => 'nullable|string|in:pembelian,hadiah_hibah,penggantian,penggandaan,tukar_menukar,terbitan_sendiri,deposit',
            'sumber_nama' => 'nullable|string|max:255',
            'harga' => 'nullable|numeric|min:0',
            'rak' => 'nullable|string|max:50',
            'kategori_id' => 'required|exists:kategori_buku,id',
            'format' => 'nullable|string|max:50',
            'denda_per_hari' => 'nullable|numeric|min:0',
            'kode_lokasi_perpus' => 'nullable|string|max:50',
            'kode_lokasi_ruang' => 'nullable|string|max:50',
            'kategori_koleksi' => 'nullable|in:buku_paket,fisik,referensi,non_fiksi,umum,paket',
            'tanggal_pengadaan' => 'nullable|date',
            'judul' => 'required|string|max:255',
            'sub_judul' => 'nullable|string|max:255',
            'pernyataan_tanggungjawab' => 'nullable|string',
            'pengarang' => 'nullable|string|max:255',
            'pengarang_badan' => 'nullable|string|max:255',
            'pengarang_tambahan' => 'nullable|string|max:255',
            'edisi' => 'nullable|string|max:100',
            'kota_terbit' => 'nullable|string|max:100',
            'penerbit' => 'nullable|string|max:255',
            'tahun_terbit' => 'nullable|integer|min:1000|max:' . date('Y'),
            'jumlah_halaman' => 'nullable|integer|min:1',
            'ukuran' => 'nullable|string|max:50',
            'isbn' => 'nullable|string|max:20|unique:buku,isbn',
            'issn' => 'nullable|string|max:20',
            'no_ddc' => 'nullable|string|max:50',
            'nomor_panggil' => 'nullable|string|max:50',
            'nomor_panggil_katalog' => 'nullable|string|max:50',
            'bahasa' => 'nullable|string|max:50',
            'deskripsi' => 'nullable|string',
            'kata_kunci' => 'nullable|string|max:500',
            'edisi_serial' => 'nullable|string|max:100',
            'tanggal_terbit_serial' => 'nullable|date',
            'bahan_sertaan' => 'nullable|string|max:50',
            'file_path' => 'nullable|file|mimes:pdf,epub|max:20480',
            'jenis_koleksi' => 'nullable|in:ebook,soal,modul,dokumen',
            'jumlah_lisensi' => 'nullable|integer|min:1',
            'durasi_pinjam_hari' => 'nullable|integer|min:1|max:168',
            'access_level' => 'nullable|in:public,member_only',
            'sampul' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle sampul upload
        if ($request->hasFile('sampul')) {
            $path = $request->file('sampul')->store('sampul-buku', 'public');
            $validatedData['sampul'] = $path;
        }

        // Handle file ebook upload
        if ($request->hasFile('file_path')) {
            $path = $request->file('file_path')->store('ebook', 'public');
            $validatedData['file_path'] = $path;
            $validatedData['file_size'] = $request->file('file_path')->getSize();
            $validatedData['file_type'] = $request->file('file_path')->getMimeType();
            $validatedData['format'] = $request->file('file_path')->getClientOriginalExtension();
        }

        // Set default values
        $validatedData['stok_tersedia'] = $validatedData['stok'];
        $validatedData['stok_dipinjam'] = 0;
        $validatedData['stok_rusak'] = 0;
        $validatedData['stok_hilang'] = 0;
        $validatedData['stok_direservasi'] = 0;
        $validatedData['total_dipinjam'] = 0;
        $validatedData['total_denda'] = 0;
        $validatedData['rating'] = 0;
        $validatedData['views'] = 0;
        $validatedData['created_by'] = Auth::id();

        if (empty($validatedData['kategori_koleksi'])) {
            $validatedData['kategori_koleksi'] = 'umum';
        }

        if (empty($validatedData['tanggal_pengadaan'])) {
            $validatedData['tanggal_pengadaan'] = date('Y-m-d');
        }

        if (empty($validatedData['denda_per_hari'])) {
            $validatedData['denda_per_hari'] = 500;
        }

        $validatedData['status'] = ($validatedData['stok'] > 0) ? 'tersedia' : 'habis';

        // Handle digital book specific settings
        if ($validatedData['tipe'] == 'digital') {
            $validatedData['stok'] = 0;
            $validatedData['stok_tersedia'] = 0;
            
            $jenisKoleksi = $validatedData['jenis_koleksi'] ?? 'ebook';
            $validatedData['jenis_koleksi'] = $jenisKoleksi;
            $validatedData['bisa_download'] = in_array($jenisKoleksi, ['soal', 'modul', 'dokumen']);
            
            if ($validatedData['bisa_download']) {
                $validatedData['jumlah_lisensi'] = 999;
                $validatedData['durasi_pinjam_hari'] = 0;
                $validatedData['akses_digital'] = 'full_access';
                $validatedData['access_level'] = 'public';
                $validatedData['drm_enabled'] = false;
            } else {
                $validatedData['jumlah_lisensi'] = $validatedData['jumlah_lisensi'] ?? 3;
                $validatedData['durasi_pinjam_hari'] = $validatedData['durasi_pinjam_hari'] ?? 7;
                $validatedData['akses_digital'] = 'online_only';
                $validatedData['access_level'] = 'member_only';
                $validatedData['drm_enabled'] = true;
            }
        }

        Buku::create($validatedData);

        return redirect()
            ->route('petugas.buku.index')
            ->with('success', 'Buku "' . $request->judul . '" berhasil ditambahkan.');
    }

    /**
     * Display the specified book (for AJAX).
     */
    public function show($id)
    {
        $buku = Buku::with('kategori')->findOrFail($id);
        return response()->json($buku);
    }

    /**
     * Show the form for editing the specified book.
     */
    public function edit($id)
    {
        $buku = Buku::findOrFail($id);
        $kategori = KategoriBuku::all();
        return view('petugas.pages.buku.edit', compact('buku', 'kategori'));
    }

    /**
     * Update the specified book in storage.
     */
    public function update(Request $request, $id)
    {
        $buku = Buku::findOrFail($id);

        $validatedData = $request->validate([
            'barcode' => 'nullable|string|max:50|unique:buku,barcode,' . $buku->id,
            'rfid' => 'nullable|string|max:50',
            'tipe' => 'required|in:fisik,digital',
            'stok' => 'required|integer|min:0',
            'sumber_jenis' => 'nullable|string|in:pembelian,hadiah_hibah,penggantian,penggandaan,tukar_menukar,terbitan_sendiri,deposit',
            'sumber_nama' => 'nullable|string|max:255',
            'harga' => 'nullable|numeric|min:0',
            'rak' => 'nullable|string|max:50',
            'kategori_id' => 'required|exists:kategori_buku,id',
            'format' => 'nullable|string|max:50',
            'denda_per_hari' => 'nullable|numeric|min:0',
            'kode_lokasi_perpus' => 'nullable|string|max:50',
            'kode_lokasi_ruang' => 'nullable|string|max:50',
            'kategori_koleksi' => 'nullable|in:buku_paket,fisik,referensi,non_fiksi,umum,paket',
            'tanggal_pengadaan' => 'nullable|date',
            'judul' => 'required|string|max:255',
            'sub_judul' => 'nullable|string|max:255',
            'pernyataan_tanggungjawab' => 'nullable|string',
            'pengarang' => 'nullable|string|max:255',
            'pengarang_badan' => 'nullable|string|max:255',
            'pengarang_tambahan' => 'nullable|string|max:255',
            'edisi' => 'nullable|string|max:100',
            'kota_terbit' => 'nullable|string|max:100',
            'penerbit' => 'nullable|string|max:255',
            'tahun_terbit' => 'nullable|integer|min:1000|max:' . date('Y'),
            'jumlah_halaman' => 'nullable|integer|min:1',
            'ukuran' => 'nullable|string|max:50',
            'isbn' => 'nullable|string|max:20|unique:buku,isbn,' . $buku->id,
            'issn' => 'nullable|string|max:20',
            'no_ddc' => 'nullable|string|max:50',
            'nomor_panggil' => 'nullable|string|max:50',
            'nomor_panggil_katalog' => 'nullable|string|max:50',
            'bahasa' => 'nullable|string|max:50',
            'deskripsi' => 'nullable|string',
            'kata_kunci' => 'nullable|string|max:500',
            'edisi_serial' => 'nullable|string|max:100',
            'tanggal_terbit_serial' => 'nullable|date',
            'bahan_sertaan' => 'nullable|string|max:50',
            'file_path' => 'nullable|file|mimes:pdf,epub|max:20480',
            'jenis_koleksi' => 'nullable|in:ebook,soal,modul,dokumen',
            'jumlah_lisensi' => 'nullable|integer|min:1',
            'durasi_pinjam_hari' => 'nullable|integer|min:1|max:168',
            'access_level' => 'nullable|in:public,member_only',
            'sampul' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update stok tersedia
        $stokLama = $buku->stok;
        $stokBaru = $validatedData['stok'];
        $selisihStok = $stokBaru - $stokLama;
        $validatedData['stok_tersedia'] = $buku->stok_tersedia + $selisihStok;
        if ($validatedData['stok_tersedia'] < 0) $validatedData['stok_tersedia'] = 0;

        // Update status
        if ($validatedData['stok_tersedia'] > 0) {
            $validatedData['status'] = 'tersedia';
        } elseif ($buku->stok_dipinjam > 0) {
            $validatedData['status'] = 'dipinjam';
        } else {
            $validatedData['status'] = 'habis';
        }

        // Handle sampul upload
        if ($request->hasFile('sampul')) {
            if ($buku->sampul) Storage::disk('public')->delete($buku->sampul);
            $validatedData['sampul'] = $request->file('sampul')->store('sampul-buku', 'public');
        }

        // Handle file ebook upload
        if ($request->hasFile('file_path')) {
            if ($buku->file_path) Storage::disk('public')->delete($buku->file_path);
            $path = $request->file('file_path')->store('ebook', 'public');
            $validatedData['file_path'] = $path;
            $validatedData['file_size'] = $request->file('file_path')->getSize();
            $validatedData['file_type'] = $request->file('file_path')->getMimeType();
            $validatedData['format'] = $request->file('file_path')->getClientOriginalExtension();
        }

        // Set default values if empty
        if (empty($validatedData['kategori_koleksi'])) {
            $validatedData['kategori_koleksi'] = $buku->kategori_koleksi ?? 'umum';
        }

        if (empty($validatedData['tanggal_pengadaan'])) {
            $validatedData['tanggal_pengadaan'] = $buku->tanggal_pengadaan ?? date('Y-m-d');
        }

        if (empty($validatedData['denda_per_hari'])) {
            $validatedData['denda_per_hari'] = $buku->denda_per_hari ?? 500;
        }

        // Handle digital book specific settings
        if ($validatedData['tipe'] == 'digital') {
            $validatedData['stok'] = 0;
            $validatedData['stok_tersedia'] = 0;
            
            $jenisKoleksi = $validatedData['jenis_koleksi'] ?? $buku->jenis_koleksi;
            $validatedData['jenis_koleksi'] = $jenisKoleksi;
            $validatedData['bisa_download'] = in_array($jenisKoleksi, ['soal', 'modul', 'dokumen']);
            
            if ($validatedData['bisa_download']) {
                $validatedData['jumlah_lisensi'] = 999;
                $validatedData['durasi_pinjam_hari'] = 0;
                $validatedData['akses_digital'] = 'full_access';
                $validatedData['access_level'] = 'public';
            } else {
                $validatedData['jumlah_lisensi'] = $validatedData['jumlah_lisensi'] ?? $buku->jumlah_lisensi ?? 3;
                $validatedData['durasi_pinjam_hari'] = $validatedData['durasi_pinjam_hari'] ?? $buku->durasi_pinjam_hari ?? 7;
                $validatedData['akses_digital'] = $validatedData['akses_digital'] ?? $buku->akses_digital ?? 'online_only';
                $validatedData['access_level'] = 'member_only';
            }
        }

        $validatedData['updated_by'] = Auth::id();
        $buku->update($validatedData);

        return redirect()
            ->route('petugas.buku.index')
            ->with('success', 'Buku "' . $buku->judul . '" berhasil diperbarui.');
    }

    /**
     * Remove the specified book from storage.
     */
    public function destroy($id)
    {
        $buku = Buku::findOrFail($id);

        if ($buku->stok_dipinjam > 0) {
            return redirect()
                ->route('petugas.buku.index')
                ->with('error', 'Buku tidak dapat dihapus karena masih ada ' . $buku->stok_dipinjam . ' eksemplar yang dipinjam.');
        }

        // Delete associated files
        if ($buku->sampul) Storage::disk('public')->delete($buku->sampul);
        if ($buku->file_path) Storage::disk('public')->delete($buku->file_path);
        if ($buku->cover_path) Storage::disk('public')->delete($buku->cover_path);

        $buku->delete();

        return redirect()
            ->route('petugas.buku.index')
            ->with('success', 'Buku "' . $buku->judul . '" berhasil dihapus.');
    }

    /**
     * Scan barcode from uploaded image.
     */
    public function scanBarcode(Request $request)
    {
        $request->validate([
            'barcode_image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            $image = $request->file('barcode_image');
            $tempPath = $image->store('temp_barcode', 'public');
            $fullPath = storage_path('app/public/' . $tempPath);
            
            $barcodeNumber = null;
            
            // Try to read barcode using zbar (if available)
            if (function_exists('shell_exec')) {
                $checkZbar = shell_exec("which zbarimg 2>/dev/null");
                if (!empty(trim($checkZbar))) {
                    $output = shell_exec("zbarimg --raw -q '{$fullPath}' 2>&1");
                    if ($output && trim($output)) {
                        if (preg_match('/[\d]{8,13}/', trim($output), $matches)) {
                            $barcodeNumber = $matches[0];
                        }
                    }
                }
            }
            
            // Clean up temp file
            if (Storage::disk('public')->exists($tempPath)) {
                Storage::disk('public')->delete($tempPath);
            }
            
            if (!$barcodeNumber) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat membaca barcode. Silakan masukkan kode barcode secara manual.'
                ], 422);
            }
            
            if (strlen($barcodeNumber) < 8 || strlen($barcodeNumber) > 13) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barcode yang terdeteksi tidak valid (panjang: ' . strlen($barcodeNumber) . ' digit)'
                ], 422);
            }
            
            $existingBook = Buku::where('barcode', $barcodeNumber)
                ->orWhere('isbn', $barcodeNumber)
                ->first();
            
            return response()->json([
                'success' => true,
                'barcode' => $barcodeNumber,
                'exists' => $existingBook ? true : false,
                'message' => $existingBook 
                    ? "Barcode ditemukan! Buku '{$existingBook->judul}' sudah terdaftar."
                    : "Barcode berhasil discan! Kode: {$barcodeNumber}"
            ]);
            
        } catch (\Exception $e) {
            Log::error('Barcode scan error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import books from Excel file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            $import = new BukuImport();
            Excel::import($import, $request->file('file_excel'));

            $successCount = $import->getSuccessCount();
            $failedCount = $import->getFailedCount();
            $errors = $import->getErrors();
            $generatedBarcodes = $import->getGeneratedBarcodes();

            $message = "✅ Import selesai! {$successCount} buku berhasil diimport.";
            
            if ($failedCount > 0) {
                $message .= " {$failedCount} gagal.";
            }

            if ($successCount > 0 && !empty($generatedBarcodes)) {
                session()->flash('imported_barcodes', $generatedBarcodes);
                session()->flash('import_success_count', $successCount);
                $message .= " Silakan tambahkan gambar cover untuk buku yang baru diimport.";
            }

            if (!empty($errors)) {
                session()->flash('import_errors', array_slice($errors, 0, 20));
            }

            return redirect()
                ->route('petugas.buku.index')
                ->with($failedCount > 0 ? 'warning' : 'success', $message);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }
            
            return back()
                ->with('error', 'Gagal mengimport data. Periksa format file Anda.')
                ->with('import_errors', array_slice($errorMessages, 0, 20));
                
        } catch (\Exception $e) {
            Log::error('Import buku error: ' . $e->getMessage());
            return back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Download template Excel for book import.
     */
    public function downloadTemplate()
    {
        $fileName = 'template_import_buku.csv';
        
        $headers = [
            'judul',
            'sub_judul',
            'pengarang',
            'pengarang_tambahan',
            'penerbit',
            'kota_terbit',
            'tahun_terbit',
            'isbn',
            'issn',
            'no_ddc',
            'nomor_panggil',
            'bahasa',
            'deskripsi',
            'kata_kunci',
            'jumlah_halaman',
            'ukuran',
            'kategori',
            'kategori_koleksi',
            'rak',
            'sumber_jenis',
            'sumber_nama',
            'harga',
            'tanggal_pengadaan',
            'stok',
            'format',
            'tipe'
        ];
        
        $exampleData = [
            [
                'Contoh Judul Buku',
                'Contoh Sub Judul',
                'Nama Pengarang',
                'Pengarang Kedua',
                'Nama Penerbit',
                'Jakarta',
                '2024',
                '978-602-03-1234-5',
                '1234-5678',
                '823',
                '823 CON c',
                'Indonesia',
                'Deskripsi atau sinopsis buku',
                'fiksi, novel, petualangan',
                '250',
                '15 x 23 cm',
                'Fiksi',
                'umum',
                'R-A1',
                'pembelian',
                'Toko Buku Gramedia',
                '100000',
                date('Y-m-d'),
                '1',
                'Cetak',
                'fisik'
            ]
        ];
        
        $callback = function() use ($headers, $exampleData) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Indonesian characters
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Write headers
            fputcsv($file, $headers);
            
            // Write example data
            foreach ($exampleData as $row) {
                fputcsv($file, $row);
            }
            
            // Write 10 empty rows for user input
            for ($i = 0; $i < 10; $i++) {
                $emptyRow = array_fill(0, count($headers), '');
                fputcsv($file, $emptyRow);
            }
            
            fclose($file);
        };
        
        return response()->streamDownload($callback, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}