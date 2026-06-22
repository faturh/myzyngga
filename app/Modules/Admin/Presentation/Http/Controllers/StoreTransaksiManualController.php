<?php

namespace App\Modules\Admin\Presentation\Http\Controllers;

use App\Modules\Admin\Application\Services\AdminService;
use App\Modules\Admin\Presentation\Http\Requests\StoreTransaksiManualRequest;
use App\Shared\Http\ApiResponse;

class StoreTransaksiManualController
{
    public function __construct(
        private readonly AdminService $service,
    ) {
    }

    public function __invoke(StoreTransaksiManualRequest $request)
    {
        $transaksi = $this->service->createManualTransaksi($request->validated());

        return ApiResponse::success([
            'transaksi' => [
                'id' => $transaksi->id,
                'nota' => $transaksi->nota,
                'status' => $transaksi->status,
                'payment_status' => $transaksi->payment_status,
            ],
        ], 201);
    }
}
