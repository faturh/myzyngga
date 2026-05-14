<?php

use App\Modules\Admin\Presentation\Web\Controllers\WebDashboardController;
use App\Modules\Order\Presentation\Web\Controllers\OrderPageController;
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

    Route::post('/order/update-session', [OrderPageController::class, 'updateSession'])
        ->name('order.update-session');
    
    Route::get('/order/cancel', [OrderPageController::class, 'cancel'])
        ->name('order.cancel');
 
 
     Route::get('/order/history', [OrderPageController::class, 'history'])
        ->name('order.history');
});

// Public Order Routes (Enabled for Guests)
Route::get('/order/{service}/pickup', [OrderPageController::class, 'pickupLocation'])
    ->name('order.pickup');

Route::get('/order/pickup/{service}/details', [OrderPageController::class, 'pickupDetails'])
    ->name('order.pickup.details');

Route::post('/order/pickup/details/store', [OrderPageController::class, 'storePickupDetails'])
    ->name('order.pickup.details.store');

Route::post('/order/pickup', [OrderPageController::class, 'storePickupLocation'])
    ->name('order.pickup.store');

Route::get('/order/booking', [OrderPageController::class, 'booking'])
    ->name('order.booking');

Route::post('/order/confirm', [OrderPageController::class, 'confirm'])
    ->name('order.confirm');
 
 Route::get('/order/detail/{id?}', [OrderPageController::class, 'detail'])
    ->name('order.detail');


// Public Order Check
Route::match(['get', 'post'], '/order/check', [OrderPageController::class, 'check'])
    ->name('order.check');
