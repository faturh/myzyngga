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

        $transaksi->load(['layananPrioritas', 'timbangan.items.jenisPakaian', 'pegawai', 'pelanggan', 'listPengerjaan']);

        return response()->json([
            'data' => [
                'transaksi' => $transaksi
            ],
            'message' => 'Pesanan Manual #' . $transaksi->nota . ' berhasil dibuat.',
            'status' => 200
        ], 200);
    }
}
