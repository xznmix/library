<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class KinerjaExport implements WithMultipleSheets
{
    protected $petugasStats;
    protected $totalAnggota;
    protected $totalPeminjaman;
    protected $totalKunjungan;

    public function __construct($petugasStats, $totalAnggota, $totalPeminjaman, $totalKunjungan)
    {
        $this->petugasStats = $petugasStats;
        $this->totalAnggota = $totalAnggota;
        $this->totalPeminjaman = $totalPeminjaman;
        $this->totalKunjungan = $totalKunjungan;
    }

    public function sheets(): array
    {
        $sheets = [];
        
        // Sheet 1: Ringkasan Kinerja
        $sheets[] = new KinerjaSummarySheet($this->totalAnggota, $this->totalPeminjaman, $this->totalKunjungan);
        
        // Sheet 2: Statistik Petugas
        $sheets[] = new PetugasStatsSheet($this->petugasStats);
        
        return $sheets;
    }
}

// Summary Sheet Class
class KinerjaSummarySheet implements FromArray, WithHeadings
{
    protected $totalAnggota;
    protected $totalPeminjaman;
    protected $totalKunjungan;

    public function __construct($totalAnggota, $totalPeminjaman, $totalKunjungan)
    {
        $this->totalAnggota = $totalAnggota;
        $this->totalPeminjaman = $totalPeminjaman;
        $this->totalKunjungan = $totalKunjungan;
    }

    public function array(): array
    {
        return [
            ['Total Anggota', $this->totalAnggota],
            ['Total Peminjaman (Tahun Ini)', $this->totalPeminjaman],
            ['Total Kunjungan (Tahun Ini)', $this->totalKunjungan],
        ];
    }

    public function headings(): array
    {
        return ['Metrik', 'Jumlah'];
    }
}

// Petugas Stats Sheet Class
class PetugasStatsSheet implements FromArray, WithHeadings
{
    protected $petugasStats;

    public function __construct($petugasStats)
    {
        $this->petugasStats = $petugasStats;
    }

    public function array(): array
    {
        $data = [];
        foreach ($this->petugasStats as $index => $petugas) {
            $data[] = [
                $index + 1,
                $petugas->name,
                $petugas->email,
                $petugas->total_peminjaman ?? 0
            ];
        }
        return $data;
    }

    public function headings(): array
    {
        return ['No', 'Nama Petugas', 'Email', 'Total Peminjaman (Tahun Ini)'];
    }
}