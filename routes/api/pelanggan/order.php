<?php

use App\Modules\Order\Presentation\Http\Controllers\CreateOrderController;
use App\Modules\Order\Presentation\Http\Controllers\GetOrderController;
use App\Modules\Order\Presentation\Http\Controllers\GetOrderPaymentStatusController;
use App\Modules\Order\Presentation\Http\Controllers\ListOrderHistoryController;
use App\Modules\Order\Presentation\Http\Controllers\UpdateOrderStatusController;
use App\Modules\Order\Presentation\Web\Controllers\OrderPageController;
use Illuminate\Support\Facades\Route;

// Throttled: pencarian cocok berdasarkan nama pelanggan (mudah ditebak) +
// phone_last_4 (cuma 4 digit) — tanpa batas percobaan bisa di-brute-force.
Route::post('/orders/track', [OrderPageController::class, 'check'])
    ->middleware('throttle:10,1');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/orders', CreateOrderController::class);
    Route::get('/orders/history', ListOrderHistoryController::class);
    Route::get('/orders/{orderId}', GetOrderController::class);
    Route::patch('/orders/{orderId}/status', UpdateOrderStatusController::class);
    Route::get('/orders/{orderId}/payment-status', GetOrderPaymentStatusController::class);

    Route::post('/orders/{id}/complaint', [OrderPageController::class, 'storeComplaint']);
    Route::post('/orders/{id}/upgrade', [OrderPageController::class, 'processUpgrade']);
    Route::post('/orders/{id}/delivery-request', [OrderPageController::class, 'storeRequestDelivery']);
});
