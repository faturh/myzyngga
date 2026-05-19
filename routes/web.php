<?php

use App\Modules\Order\Presentation\Web\Controllers\PublicNotaLookupController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('home');
    }
    return view('welcome');
})->middleware('guest')->name('landing');

Route::match(['get', 'post'], '/cek-nota', PublicNotaLookupController::class)->name('landing-page.nota');

require __DIR__.'/web/pelanggan.php';
require __DIR__.'/web/operator.php';
require __DIR__.'/auth.php';
