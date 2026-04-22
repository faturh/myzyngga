<?php

use Illuminate\Support\Facades\Route;

// Guest Landing Page
Route::get('/', function () {
    return view('welcome');
})->name('landing');

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Default Redirect based on role
    Route::get('/dashboard', function () {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return view('dashboard');
    })->name('dashboard');

    // Profile (Shared)
    Route::view('profile', 'profile')->name('profile');

    // Admin Specific Routes
    Route::middleware(['admin'])->group(function () {
        Route::get('/admin/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
    });

    // Order Routes
    Route::get('/order/{service}/pickup', [App\Http\Controllers\OrderController::class, 'pickupLocation'])
        ->name('order.pickup');

    Route::post('/order/pickup', [App\Http\Controllers\OrderController::class, 'storePickupLocation'])
        ->name('order.pickup.store');

    Route::get('/order/booking', [App\Http\Controllers\OrderController::class, 'booking'])
        ->name('order.booking');

    Route::post('/order/confirm', [App\Http\Controllers\OrderController::class, 'confirm'])
        ->name('order.confirm');

    Route::get('/order/detail', [App\Http\Controllers\OrderController::class, 'detail'])
        ->name('order.detail');

    Route::get('/order/history', [App\Http\Controllers\OrderController::class, 'history'])
        ->name('order.history');
});

require __DIR__.'/auth.php';
