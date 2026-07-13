<?php

namespace App\Modules\Customer\Presentation\Http\Controllers;

use App\Modules\Customer\Application\Services\CustomerService;
use App\Shared\Exceptions\DomainException;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\Request;

class SetPrimaryAddressController
{
    public function __construct(
        private readonly CustomerService $service,
    ) {
    }

    public function __invoke(Request $request, $id)
    {
        $id = (int) $id;
        try {
            $address = $this->service->setPrimaryForUser($request->user(), $id);
        } catch (DomainException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode() ?: 422);
        }

        return ApiResponse::success([
            'address' => [
                'id'         => $address->id,
                'label'      => $address->label,
                'is_primary' => (bool) $address->is_default,
            ],
            'message' => 'Alamat utama berhasil diatur.',
        ]);
    }
}
