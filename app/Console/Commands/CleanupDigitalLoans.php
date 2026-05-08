<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PeminjamanDigital;
use App\Models\DigitalAccessLog;

class CleanupDigitalLoans extends Command
{
    protected $signature = 'digital:cleanup';
    protected $description = 'Bersihkan peminjaman digital yang expired';

    public function handle()
    {
        // Ambil semua peminjaman yang expired
        $expired = PeminjamanDigital::with('buku')
            ->where('status', 'aktif')
            ->where('tanggal_expired', '<', now())
            ->get();

        $count = 0;
        
        foreach ($expired as $pinjam) {
            /** @var \App\Models\PeminjamanDigital $pinjam */ // Type hint untuk Intelephense
            
            // Update status peminjaman
            $pinjam->update([
                'status' => 'expired',
                'catatan' => 'Otomatis expired oleh sistem'
            ]);
            
            // Kurangi lisensi yang dipinjam
            if ($pinjam->buku) {
                $pinjam->buku->decrement('lisensi_dipinjam');
            }
            
            // Catat log
            DigitalAccessLog::create([
                'peminjaman_digital_id' => $pinjam->id,
                'user_id' => $pinjam->user_id,
                'buku_id' => $pinjam->buku_id,
                'aksi' => 'expired',
                'status' => 'berhasil',
                'keterangan' => 'Otomatis expired'
            ]);
            
            $count++;
        }

        $this->info("✅ {$count} peminjaman expired telah dibersihkan.");
        
        // Gunakan self::SUCCESS untuk menghindari warning
        return self::SUCCESS;
    }
}