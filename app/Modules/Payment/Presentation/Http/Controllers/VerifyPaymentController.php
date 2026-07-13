<?php

namespace App\Modules\Payment\Presentation\Http\Controllers;

use App\Modules\Payment\Application\Services\PaymentService;
use App\Modules\Payment\Presentation\Http\Requests\VerifyPaymentRequest;
use App\Shared\Http\ApiResponse;
use Illuminate\Support\Facades\Gate;

class VerifyPaymentController
{
    public function __construct(
        private readonly PaymentService $service,
    ) {
    }

    public function __invoke(VerifyPaymentRequest $request, string $orderId)
    {
        Gate::authorize('verify-payment');
        $payment = $this->service->verifyPayment($orderId, $request->user(), $request->validated());

        $payment->load(['verifier', 'transaksi.layananPrioritas', 'transaksi.timbangan.items.jenisPakaian', 'transaksi.pegawai', 'transaksi.pelanggan', 'transaksi.listPengerjaan']);

        return response()->json([
            'data' => [
                'payment' => $payment
            ],
            'message' => 'Pembayaran untuk transaksi ' . optional($payment->transaksi)->nota . ' berhasil diverifikasi oleh ' . optional($payment->verifier)->name . '.',
            'status' => 200
        ], 200);
    }
}
