<?php

use App\Modules\Admin\Presentation\Http\Controllers\AdminDashboardController;
use App\Modules\Admin\Presentation\Http\Controllers\StoreCabangController;
use App\Modules\Admin\Presentation\Http\Controllers\StoreJenisLayananController;
use App\Modules\Admin\Presentation\Http\Controllers\StoreTransaksiManualController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function (): void {
    Route::get('/dashboard', AdminDashboardController::class);
    Route::post('/cabang', StoreCabangController::class);
    Route::post('/jenis-layanan', StoreJenisLayananController::class);
    Route::post('/transaksi/manual', StoreTransaksiManualController::class);
});
