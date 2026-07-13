<?php

namespace App\Modules\Customer\Presentation\Http\Controllers;

use App\Modules\Customer\Application\Services\CustomerService;
use App\Modules\Customer\Presentation\Http\Requests\StoreAddressRequest;
use App\Shared\Exceptions\DomainException;
use App\Shared\Http\ApiResponse;

class StoreAddressController
{
    public function __construct(
        private readonly CustomerService $service,
    ) {
    }

    public function __invoke(StoreAddressRequest $request)
    {
        try {
            $address = $this->service->storeAddress($request->user(), $request->validated());
        } catch (DomainException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode() ?: 422);
        }

        return ApiResponse::success([
            'address' => [
                'id'             => $address->id,
                'label'          => $address->label,
                'address'        => $address->address,
                'detail_address' => $address->detail_address,
                'lat'            => $address->lat,
                'lng'            => $address->lng,
                'is_primary'     => (bool) $address->is_default,
            ],
        ], 201);
    }
}
