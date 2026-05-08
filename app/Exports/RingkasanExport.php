<?php

namespace App\Exports;

use App\Models\Peminjaman;
use App\Models\User;
use App\Models\Buku;
use App\Models\Kunjungan;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class RingkasanExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function array(): array
    {
        $totalPinjam = Peminjaman::count();
        $totalDenda = Peminjaman::sum('denda');
        $totalAnggota = User::whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])->count();
        $totalBuku = Buku::count();
        $totalKunjungan = Kunjungan::count();
        
        $anggotaAktif = User::whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])
            ->where('status_anggota', 'active')
            ->count();
        
        $peminjamanHariIni = Peminjaman::whereDate('tanggal_pinjam', Carbon::today())->count();
        $pengembalianHariIni = Peminjaman::whereDate('tanggal_pengembalian', Carbon::today())->count();
        
        $bukuDipinjam = Peminjaman::where('status_pinjam', 'dipinjam')->count();
        $bukuTerlambat = Peminjaman::where('status_pinjam', 'terlambat')->count();
        
        return [
            ['METRIK', 'NILAI'],
            ['Total Peminjaman', $totalPinjam],
            ['Total Denda', 'Rp ' . number_format($totalDenda, 0, ',', '.')],
            ['Total Anggota', $totalAnggota],
            ['Anggota Aktif', $anggotaAktif],
            ['Total Buku', $totalBuku],
            ['Total Kunjungan', $totalKunjungan],
            ['Peminjaman Hari Ini', $peminjamanHariIni],
            ['Pengembalian Hari Ini', $pengembalianHariIni],
            ['Buku Sedang Dipinjam', $bukuDipinjam],
            ['Buku Terlambat', $bukuTerlambat],
            ['Tanggal Cetak', Carbon::now()->format('d/m/Y H:i:s')],
        ];
    }

    public function headings(): array
    {
        return ['RINGKASAN LAPORAN PERPUSTAKAAN', ''];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge header
        $sheet->mergeCells('A1:B1');
        
        // Style header
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
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

        // Style for metric rows
        $sheet->getStyle('A2:B13')->applyFromArray([
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

        // Bold for metric names
        $sheet->getStyle('A2:A13')->applyFromArray([
            'font' => ['bold' => true],
        ]);

        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(25);

        return [];
    }

    public function title(): string
    {
        return 'Ringkasan';
    }
}