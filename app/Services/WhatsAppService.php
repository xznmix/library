<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Peminjaman;
use Carbon\Carbon;

class WhatsAppService
{
    protected $token;
    protected $url;

    public function __construct()
    {
        $this->token = env('FONNTE_TOKEN');
        $this->url   = 'https://api.fonnte.com';
    }

    /**
     * ===================================================
     * CORE SEND WA
     * ===================================================
     */

    private function sendMessage($target, $message)
    {
        try {
            if (!$target) {
                Log::warning('WhatsApp: No target phone number');
                return false;
            }

            // If WhatsApp is disabled, just log
            if (env('WHATSAPP_ENABLED', true) === false) {
                Log::info('[MOCK MODE] WhatsApp to: ' . $target, ['message' => $message]);
                return true;
            }

            $target = $this->formatPhone($target);

            $response = Http::withHeaders([
                'Authorization' => $this->token
            ])->post($this->url . '/send', [
                'target'      => $target,
                'message'     => $message,
                'countryCode' => '62'
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp sent successfully', [
                    'target' => $target,
                    'response' => $response->json()
                ]);
                return true;
            } else {
                Log::error('WhatsApp failed', [
                    'target' => $target,
                    'response' => $response->body()
                ]);
                return false;
            }

        } catch (\Exception $e) {
            Log::error('WhatsApp Error: ' . $e->getMessage());
            return false;
        }
    }

    private function formatPhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (substr($phone, 0, 1) == '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        if (substr($phone, 0, 2) != '62') {
            $phone = '62' . $phone;
        }
        
        return $phone;
    }

    /**
     * ===================================================
     * PEMINJAMAN
     * ===================================================
     */

    public function sendSuccessBorrowNotification($peminjaman)
    {
        try {
            if (!$peminjaman || !$peminjaman->user || !$peminjaman->buku) {
                Log::error('Invalid peminjaman data');
                return false;
            }

            $user = $peminjaman->user;
            $buku = $peminjaman->buku;
            
            $tglPinjam = $peminjaman->tgl_pinjam instanceof Carbon 
                ? $peminjaman->tgl_pinjam->format('d/m/Y') 
                : date('d/m/Y', strtotime($peminjaman->tgl_pinjam));
            
            $tglJatuhTempo = $peminjaman->tgl_kembali instanceof Carbon 
                ? $peminjaman->tgl_kembali->format('d/m/Y') 
                : date('d/m/Y', strtotime($peminjaman->tgl_kembali));

            $message = "📚 *PEMINJAMAN BERHASIL*\n\n"
                . "Halo *{$user->name}*\n\n"
                . "Buku berhasil dipinjam.\n\n"
                . "📖 *Judul:*\n{$buku->judul}\n\n"
                . "📅 *Tanggal Pinjam:*\n{$tglPinjam}\n\n"
                . "⏰ *Jatuh Tempo:*\n{$tglJatuhTempo}\n\n"
                . "Harap dikembalikan tepat waktu.\n\n"
                . "Perpustakaan SMAN 1 Tambang";

            return $this->sendMessage($user->phone, $message);

        } catch (\Exception $e) {
            Log::error('sendSuccessBorrowNotification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ===================================================
     * PENGEMBALIAN
     * ===================================================
     */

    public function sendReturnNotification($peminjaman, $denda = 0)
    {
        try {
            if (!$peminjaman || !$peminjaman->user || !$peminjaman->buku) {
                Log::error('Invalid peminjaman data');
                return false;
            }

            $user = $peminjaman->user;
            $buku = $peminjaman->buku;

            $message = "✅ *PENGEMBALIAN BERHASIL*\n\n"
                . "Halo *{$user->name}*\n\n"
                . "Buku:\n*{$buku->judul}*\n\n"
                . "Berhasil dikembalikan.\n\n"
                . "💰 *Denda:*\nRp " . number_format($denda, 0, ',', '.') . "\n\n"
                . "Terima kasih.\n\n"
                . "Perpustakaan SMAN 1 Tambang";

            return $this->sendMessage($user->phone, $message);

        } catch (\Exception $e) {
            Log::error('sendReturnNotification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ===================================================
     * NOTIFIKASI KETERLAMBATAN (FIXED!)
     * ===================================================
     */
    public function sendLateReturnNotification($peminjaman, $hariTerlambat, $denda)
    {
        try {
            if (!$peminjaman || !$peminjaman->user) {
                Log::error('Invalid peminjaman or user data');
                return false;
            }

            $user = $peminjaman->user;
            $buku = $peminjaman->buku;
            
            $tglJatuhTempo = $peminjaman->tgl_jatuh_tempo instanceof Carbon 
                ? $peminjaman->tgl_jatuh_tempo 
                : Carbon::parse($peminjaman->tgl_jatuh_tempo);
            
            $tanggalFormat = $tglJatuhTempo->format('d/m/Y');

            $message = "⚠️ *KETERLAMBATAN PENGEMBALIAN* ⚠️\n\n"
                . "Halo *{$user->name}*\n\n"
                . "📖 *Buku:*\n{$buku->judul}\n\n"
                . "📅 *Jatuh Tempo:*\n{$tanggalFormat}\n\n"
                . "⏰ *Terlambat:*\n{$hariTerlambat} hari\n\n"
                . "💰 *Denda:*\nRp " . number_format($denda, 0, ',', '.') . "\n\n"
                . "⚠️ Segera kembalikan buku untuk menghindari denda lebih besar!\n\n"
                . "Mohon lebih disiplin.\n\n"
                . "Perpustakaan SMAN 1 Tambang";

            return $this->sendMessage($user->phone, $message);

        } catch (\Exception $e) {
            Log::error('sendLateReturnNotification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ===================================================
     * DENDA
     * ===================================================
     */

    public function sendDendaNotification($peminjaman, $jumlahDenda)
    {
        try {
            if (!$peminjaman || !$peminjaman->user) {
                Log::error('Invalid peminjaman or user data');
                return false;
            }

            $user = $peminjaman->user;
            $buku = $peminjaman->buku;

            $message = "💰 *NOTIFIKASI DENDA* 💰\n\n"
                . "Halo *{$user->name}*\n\n"
                . "📖 *Buku:*\n{$buku->judul}\n\n"
                . "Anda memiliki denda sebesar:\n\n"
                . "*Rp " . number_format($jumlahDenda, 0, ',', '.') . "*\n\n"
                . "Silakan segera lakukan pembayaran.\n\n"
                . "Perpustakaan SMAN 1 Tambang";

            return $this->sendMessage($user->phone, $message);

        } catch (\Exception $e) {
            Log::error('sendDendaNotification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ===================================================
     * PEMBAYARAN DENDA BERHASIL
     * ===================================================
     */

    public function sendDendaPaidNotification($peminjaman, $jumlahDenda)
    {
        try {
            if (!$peminjaman || !$peminjaman->user || !$peminjaman->buku) {
                Log::error('Invalid peminjaman data');
                return false;
            }

            $user = $peminjaman->user;
            $buku = $peminjaman->buku;

            $message = "✅ *PEMBAYARAN DENDA BERHASIL* ✅\n\n"
                . "Halo *{$user->name}*\n\n"
                . "Pembayaran denda telah diterima.\n\n"
                . "📖 *Buku:*\n{$buku->judul}\n\n"
                . "💰 *Jumlah:*\nRp " . number_format($jumlahDenda, 0, ',', '.') . "\n\n"
                . "*Status: LUNAS*\n\n"
                . "Terima kasih.\n\n"
                . "Perpustakaan SMAN 1 Tambang";

            return $this->sendMessage($user->phone, $message);

        } catch (\Exception $e) {
            Log::error('sendDendaPaidNotification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ===================================================
     * PERPANJANGAN
     * ===================================================
     */

    public function sendExtendNotification($peminjaman)
    {
        try {
            if (!$peminjaman || !$peminjaman->user || !$peminjaman->buku) {
                Log::error('Invalid peminjaman data');
                return false;
            }

            $user = $peminjaman->user;
            $buku = $peminjaman->buku;
            
            $tglJatuhTempoBaru = $peminjaman->tgl_kembali instanceof Carbon 
                ? $peminjaman->tgl_kembali->format('d/m/Y') 
                : date('d/m/Y', strtotime($peminjaman->tgl_kembali));

            $message = "🔄 *PERPANJANGAN BERHASIL* 🔄\n\n"
                . "Halo *{$user->name}*\n\n"
                . "Peminjaman buku berhasil diperpanjang.\n\n"
                . "📖 *Buku:*\n{$buku->judul}\n\n"
                . "📅 *Jatuh tempo baru:*\n{$tglJatuhTempoBaru}\n\n"
                . "Perpustakaan SMAN 1 Tambang";

            return $this->sendMessage($user->phone, $message);

        } catch (\Exception $e) {
            Log::error('sendExtendNotification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ===================================================
     * REMINDER JATUH TEMPO (FIXED!)
     * ===================================================
     */
    public function sendReminderDueSoon($peminjaman)
    {
        try {
            if (!$peminjaman || !$peminjaman->user || !$peminjaman->buku) {
                Log::error('Invalid peminjaman data');
                return false;
            }

            $user = $peminjaman->user;
            $buku = $peminjaman->buku;
            
            // Gunakan tgl_jatuh_tempo
            $tglJatuhTempo = $peminjaman->tgl_jatuh_tempo instanceof Carbon 
                ? $peminjaman->tgl_jatuh_tempo 
                : Carbon::parse($peminjaman->tgl_jatuh_tempo);
            
            $tanggalFormat = $tglJatuhTempo->format('d/m/Y');
            
            // Hitung sisa hari
            $sisaHari = Carbon::now()->diffInDays($tglJatuhTempo, false);
            $sisaHariText = $sisaHari <= 0 ? 'HARI INI!' : "{$sisaHari} hari lagi";

            $message = "⏰ *PENGINGAT JATUH TEMPO* ⏰\n\n"
                . "Halo *{$user->name}*\n\n"
                . "📖 *Buku:*\n{$buku->judul}\n\n"
                . "Akan jatuh tempo *{$sisaHariText}* pada:\n\n"
                . "📅 *Tanggal:*\n{$tanggalFormat}\n\n"
                . "Mohon segera dikembalikan.\n\n"
                . "Perpustakaan SMAN 1 Tambang";

            return $this->sendMessage($user->phone, $message);

        } catch (\Exception $e) {
            Log::error('sendReminderDueSoon: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ===================================================
     * EXTRA: KIRIM MANUAL KE NOMOR TERTENTU
     * ===================================================
     */

    public function sendCustomMessage($phone, $message)
    {
        return $this->sendMessage($phone, $message);
    }
}