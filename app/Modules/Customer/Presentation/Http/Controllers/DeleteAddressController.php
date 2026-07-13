<?php

namespace App\Modules\Customer\Presentation\Http\Controllers;

use App\Modules\Customer\Application\Services\CustomerService;
use App\Shared\Exceptions\DomainException;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\Request;

class DeleteAddressController
{
    public function __construct(
        private readonly CustomerService $service,
    ) {
    }

    public function __invoke(Request $request, $id)
    {
        $id = (int) $id;
        try {
            $this->service->deleteAddressForUser($request->user(), $id);
        } catch (DomainException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode() ?: 422);
        }

        return ApiResponse::success(['message' => 'Alamat berhasil dihapus.']);
    }
}
