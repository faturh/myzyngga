<?php

namespace App\Modules\Order\Presentation\Http\Controllers;

use App\Modules\Order\Application\Services\OrderService;
use App\Modules\Order\Presentation\Http\Resources\OrderResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GetOrderController
{
    public function __construct(
        private readonly OrderService $service,
    ) {
    }

    public function __invoke(Request $request, string $orderId)
    {
        $order = $this->service->getOrder($orderId);
        Gate::authorize('view-order', $order);

        return ApiResponse::success([
            'order' => new OrderResource($order),
        ]);
    }
}
