<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Denda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\MidtransService;

class PaymentController extends Controller
{
    protected $midtrans;

    public function __construct(MidtransService $midtrans)
    {
        $this->midtrans = $midtrans;
    }

    public function generateQRIS($id_denda)
    {
        try {
            $denda = Denda::with('anggota')->findOrFail($id_denda);

            if ($denda->payment_status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Denda sudah lunas'
                ], 400);
            }

            $result = $this->midtrans->createQrisPayment($denda, $denda->anggota);

            if ($result['success']) {
                return response()->json($result);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 500);

        } catch (\Exception $e) {
            Log::error('QRIS Generate Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function checkStatus(Request $request)
    {
        try {
            $orderId = $request->get('order_id');
            
            if (!$orderId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order ID required'
                ], 400);
            }
            
            $denda = Denda::where('midtrans_order_id', $orderId)->first();
            
            if (!$denda) {
                return response()->json([
                    'success' => false,
                    'message' => 'Denda tidak ditemukan'
                ], 404);
            }
            
            $result = $this->midtrans->checkPaymentStatus($denda);
            
            return response()->json($result);
            
        } catch (\Exception $e) {
            Log::error('Check Status Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function handleNotification(Request $request)
    {
        try {
            $payload = $request->all();
            Log::info('Midtrans Webhook Received', $payload);

            if (!$this->midtrans->verifyNotificationSignature($payload)) {
                Log::error('Invalid signature from Midtrans');
                return response()->json(['message' => 'Invalid signature'], 403);
            }

            $orderId = $payload['order_id'];
            $transactionStatus = $payload['transaction_status'];

            $denda = Denda::where('midtrans_order_id', $orderId)->first();
            
            if (!$denda) {
                return response()->json(['message' => 'Denda not found'], 404);
            }

            if (in_array($transactionStatus, ['settlement', 'capture'])) {
                $denda->update([
                    'payment_status' => 'paid',
                    'status' => 'lunas',
                    'paid_at' => now(),
                    'keterangan' => 'Lunas via QRIS'
                ]);

                if ($denda->peminjaman) {
                    $denda->peminjaman->update([
                        'status_verifikasi' => 'selesai'
                    ]);
                }
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Webhook Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}