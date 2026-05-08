<?php

namespace App\Http\Controllers;

use App\Models\PeminjamanDigital;
use App\Models\DigitalAccessLog;
use App\Services\DigitalBorrowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DigitalReadController extends Controller
{
    protected $digitalService;

    public function __construct(DigitalBorrowService $digitalService)
    {
        $this->digitalService = $digitalService;
    }

    public function read(Request $request)
    {
        $token = $request->token;
        $expires = $request->expires;
        $signature = $request->signature;

        // Validasi signature
        $expectedSignature = hash_hmac('sha256', $token . $expires, config('app.key'));
        
        if (!hash_equals($expectedSignature, $signature)) {
            abort(403, 'Link akses tidak valid');
        }

        if (now()->timestamp > $expires) {
            abort(403, 'Link akses telah kadaluarsa');
        }

        $peminjaman = PeminjamanDigital::with(['user', 'buku'])
            ->where('token_akses', $token)
            ->where('status', 'aktif')
            ->first();

        if (!$peminjaman) {
            abort(404, 'Peminjaman tidak ditemukan');
        }

        if ($peminjaman->isExpired()) {
            $peminjaman->update(['status' => 'expired']);
            $peminjaman->buku->decrement('lisensi_dipinjam');
            abort(403, 'Masa pinjam telah habis');
        }

        // Update akses
        $peminjaman->increment('jumlah_akses');
        $peminjaman->update(['terakhir_akses' => now()]);

        // Log akses
        DigitalAccessLog::create([
            'peminjaman_digital_id' => $peminjaman->id,
            'user_id' => $peminjaman->user_id,
            'buku_id' => $peminjaman->buku_id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'aksi' => 'baca',
            'status' => 'berhasil'
        ]);

        $filePath = storage_path('app/public/' . $peminjaman->buku->file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->file($filePath, [
            'Content-Disposition' => 'inline; filename="' . $peminjaman->buku->judul . '.pdf"',
            'Cache-Control' => 'no-cache, must-revalidate',
            'X-Robots-Tag' => 'noindex, nofollow',
            'Permissions-Policy' => 'download=()' // Mencegah download di beberapa browser
        ]);
    }
}