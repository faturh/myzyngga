<?php

namespace App\Modules\Auth\Presentation\Web\Controllers;

use App\Modules\Auth\Application\Services\LogoutService;
use Illuminate\Http\Request;

class LogoutController
{
    public function __construct(
        private readonly LogoutService $logoutService,
    ) {
    }

    public function __invoke(Request $request)
    {
        return $this->logoutService->logout($request);
    }
}
