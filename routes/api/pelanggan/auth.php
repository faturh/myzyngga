<?php

use App\Modules\Auth\Presentation\Http\Controllers\ApiAuthController;
use App\Modules\Auth\Presentation\Http\Controllers\GoogleAuthController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [ApiAuthController::class, 'register']);
Route::post('/auth/login', [ApiAuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/auth/logout', [ApiAuthController::class, 'logout']);
});
