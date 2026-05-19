<?php

namespace App\Modules\Customer\Presentation\Web\Controllers;

use App\Modules\Order\Application\Services\OrderWebService;
use Illuminate\Http\Request;

class CustomerDashboardController
{
    public function __construct(
        private readonly OrderWebService $orderWebService,
    ) {
    }

    public function __invoke(Request $request)
    {
        if ($request->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return view('pelanggan.dashboard.index', $this->orderWebService->dashboardData($request->user()));
    }
}
