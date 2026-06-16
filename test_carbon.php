<?php
$app = require __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$ws = app(\App\Modules\Order\Application\Services\OrderWebService::class);
$o = \App\Models\Transaksi::where('status', 'Baru')->first();
$currentPriority = $o->layananPrioritas;
$availableUpgrades = \App\Models\LayananPrioritas::where('cabang_id', $currentPriority->cabang_id)
            ->where('prioritas', '>', $currentPriority->prioritas)
            ->get();

$baseDate = \Carbon\Carbon::parse($o->pickup_date ?? $o->waktu ?? now());
echo "Base date: " . $baseDate->toDateTimeString() . "\n";
echo "Now: " . now()->toDateTimeString() . "\n";
foreach ($availableUpgrades as $upgrade) {
    echo "Upgrade: " . $upgrade->nama . "\n";
    $durationHours = 24; // assuming
    $newFinishTime = $baseDate->copy()->addHours($durationHours);
    echo "New Finish Time: " . $newFinishTime->toDateTimeString() . "\n";
    echo "Minus 5 hrs: " . $newFinishTime->copy()->subHours(5)->toDateTimeString() . "\n";
    $lte = now()->lte($newFinishTime->copy()->subHours(5));
    echo "now <= minus 5 hrs? " . ($lte ? "YES" : "NO") . "\n";
}
