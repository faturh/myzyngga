<?php

namespace App\Modules\Admin\Presentation\Http\Controllers;

use App\Modules\Admin\Application\Services\AdminService;
use App\Modules\Admin\Presentation\Http\Requests\StoreCabangRequest;
use App\Shared\Http\ApiResponse;

class StoreCabangController
{
    public function __construct(
        private readonly AdminService $service,
    ) {
    }

    public function __invoke(StoreCabangRequest $request)
    {
        $cabang = $this->service->createCabang($request->validated());

        return ApiResponse::success([
            'cabang' => [
                'id' => $cabang->id,
                'nama' => $cabang->nama,
                'lokasi' => $cabang->lokasi,
                'alamat' => $cabang->alamat,
            ],
        ], 201);
    }
}
