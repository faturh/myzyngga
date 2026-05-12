<?php

namespace App\Modules\Auth\Presentation\Web\Controllers;

use App\Modules\Auth\Application\Services\VerifyEmailService;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerifyEmailController
{
    public function __construct(
        private readonly VerifyEmailService $verifyEmailService,
    ) {
    }

    public function __invoke(EmailVerificationRequest $request)
    {
        return $this->verifyEmailService->verify($request);
    }
}
