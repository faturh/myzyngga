<?php

namespace App\Modules\Admin\Presentation\Http\Controllers;

use App\Modules\Admin\Application\Services\AdminService;
use App\Modules\Admin\Presentation\Http\Requests\StoreJenisLayananRequest;
use App\Shared\Http\ApiResponse;

class StoreJenisLayananController
{
    public function __construct(
        private readonly AdminService $service,
    ) {
    }

    public function __invoke(StoreJenisLayananRequest $request)
    {
        $jenisLayanan = $this->service->createJenisLayanan($request->validated());

        return ApiResponse::success([
            'jenis_layanan' => [
                'id' => $jenisLayanan->id,
                'nama' => $jenisLayanan->nama,
                'deskripsi' => $jenisLayanan->deskripsi,
                'cabang_id' => $jenisLayanan->cabang_id,
            ],
        ], 201);
    }
}
