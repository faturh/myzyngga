<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$order = \App\Models\Transaksi::latest()->first();
$orderId = $order->id;
$grossAmount = 10000.00;
$serverKey = config('midtrans.server_key');

$payload = [
    'transaction_time' => date('Y-m-d H:i:s'),
    'transaction_status' => 'settlement',
    'transaction_id' => 'dummy-txn-123',
    'status_message' => 'midtrans payment notification',
    'status_code' => '200',
    'signature_key' => hash('sha512', $orderId . '200' . '10000.00' . $serverKey),
    'payment_type' => 'qris',
    'order_id' => $orderId,
    'merchant_id' => 'G123456789',
    'gross_amount' => '10000.00',
    'fraud_status' => 'accept',
    'currency' => 'IDR'
];

$service = app(\App\Modules\Payment\Application\Services\PaymentWebhookService::class);
$service->handleMidtransNotification($payload);

echo "Success! Current order bayar is now: " . \App\Models\Transaksi::find($orderId)->bayar . "\n";
