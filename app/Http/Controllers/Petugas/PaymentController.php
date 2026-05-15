<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Denda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\MidtransService;
use App\Services\WhatsAppService;

class PaymentController extends Controller
{
    protected $midtrans;
    protected $whatsapp;

    public function __construct(MidtransService $midtrans = null, WhatsAppService $whatsapp = null)
    {
        $this->midtrans = $midtrans;
        $this->whatsapp = $whatsapp;
    }

    /**
     * Generate QRIS payment for fine.
     *
     * BUG FIXES vs versi lama:
     * 1. $denda->id_denda diganti $denda->id — primaryKey model Denda sudah 'id'.
     * 2. generateSimpleQrString() dan getQrImageUrl() tidak lagi pakai id_denda.
     * 3. markAsPaid() tidak lagi pakai auth:: (lowercase) → Auth::id().
     * 4. Pengecekan $denda->payment_status === 'paid' diganti $denda->isPaid()
     *    agar konsisten dengan method di model.
     */
    public function generateQRIS($id)
    {
        try {
            Log::info('Generate QRIS called for denda ID: ' . $id);

            // ✅ FIX: findOrFail($id) sesuai primaryKey 'id'
            $denda = Denda::with(['anggota', 'peminjaman.buku'])->findOrFail($id);

            // ✅ FIX: pakai isPaid() dari model
            if ($denda->isPaid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Denda sudah lunas',
                ], 400);
            }

            // Ada transaksi Midtrans pending sebelumnya → kembalikan data lama
            if ($denda->midtrans_order_id && $denda->isPending()) {
                Log::info('Using existing order_id: ' . $denda->midtrans_order_id);

                return response()->json([
                    'success'    => true,
                    'existing'   => true,
                    'order_id'   => $denda->midtrans_order_id,
                    'qr_string'  => $denda->qr_code_path ?? $this->generateSimpleQrString($denda),
                    'qr_image'   => $this->getQrImageUrl($denda),
                    'amount'     => (int) $denda->jumlah_denda,
                    'expiry'     => now()->addMinutes(30)->format('Y-m-d H:i:s'),
                ]);
            }

            // Coba buat transaksi via Midtrans
            if ($this->midtrans && method_exists($this->midtrans, 'createQrisPayment')) {
                try {
                    $result = $this->midtrans->createQrisPayment($denda, $denda->anggota);

                    if ($result && !empty($result['success'])) {
                        $denda->update([
                            'midtrans_order_id' => $result['order_id']  ?? null,
                            'midtrans_token'    => $result['token']     ?? null,
                            'qr_code_path'      => $result['qr_string'] ?? null,
                            'payment_status'    => 'pending',
                        ]);

                        return response()->json([
                            'success'   => true,
                            'order_id'  => $result['order_id'],
                            'qr_string' => $result['qr_string'] ?? $this->generateSimpleQrString($denda),
                            'qr_image'  => $result['qr_image']  ?? null,
                            'amount'    => (int) $denda->jumlah_denda,
                            'expiry'    => now()->addMinutes(30)->format('Y-m-d H:i:s'),
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::warning('Midtrans service failed, using fallback: ' . $e->getMessage());
                }
            }

            // FALLBACK: generate QR tanpa Midtrans (testing / sandbox belum dikonfigurasi)
            // ✅ FIX: pakai $denda->id bukan $denda->id_denda
            $orderId  = 'DENDA-' . $denda->id . '-' . time();
            $qrString = $this->generateSimpleQrString($denda, $orderId);

            $denda->update([
                'midtrans_order_id' => $orderId,
                'qr_code_path'      => $qrString,
                'payment_status'    => 'pending',
            ]);

            Log::info('QRIS generated with fallback, order_id: ' . $orderId);

            return response()->json([
                'success'     => true,
                'order_id'    => $orderId,
                'qr_string'   => $qrString,
                'qr_image'    => null,
                'amount'      => (int) $denda->jumlah_denda,
                'expiry'      => now()->addMinutes(30)->format('Y-m-d H:i:s'),
                'is_fallback' => true,
            ]);

        } catch (\Exception $e) {
            Log::error('QRIS Generate Error: ' . $e->getMessage(), [
                'id'    => $id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal generate QRIS: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check payment status by order_id (dipanggil polling dari JS).
     */
    public function checkStatus(Request $request)
    {
        try {
            $orderId = $request->get('order_id');

            Log::info('Check status called for order_id: ' . $orderId);

            if (!$orderId) {
                return response()->json(['success' => false, 'message' => 'Order ID required'], 400);
            }

            $denda = Denda::where('midtrans_order_id', $orderId)->first();

            if (!$denda) {
                return response()->json([
                    'success' => false,
                    'message' => 'Denda tidak ditemukan',
                    'status'  => 'not_found',
                ], 404);
            }

            // Sudah lunas
            if ($denda->isPaid()) {
                return response()->json(['success' => true, 'status' => 'paid', 'paid' => true]);
            }

            // Cek ke Midtrans jika service tersedia
            if ($this->midtrans && method_exists($this->midtrans, 'checkPaymentStatus')) {
                try {
                    $result = $this->midtrans->checkPaymentStatus($denda);

                    if ($result && !empty($result['success'])) {
                        if (in_array($result['status'] ?? '', ['settlement', 'capture', 'paid'])) {
                            $this->markAsPaid($denda);
                            return response()->json(['success' => true, 'status' => 'paid', 'paid' => true]);
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Midtrans check failed: ' . $e->getMessage());
                }
            }

            return response()->json(['success' => true, 'status' => 'pending', 'paid' => false]);

        } catch (\Exception $e) {
            Log::error('Check Status Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal cek status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle Midtrans webhook notification.
     */
    public function handleNotification(Request $request)
    {
        try {
            $payload = $request->all();
            Log::info('Midtrans Webhook Received', $payload);

            if ($this->midtrans && method_exists($this->midtrans, 'verifyNotificationSignature')) {
                if (!$this->midtrans->verifyNotificationSignature($payload)) {
                    Log::error('Invalid signature from Midtrans');
                    return response()->json(['message' => 'Invalid signature'], 403);
                }
            }

            $orderId           = $payload['order_id']           ?? null;
            $transactionStatus = $payload['transaction_status'] ?? null;

            if (!$orderId) {
                return response()->json(['message' => 'Order ID required'], 400);
            }

            $denda = Denda::where('midtrans_order_id', $orderId)->first();

            if (!$denda) {
                Log::warning('Denda not found for order_id: ' . $orderId);
                return response()->json(['message' => 'Denda not found'], 404);
            }

            if (in_array($transactionStatus, ['settlement', 'capture', 'success'])) {
                $this->markAsPaid($denda);
            } elseif (in_array($transactionStatus, ['expire', 'cancel', 'deny'])) {
                $denda->update(['payment_status' => 'failed', 'status' => 'failed']);
                Log::info('Payment failed for order_id: ' . $orderId);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Webhook Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Manual payment confirmation (tunai/transfer).
     * Dipanggil dari route POST /petugas/sirkulasi/pembayaran/{id}/confirm
     * via PaymentController (bukan SirkulasiController).
     */
    public function confirmPayment(Request $request, $id)
    {
        try {
            $request->validate(['metode' => 'required|in:tunai,transfer,qris']);

            // ✅ FIX: findOrFail($id) sesuai primaryKey 'id'
            $denda = Denda::findOrFail($id);

            if ($denda->isPaid()) {
                return response()->json(['success' => false, 'message' => 'Denda sudah dibayar'], 400);
            }

            DB::beginTransaction();
            $this->markAsPaid($denda, $request->metode);
            DB::commit();

            Log::info('Payment confirmed manually', [
                'denda_id' => $id,
                'method'   => $request->metode,
                'user_id'  => Auth::id(),
            ]);

            return response()->json(['success' => true, 'message' => 'Pembayaran berhasil dikonfirmasi']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Confirm payment error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal konfirmasi pembayaran: ' . $e->getMessage(),
            ], 500);
        }
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Tandai denda sebagai lunas dan update peminjaman terkait.
     * ✅ SUDAH DITAMBAHKAN KIRIM NOTIFIKASI WA
     *
     * ✅ FIX: auth:: (lowercase, tidak valid) → Auth::id()
     */
    private function markAsPaid(Denda $denda, string $paymentMethod = 'qris'): Denda
    {
        $denda->update([
            'payment_status' => 'paid',
            'status'         => 'lunas',
            'paid_at'        => now(),
            'payment_method' => $paymentMethod,
            'confirmed_by'   => Auth::id(),
        ]);

        if ($denda->peminjaman) {
            $denda->peminjaman->update(['status_verifikasi' => 'selesai']);
        }

        // ✅ TAMBAHKAN KIRIM WA NOTIFIKASI PEMBAYARAN BERHASIL (via WhatsAppService)
        $this->kirimNotifikasiPembayaranBerhasil($denda, $paymentMethod);

        return $denda;
    }

    /**
     * ✅ TAMBAHKAN METHOD BARU INI: Kirim notifikasi WhatsApp pembayaran berhasil
     * 
     * @param Denda $denda
     * @param string $paymentMethod
     * @return void
     */
    private function kirimNotifikasiPembayaranBerhasil(Denda $denda, string $paymentMethod): void
    {
        try {
            // Cek apakah WhatsAppService tersedia
            if (!$this->whatsapp) {
                Log::warning('WhatsAppService tidak tersedia, tidak bisa kirim WA pembayaran');
                return;
            }

            // Cek apakah ada data anggota dan nomor HP
            $anggota = $denda->anggota;
            if (!$anggota || !$anggota->phone) {
                Log::warning('No HP anggota tidak ditemukan untuk WA pembayaran', [
                    'denda_id' => $denda->id,
                    'anggota_id' => $anggota->id ?? null
                ]);
                return;
            }

            // Ambil data peminjaman dan buku
            $peminjaman = $denda->peminjaman;
            $judulBuku = $peminjaman && $peminjaman->buku 
                ? $peminjaman->buku->judul 
                : 'Buku';
            
            $jumlahDenda = $denda->jumlah_denda;
            $metodeDisplay = strtoupper($paymentMethod);
            
            // Format pesan WhatsApp
            $message = "✅ *PEMBAYARAN DENDA BERHASIL* ✅\n\n"
                . "Halo *{$anggota->name}*,\n\n"
                . "Pembayaran denda telah kami terima.\n\n"
                . "📖 *Buku:* {$judulBuku}\n"
                . "💰 *Jumlah:* Rp " . number_format($jumlahDenda, 0, ',', '.') . "\n"
                . "💳 *Metode:* {$metodeDisplay}\n"
                . "📅 *Tanggal:* " . now()->format('d/m/Y H:i') . "\n\n"
                . "*Status: LUNAS* ✅\n\n"
                . "Terima kasih telah menggunakan layanan perpustakaan.\n\n"
                . "_Pesan ini dikirim otomatis oleh sistem Perpustakaan SMAN 1 Tambang._";

            // Kirim WA via WhatsAppService
            $result = $this->whatsapp->sendCustomMessage($anggota->phone, $message);
            
            if ($result) {
                Log::info('WA pembayaran berhasil dikirim', [
                    'anggota' => $anggota->name,
                    'phone' => $anggota->phone,
                    'denda_id' => $denda->id,
                    'nominal' => $jumlahDenda
                ]);
            } else {
                Log::warning('Gagal kirim WA pembayaran, mungkin token tidak valid', [
                    'anggota' => $anggota->name,
                    'denda_id' => $denda->id
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Gagal kirim WA notifikasi pembayaran: ' . $e->getMessage(), [
                'denda_id' => $denda->id,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Generate QR string fallback (tanpa Midtrans).
     *
     * ✅ FIX: pakai $denda->id bukan $denda->id_denda
     */
    private function generateSimpleQrString(Denda $denda, string $orderId = null): string
    {
        // ✅ FIX: $denda->id (bukan $denda->id_denda)
        $orderId = $orderId ?? ('DENDA-' . $denda->id . '-' . time());

        return json_encode([
            'type'      => 'denda',
            'id'        => $denda->id,
            'order_id'  => $orderId,
            'amount'    => (int) $denda->jumlah_denda,
            'merchant'  => 'Perpustakaan Digital',
            'timestamp' => time(),
        ]);
    }

    /**
     * Buat URL gambar QR menggunakan quickchart.io (fallback jika Midtrans tidak ada).
     */
    private function getQrImageUrl(Denda $denda): string
    {
        $qrData      = $denda->qr_code_path ?? $this->generateSimpleQrString($denda);
        $encodedData = urlencode($qrData);

        return "https://quickchart.io/qr?text={$encodedData}&size=256&margin=2";
    }
}