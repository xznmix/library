<?php

namespace App\Exports;

use App\Models\Kunjungan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KunjunganExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $request;
    protected $year;

    public function __construct($request)
    {
        $this->request = $request;
        $this->year = $request->get('year', Carbon::now()->year);
    }

    public function collection()
    {
        $kunjungan = Kunjungan::select(
                DB::raw('MONTH(tanggal) as bulan'),
                DB::raw('COUNT(*) as total'),
                DB::raw('DAYOFWEEK(tanggal) as hari')
            )
            ->whereYear('tanggal', $this->year)
            ->groupBy('bulan', 'hari')
            ->orderBy('bulan')
            ->orderBy('hari')
            ->get()
            ->groupBy('bulan')
            ->map(function ($items, $bulan) {
                $bulanList = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ];
                
                $hariList = [
                    1 => 'Minggu', 2 => 'Senin', 3 => 'Selasa', 4 => 'Rabu',
                    5 => 'Kamis', 6 => 'Jumat', 7 => 'Sabtu'
                ];
                
                $totalBulan = $items->sum('total');
                $rataHari = $items->avg('total');
                
                $detailHari = [];
                foreach ($items as $item) {
                    $detailHari[] = $hariList[$item->hari] . ': ' . $item->total;
                }
                
                return (object)[
                    'bulan' => $bulanList[$bulan],
                    'total' => $totalBulan,
                    'rata_rata' => round($rataHari, 1),
                    'detail' => implode(', ', $detailHari)
                ];
            });
        
        return collect($kunjungan->values());
    }

    public function headings(): array
    {
        return [
            'BULAN',
            'TOTAL KUNJUNGAN',
            'RATA-RATA PER HARI',
            'DETAIL KUNJUNGAN'
        ];
    }

    public function map($item): array
    {
        return [
            $item->bulan,
            $item->total,
            $item->rata_rata,
            $item->detail
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '7C3AED'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        foreach (range('A', 'D') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:D' . $lastRow)->applyFromArray([
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
        return 'Kunjungan Tahun ' . $this->year;
    }
}