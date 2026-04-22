<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Order\Presentation\Web\Controllers\OrderPageController;
use App\Modules\Admin\Presentation\Web\Controllers\WebDashboardController;
use App\Modules\Admin\Presentation\Web\Controllers\WebAdminDashboardController;

// Guest Landing Page
Route::get('/', function () {
    return view('welcome');
})->name('landing');

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', WebDashboardController::class)->name('dashboard');

    // Profile (Shared)
    Route::view('profile', 'profile')->name('profile');

    // Admin Specific Routes
    Route::middleware(['admin'])->group(function () {
        Route::get('/admin/dashboard', WebAdminDashboardController::class)->name('admin.dashboard');
    });

    // Order Routes
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

require __DIR__.'/auth.php';
