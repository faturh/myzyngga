<?php

namespace Tests\Unit\Modules\Customer;

use App\Modules\Customer\Presentation\Http\Requests\UpsertCustomerAddressRequest;
use App\Modules\Customer\Presentation\Http\Requests\UpsertCustomerPreferenceRequest;
use PHPUnit\Framework\TestCase;

class CustomerRequestsTest extends TestCase
{
    public function test_address_request_rules_exist(): void
    {
        $rules = (new UpsertCustomerAddressRequest())->rules();

        $this->assertArrayHasKey('address', $rules);
        $this->assertArrayHasKey('lat', $rules);
        $this->assertContains('required', $rules['address']);
    }

    public function test_preference_request_rules_exist(): void
    {
        $rules = (new UpsertCustomerPreferenceRequest())->rules();

        $this->assertArrayHasKey('default_parfum', $rules);
        $this->assertArrayHasKey('default_payment_method', $rules);
    }
}
