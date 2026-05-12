<?php

use Illuminate\Support\Facades\Route;

// Guest Landing Page
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('home');
    }
    return view('welcome');
})->middleware('guest')->name('landing');

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Default Redirect based on role
    Route::get('/home', function () {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return view('dashboard');
    })->name('home');

    // Profile (Shared)
    Route::view('profile', 'profile')->name('profile');
    Route::view('profile/account', 'profile.account')->name('profile.account');

    // Address Management
    Route::get('addresses/create/details', [App\Http\Controllers\AddressController::class, 'createDetails'])->name('addresses.create.details');
    Route::resource('addresses', App\Http\Controllers\AddressController::class)->except(['show']);
    Route::post('addresses/{address}/primary', [App\Http\Controllers\AddressController::class, 'setPrimary'])->name('addresses.primary');

    // Order Pickup Details (Booking Flow - Figma 77:301 style review page)
    Route::get('/order/pickup/{service}/details', [App\Http\Controllers\OrderController::class, 'pickupDetails'])->name('order.pickup.details');
    Route::post('/order/pickup/details/store', [App\Http\Controllers\OrderController::class, 'storePickupDetails'])->name('order.pickup.details.store');

    // Admin Specific Routes
    Route::middleware(['admin'])->group(function () {
        Route::get('/admin/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
    });
});

// Order Routes (Public & Authenticated)
Route::get('/order/detail', [App\Http\Controllers\OrderController::class, 'detail'])
    ->name('order.detail');

Route::get('/order/history', [App\Http\Controllers\OrderController::class, 'history'])
    ->name('order.history');

Route::match(['get', 'post'], '/order/check', [App\Http\Controllers\OrderController::class, 'check'])
    ->name('order.check');

Route::get('/order/{service}/pickup', [App\Http\Controllers\OrderController::class, 'pickupLocation'])
    ->name('order.pickup');

Route::post('/order/pickup', [App\Http\Controllers\OrderController::class, 'storePickupLocation'])
    ->name('order.pickup.store');

Route::get('/order/booking', [App\Http\Controllers\OrderController::class, 'booking'])
    ->name('order.booking');

Route::post('/order/confirm', [App\Http\Controllers\OrderController::class, 'confirm'])
    ->name('order.confirm');

require __DIR__.'/auth.php';
