<?php

namespace App\Exports;

use App\Models\Buku;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class BukuExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Buku::with('kategori')
            ->withCount('peminjaman');
        
        if ($this->request->filled('kategori')) {
            $query->where('kategori_id', $this->request->kategori);
        }
        
        if ($this->request->filled('tipe')) {
            $query->where('tipe', $this->request->tipe);
        }
        
        return $query->orderBy('peminjaman_count', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'KODE BUKU',
            'JUDUL',
            'PENGARANG',
            'PENERBIT',
            'TAHUN',
            'KATEGORI',
            'TIPE',
            'STOK',
            'TERSEDIA',
            'DIPINJAM',
            'POPULARITAS'
        ];
    }

    public function map($buku): array
    {
        static $no = 0;
        $no++;
        
        $popularitas = 'Rendah';
        if ($buku->peminjaman_count > 20) {
            $popularitas = 'Tinggi';
        } elseif ($buku->peminjaman_count > 10) {
            $popularitas = 'Sedang';
        }
        
        return [
            $no,
            $buku->kode_buku ?? '-',
            $buku->judul,
            $buku->pengarang ?? '-',
            $buku->penerbit ?? '-',
            $buku->tahun_terbit ?? '-',
            $buku->kategori->nama ?? '-',
            ucfirst($buku->tipe),
            $buku->stok,
            $buku->stok_tersedia,
            $buku->peminjaman_count ?? 0,
            $popularitas
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:L1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2563EB'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        foreach (range('A', 'L') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:L' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DDDDDD'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        return [];
    }

    public function title(): string
    {
        return 'Laporan Buku';
    }
}