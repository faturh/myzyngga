<?php

namespace App\Modules\Order\Presentation\Web\Controllers;

use App\Modules\Order\Application\Services\OrderService;
use App\Modules\Order\Application\Services\OrderWebService;
use Illuminate\Http\Request;

class OrderPageController
{
    public function __construct(
        private readonly OrderWebService $webService,
        private readonly OrderService $orderService,
    ) {
    }

    public function pickupLocation(Request $request, string $service)
    {
        return view('order.pickup-location', $this->webService->pickupLocationData($service));
    }

    public function storePickupLocation(Request $request)
    {
        return $this->webService->storePickupLocation($request);
    }

    public function booking(Request $request)
    {
        $payload = $this->webService->bookingData();
        if ($payload === null) {
            return redirect()->route('dashboard');
        }

        return view('order.booking', $payload);
    }

    public function confirm(Request $request)
    {
        return $this->webService->confirmOrder($request, $this->orderService);
    }

    public function detail(Request $request)
    {
        return view('order.detail');
    }

    public function history(Request $request)
    {
        return view('order.history');
    }
}
