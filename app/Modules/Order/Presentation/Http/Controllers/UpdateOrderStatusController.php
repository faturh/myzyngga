<?php

namespace App\Modules\Order\Presentation\Http\Controllers;

use App\Modules\Order\Application\Services\OrderService;
use App\Modules\Order\Presentation\Http\Requests\UpdateOrderStatusRequest;
use App\Modules\Order\Presentation\Http\Resources\OrderResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Support\Facades\Gate;

class UpdateOrderStatusController
{
    public function __construct(
        private readonly OrderService $service,
    ) {
    }

    public function __invoke(UpdateOrderStatusRequest $request, string $orderId)
    {
        Gate::authorize('manage-order-status');
        $order = $this->service->updateOrderStatus($orderId, $request->validated('status'));

        return ApiResponse::success([
            'order' => new OrderResource($order),
        ]);
    }
}
