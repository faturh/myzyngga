<?php

use App\Modules\Customer\Presentation\Http\Controllers\GetCustomerProfileController;
use App\Modules\Customer\Presentation\Http\Controllers\UpsertCustomerAddressController;
use App\Modules\Customer\Presentation\Http\Controllers\UpsertCustomerPreferenceController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {
    Route::get('/customer/profile', GetCustomerProfileController::class);
    Route::put('/customer/address', UpsertCustomerAddressController::class);
    Route::put('/customer/preferences', UpsertCustomerPreferenceController::class);
});
