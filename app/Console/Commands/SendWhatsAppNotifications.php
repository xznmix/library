<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WhatsAppService;
use App\Models\Peminjaman;
use Carbon\Carbon;

class SendWhatsAppNotifications extends Command
{
    protected $signature = 'notifications:send-whatsapp';
    protected $description = 'Send WhatsApp notifications for late returns and reminders';

    public function handle(WhatsAppService $whatsappService)
    {
        $this->info('Sending WhatsApp notifications...');
        
        // Kirim reminder untuk yang akan jatuh tempo dalam 2 hari
        try {
            $reminders = Peminjaman::where('status_pinjam', 'dipinjam')
                ->whereDate('tgl_jatuh_tempo', Carbon::now()->addDays(2))
                ->get();
            
            $reminderCount = 0;
            foreach ($reminders as $loan) {
                if ($loan->user && $loan->user->phone) {
                    $result = $whatsappService->sendReminderDueSoon($loan);
                    if ($result) {
                        $reminderCount++;
                        $this->line("✓ Reminder sent to: {$loan->user->name} - {$loan->buku->judul}");
                    }
                }
            }
            $this->info("Sent {$reminderCount} reminders");
            
        } catch (\Exception $e) {
            $this->error('Error sending reminders: ' . $e->getMessage());
        }
        
        // Kirim notifikasi untuk yang sudah terlambat
        try {
            $lates = Peminjaman::where('status_pinjam', 'dipinjam')
                ->whereDate('tgl_jatuh_tempo', '<', Carbon::now())
                ->get();
            
            $lateCount = 0;
            foreach ($lates as $loan) {
                if ($loan->user && $loan->user->phone) {
                    $hariTerlambat = Carbon::parse($loan->tgl_jatuh_tempo)->diffInDays(Carbon::now());
                    $denda = $hariTerlambat * ($loan->buku->denda_per_hari ?? 1000);
                    $result = $whatsappService->sendLateReturnNotification($loan, $hariTerlambat, $denda);
                    if ($result) {
                        $lateCount++;
                        $this->line("⚠️ Late notice sent to: {$loan->user->name} - {$loan->buku->judul} (Terlambat {$hariTerlambat} hari)");
                    }
                }
            }
            $this->info("Sent {$lateCount} late notifications");
            
        } catch (\Exception $e) {
            $this->error('Error sending late notifications: ' . $e->getMessage());
        }
        
        $this->info('Done!');
        return Command::SUCCESS;
    }
}