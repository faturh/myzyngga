<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    require __DIR__.'/api/order.php';
    require __DIR__.'/api/customer.php';
    require __DIR__.'/api/payment.php';
    require __DIR__.'/api/admin.php';
});
