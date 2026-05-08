<?php

namespace App\Imports;

use App\Models\Buku;
use App\Models\KategoriBuku;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class BukuImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    private $successCount = 0;
    private $failedCount = 0;
    private $errors = [];
    private $generatedBarcodes = [];

    public function collection(Collection $rows)
    {
        DB::beginTransaction();
        
        try {
            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;
                
                try {
                    if (empty($row['judul'])) {
                        $this->failedCount++;
                        $this->errors[] = "Baris {$rowNumber}: Judul kosong, dilewati";
                        continue;
                    }

                    $existing = Buku::where('judul', 'LIKE', $row['judul']);
                    
                    if (!empty($row['pengarang'])) {
                        $existing->where('pengarang', 'LIKE', $row['pengarang']);
                    }
                    
                    if ($existing->exists()) {
                        $this->failedCount++;
                        $this->errors[] = "Baris {$rowNumber}: Buku '{$row['judul']}' sudah ada, dilewati";
                        continue;
                    }

                    $kategoriId = $this->getKategoriId($row['kategori'] ?? null);
                    $barcode = $this->generateUniqueBarcode();
                    $this->generatedBarcodes[] = $barcode;
                    $noInduk = $this->generateNoInduk();

                    $bukuData = [
                        'judul' => trim($row['judul']),
                        'sub_judul' => $row['sub_judul'] ?? null,
                        'pengarang' => $row['pengarang'] ?? null,
                        'pengarang_tambahan' => $row['pengarang_tambahan'] ?? null,
                        'penerbit' => $row['penerbit'] ?? null,
                        'kota_terbit' => $row['kota_terbit'] ?? 'Jakarta',
                        'tahun_terbit' => !empty($row['tahun_terbit']) ? (int)$row['tahun_terbit'] : null,
                        'isbn' => $row['isbn'] ?? null,
                        'issn' => $row['issn'] ?? null,
                        'no_ddc' => $row['no_ddc'] ?? null,
                        'nomor_panggil' => $row['nomor_panggil'] ?? null,
                        'bahasa' => $row['bahasa'] ?? 'Indonesia',
                        'deskripsi' => $row['deskripsi'] ?? null,
                        'kata_kunci' => $row['kata_kunci'] ?? null,
                        'jumlah_halaman' => !empty($row['jumlah_halaman']) ? (int)$row['jumlah_halaman'] : null,
                        'ukuran' => $row['ukuran'] ?? null,
                        'barcode' => $barcode,
                        'no_induk' => $noInduk,
                        'tipe' => 'fisik',
                        'kategori_id' => $kategoriId,
                        'kategori_koleksi' => $row['kategori_koleksi'] ?? 'umum',
                        'rak' => $row['rak'] ?? null,
                        'lokasi' => 'Ruang Baca Umum Perpustakaan Tambang Ilmu',
                        'sumber_jenis' => $row['sumber_jenis'] ?? 'pembelian',
                        'sumber_nama' => $row['sumber_nama'] ?? null,
                        'harga' => !empty($row['harga']) ? (int)preg_replace('/[^0-9]/', '', $row['harga']) : 0,
                        'tanggal_pengadaan' => !empty($row['tanggal_pengadaan']) ? date('Y-m-d', strtotime($row['tanggal_pengadaan'])) : date('Y-m-d'),
                        'denda_per_hari' => 500,
                        'stok' => !empty($row['stok']) ? (int)$row['stok'] : 1,
                        'stok_tersedia' => !empty($row['stok']) ? (int)$row['stok'] : 1,
                        'stok_dipinjam' => 0,
                        'stok_rusak' => 0,
                        'stok_hilang' => 0,
                        'stok_direservasi' => 0,
                        'total_dipinjam' => 0,
                        'total_denda' => 0,
                        'rating' => 0,
                        'views' => 0,
                        'status' => 'tersedia',
                        'created_by' => Auth::id(),
                    ];

                    Buku::create($bukuData);
                    $this->successCount++;

                    if ($this->successCount % 10 === 0) {
                        Log::info("Import progress: {$this->successCount} buku diimport");
                    }

                } catch (\Exception $e) {
                    $this->failedCount++;
                    $this->errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                    Log::error("Import error baris {$rowNumber}: " . $e->getMessage());
                }
            }
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import buku batch error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function getKategoriId($kategoriNama)
    {
        if (empty($kategoriNama)) {
            $default = KategoriBuku::where('nama', 'LIKE', '%Umum%')->first();
            return $default ? $default->id : 1;
        }

        $kategori = KategoriBuku::where('nama', 'LIKE', '%' . $kategoriNama . '%')->first();
        
        if (!$kategori) {
            $kategori = KategoriBuku::create([
                'nama' => $kategoriNama,
                'deskripsi' => 'Kategori dari import Excel',
                'created_by' => Auth::id(),
            ]);
            Log::info("Kategori baru dibuat: {$kategoriNama} (ID: {$kategori->id})");
        }
        
        return $kategori->id;
    }

    private function generateUniqueBarcode()
    {
        $prefix = 'BK';
        $year = date('y');
        $month = date('m');
        
        do {
            $random = strtoupper(Str::random(6));
            $barcode = $prefix . $year . $month . $random;
        } while (Buku::where('barcode', $barcode)->exists());
        
        return $barcode;
    }

    private function generateNoInduk()
    {
        $year = date('Y');
        $lastBuku = Buku::orderBy('id', 'desc')->first();
        $lastNumber = $lastBuku ? (int)substr($lastBuku->no_induk ?? '0000', -4) : 0;
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        
        return "{$year}.{$newNumber}";
    }

    public function rules(): array
    {
        return [
            'judul' => 'required|string|max:255',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'judul.required' => 'Judul buku harus diisi',
        ];
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getFailedCount(): int
    {
        return $this->failedCount;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getGeneratedBarcodes(): array
    {
        return $this->generatedBarcodes;
    }
}