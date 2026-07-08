<?php

namespace App\Modules\Order\Presentation\Http\Controllers;

use App\Modules\Order\Application\DTO\CreateOrderData;
use App\Modules\Order\Application\Services\OrderService;
use App\Modules\Order\Presentation\Http\Requests\CreateOrderRequest;
use App\Modules\Order\Presentation\Http\Resources\OrderResource;
use App\Shared\Http\ApiResponse;

class CreateOrderController
{
    public function __construct(
        private readonly OrderService $service,
    ) {
    }

    public function __invoke(CreateOrderRequest $request)
    {
        $validated = $request->validated();

        $order = $this->service->createOrder(new CreateOrderData(
            pelangganId: (int) $validated['pelanggan_id'],
            cabangId: (int) $validated['cabang_id'],
            layananPrioritasId: (int) $validated['layanan_prioritas_id'],
            pickupAddress: $validated['pickup_address'],
            pickupDetailAddress: $validated['pickup_detail_address'] ?? null,
            pickupDate: $validated['pickup_date'],
            pickupTime: $validated['pickup_time'],
            parfum: $validated['parfum'] ?? null,
            catatan: $validated['catatan'] ?? null,
            paymentMethod: $validated['payment_method'],
            estimatedTotal: (float) $validated['estimated_total'],
        ));

        $order->load(['layananPrioritas', 'timbangan.items.jenisPakaian', 'pegawai', 'pelanggan', 'listPengerjaan']);

        return ApiResponse::success([
            'order' => new OrderResource($order),
        ], 201);
    }
}
