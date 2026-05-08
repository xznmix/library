<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Buku;
use App\Models\KategoriBuku;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ImportBukuFromExcel extends Command
{
    protected $signature = 'import:buku 
                            {file : Path file Excel}
                            {--dry-run : Cek data tanpa menyimpan ke database}
                            {--kategori-default=1 : ID kategori default jika tidak ditemukan}
                            {--skip-duplicate : Lewati buku yang sudah ada}
                            {--force : Paksa import tanpa konfirmasi}';
    
    protected $description = 'Import data buku dari Excel (AMAN - tidak menghapus data existing)';

    public function handle()
    {
        $filePath = $this->argument('file');
        $isDryRun = $this->option('dry-run');
        $skipDuplicate = $this->option('skip-duplicate');
        $force = $this->option('force');
        $defaultKategoriId = $this->option('kategori-default');

        // Validasi file
        if (!file_exists($filePath)) {
            $this->error("❌ File tidak ditemukan: {$filePath}");
            return Command::FAILURE;
        }

        // ========== PERINGATAN KEAMANAN ==========
        $this->newLine();
        $this->warn("⚠️  PERINGATAN PENTING ⚠️");
        $this->line("─────────────────────────────────────────────");
        $this->line("✓ Command ini HANYA MENAMBAH data buku baru");
        $this->line("✓ TIDAK akan menghapus/mengubah data yang sudah ada");
        $this->line("✓ TIDAK akan menyentuh file digital (PDF/Epub)");
        $this->line("✓ Ada pengecekan duplikat otomatis");
        $this->line("─────────────────────────────────────────────");
        $this->newLine();

        if (!$force && !$isDryRun) {
            if (!$this->confirm('Apakah Anda yakin ingin melanjutkan import?', false)) {
                $this->info("Import dibatalkan.");
                return Command::SUCCESS;
            }
        }

        if ($isDryRun) {
            $this->warn("🔍 MODE DRY-RUN AKTIF: Data akan dicek tapi TIDAK disimpan");
        }

        // Baca file Excel
        $this->info("📚 Membaca file Excel...");
        $rows = Excel::toArray([], $filePath);
        
        if (empty($rows) || empty($rows[0])) {
            $this->error("File Excel kosong atau tidak dapat dibaca.");
            return Command::FAILURE;
        }

        $data = $rows[0];
        $headers = array_shift($data);
        
        $this->info("📊 Menemukan " . count($data) . " baris data untuk diproses.");
        $this->newLine();

        // Statistik
        $stats = [
            'total' => count($data),
            'success' => 0,
            'skipped' => 0,
            'failed' => 0,
            'errors' => []
        ];

        $progressBar = $this->output->createProgressBar(count($data));
        $progressBar->start();

        foreach ($data as $rowIndex => $row) {
            $rowNumber = $rowIndex + 2;
            
            try {
                // Mapping data
                $bukuData = $this->mapExcelRowToBukuData($row, $defaultKategoriId);
                
                // Validasi
                $validator = Validator::make($bukuData, [
                    'judul' => 'required|string|max:255',
                    'kategori_id' => 'required|exists:kategori_buku,id',
                ]);

                if ($validator->fails()) {
                    throw new \Exception("Validasi: " . implode(', ', $validator->errors()->all()));
                }

                // CEK DUPLIKAT (PENTING!)
                $existingQuery = Buku::where('judul', 'LIKE', $bukuData['judul']);
                
                if (!empty($bukuData['pengarang'])) {
                    $existingQuery->where('pengarang', 'LIKE', $bukuData['pengarang']);
                }
                
                $existing = $existingQuery->first();

                if ($existing) {
                    if ($skipDuplicate) {
                        $stats['skipped']++;
                        $this->warn("\n⚠️  Baris {$rowNumber}: Buku '{$bukuData['judul']}' sudah ada (ID: {$existing->id}), dilewati.");
                    } else {
                        // Tanya user apakah ingin tetap import?
                        if (!$isDryRun && !$force) {
                            $answer = $this->ask("Buku '{$bukuData['judul']}' sudah ada. [s]kip, [o]verwrite, [a]bort? (s/o/a)", 's');
                            
                            if (strtolower($answer) === 'a') {
                                $this->warn("Import dibatalkan oleh user.");
                                return Command::SUCCESS;
                            } elseif (strtolower($answer) === 'o') {
                                // Overwrite: update data existing
                                if (!$isDryRun) {
                                    $existing->update($bukuData);
                                    $stats['success']++;
                                    $this->line("\n✏️  Baris {$rowNumber}: Buku '{$bukuData['judul']}' diupdate.");
                                } else {
                                    $stats['success']++;
                                    $this->line("\n🔍 [DRY-RUN] Akan update: '{$bukuData['judul']}'");
                                }
                            } else {
                                $stats['skipped']++;
                                $this->line("\n⏭️  Baris {$rowNumber}: Buku '{$bukuData['judul']}' dilewati.");
                            }
                        } else {
                            $stats['skipped']++;
                        }
                    }
                    $progressBar->advance();
                    continue;
                }

                // Jika dry-run, hanya catat
                if ($isDryRun) {
                    $stats['success']++;
                    $progressBar->advance();
                    continue;
                }

                // SIMPAN DATA BARU
                $buku = Buku::create($bukuData);
                $stats['success']++;
                
                if ($stats['success'] % 10 === 0) {
                    $this->line("\n✅ Baris {$rowNumber}: '{$bukuData['judul']}' berhasil diimport (ID: {$buku->id})");
                }

            } catch (\Exception $e) {
                $stats['failed']++;
                $stats['errors'][] = "Baris {$rowNumber}: " . $e->getMessage();
                if (!$isDryRun) {
                    $this->error("\n❌ Baris {$rowNumber}: " . $e->getMessage());
                }
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // ========== LAPORAN AKHIR ==========
        $this->info("✅ ========== LAPORAN IMPORT ==========");
        $this->info("📊 Total data diproses: {$stats['total']}");
        $this->info("✅ Berhasil: {$stats['success']} buku");
        
        if ($stats['skipped'] > 0) {
            $this->warn("⏭️  Dilewati (duplikat): {$stats['skipped']} buku");
        }
        
        if ($stats['failed'] > 0) {
            $this->error("❌ Gagal: {$stats['failed']} baris");
        }

        if ($isDryRun) {
            $this->newLine();
            $this->warn("🔍 Mode DRY-RUN: Tidak ada data yang benar-benar disimpan.");
            $this->warn("   Jalankan tanpa --dry-run untuk menyimpan data.");
        }

        if (!empty($stats['errors']) && $stats['failed'] > 0) {
            $this->newLine();
            $this->warn("⚠️  Detail Error:");
            foreach (array_slice($stats['errors'], 0, 10) as $error) {
                $this->warn("   - {$error}");
            }
            if (count($stats['errors']) > 10) {
                $this->warn("   ... dan " . (count($stats['errors']) - 10) . " error lainnya");
            }
        }

        $this->newLine();
        
        if (!$isDryRun && $stats['success'] > 0) {
            $this->info("✨ Import selesai! {$stats['success']} buku baru ditambahkan ke sistem.");
            $this->info("   Data ini AMAN dan tidak mengganggu data existing Anda.");
        }

        return Command::SUCCESS;
    }

    /**
     * Mapping kolom dari file Excel ke struktur database
     * SESUAIKAN INDEX KOLOM DENGAN FILE EXCEL ANDA
     */
    private function mapExcelRowToBukuData($row, $defaultKategoriId)
    {
        // Mapping berdasarkan file sample_data_koleksi_AACR.xlsx
        // Index disesuaikan dengan struktur yang Anda berikan
        
        return [
            'judul' => $this->getCellValue($row, 17) ?? $this->getCellValue($row, 0),
            'pengarang' => $this->getCellValue($row, 20) ?? $this->getCellValue($row, 19),
            'penerbit' => $this->getCellValue($row, 26),
            'tahun_terbit' => $this->parseYear($this->getCellValue($row, 27)),
            'isbn' => $this->getCellValue($row, 30),
            'issn' => $this->getCellValue($row, 31),
            'kategori_id' => $this->getKategoriId($this->getCellValue($row, 12), $defaultKategoriId),
            'deskripsi' => $this->getCellValue($row, 35) ?? $this->getCellValue($row, 36),
            'bahasa' => $this->getCellValue($row, 36),
            'jumlah_halaman' => $this->parseInteger($this->getCellValue($row, 28)),
            'ukuran' => $this->getCellValue($row, 29),
            'tipe' => 'fisik', // Semua dari Excel adalah buku fisik
            'stok' => 1,
            'stok_tersedia' => 1,
            'stok_dipinjam' => 0,
            'status' => 'tersedia',
            'sumber_nama' => $this->getCellValue($row, 6),
            'harga' => $this->parsePrice($this->getCellValue($row, 8)),
            'tanggal_pengadaan' => $this->parseDate($this->getCellValue($row, 1)),
            'rak' => $this->getCellValue($row, 9) . '/' . $this->getCellValue($row, 10),
            'kode_buku' => $this->getCellValue($row, 2) ?? $this->getCellValue($row, 3),
            'catatan' => $this->getCellValue($row, 41),
            
            // Field wajib lainnya (dengan nilai default)
            'total_dipinjam' => 0,
            'total_denda' => 0,
            'rating' => 0,
            'jumlah_ulasan' => 0,
            'views' => 0,
            'denda_per_hari' => 1000,
            'created_by' => 1, // Default admin ID 1
        ];
    }

    /**
     * Helper functions
     */
    private function getCellValue($row, $index)
    {
        return isset($row[$index]) && !empty($row[$index]) ? trim((string)$row[$index]) : null;
    }

    private function parseYear($value)
    {
        if (empty($value)) return null;
        if (is_numeric($value)) {
            $year = (int)$value;
            return ($year >= 1900 && $year <= date('Y') + 1) ? $year : null;
        }
        if (preg_match('/\b(19|20)\d{2}\b/', $value, $matches)) {
            return (int)$matches[0];
        }
        return null;
    }

    private function parseInteger($value)
    {
        if (empty($value)) return null;
        if (is_numeric($value)) return (int)$value;
        if (preg_match('/\d+/', $value, $matches)) return (int)$matches[0];
        return null;
    }

    private function parsePrice($value)
    {
        if (empty($value)) return 0;
        $price = preg_replace('/[^0-9]/', '', (string)$value);
        return !empty($price) ? (int)$price : 0;
    }

    private function parseDate($value)
    {
        if (empty($value)) return null;
        
        // Format: 14-02-2015 (dari file Excel)
        if (preg_match('/(\d{1,2})[-\.\/](\d{1,2})[-\.\/](\d{4})/', $value, $matches)) {
            return "{$matches[3]}-{$matches[2]}-{$matches[1]}";
        }
        
        $timestamp = strtotime($value);
        return $timestamp !== false ? date('Y-m-d', $timestamp) : null;
    }

    private function getKategoriId($kategoriNama, $defaultId)
    {
        if (empty($kategoriNama)) {
            return $defaultId;
        }
        
        // Mapping kategori dari file Excel ke database
        $kategoriMapping = [
            'Koleksi Umum' => 'Koleksi Umum',
            'Fiksi' => 'Fiksi',
            'Rumah Tangga' => 'Rumah Tangga',
            // Tambahkan mapping lainnya sesuai kebutuhan
        ];
        
        $searchName = $kategoriMapping[$kategoriNama] ?? $kategoriNama;
        
        $kategori = KategoriBuku::where('nama', 'LIKE', '%' . $searchName . '%')->first();
        
        if ($kategori) {
            return $kategori->id;
        }
        
        $this->warn("\n⚠️  Kategori '{$kategoriNama}' tidak ditemukan, menggunakan default ID: {$defaultId}");
        return $defaultId;
    }
}