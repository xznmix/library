<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'Email',
            'NISN/NIK',
            'Role',
            'Status',
            'Tanggal Daftar',
            'Terakhir Login'
        ];
    }

    public function map($user): array
    {
        static $no = 0;
        $no++;
        
        return [
            $no,
            $user->name,
            $user->email ?? '-',
            $user->nisn_nik ?? '-',
            ucfirst($user->role),
            $user->status == 'active' ? 'Aktif' : 'Nonaktif',
            $user->created_at->format('d/m/Y'),
            $user->updated_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            'A1:H1' => ['fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5']
            ]],
        ];
    }
}