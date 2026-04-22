<?php

namespace Tests\Unit\Modules\Payment;

use App\Modules\Payment\Presentation\Http\Requests\VerifyPaymentRequest;
use PHPUnit\Framework\TestCase;

class VerifyPaymentRequestTest extends TestCase
{
    public function test_verify_payment_rules_exist(): void
    {
        $rules = (new VerifyPaymentRequest())->rules();

        $this->assertArrayHasKey('method', $rules);
        $this->assertArrayHasKey('amount', $rules);
        $this->assertContains('required', $rules['method']);
    }
}
