<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\Notifikasi;
use Carbon\Carbon;

class CheckExpiredBookings extends Command
{
    protected $signature = 'bookings:check-expired';
    protected $description = 'Check and expire bookings that have passed the pickup deadline';

    public function handle()
    {
        $this->info('Checking expired bookings...');
        
        // Booking yang sudah disetujui tapi melewati batas ambil
        $expiredBookings = Booking::with(['buku', 'user'])
            ->where('status', 'disetujui')
            ->where('batas_ambil', '<', now())
            ->get();
        
        $count = 0;
        
        foreach ($expiredBookings as $booking) {
            // Kembalikan stok
            $booking->buku->stok_tersedia += 1;
            $booking->buku->stok_direservasi -= 1;
            $booking->buku->save();
            
            // Update status
            $booking->status = 'hangus';
            $booking->save();
            
            // Notifikasi ke anggota
            Notifikasi::create([
                'user_id' => $booking->user_id,
                'judul' => '⏰ Booking Buku Hangus',
                'isi' => 'Booking buku "' . $booking->buku->judul . '" telah hangus karena tidak diambil tepat waktu.',
                'type' => 'warning',
                'link' => route('anggota.booking.show', $booking->id),
            ]);
            
            $count++;
            $this->info("Expired booking: {$booking->kode_booking} - {$booking->buku->judul}");
        }
        
        $this->info("Total expired bookings processed: {$count}");
        
        return Command::SUCCESS;
    }
}