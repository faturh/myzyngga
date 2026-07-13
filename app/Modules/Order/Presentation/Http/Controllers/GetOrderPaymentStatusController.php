<?php

namespace App\Modules\Order\Presentation\Http\Controllers;

use App\Modules\Order\Application\Services\OrderService;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GetOrderPaymentStatusController
{
    public function __construct(
        private readonly OrderService $service,
    ) {
    }

    public function __invoke(Request $request, string $orderId)
    {
        $order = $this->service->getOrder($orderId);

        // IDOR protection: reuse Gate yang sudah ada
        Gate::authorize('view-order', $order);

        return ApiResponse::success([
            'order_id'       => $order->id,
            'nota'           => $order->nota,
            'payment_status' => $order->payment_status, // pending | paid | failed
            'paid_at'        => $order->paid_at?->toISOString(),
            'total_bayar'    => $order->total_bayar_akhir,
        ]);
    }
}
