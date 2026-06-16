<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    require __DIR__.'/api/pelanggan.php';
    require __DIR__.'/api/operator.php';
});
