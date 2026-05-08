<?php

namespace App\Exports;

use App\Models\Peminjaman;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Carbon\Carbon;

class DendaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnFormatting
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Peminjaman::with(['user', 'buku'])
            ->where('denda', '>', 0);
        
        if ($this->request->filled('start_date')) {
            $query->whereDate('tanggal_pengembalian', '>=', $this->request->start_date);
        }
        
        if ($this->request->filled('end_date')) {
            $query->whereDate('tanggal_pengembalian', '<=', $this->request->end_date);
        }
        
        return $query->orderBy('tanggal_pengembalian', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'TANGGAL',
            'NAMA ANGGOTA',
            'JUDUL BUKU',
            'TGL KEMBALI',
            'TGL JATUH TEMPO',
            'KETERLAMBATAN',
            'DENDA',
            'STATUS PEMBAYARAN'
        ];
    }

    public function map($item): array
    {
        static $no = 0;
        $no++;
        
        $jatuhTempo = Carbon::parse($item->tgl_jatuh_tempo);
        $kembali = Carbon::parse($item->tanggal_pengembalian);
        $terlambat = $kembali->diffInDays($jatuhTempo);
        
        return [
            $no,
            Carbon::parse($item->tanggal_pinjam)->format('d/m/Y'),
            $item->user->name ?? '-',
            $item->buku->judul ?? '-',
            Carbon::parse($item->tanggal_pengembalian)->format('d/m/Y'),
            Carbon::parse($item->tgl_jatuh_tempo)->format('d/m/Y'),
            $terlambat . ' hari',
            $item->denda,
            $item->status_denda ?? 'Belum Dibayar'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'DC2626'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:I' . $lastRow)->applyFromArray([
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

        // Color for denda column
        $sheet->getStyle('H2:H' . $lastRow)->applyFromArray([
            'font' => [
                'color' => ['rgb' => 'DC2626'],
                'bold' => true,
            ],
        ]);

        return [];
    }

    public function columnFormats(): array
    {
        return [
            'H' => '"Rp" #,##0',
        ];
    }

    public function title(): string
    {
        return 'Laporan Denda';
    }
}