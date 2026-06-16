<?php

namespace App\Modules\Customer\Presentation\Http\Controllers;

use App\Modules\Customer\Application\Services\CustomerService;
use App\Modules\Customer\Presentation\Http\Requests\UpsertCustomerPreferenceRequest;
use App\Shared\Http\ApiResponse;

class UpsertCustomerPreferenceController
{
    public function __construct(
        private readonly CustomerService $service,
    ) {
    }

    public function __invoke(UpsertCustomerPreferenceRequest $request)
    {
        $preference = $this->service->upsertPreference($request->user(), $request->validated());

        return ApiResponse::success([
            'preferences' => [
                'default_parfum' => $preference->default_parfum,
                'default_note' => $preference->default_note,
                'default_payment_method' => $preference->default_payment_method,
            ],
        ]);
    }
}
