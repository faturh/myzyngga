<?php

namespace App\Modules\Customer\Presentation\Web\Controllers;

use App\Modules\Order\Application\Services\OrderWebService;
use Illuminate\Http\Request;

class CustomerNotificationController
{
    public function __construct(
        private readonly OrderWebService $orderWebService,
    ) {
    }

    public function __invoke(Request $request)
    {
        return view('pelanggan.notifications.index', $this->orderWebService->notificationData($request->user()));
    }
}
