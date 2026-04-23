<?php

use App\Modules\Admin\Presentation\Web\Controllers\WebDashboardController;
use App\Modules\Order\Presentation\Web\Controllers\OrderPageController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', WebDashboardController::class)->name('dashboard');

    Route::view('profile', 'pelanggan.profile.index')->name('profile');

    Route::get('/order/{service}/pickup', [OrderPageController::class, 'pickupLocation'])
        ->name('order.pickup');

    Route::post('/order/pickup', [OrderPageController::class, 'storePickupLocation'])
        ->name('order.pickup.store');

    Route::get('/order/booking', [OrderPageController::class, 'booking'])
        ->name('order.booking');

    Route::post('/order/confirm', [OrderPageController::class, 'confirm'])
        ->name('order.confirm');

    Route::get('/order/detail', [OrderPageController::class, 'detail'])
        ->name('order.detail');

    Route::get('/order/history', [OrderPageController::class, 'history'])
        ->name('order.history');
});
