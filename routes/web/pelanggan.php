<?php

use App\Modules\Admin\Presentation\Web\Controllers\WebDashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AddressController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    // Default Redirect based on role
    Route::get('/home', function () {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('dashboard');
    })->name('home');

    Route::get('/dashboard', WebDashboardController::class)->name('dashboard');

    // Profile & Notifications
    Route::view('profile', 'pelanggan.profile.index')->name('profile');
    Route::view('profile/account', 'pelanggan.profile.account')->name('profile.account');
    Route::view('notifications', 'pelanggan.notifications.index')->name('notifications');

    // Address Management
    Route::get('addresses/create/details', [AddressController::class, 'createDetails'])->name('addresses.create.details');
    Route::resource('addresses', AddressController::class)->except(['show']);
    Route::post('addresses/{address}/primary', [AddressController::class, 'setPrimary'])->name('addresses.primary');

    // Order Routes
    Route::get('/order/{service}/pickup', [OrderController::class, 'pickupLocation'])
        ->name('order.pickup');

    Route::get('/order/pickup/{service}/details', [OrderController::class, 'pickupDetails'])
        ->name('order.pickup.details');

    Route::post('/order/pickup/details/store', [OrderController::class, 'storePickupDetails'])
        ->name('order.pickup.details.store');

    Route::post('/order/pickup', [OrderController::class, 'storePickupLocation'])
        ->name('order.pickup.store');

    Route::get('/order/booking', [OrderController::class, 'booking'])
        ->name('order.booking');

    Route::post('/order/confirm', [OrderController::class, 'confirm'])
        ->name('order.confirm');

    Route::post('/order/update-session', [OrderController::class, 'updateSession'])
        ->name('order.update-session');

    Route::get('/order/detail', [OrderController::class, 'detail'])
        ->name('order.detail');

    Route::get('/order/history', [OrderController::class, 'history'])
        ->name('order.history');
});

// Public Order Check
Route::match(['get', 'post'], '/order/check', [OrderController::class, 'check'])
    ->name('order.check');
