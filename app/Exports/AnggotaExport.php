<?php

namespace App\Exports;

use App\Models\User;
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

class AnggotaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = User::whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])
            ->withCount('peminjaman');
        
        if ($this->request->filled('status')) {
            $query->where('status_anggota', $this->request->status);
        }
        
        if ($this->request->filled('jenis')) {
            $query->where('jenis', $this->request->jenis);
        }
        
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'NO. ANGGOTA',
            'NAMA LENGKAP',
            'EMAIL',
            'JENIS',
            'KELAS/JURUSAN',
            'STATUS',
            'TANGGAL DAFTAR',
            'MASA BERLAKU',
            'TOTAL PINJAM',
            'LAST LOGIN'
        ];
    }

    public function map($anggota): array
    {
        static $no = 0;
        $no++;
        
        $statusText = '';
        switch ($anggota->status_anggota) {
            case 'active':
                $statusText = 'Aktif';
                break;
            case 'pending':
                $statusText = 'Pending';
                break;
            default:
                $statusText = 'Nonaktif';
        }
        
        return [
            $no,
            $anggota->no_anggota ?? '-',
            $anggota->name,
            $anggota->email,
            ucfirst($anggota->jenis ?? 'Umum'),
            $anggota->kelas ?? '-',
            $statusText,
            $anggota->tanggal_daftar ? Carbon::parse($anggota->tanggal_daftar)->format('d/m/Y') : '-',
            $anggota->masa_berlaku ? Carbon::parse($anggota->masa_berlaku)->format('d/m/Y') : '-',
            $anggota->peminjaman_count ?? 0,
            $anggota->last_login ? Carbon::parse($anggota->last_login)->format('d/m/Y H:i') : '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '059669'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        foreach (range('A', 'K') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:K' . $lastRow)->applyFromArray([
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
        return 'Laporan Anggota';
    }
}