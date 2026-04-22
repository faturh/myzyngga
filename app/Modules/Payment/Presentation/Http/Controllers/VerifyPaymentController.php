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

        return ApiResponse::success([
            'payment' => [
                'id' => $payment->id,
                'status' => $payment->status,
                'method' => $payment->method,
                'amount' => $payment->amount,
                'verified_at' => optional($payment->verified_at)->toISOString(),
            ],
        ]);
    }
}
