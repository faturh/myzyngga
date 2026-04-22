<?php

namespace App\Modules\Customer\Presentation\Http\Controllers;

use App\Modules\Customer\Application\Services\CustomerService;
use App\Modules\Customer\Presentation\Http\Requests\UpsertCustomerAddressRequest;
use App\Shared\Http\ApiResponse;

class UpsertCustomerAddressController
{
    public function __construct(
        private readonly CustomerService $service,
    ) {
    }

    public function __invoke(UpsertCustomerAddressRequest $request)
    {
        $address = $this->service->upsertAddress($request->user(), $request->validated());

        return ApiResponse::success([
            'address' => [
                'id' => $address->id,
                'label' => $address->label,
                'address' => $address->address,
                'detail_address' => $address->detail_address,
                'lat' => $address->lat,
                'lng' => $address->lng,
            ],
        ]);
    }
}
