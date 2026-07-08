<?php

namespace App\Modules\Customer\Presentation\Http\Controllers;

use App\Modules\Customer\Application\Services\CustomerService;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\Request;

class ListAddressesController
{
    public function __construct(
        private readonly CustomerService $service,
    ) {
    }

    public function __invoke(Request $request)
    {
        $addresses = $this->service->listAddresses($request->user());

        return ApiResponse::success([
            'addresses' => $addresses->map(fn ($addr) => [
                'id'             => $addr->id,
                'label'          => $addr->label,
                'address'        => $addr->address,
                'detail_address' => $addr->detail_address,
                'lat'            => $addr->lat,
                'lng'            => $addr->lng,
                'is_primary'     => (bool) $addr->is_default,
            ])->values(),
        ]);
    }
}
