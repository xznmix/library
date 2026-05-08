<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PeminjamanDigital;
use App\Models\Buku;
use App\Models\DigitalAccessLog;
use Carbon\Carbon;

class AutoReturnExpiredDigitalLoans extends Command
{
    protected $signature = 'digital:auto-return';
    protected $description = 'Auto return expired digital loans and restore license count';

    public function handle()
    {
        $this->info('Starting auto-return process...');
        
        $expiredLoans = PeminjamanDigital::where('status', 'aktif')
            ->where('tanggal_expired', '<', Carbon::now())
            ->get();

        $count = 0;
        
        foreach ($expiredLoans as $loan) {
            // Update status peminjaman
            $loan->update([
                'status' => 'expired',
                'tanggal_dikembalikan' => Carbon::now()
            ]);
            
            // Kembalikan lisensi ke stok (hanya untuk ebook)
            $buku = Buku::find($loan->buku_id);
            if ($buku && $buku->jenis_koleksi === 'ebook' && !$buku->bisa_download) {
                $buku->decrement('lisensi_dipinjam');
            }
            
            // Log aktivitas
            DigitalAccessLog::create([
                'peminjaman_digital_id' => $loan->id,
                'user_id' => $loan->user_id,
                'buku_id' => $loan->buku_id,
                'aksi' => 'kembali',
                'status' => 'berhasil',
                'keterangan' => 'Auto-return karena expired'
            ]);
            
            $count++;
        }
        
        $this->info("Auto-returned {$count} expired digital loans.");
        
        return Command::SUCCESS;
    }
}