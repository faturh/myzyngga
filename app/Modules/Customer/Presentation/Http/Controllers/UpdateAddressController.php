<?php

namespace App\Modules\Customer\Presentation\Http\Controllers;

use App\Modules\Customer\Application\Services\CustomerService;
use App\Modules\Customer\Presentation\Http\Requests\UpdateAddressRequest;
use App\Shared\Exceptions\DomainException;
use App\Shared\Http\ApiResponse;

class UpdateAddressController
{
    public function __construct(
        private readonly CustomerService $service,
    ) {
    }

    public function __invoke(UpdateAddressRequest $request, int $id)
    {
        try {
            $address = $this->service->updateAddressForUser(
                $request->user(),
                $id,
                $request->validated()
            );
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
        ]);
    }
}
