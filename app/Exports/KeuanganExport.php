<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class KeuanganExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Peminjam',
            'Buku',
            'Jumlah Denda',
            'Status',
            'Tanggal Bayar',
            'Tanggal Dibuat'
        ];
    }

    public function map($denda): array
    {
        static $rowNumber = 0;
        $rowNumber++;
        
        return [
            $rowNumber,
            $denda->peminjaman->user->name ?? '-',
            $denda->peminjaman->buku->judul ?? '-',
            'Rp ' . number_format($denda->jumlah_denda, 0, ',', '.'),
            $denda->status == 'paid' ? 'Lunas' : 'Belum Bayar',
            $denda->tanggal_bayar ? $denda->tanggal_bayar->format('d/m/Y H:i') : '-',
            $denda->created_at->format('d/m/Y H:i')
        ];
    }
}