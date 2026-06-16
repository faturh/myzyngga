<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$order = \App\Models\Transaksi::find('a206e1ae-3ec4-43f6-9f02-9042388ecdfc'); 
echo 'id: ' . $order->id . PHP_EOL;
echo 'total_bayar_akhir: ' . $order->total_bayar_akhir . PHP_EOL;
echo 'bayar: ' . $order->bayar . PHP_EOL;
