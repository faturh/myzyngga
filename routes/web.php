<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('landing');

require __DIR__.'/web/pelanggan.php';
require __DIR__.'/web/operator.php';
require __DIR__.'/auth.php';
