<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Denda;
use Illuminate\Http\Client\ConnectionException;

class MidtransService
{
    protected $serverKey;
    protected $clientKey;
    protected $isProduction;

    public function __construct()
    {
        $this->serverKey = config('midtrans.server_key');
        $this->clientKey = config('midtrans.client_key');
        $this->isProduction = config('midtrans.is_production', false);
    }

    /**
     * Get base URL based on environment
     */
    protected function getBaseUrl()
    {
        return $this->isProduction 
            ? 'https://api.midtrans.com' 
            : 'https://api.sandbox.midtrans.com';
    }

    /**
     * Create QRIS payment
     */
    public function createQrisPayment($denda, $user)
    {
        try {
            $orderId = 'DND-' . $denda->id_denda . '-' . time();

            $payload = [
                'payment_type' => 'qris',
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $denda->jumlah_denda
                ],
                'customer_details' => [
                    'first_name' => $user->name ?? 'Customer',
                    'email' => $user->email ?? 'customer@example.com',
                    'phone' => $user->phone ?? '',
                ],
                'qris' => [
                    'acquirer' => 'gopay'
                ]
            ];

            Log::info('Midtrans Request', [
                'url' => $this->getBaseUrl() . '/v2/charge',
                'payload' => $payload
            ]);

            $response = Http::withBasicAuth($this->serverKey, '')
                ->withOptions([
                    'verify' => false,
                    'timeout' => 30,
                ])
                ->post($this->getBaseUrl() . '/v2/charge', $payload);

            Log::info('Midtrans Response', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            if ($response->successful()) {
                $result = $response->json();
                
                // Get QR URL from actions
                $qrImageUrl = null;
                if (!empty($result['actions'])) {
                    foreach ($result['actions'] as $action) {
                        if ($action['name'] === 'generate-qr-code') {
                            $qrImageUrl = $action['url'];
                            break;
                        }
                    }
                }

                // Save to database
                $denda->update([
                    'midtrans_order_id' => $orderId,
                    'qr_code_path' => $result['qr_string'] ?? null,
                    'qr_image_url' => $qrImageUrl,
                    'payment_status' => 'pending',
                    'midtrans_transaction_id' => $result['transaction_id'] ?? null
                ]);

                return [
                    'success' => true,
                    'qr_string' => $result['qr_string'] ?? null,
                    'qr_image' => $qrImageUrl,
                    'order_id' => $orderId,
                    'amount' => $denda->jumlah_denda,
                    'expiry' => now()->addMinutes(30)->toDateTimeString()
                ];
            }

            $errorMsg = $response->json()['status_message'] ?? 'Unknown error';
            Log::error('Midtrans QRIS Error: ' . $errorMsg);

            return [
                'success' => false,
                'message' => $errorMsg
            ];

        } catch (ConnectionException $e) {
            Log::error('Midtrans Connection Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Koneksi ke Midtrans gagal: ' . $e->getMessage()
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans Service Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus($denda)
    {
        try {
            $orderId = $denda->midtrans_order_id;
            
            if (!$orderId) {
                return [
                    'success' => false,
                    'status' => 'pending',
                    'message' => 'Order ID tidak ditemukan'
                ];
            }

            Log::info('Checking status for order: ' . $orderId);

            $response = Http::withBasicAuth($this->serverKey, '')
                ->withOptions([
                    'verify' => false,
                    'timeout' => 30,
                ])
                ->get($this->getBaseUrl() . '/v2/' . $orderId . '/status');

            Log::info('Status Response', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $transactionStatus = $result['transaction_status'] ?? 'pending';

                if (in_array($transactionStatus, ['settlement', 'capture'])) {
                    // Update denda status
                    $denda->update([
                        'payment_status' => 'paid',
                        'status' => 'lunas',
                        'paid_at' => now(),
                        'payment_method' => 'qris'
                    ]);

                    // Update peminjaman terkait
                    if ($denda->peminjaman) {
                        $denda->peminjaman->update([
                            'status_verifikasi' => 'selesai'
                        ]);
                    }

                    return [
                        'success' => true,
                        'status' => 'paid',
                        'message' => 'Pembayaran berhasil'
                    ];
                }

                return [
                    'success' => true,
                    'status' => $transactionStatus,
                    'message' => 'Pembayaran masih ' . $transactionStatus
                ];
            }

            return [
                'success' => false,
                'status' => 'pending',
                'message' => 'Gagal cek status'
            ];

        } catch (ConnectionException $e) {
            Log::error('Status Check Connection Error: ' . $e->getMessage());
            return [
                'success' => false,
                'status' => 'pending',
                'message' => 'Gagal terhubung ke Midtrans'
            ];
        } catch (\Exception $e) {
            Log::error('Status Check Error: ' . $e->getMessage());
            return [
                'success' => false,
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Verify notification signature
     */
    public function verifyNotificationSignature($payload)
    {
        if (!isset($payload['order_id']) || !isset($payload['status_code']) || !isset($payload['gross_amount'])) {
            return false;
        }

        $signatureKey = hash('sha512', 
            $payload['order_id'] . 
            $payload['status_code'] . 
            $payload['gross_amount'] . 
            $this->serverKey
        );

        return isset($payload['signature_key']) && $payload['signature_key'] === $signatureKey;
    }
}