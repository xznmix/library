<?php

return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'merchant_id' => env('MIDTRANS_MERCHANT_ID'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'environment' => env('MIDTRANS_ENVIRONMENT', 'sandbox'),
    
    // Gunakan Guzzle dengan konfigurasi yang tepat
    'http_client_options' => [
        'timeout' => 30,
        'connect_timeout' => 30,
        'verify' => false, // Hanya untuk development (disable SSL verify)
    ],
];