<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Carbon\Carbon;

class LaporanGabunganExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        $sheets = [];
        
        // Sheet 1: Ringkasan
        $sheets[] = new RingkasanExport();
        
        // Sheet 2: Peminjaman
        $sheets[] = new PeminjamanExport(request());
        
        // Sheet 3: Anggota
        $sheets[] = new AnggotaExport(request());
        
        // Sheet 4: Buku
        $sheets[] = new BukuExport(request());
        
        // Sheet 5: Kunjungan
        $sheets[] = new KunjunganExport(request());
        
        // Sheet 6: Denda
        $sheets[] = new DendaExport(request());
        
        return $sheets;
    }
}