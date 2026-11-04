<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$t = App\Models\Transaksi::whereNotIn('status', ['Pesanan Selesai', 'Selesai', 'Batal'])->latest()->first();
if($t) {
    $t->waktu = now();
    $t->save();
    echo "ID: " . $t->id . "\n";
    echo $t->canBeUpgraded() ? 'Bisa diupgrade' : 'Tetap gak bisa';
} else {
    echo 'Tidak ada transaksi aktif';
}
