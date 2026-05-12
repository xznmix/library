<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Denda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\MidtransService;

class PaymentController extends Controller
{
    protected $midtrans;

    public function __construct(MidtransService $midtrans = null)
    {
        $this->midtrans = $midtrans;
    }

    /**
     * Generate QRIS payment for fine
     */
    public function generateQRIS($id_denda)
    {
        try {
            Log::info('Generate QRIS called for denda ID: ' . $id_denda);
            
            $denda = Denda::with(['anggota', 'peminjaman.buku'])->findOrFail($id_denda);

            // Check if already paid
            if ($denda->payment_status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Denda sudah lunas'
                ], 400);
            }

            // Check if already has pending transaction
            if ($denda->midtrans_order_id && $denda->payment_status === 'pending') {
                Log::info('Using existing order_id: ' . $denda->midtrans_order_id);
                
                return response()->json([
                    'success' => true,
                    'existing' => true,
                    'order_id' => $denda->midtrans_order_id,
                    'qr_string' => $denda->qr_code_path ?? $this->generateSimpleQrString($denda),
                    'qr_image' => $this->getQrImageUrl($denda),
                    'amount' => (int) $denda->jumlah_denda,
                    'expiry' => now()->addMinutes(30)->format('Y-m-d H:i:s')
                ]);
            }

            // Try to use Midtrans service if available
            if ($this->midtrans && method_exists($this->midtrans, 'createQrisPayment')) {
                try {
                    $result = $this->midtrans->createQrisPayment($denda, $denda->anggota);
                    
                    if ($result && isset($result['success']) && $result['success']) {
                        // Update denda with Midtrans data
                        $denda->update([
                            'midtrans_order_id' => $result['order_id'] ?? null,
                            'midtrans_token' => $result['token'] ?? null,
                            'qr_code_path' => $result['qr_string'] ?? null,
                            'payment_status' => 'pending'
                        ]);
                        
                        return response()->json([
                            'success' => true,
                            'order_id' => $result['order_id'],
                            'qr_string' => $result['qr_string'] ?? $this->generateSimpleQrString($denda),
                            'qr_image' => $result['qr_image'] ?? null,
                            'amount' => (int) $denda->jumlah_denda,
                            'expiry' => now()->addMinutes(30)->format('Y-m-d H:i:s')
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::warning('Midtrans service failed, using fallback: ' . $e->getMessage());
                }
            }

            // FALLBACK: Generate QRIS without Midtrans (for testing)
            $orderId = 'DENDA-' . $denda->id_denda . '-' . time();
            $qrString = $this->generateSimpleQrString($denda, $orderId);
            
            // Update denda
            $denda->update([
                'midtrans_order_id' => $orderId,
                'qr_code_path' => $qrString,
                'payment_status' => 'pending'
            ]);
            
            Log::info('QRIS generated with fallback, order_id: ' . $orderId);
            
            return response()->json([
                'success' => true,
                'order_id' => $orderId,
                'qr_string' => $qrString,
                'qr_image' => null,
                'amount' => (int) $denda->jumlah_denda,
                'expiry' => now()->addMinutes(30)->format('Y-m-d H:i:s'),
                'is_fallback' => true
            ]);
            
        } catch (\Exception $e) {
            Log::error('QRIS Generate Error: ' . $e->getMessage(), [
                'id_denda' => $id_denda,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate QRIS: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check payment status
     */
    public function checkStatus(Request $request)
    {
        try {
            $orderId = $request->get('order_id');
            
            Log::info('Check status called for order_id: ' . $orderId);
            
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
                    'message' => 'Denda tidak ditemukan',
                    'status' => 'not_found'
                ], 404);
            }
            
            // Check if already paid
            if ($denda->payment_status === 'paid') {
                return response()->json([
                    'success' => true,
                    'status' => 'paid',
                    'paid' => true
                ]);
            }
            
            // If using Midtrans, check via service
            if ($this->midtrans && method_exists($this->midtrans, 'checkPaymentStatus')) {
                try {
                    $result = $this->midtrans->checkPaymentStatus($denda);
                    
                    if ($result && isset($result['success']) && $result['success']) {
                        if (in_array($result['status'] ?? '', ['settlement', 'capture', 'paid'])) {
                            // Update status
                            $this->markAsPaid($denda);
                            
                            return response()->json([
                                'success' => true,
                                'status' => 'paid',
                                'paid' => true
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Midtrans check failed: ' . $e->getMessage());
                }
            }
            
            // For fallback: check if paid via manual confirmation
            if ($denda->payment_status === 'paid') {
                return response()->json([
                    'success' => true,
                    'status' => 'paid',
                    'paid' => true
                ]);
            }
            
            return response()->json([
                'success' => true,
                'status' => 'pending',
                'paid' => false
            ]);
            
        } catch (\Exception $e) {
            Log::error('Check Status Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal cek status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle Midtrans webhook notification
     */
    public function handleNotification(Request $request)
    {
        try {
            $payload = $request->all();
            Log::info('Midtrans Webhook Received', $payload);

            // Verify signature if Midtrans service available
            if ($this->midtrans && method_exists($this->midtrans, 'verifyNotificationSignature')) {
                if (!$this->midtrans->verifyNotificationSignature($payload)) {
                    Log::error('Invalid signature from Midtrans');
                    return response()->json(['message' => 'Invalid signature'], 403);
                }
            }

            $orderId = $payload['order_id'] ?? null;
            $transactionStatus = $payload['transaction_status'] ?? null;

            if (!$orderId) {
                return response()->json(['message' => 'Order ID required'], 400);
            }

            $denda = Denda::where('midtrans_order_id', $orderId)->first();
            
            if (!$denda) {
                Log::warning('Denda not found for order_id: ' . $orderId);
                return response()->json(['message' => 'Denda not found'], 404);
            }

            // Update status based on transaction status
            if (in_array($transactionStatus, ['settlement', 'capture', 'success'])) {
                $this->markAsPaid($denda);
            } elseif (in_array($transactionStatus, ['expire', 'cancel', 'deny'])) {
                $denda->update([
                    'payment_status' => 'failed',
                    'status' => 'failed'
                ]);
                Log::info('Payment failed for order_id: ' . $orderId);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Webhook Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Manual payment confirmation (for tunai/transfer)
     */
    public function confirmPayment(Request $request, $id)
    {
        try {
            $request->validate([
                'metode' => 'required|in:tunai,transfer,qris'
            ]);
            
            $denda = Denda::findOrFail($id);
            
            if ($denda->isPaid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Denda sudah dibayar'
                ], 400);
            }
            
            DB::beginTransaction();
            
            $this->markAsPaid($denda, $request->metode);
            
            DB::commit();
            
            Log::info('Payment confirmed manually', [
                'denda_id' => $id,
                'method' => $request->metode,
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dikonfirmasi'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Confirm payment error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal konfirmasi pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark denda as paid
     */
    private function markAsPaid(Denda $denda, string $paymentMethod = 'qris')
    {
        $denda->update([
            'payment_status' => 'paid',
            'status' => 'lunas',
            'paid_at' => now(),
            'payment_method' => $paymentMethod,
            'confirmed_by' => auth::id()
        ]);
        
        // Update related peminjaman
        if ($denda->peminjaman) {
            $denda->peminjaman->update([
                'status_verifikasi' => 'selesai'
            ]);
        }
        
        return $denda;
    }

    /**
     * Generate simple QR string for fallback
     */
    private function generateSimpleQrString(Denda $denda, string $orderId = null)
    {
        $orderId = $orderId ?? 'DENDA-' . $denda->id_denda . '-' . time();
        
        // Format QRIS-like string
        $qrData = [
            'type' => 'denda',
            'id' => $denda->id_denda,
            'order_id' => $orderId,
            'amount' => (int) $denda->jumlah_denda,
            'merchant' => 'Perpustakaan Digital',
            'timestamp' => time()
        ];
        
        return json_encode($qrData);
    }

    /**
     * Get QR image URL (for fallback)
     */
    private function getQrImageUrl(Denda $denda)
    {
        // Use QR code generator API
        $qrData = $denda->qr_code_path ?? $this->generateSimpleQrString($denda);
        $encodedData = urlencode($qrData);
        
        return "https://quickchart.io/qr?text={$encodedData}&size=256&margin=2";
    }
}