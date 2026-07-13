<?php

namespace App\Modules\Order\Presentation\Http\Controllers;

use App\Modules\Order\Application\Services\OrderService;
use App\Modules\Order\Presentation\Http\Resources\OrderResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\Request;

class ListOrderHistoryController
{
    public function __construct(
        private readonly OrderService $service,
    ) {
    }

    public function __invoke(Request $request)
    {
        $perPage = (int) $request->integer('per_page', 10);
        $paginator = $this->service->historyForUser($request->user(), $perPage);

        $paginator->getCollection()->each(function ($order) {
            $order->load(['layananPrioritas', 'timbangan.items.jenisPakaian', 'pegawai', 'pelanggan', 'listPengerjaan']);
        });

        return response()->json([
            'data' => [
                'orders' => OrderResource::collection($paginator->items())
            ],
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
            'status' => 200
        ], 200);
    }
}
