<?php

namespace App\Modules\Admin\Presentation\Http\Controllers;

use App\Modules\Admin\Application\Services\AdminService;
use App\Shared\Http\ApiResponse;

class AdminDashboardController
{
    public function __construct(
        private readonly AdminService $service,
    ) {
    }

    public function __invoke()
    {
        return ApiResponse::success([
            'summary' => $this->service->dashboardSummary(),
        ]);
    }
}
