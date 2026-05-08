<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;

class FonnteService
{
    protected $token;
    protected $url;

    public function __construct()
    {
        $this->token = env('FONNTE_TOKEN');
        $this->url = 'https://api.fonnte.com/send';
    }

    /**
     * Kirim pesan WhatsApp
     * 
     * @param string $target
     * @param string $message
     * @return bool
     */
    public function sendMessage(string $target, string $message): bool
    {
        if (!$this->token) {
            Log::error('Fonnte token not found in .env');
            return false;
        }

        try {
            /** @var Response $response */
            $response = Http::withHeaders([
                'Authorization' => $this->token
            ])->post($this->url, [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62',
            ]);

            if ($response->successful()) {
                $result = $response->json();
                
                if (isset($result['status']) && $result['status'] == 'success') {
                    Log::info("WhatsApp berhasil dikirim ke {$target}");
                    return true;
                }
                
                Log::error("Fonnte error: " . json_encode($result));
                return false;
            }

            Log::error("Fonnte HTTP error: " . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error("Fonnte exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim notifikasi peminjaman
     * 
     * @param mixed $peminjaman
     * @return bool
     */
    public function notifikasiPeminjaman($peminjaman): bool
    {
        $anggota = $peminjaman->user;
        $buku = $peminjaman->buku;
        
        $phone = $this->formatPhoneNumber($anggota->phone);
        
        if (!$phone) {
            Log::warning("No HP tidak valid untuk {$anggota->name}");
            return false;
        }

        $message = "📚 *NOTIFIKASI PEMINJAMAN BUKU*\n\n";
        $message .= "Halo *{$anggota->name}*,\n\n";
        $message .= "Buku yang Anda pinjam:\n";
        $message .= "📖 Judul: {$buku->judul}\n";
        $message .= "👤 Pengarang: {$buku->pengarang}\n";
        $message .= "📅 Tanggal Pinjam: " . \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') . "\n";
        $message .= "⏰ Jatuh Tempo: " . \Carbon\Carbon::parse($peminjaman->tgl_jatuh_tempo)->format('d/m/Y') . "\n\n";
        $message .= "Harap kembalikan tepat waktu ya! Terima kasih 🙏\n\n";
        $message .= "_Pesan ini dikirim otomatis oleh sistem perpustakaan SMAN 1 Tambang._";

        return $this->sendMessage($phone, $message);
    }

    /**
     * Kirim notifikasi pengembalian
     * 
     * @param mixed $peminjaman
     * @return bool
     */
    public function notifikasiPengembalian($peminjaman): bool
    {
        $anggota = $peminjaman->user;
        $buku = $peminjaman->buku;
        
        $phone = $this->formatPhoneNumber($anggota->phone);
        
        if (!$phone) return false;

        $message = "✅ *KONFIRMASI PENGEMBALIAN BUKU*\n\n";
        $message .= "Halo *{$anggota->name}*,\n\n";
        $message .= "Buku berikut telah dikembalikan:\n";
        $message .= "📖 Judul: {$buku->judul}\n";
        $message .= "📅 Tanggal Kembali: " . now()->format('d/m/Y') . "\n";
        
        if ($peminjaman->denda > 0) {
            $message .= "💰 Denda: Rp " . number_format($peminjaman->denda, 0, ',', '.') . "\n";
        } else {
            $message .= "💰 Denda: Rp 0 (tepat waktu)\n";
        }
        
        $message .= "\nTerima kasih telah menggunakan layanan perpustakaan! 📚\n\n";
        $message .= "_Pesan ini dikirim otomatis oleh sistem perpustakaan SMAN 1 Tambang._";
        
        return $this->sendMessage($phone, $message);
    }

    /**
     * Kirim notifikasi jatuh tempo
     * 
     * @param mixed $peminjaman
     * @return bool
     */
    public function notifikasiJatuhTempo($peminjaman): bool
    {
        $anggota = $peminjaman->user;
        $buku = $peminjaman->buku;
        
        $phone = $this->formatPhoneNumber($anggota->phone);
        
        if (!$phone) return false;

        $message = "⚠️ *PERINGATAN JATUH TEMPO*\n\n";
        $message .= "Halo *{$anggota->name}*,\n\n";
        $message .= "Buku yang Anda pinjam akan jatuh tempo besok:\n";
        $message .= "📖 Judul: {$buku->judul}\n";
        $message .= "📅 Tanggal Pinjam: " . \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') . "\n";
        $message .= "⏰ Jatuh Tempo: " . \Carbon\Carbon::parse($peminjaman->tgl_jatuh_tempo)->format('d/m/Y') . "\n\n";
        $message .= "Segera kembalikan untuk menghindari denda! 🙏\n\n";
        $message .= "_Pesan ini dikirim otomatis oleh sistem perpustakaan SMAN 1 Tambang._";
        
        return $this->sendMessage($phone, $message);
    }

    /**
     * Kirim notifikasi keterlambatan
     * 
     * @param mixed $peminjaman
     * @return bool
     */
    public function notifikasiTerlambat($peminjaman): bool
    {
        $anggota = $peminjaman->user;
        $buku = $peminjaman->buku;
        
        $phone = $this->formatPhoneNumber($anggota->phone);
        
        if (!$phone) return false;

        $hariTerlambat = now()->diffInDays(\Carbon\Carbon::parse($peminjaman->tgl_jatuh_tempo));
        $denda = $hariTerlambat * ($buku->denda_per_hari ?? 1000);
        
        $message = "⛔ *PERINGATAN KETERLAMBATAN*\n\n";
        $message .= "Halo *{$anggota->name}*,\n\n";
        $message .= "Buku berikut TELAT dikembalikan:\n";
        $message .= "📖 Judul: {$buku->judul}\n";
        $message .= "⏰ Terlambat: {$hariTerlambat} hari\n";
        $message .= "💰 Denda: Rp " . number_format($denda, 0, ',', '.') . "\n\n";
        $message .= "Segera kembalikan untuk menghentikan denda! 🏃\n\n";
        $message .= "_Pesan ini dikirim otomatis oleh sistem perpustakaan SMAN 1 Tambang._";
        
        return $this->sendMessage($phone, $message);
    }

    /**
     * Format nomor HP ke format internasional
     * 
     * @param string|null $phone
     * @return string|null
     */
    private function formatPhoneNumber($phone): ?string
    {
        if (!$phone) return null;
        
        // Hapus semua karakter non-digit
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Jika diawali 0, ganti 62
        if (substr($phone, 0, 1) == '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        // Jika tidak diawali 62, tambahkan 62
        if (substr($phone, 0, 2) != '62') {
            $phone = '62' . $phone;
        }
        
        return $phone;
    }
}