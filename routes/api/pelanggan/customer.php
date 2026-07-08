<?php

use App\Modules\Customer\Presentation\Http\Controllers\DeleteAddressController;
use App\Modules\Customer\Presentation\Http\Controllers\GetCustomerProfileController;
use App\Modules\Customer\Presentation\Http\Controllers\ListAddressesController;
use App\Modules\Customer\Presentation\Http\Controllers\ListNotificationsController;
use App\Modules\Customer\Presentation\Http\Controllers\MarkNotificationReadController;
use App\Modules\Customer\Presentation\Http\Controllers\SetPrimaryAddressController;
use App\Modules\Customer\Presentation\Http\Controllers\StoreAddressController;
use App\Modules\Customer\Presentation\Http\Controllers\UpdateAddressController;
use App\Modules\Customer\Presentation\Http\Controllers\UpsertCustomerAddressController;
use App\Modules\Customer\Presentation\Http\Controllers\UpsertCustomerPreferenceController;
use App\Modules\Order\Presentation\Http\Controllers\ListComplaintsController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    // ── Profil & Preferensi ───────────────────────────────────────────────
    Route::get('/customer/profile', GetCustomerProfileController::class);
    Route::put('/customer/address', UpsertCustomerAddressController::class);    // legacy, tetap ada
    Route::put('/customer/preferences', UpsertCustomerPreferenceController::class);

    // ── Multi-Address CRUD ────────────────────────────────────────────────
    Route::get('/customer/addresses', ListAddressesController::class);
    Route::post('/customer/addresses', StoreAddressController::class);
    Route::put('/customer/addresses/{id}', UpdateAddressController::class);
    Route::delete('/customer/addresses/{id}', DeleteAddressController::class);
    Route::post('/customer/addresses/{id}/primary', SetPrimaryAddressController::class);

    // ── Notifikasi ────────────────────────────────────────────────────────
    Route::get('/customer/notifications', ListNotificationsController::class);
    Route::post('/customer/notifications/{id}/read', MarkNotificationReadController::class);

    // ── Komplain ──────────────────────────────────────────────────────────
    Route::get('/customer/complaints', ListComplaintsController::class);
});
