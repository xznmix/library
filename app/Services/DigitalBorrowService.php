<?php

namespace App\Services;

use App\Models\Buku;
use App\Models\User;
use App\Models\PeminjamanDigital;
use App\Models\DigitalAccessLog;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DigitalBorrowService
{
    /**
     * Cek ketersediaan lisensi
     */
    public function cekKetersediaan(Buku $buku)
    {
        $tersedia = $buku->jumlah_lisensi - $buku->lisensi_dipinjam;
        
        return [
            'total_lisensi' => $buku->jumlah_lisensi,
            'sedang_dipinjam' => $buku->lisensi_dipinjam,
            'tersedia' => max(0, $tersedia),
            'bisa_dipinjam' => $tersedia > 0,
            'durasi_pinjam' => $buku->durasi_pinjam_hari . ' hari'
        ];
    }

    /**
     * Proses peminjaman
     */
    public function pinjam(User $user, Buku $buku, $petugasId = null)
    {
        if ($buku->tipe !== 'digital') {
            return ['success' => false, 'message' => 'Bukan koleksi digital'];
        }

        if (!$buku->file_path) {
            return ['success' => false, 'message' => 'File tidak tersedia'];
        }

        $ketersediaan = $this->cekKetersediaan($buku);
        if (!$ketersediaan['bisa_dipinjam']) {
            return ['success' => false, 'message' => 'Semua lisensi sedang dipinjam'];
        }

        $existing = PeminjamanDigital::where('user_id', $user->id)
            ->where('buku_id', $buku->id)
            ->where('status', 'aktif')
            ->where('tanggal_expired', '>', now())
            ->first();

        if ($existing) {
            return [
                'success' => false, 
                'message' => 'Anda masih memiliki peminjaman aktif hingga ' . 
                             $existing->tanggal_expired->format('d M Y H:i')
            ];
        }

        $token = Str::random(60);
        $tanggalExpired = now()->addDays($buku->durasi_pinjam_hari);

        $peminjaman = PeminjamanDigital::create([
            'user_id' => $user->id,
            'buku_id' => $buku->id,
            'petugas_id' => $petugasId ?? Auth::id(),
            'tanggal_pinjam' => now(),
            'tanggal_expired' => $tanggalExpired,
            'token_akses' => $token,
            'status' => 'aktif',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        $buku->increment('lisensi_dipinjam');

        DigitalAccessLog::create([
            'peminjaman_digital_id' => $peminjaman->id,
            'user_id' => $user->id,
            'buku_id' => $buku->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'aksi' => 'pinjam',
            'status' => 'berhasil'
        ]);

        return [
            'success' => true,
            'message' => 'Berhasil meminjam',
            'peminjaman' => $peminjaman
        ];
    }

    /**
     * Generate signed URL
     */
    public function generateSignedUrl(PeminjamanDigital $peminjaman)
    {
        $expires = now()->addHours(24)->timestamp;
        $signature = hash_hmac('sha256', $peminjaman->token_akses . $expires, config('app.key'));
        
        return route('digital.read', [
            'token' => $peminjaman->token_akses,
            'expires' => $expires,
            'signature' => $signature
        ]);
    }
}