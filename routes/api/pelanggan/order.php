<?php

use App\Modules\Order\Presentation\Http\Controllers\CreateOrderController;
use App\Modules\Order\Presentation\Http\Controllers\GetOrderController;
use App\Modules\Order\Presentation\Http\Controllers\ListOrderHistoryController;
use App\Modules\Order\Presentation\Http\Controllers\UpdateOrderStatusController;
use App\Modules\Order\Presentation\Web\Controllers\OrderPageController;
use Illuminate\Support\Facades\Route;

Route::post('/orders/track', [OrderPageController::class, 'check']);

Route::middleware('auth')->group(function (): void {
    Route::post('/orders', CreateOrderController::class);
    Route::get('/orders/history', ListOrderHistoryController::class);
    Route::get('/orders/{orderId}', GetOrderController::class);
    Route::patch('/orders/{orderId}/status', UpdateOrderStatusController::class);
    
    Route::post('/orders/{id}/complaint', [OrderPageController::class, 'storeComplaint']);
    Route::post('/orders/{id}/upgrade', [OrderPageController::class, 'processUpgrade']);
    Route::post('/orders/{id}/delivery-request', [OrderPageController::class, 'storeRequestDelivery']);
});
