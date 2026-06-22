<?php

use App\Modules\Customer\Presentation\Web\Controllers\CustomerDashboardController;
use App\Modules\Customer\Presentation\Web\Controllers\CustomerNotificationController;
use App\Modules\Order\Presentation\Web\Controllers\OrderPageController;
use App\Modules\Order\Presentation\Web\Controllers\PublicStrukController;
use App\Http\Controllers\AddressController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    // Default Redirect based on role
    Route::get('/home', function () {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('dashboard');
    })->name('home');

    Route::get('/dashboard', CustomerDashboardController::class)->name('dashboard');

    // Profile & Notifications
    Route::view('profile', 'pelanggan.profile.index')->name('profile');
    Route::view('profile/account', 'pelanggan.profile.account')->name('profile.account');
    Route::get('notifications', CustomerNotificationController::class)->name('notifications');
    Route::get('profile/complaints', [OrderPageController::class, 'complaintsHistory'])->name('profile.complaints');
    Route::get('profile/complaints/{id}', [OrderPageController::class, 'complaintDetail'])->name('profile.complaint.detail');

    // Address Management
    Route::get('addresses/create/details', [AddressController::class, 'createDetails'])->name('addresses.create.details');
    Route::resource('addresses', AddressController::class)->except(['show']);
    Route::post('addresses/{address}/primary', [AddressController::class, 'setPrimary'])->name('addresses.primary');

     Route::get('/order/history', [OrderPageController::class, 'history'])
        ->name('order.history');
});

// Public Order Routes (Enabled for Guests)
Route::post('/order/update-session', [OrderPageController::class, 'updateSession'])
    ->name('order.update-session');

Route::get('/order/cancel', [OrderPageController::class, 'cancel'])
    ->name('order.cancel');

Route::get('/order/{service}/pickup', [OrderPageController::class, 'pickupLocation'])
    ->name('order.pickup');

Route::get('/order/pickup/{service}/details', [OrderPageController::class, 'pickupDetails'])
    ->name('order.pickup.details');

Route::post('/order/pickup/details/store', [OrderPageController::class, 'storePickupDetails'])
    ->name('order.pickup.details.store');

Route::post('/order/pickup', [OrderPageController::class, 'storePickupLocation'])
    ->name('order.pickup.store');

Route::get('/order/booking', [OrderPageController::class, 'booking'])
    ->name('order.booking');

Route::post('/order/confirm', [OrderPageController::class, 'confirm'])
    ->name('order.confirm');
 
 Route::get('/order/detail/{id?}', [OrderPageController::class, 'detail'])
    ->name('order.detail');

 Route::get('/order/{id}/repeat', [OrderPageController::class, 'repeat'])
    ->name('order.repeat');

 Route::get('/order/{id}/download-receipt', [OrderPageController::class, 'downloadReceipt'])
    ->name('order.download-receipt');

 Route::get('/order/{id}/request-delivery', [OrderPageController::class, 'requestDelivery'])
    ->name('order.request.delivery');
 Route::get('/order/{id}/request-delivery-confirm', [OrderPageController::class, 'requestDeliveryConfirm'])
    ->name('order.request.delivery.confirm');
 Route::post('/order/{id}/request-delivery', [OrderPageController::class, 'storeRequestDelivery'])
    ->name('order.delivery.store');
 Route::post('/order/{id}/request-delivery/rollback', [OrderPageController::class, 'rollbackDelivery'])
    ->name('order.delivery.rollback');

 Route::get('/order/{id}/complaint', [OrderPageController::class, 'complaint'])
    ->name('order.complaint');
 Route::post('/order/{id}/complaint', [OrderPageController::class, 'storeComplaint'])
    ->name('order.complaint.store');

 Route::get('/order/{id}/upgrade', [OrderPageController::class, 'upgrade'])
    ->name('order.upgrade');

 Route::post('/order/{id}/upgrade', [OrderPageController::class, 'processUpgrade'])
    ->name('order.upgrade.process');
 Route::post('/order/{id}/upgrade/rollback', [OrderPageController::class, 'rollbackUpgrade'])
    ->name('order.upgrade.rollback');

 Route::get('/order/{id}/payment', [OrderPageController::class, 'payment'])
    ->name('order.payment');

 Route::post('/order/{id}/payment', [OrderPageController::class, 'updatePayment'])
    ->name('order.payment.update');

 Route::get('/order/{id}/payment-method', [OrderPageController::class, 'paymentMethod'])
    ->name('order.payment-method');
 Route::get('/order/{id}/payment-waiting', [OrderPageController::class, 'paymentWaiting'])
    ->name('order.payment.waiting');
 Route::post('/order/{id}/process-payment', [OrderPageController::class, 'processPayment'])
    ->name('order.process-payment');
 Route::get('/order/{id}/payment-instruction', [OrderPageController::class, 'paymentInstruction'])
    ->name('order.payment-instruction');
 Route::get('/order/{id}/payment-status', [OrderPageController::class, 'paymentStatus'])
    ->name('order.payment-status');
 Route::post('/order/{id}/payment-cancel', [OrderPageController::class, 'paymentCancel'])
    ->name('order.payment-cancel');

// Proxy route to download external QR images securely
Route::get('/download-image', function (\Illuminate\Http\Request $request) {
    $url = $request->query('url');
    if (!$url || !str_contains($url, 'midtrans.com')) return abort(404);
    try {
        $content = file_get_contents($url);
        return response($content)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="QR_Code_Pembayaran.png"');
    } catch (\Exception $e) {
        abort(404);
    }
})->name('download.image');

// Public Order Check
Route::match(['get', 'post'], '/order/check', [OrderPageController::class, 'check'])
    ->name('order.check');

Route::get('/public-struk/{idOrNota}', PublicStrukController::class)
    ->name('public.cetak-struk');

