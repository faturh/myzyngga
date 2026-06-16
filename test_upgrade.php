<?php
$app = require __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$ws = app(\App\Modules\Order\Application\Services\OrderWebService::class);
$orders = \App\Models\Transaksi::where('status', 'Baru')->get();

foreach($orders as $o) {
    try {
        $ws->upgradeData($o->id, null);
        echo "Order {$o->id}: Success\n";
    } catch(\Exception $e) {
        echo "Order {$o->id}: Error - " . $e->getMessage() . "\n";
    }
}
