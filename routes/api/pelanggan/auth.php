<?php

use App\Modules\Auth\Presentation\Http\Controllers\ApiAuthController;
use App\Modules\Auth\Presentation\Http\Controllers\GoogleAuthController;
use Illuminate\Support\Facades\Route;

// Throttled: register/login lewat API ini sebelumnya tidak punya proteksi
// brute-force sama sekali (beda dengan login web yang sudah pakai RateLimiter
// sendiri) — tanpa ini, password akun pelanggan bisa dicoba tanpa batas.
Route::post('/auth/register', [ApiAuthController::class, 'register'])
    ->middleware('throttle:10,1');
Route::post('/auth/login', [ApiAuthController::class, 'login'])
    ->middleware('throttle:5,1');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/auth/logout', [ApiAuthController::class, 'logout']);
});
