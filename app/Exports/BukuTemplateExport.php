<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class BukuTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
     * Data template untuk contoh import
     */
    public function array(): array
    {
        return [
            [
                'Bumi Manusia',
                '',
                'Pramoedya Ananta Toer',
                '',
                'Hasta Mitra',
                'Jakarta',
                '1980',
                '9789793064425',
                '',
                '899.221',
                '899.221 PRA b',
                'Indonesia',
                'Novel sejarah tentang perjuangan bangsa Indonesia',
                'sastra, sejarah, perjuangan, kolonial',
                '400',
                '20 cm',
                'Fiksi',
                'umum',
                'R-A1',
                'hadiah_hibah',
                'Yayasan Idayu',
                '50000',
                '2024-01-15',
                '5',
                'Cetak',
                'fisik',
            ],
            [
                'Laskar Pelangi',
                'Sebuah Novel',
                'Andrea Hirata',
                '',
                'Bentang Pustaka',
                'Yogyakarta',
                '2005',
                '9789793062797',
                '',
                '899.221',
                '899.221 AND l',
                'Indonesia',
                'Kisah inspiratif perjuangan anak-anak Belitung',
                'pendidikan, motivasi, perjuangan, inspirasi',
                '529',
                '20 cm',
                'Fiksi',
                'umum',
                'R-B2',
                'pembelian',
                'Toko Buku Gramedia',
                '85000',
                '2024-01-20',
                '3',
                'Cetak',
                'fisik',
            ],
            [
                'C# Untuk Pemula',
                'Panduan Belajar C#',
                'Joko Widodo',
                'Budi Santoso',
                'Elex Media Komputindo',
                'Jakarta',
                '2023',
                '9786230023456',
                '',
                '005.133',
                '005.133 WID c',
                'Indonesia',
                'Buku panduan belajar pemrograman C# dari dasar',
                'pemrograman, C#, programming, coding',
                '350',
                '19 x 23 cm',
                'Komputer',
                'referensi',
                'R-C3',
                'pembelian',
                'Toko Buku Online',
                '120000',
                '2024-02-01',
                '2',
                'Cetak',
                'fisik',
            ],
            [
                'Belajar Laravel 11',
                'Dari Dasar hingga Mahir',
                'Sandhika Galih',
                '',
                'WPU Publishing',
                'Bandung',
                '2024',
                '9786239876500',
                '',
                '005.276',
                '005.276 GAL b',
                'Indonesia',
                'Panduan lengkap belajar Laravel 11',
                'laravel, php, framework, web development',
                '280',
                '17 x 24 cm',
                'Teknologi',
                'umum',
                'Digital',
                'pembelian',
                'E-Commerce',
                '150000',
                '2024-03-01',
                '0',
                'PDF',
                'digital',
            ],
        ];
    }

    /**
     * Header kolom untuk file Excel
     */
    public function headings(): array
    {
        return [
            'judul*',
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
            'tipe',
        ];
    }

    /**
     * Styling untuk sheet Excel
     */
    public function styles(Worksheet $sheet)
    {
        // Style untuk header
        $sheet->getStyle('A1:Z1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5'], // Indigo color
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Style untuk baris contoh data
        $sheet->getStyle('A2:Z5')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F3F4F6'], // Light gray
            ],
        ]);

        // Set height untuk header
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Auto-size columns
        foreach (range('A', 'Z') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Freeze header row
        $sheet->freezePane('A2');

        // Add Sheet baru untuk petunjuk
        $instructionSheet = $sheet->getParent()->createSheet();
        $instructionSheet->setTitle('Petunjuk');
        
        $instructionSheet->setCellValue('A1', '📚 PETUNJUK IMPORT BUKU');
        $instructionSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        
        $instructions = [
            '',
            '═══════════════════════════════════════════════════════════════',
            'KOLOM YANG WAJIB DIISI (ditandai *):',
            '═══════════════════════════════════════════════════════════════',
            '• judul - Judul buku (WAJIB DIISI)',
            '',
            '═══════════════════════════════════════════════════════════════',
            'KATEGORI KOLEKSI (kolom R):',
            '═══════════════════════════════════════════════════════════════',
            '• buku_paket - Buku Paket',
            '• fisik - Koleksi Fisik',
            '• referensi - Koleksi Referensi',
            '• non_fiksi - Koleksi Non Fiksi',
            '• umum - Koleksi Umum',
            '• paket - Koleksi Paket',
            '',
            '═══════════════════════════════════════════════════════════════',
            'SUMBER JENIS (kolom T):',
            '═══════════════════════════════════════════════════════════════',
            '• pembelian - Pembelian',
            '• hadiah_hibah - Hadiah/Hibah',
            '• penggantian - Penggantian',
            '• penggandaan - Penggandaan',
            '• tukar_menukar - Tukar Menukar',
            '• terbitan_sendiri - Terbitan Sendiri',
            '• deposit - Deposit (UU No.4/1990)',
            '',
            '═══════════════════════════════════════════════════════════════',
            'TIPE (kolom Z):',
            '═══════════════════════════════════════════════════════════════',
            '• fisik - Buku Fisik',
            '• digital - Buku Digital (E-book)',
            '',
            '═══════════════════════════════════════════════════════════════',
            'FORMAT (kolom Y):',
            '═══════════════════════════════════════════════════════════════',
            '• Cetak - Buku Cetak',
            '• PDF - File PDF',
            '• EPUB - File EPUB',
            '',
            '═══════════════════════════════════════════════════════════════',
            'CATATAN PENTING:',
            '═══════════════════════════════════════════════════════════════',
            '1. Baris pertama (header) JANGAN DIHAPUS',
            '2. Barcode akan digenerate otomatis oleh sistem',
            '3. Buku dengan judul dan pengarang yang sama akan dilewati',
            '4. Stok default = 1 jika tidak diisi',
            '5. Untuk buku digital, stok diisi 0',
            '6. Tanggal pengadaan format: YYYY-MM-DD',
            '7. Harga tanpa titik atau koma (contoh: 50000)',
            '',
            '═══════════════════════════════════════════════════════════════',
            'CONTOH DATA:',
            '═══════════════════════════════════════════════════════════════',
            'Lihat sheet "Template" untuk contoh data yang sudah disediakan',
            '',
            'Silakan copy baris contoh dan edit sesuai kebutuhan Anda.',
        ];
        
        $row = 3;
        foreach ($instructions as $instruction) {
            $instructionSheet->setCellValue('A' . $row, $instruction);
            $row++;
        }
        
        $instructionSheet->getColumnDimension('A')->setWidth(80);
        $instructionSheet->getStyle('A1:A' . ($row - 1))->getAlignment()->setWrapText(true);

        // Set active sheet ke template utama
        $sheet->getParent()->setActiveSheetIndex(0);
        
        return $sheet;
    }
}