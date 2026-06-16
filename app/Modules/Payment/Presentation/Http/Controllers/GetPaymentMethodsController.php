<?php

namespace App\Modules\Payment\Presentation\Http\Controllers;

use App\Modules\Payment\Application\Services\PaymentService;
use App\Shared\Http\ApiResponse;

class GetPaymentMethodsController
{
    public function __construct(
        private readonly PaymentService $service,
    ) {
    }

    public function __invoke()
    {
        return ApiResponse::success([
            'methods' => $this->service->paymentMethods(),
        ]);
    }
}
