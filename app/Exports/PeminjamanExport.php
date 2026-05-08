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
use Carbon\Carbon;

class PeminjamanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Peminjaman::with(['user', 'buku']);
        
        if ($this->request->filled('start_date')) {
            $query->whereDate('tanggal_pinjam', '>=', $this->request->start_date);
        }
        
        if ($this->request->filled('end_date')) {
            $query->whereDate('tanggal_pinjam', '<=', $this->request->end_date);
        }
        
        if ($this->request->filled('status')) {
            $query->where('status_pinjam', $this->request->status);
        }
        
        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'TANGGAL PINJAM',
            'NAMA ANGGOTA',
            'JENIS ANGGOTA',
            'JUDUL BUKU',
            'TANGGAL KEMBALI',
            'STATUS',
            'DENDA',
            'KETERANGAN'
        ];
    }

    public function map($peminjaman): array
    {
        static $no = 0;
        $no++;
        
        $status = '';
        $keterangan = '';
        
        if ($peminjaman->status_pinjam == 'dipinjam') {
            $status = 'Dipinjam';
            $jatuhTempo = Carbon::parse($peminjaman->tgl_jatuh_tempo);
            if (now() > $jatuhTempo) {
                $hariTerlambat = now()->diffInDays($jatuhTempo);
                $keterangan = "Terlambat {$hariTerlambat} hari";
            }
        } elseif ($peminjaman->status_pinjam == 'terlambat') {
            $status = 'Terlambat';
            $jatuhTempo = Carbon::parse($peminjaman->tgl_jatuh_tempo);
            $kembali = Carbon::parse($peminjaman->tanggal_pengembalian);
            $hariTerlambat = $kembali->diffInDays($jatuhTempo);
            $keterangan = "Terlambat {$hariTerlambat} hari";
        } else {
            $status = 'Dikembalikan';
            $keterangan = 'Tepat waktu';
        }
        
        return [
            $no,
            Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y'),
            $peminjaman->user->name ?? '-',
            ucfirst($peminjaman->user->jenis ?? '-'),
            $peminjaman->buku->judul ?? '-',
            $peminjaman->tanggal_pengembalian ? Carbon::parse($peminjaman->tanggal_pengembalian)->format('d/m/Y') : '-',
            $status,
            'Rp ' . number_format($peminjaman->denda, 0, ',', '.'),
            $keterangan
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style for header
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Auto-size columns
        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Border for all cells
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

        return [];
    }

    public function title(): string
    {
        return 'Laporan Peminjaman';
    }
}