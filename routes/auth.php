<?php

use App\Modules\Auth\Presentation\Web\Controllers\LoginAttemptController;
use App\Modules\Auth\Presentation\Web\Controllers\LogoutController;
use App\Modules\Auth\Presentation\Web\Controllers\SendEmailVerificationNotificationController;
use App\Modules\Auth\Presentation\Web\Controllers\SendPasswordResetLinkController;
use App\Modules\Auth\Presentation\Web\Controllers\StoreNewPasswordController;
use App\Modules\Auth\Presentation\Web\Controllers\UpdateAuthenticatedPasswordController;
use App\Modules\Auth\Presentation\Web\Controllers\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('guest')->group(function () {
    Volt::route('register', 'pages.auth.register')
        ->name('register');

    Volt::route('login', 'pages.auth.login')
        ->name('login');

    Route::post('login', LoginAttemptController::class)
        ->name('login.attempt');

    Volt::route('forgot-password', 'pages.auth.forgot-password')
        ->name('password.request');

    Route::post('forgot-password', SendPasswordResetLinkController::class)
        ->name('password.email');

    Volt::route('reset-password/{token}', 'pages.auth.reset-password')
        ->name('password.reset');

    Route::post('reset-password', StoreNewPasswordController::class)
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Volt::route('register/phone', 'pages.auth.register-phone')
        ->name('register.phone');

    Volt::route('verify-email', 'pages.auth.verify-email')
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', SendEmailVerificationNotificationController::class)
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Volt::route('confirm-password', 'pages.auth.confirm-password')
        ->name('password.confirm');

    Route::post('logout', LogoutController::class)
        ->name('logout');

    Route::put('password', UpdateAuthenticatedPasswordController::class)
        ->name('password.update');
});
