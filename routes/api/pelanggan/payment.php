<?php

use App\Modules\Payment\Presentation\Http\Controllers\GetPaymentMethodsController;
use App\Modules\Payment\Presentation\Http\Controllers\VerifyPaymentController;
use Illuminate\Support\Facades\Route;

Route::post('/payment/notification', [\App\Modules\Payment\Presentation\Http\Controllers\MidtransWebhookController::class, 'handle']);

Route::middleware('auth')->group(function (): void {
    Route::get('/payment/methods', GetPaymentMethodsController::class);
    Route::post('/payments/{orderId}/verify', VerifyPaymentController::class);
});
