<?php

namespace App\Modules\Customer\Presentation\Http\Controllers;

use App\Modules\Customer\Application\Services\CustomerService;
use App\Modules\Customer\Presentation\Http\Resources\CustomerProfileResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\Request;

class GetCustomerProfileController
{
    public function __construct(
        private readonly CustomerService $service,
    ) {
    }

    public function __invoke(Request $request)
    {
        $profile = $this->service->getProfile($request->user());

        $profile->load(['addresses', 'preference', 'user']);

        return ApiResponse::success([
            'profile' => new CustomerProfileResource($profile),
        ]);
    }
}
