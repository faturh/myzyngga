<?php

use App\Modules\Order\Presentation\Http\Controllers\CreateOrderController;
use App\Modules\Order\Presentation\Http\Controllers\GetOrderController;
use App\Modules\Order\Presentation\Http\Controllers\ListOrderHistoryController;
use App\Modules\Order\Presentation\Http\Controllers\UpdateOrderStatusController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {
    Route::post('/orders', CreateOrderController::class);
    Route::get('/orders/history', ListOrderHistoryController::class);
    Route::get('/orders/{orderId}', GetOrderController::class);
    Route::patch('/orders/{orderId}/status', UpdateOrderStatusController::class);
});
