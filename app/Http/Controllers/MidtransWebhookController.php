<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Denda;
use App\Models\Notifikasi;
use App\Models\User;
use App\Services\WhatsAppService;

class MidtransWebhookController extends Controller
{
    protected $wa;

    public function __construct(WhatsAppService $wa)
    {
        $this->wa = $wa;
    }

    public function handle(Request $request)
    {
        try {
            Log::info('Midtrans Callback Received', $request->all());

            $orderId = $request->order_id;
            $statusCode = $request->status_code;
            $grossAmount = $request->gross_amount;
            $serverKey = env('MIDTRANS_SERVER_KEY');

            // 1. Validasi Signature Key (KEAMANAN)
            $signatureKey = hash("sha512", $orderId . $statusCode . $grossAmount . $serverKey);
            if ($signatureKey !== $request->signature_key) {
                Log::warning('Invalid Signature Key from Midtrans: ' . $orderId);
                return response()->json(['message' => 'Invalid Signature'], 403);
            }

            // 2. Ekstrak ID Denda dari Order ID (Format: DND-{ID}-{TIME})
            preg_match('/(?:DENDA|DND)-(\d+)/', $orderId, $match);

            if (!isset($match[1])) {
                Log::warning('Format order id tidak dikenali: ' . $orderId);
                return response()->json(['message' => 'Invalid Order ID format'], 400);
            }

            $dendaId = $match[1];
            $denda = Denda::with(['peminjaman.user', 'peminjaman.buku', 'anggota'])->find($dendaId);

            if (!$denda) {
                Log::error('Denda tidak ditemukan di Database: ' . $dendaId);
                return response()->json(['message' => 'Denda not found'], 404);
            }

            // 3. Hindari proses ulang jika sudah lunas
            if ($denda->payment_status == 'paid') {
                return response()->json(['message' => 'Already processed']);
            }

            $status = $request->transaction_status;
            $fraud = $request->fraud_status;

            // 4. Logika Update Status Lunas
            if (in_array($status, ['settlement', 'capture']) && (!$fraud || $fraud == 'accept')) {
                
                DB::beginTransaction();
                try {
                    // Update Table Denda
                    $denda->update([
                        'payment_status' => 'paid',
                        'status'         => 'lunas',
                        'paid_at'        => now(),
                        'payment_method' => $request->payment_type ?? 'qris'
                    ]);

                    // Update Table Peminjaman (Jika denda terkait peminjaman)
                    if ($denda->peminjaman) {
                        $denda->peminjaman->update([
                            'status_verifikasi' => 'selesai'
                        ]);
                    }

                    // 5. Notifikasi Internal (Kepala Pustaka)
                    $kepala = User::where('role', 'kepala_pustaka')->first();
                    if ($kepala) {
                        Notifikasi::create([
                            'user_id' => $kepala->id,
                            'judul'   => 'Denda Dibayar',
                            'isi'     => 'Pembayaran denda berhasil untuk ' . ($denda->anggota->name ?? 'Anggota'),
                            'type'    => 'success',
                            'link'    => route('kepala-pustaka.verifikasi.index')
                        ]);
                    }

                    DB::commit();

                    // 6. Kirim WhatsApp (Async-Safe)
                    try {
                        if (method_exists($this->wa, 'sendDendaPaidNotification')) {
                            $this->wa->sendDendaPaidNotification($denda);
                        }
                    } catch (\Throwable $waError) {
                        Log::warning('WhatsApp Error: ' . $waError->getMessage());
                    }

                    return response()->json(['success' => true]);

                } catch (\Throwable $e) {
                    DB::rollBack();
                    Log::error('Database Error during Webhook: ' . $e->getMessage());
                    return response()->json(['error' => 'Database error'], 500);
                }
            }

            // 7. Logika Update Status Gagal/Expired
            if (in_array($status, ['expire', 'cancel', 'deny'])) {
                $denda->update(['payment_status' => 'failed']);
                return response()->json(['status' => 'failed']);
            }

            return response()->json(['status' => 'ignored']);

        } catch (\Throwable $e) {
            Log::error('Webhook Server Error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }
}