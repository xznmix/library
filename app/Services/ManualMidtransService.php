<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ManualMidtransService
{
    protected $serverKey;
    protected $clientKey;
    protected $isProduction;
    protected $baseUrl;
    protected $merchantId;

    public function __construct()
    {
        $this->serverKey = config('midtrans.server_key');
        $this->clientKey = config('midtrans.client_key');
        $this->isProduction = config('midtrans.is_production', false);
        $this->baseUrl = $this->isProduction 
            ? 'https://app.midtrans.com' 
            : 'https://app.sandbox.midtrans.com';
        $this->merchantId = config('midtrans.merchant_id', '');
    }

    /**
     * Create QRIS payment
     */
    public function createQrisPayment($denda, $user)
    {
        try {
            $orderId = 'ORDER-' . $denda->id_denda . '-' . time();
            
            $payload = [
                'payment_type' => 'qris',
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $denda->jumlah_denda
                ],
                'customer_details' => [
                    'first_name' => $user->name ?? 'Customer',
                    'email' => $user->email ?? '',
                    'phone' => $user->phone ?? '',
                ],
                'qris' => [
                    'acquirer' => 'gopay'
                ]
            ];

            // 🔧 PERBAIKAN: Endpoint yang benar untuk Snap
            $response = $this->sendRequest('POST', '/snap/v1/transactions', $payload);

            if ($response && isset($response['status_code']) && $response['status_code'] == '201') {
                // Save order_id to denda record
                $denda->update([
                    'midtrans_order_id' => $orderId,
                    'payment_request_id' => $response['transaction_id'] ?? null
                ]);

                // 🔧 PERBAIKAN: Ambil qr_string dari response
                $qrString = $response['qr_string'] ?? null;
                
                if ($qrString) {
                    $denda->update([
                        'qr_code_path' => $qrString,
                        'midtrans_token' => $qrString
                    ]);
                }

                // Return QRIS response
                if (isset($response['actions'])) {
                    foreach ($response['actions'] as $action) {
                        if ($action['name'] == 'generate-qr-code') {
                            return [
                                'success' => true,
                                'qr_string' => $qrString,
                                'qr_code_url' => $action['url'],
                                'order_id' => $orderId,
                                'gross_amount' => $denda->jumlah_denda
                            ];
                        }
                    }
                }

                return [
                    'success' => true,
                    'qr_string' => $qrString,
                    'qr_code_url' => $response['qr_code_url'] ?? null,
                    'order_id' => $orderId,
                    'gross_amount' => $denda->jumlah_denda
                ];
            }

            return [
                'success' => false,
                'message' => $response['status_message'] ?? 'Gagal membuat pembayaran QRIS'
            ];

        } catch (\Exception $e) {
            Log::error('Midtrans QRIS Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
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

            $response = $this->sendRequest('GET', '/v2/' . $orderId . '/status');

            if ($response && isset($response['status_code'])) {
                $transactionStatus = $response['transaction_status'] ?? '';
                $fraudStatus = $response['fraud_status'] ?? '';

                $paymentStatus = $this->determinePaymentStatus($transactionStatus, $fraudStatus);

                return [
                    'success' => true,
                    'status' => $paymentStatus,
                    'transaction_status' => $transactionStatus,
                    'payment_type' => $response['payment_type'] ?? null,
                    'transaction_id' => $response['transaction_id'] ?? null,
                    'settlement_time' => $response['settlement_time'] ?? null
                ];
            }

            return [
                'success' => false,
                'status' => 'pending',
                'message' => 'Gagal mengecek status pembayaran'
            ];

        } catch (\Exception $e) {
            Log::error('Midtrans Check Status Error: ' . $e->getMessage());
            return [
                'success' => false,
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Send request to Midtrans API
     */
    protected function sendRequest($method, $endpoint, $payload = [])
    {
        try {
            $url = $this->baseUrl . $endpoint;
            $authorization = base64_encode($this->serverKey . ':');

            $headers = [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . $authorization
            ];

            $client = new \GuzzleHttp\Client([
                'verify' => !$this->isProduction,
                'http_errors' => false,
                'timeout' => 60,
                'connect_timeout' => 30,
            ]);

            $response = $client->request($method, $url, [
                'headers' => $headers,
                'json' => $payload
            ]);

            $body = $response->getBody()->getContents();
            $statusCode = $response->getStatusCode();

            Log::info('Midtrans API Response', [
                'endpoint' => $endpoint,
                'status_code' => $statusCode,
                'response' => $body
            ]);

            if ($statusCode >= 200 && $statusCode < 300) {
                return json_decode($body, true);
            }

            $errorData = json_decode($body, true);
            Log::error('Midtrans API Error', [
                'status_code' => $statusCode,
                'error' => $errorData
            ]);

            return $errorData ?: ['status_code' => $statusCode, 'status_message' => 'API Error'];

        } catch (\Exception $e) {
            Log::error('Midtrans Request Error: ' . $e->getMessage());
            return [
                'status_code' => 500,
                'status_message' => 'Request failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Determine payment status from Midtrans response
     */
    protected function determinePaymentStatus($transactionStatus, $fraudStatus)
    {
        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'accept') {
                return 'settlement';
            }
            return 'pending';
        } elseif ($transactionStatus == 'settlement') {
            return 'settlement';
        } elseif ($transactionStatus == 'pending') {
            return 'pending';
        } elseif ($transactionStatus == 'deny') {
            return 'deny';
        } elseif (in_array($transactionStatus, ['cancel', 'expire'])) {
            return 'expired';
        } elseif ($transactionStatus == 'refund') {
            return 'refund';
        }

        return 'pending';
    }

    /**
     * Create Snap payment (for regular payment)
     */
    public function createSnapPayment($denda, $user)
    {
        try {
            $orderId = 'ORDER-' . $denda->id_denda . '-' . time();

            $payload = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $denda->jumlah_denda
                ],
                'customer_details' => [
                    'first_name' => $user->name ?? 'Customer',
                    'email' => $user->email ?? '',
                    'phone' => $user->phone ?? '',
                ],
                'enabled_payments' => ['qris', 'gopay', 'bank_transfer', 'credit_card']
            ];

            $response = $this->sendRequest('POST', '/snap/v1/transactions', $payload);

            if ($response && isset($response['token'])) {
                $denda->update([
                    'midtrans_order_id' => $orderId,
                    'snap_token' => $response['token'],
                    'snap_redirect_url' => $response['redirect_url'] ?? null
                ]);

                return [
                    'success' => true,
                    'token' => $response['token'],
                    'redirect_url' => $response['redirect_url'] ?? null
                ];
            }

            return [
                'success' => false,
                'message' => $response['status_message'] ?? 'Gagal membuat pembayaran'
            ];

        } catch (\Exception $e) {
            Log::error('Midtrans Snap Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Cancel transaction
     */
    public function cancelTransaction($orderId)
    {
        try {
            $response = $this->sendRequest('POST', '/v2/' . $orderId . '/cancel');

            if ($response && isset($response['status_code']) && $response['status_code'] == '200') {
                return [
                    'success' => true,
                    'message' => 'Transaksi berhasil dibatalkan'
                ];
            }

            return [
                'success' => false,
                'message' => $response['status_message'] ?? 'Gagal membatalkan transaksi'
            ];

        } catch (\Exception $e) {
            Log::error('Midtrans Cancel Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Refund transaction
     */
    public function refundTransaction($orderId, $amount = null, $reason = null)
    {
        try {
            $payload = [
                'refund_key' => 'REFUND-' . $orderId . '-' . time()
            ];

            if ($amount) {
                $payload['amount'] = $amount;
            }

            if ($reason) {
                $payload['reason'] = $reason;
            }

            $response = $this->sendRequest('POST', '/v2/' . $orderId . '/refund', $payload);

            if ($response && isset($response['status_code']) && $response['status_code'] == '200') {
                return [
                    'success' => true,
                    'message' => 'Refund berhasil diproses'
                ];
            }

            return [
                'success' => false,
                'message' => $response['status_message'] ?? 'Gagal memproses refund'
            ];

        } catch (\Exception $e) {
            Log::error('Midtrans Refund Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate signature for notification
     */
    public function generateSignature($orderId, $statusCode, $grossAmount)
    {
        return hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);
    }

    /**
     * Verify notification signature
     */
    public function verifyNotificationSignature($payload)
    {
        if (!isset($payload['order_id']) || !isset($payload['status_code']) || !isset($payload['gross_amount'])) {
            return false;
        }

        $signature = $this->generateSignature(
            $payload['order_id'],
            $payload['status_code'],
            $payload['gross_amount']
        );

        return isset($payload['signature_key']) && $payload['signature_key'] === $signature;
    }

    /**
     * Process webhook notification
     */
    public function processNotification($payload)
    {
        try {
            if (!$this->verifyNotificationSignature($payload)) {
                Log::warning('Invalid Midtrans notification signature', ['payload' => $payload]);
                return [
                    'success' => false,
                    'message' => 'Invalid signature'
                ];
            }

            $orderId = $payload['order_id'];
            $transactionStatus = $payload['transaction_status'];
            $fraudStatus = $payload['fraud_status'] ?? '';

            $paymentStatus = $this->determinePaymentStatus($transactionStatus, $fraudStatus);

            return [
                'success' => true,
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'payment_status' => $paymentStatus,
                'payload' => $payload
            ];

        } catch (\Exception $e) {
            Log::error('Midtrans Notification Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}