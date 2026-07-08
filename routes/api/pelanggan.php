<?php

require __DIR__.'/pelanggan/customer.php';
require __DIR__.'/pelanggan/order.php';
require __DIR__.'/pelanggan/payment.php';
require __DIR__.'/pelanggan/auth.php';

Route::get('/debug-db-info', function () {
    return response()->json([
        'cabang' => \App\Models\Cabang::all(),
        'layanan_prioritas' => \App\Models\LayananPrioritas::all(),
        'users' => \App\Models\User::limit(10)->get(['id', 'name', 'email', 'role']),
        'pelanggan' => \App\Models\Pelanggan::limit(10)->get()
    ]);
});

