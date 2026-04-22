<?php

namespace Tests\Unit\Modules\Order;

use App\Modules\Order\Presentation\Http\Requests\CreateOrderRequest;
use PHPUnit\Framework\TestCase;

class CreateOrderRequestTest extends TestCase
{
    public function test_it_has_expected_validation_rules(): void
    {
        $request = new CreateOrderRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('pelanggan_id', $rules);
        $this->assertArrayHasKey('payment_method', $rules);
        $this->assertArrayHasKey('estimated_total', $rules);
        $this->assertContains('required', $rules['pickup_address']);
    }
}
