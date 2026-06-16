<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    \Midtrans\Config::$serverKey = config('midtrans.server_key');
    \Midtrans\Config::$isProduction = config('midtrans.is_production');
    \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
    \Midtrans\Config::$is3ds = config('midtrans.is_3ds');
    
    $token = \Midtrans\Snap::getSnapToken([
        'transaction_details' => [
            'order_id' => 'TEST-12345',
            'gross_amount' => 10000
        ]
    ]);
    echo "SUCCESS: " . $token;
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
