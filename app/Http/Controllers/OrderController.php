<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modules\Order\Presentation\Web\Controllers\OrderPageController;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderPageController $controller,
    ) {
    }

    public function pickupLocation(Request $request, string $service)
    {
        return $this->controller->pickupLocation($request, $service);
    }

    public function storePickupLocation(Request $request)
    {
        return $this->controller->storePickupLocation($request);
    }

    public function pickupDetails(Request $request, string $service)
    {
        return $this->controller->pickupDetails($request, $service);
    }

    public function storePickupDetails(Request $request)
    {
        return $this->controller->storePickupDetails($request);
    }

    public function booking(Request $request)
    {
        return $this->controller->booking($request);
    }

    public function updateSession(Request $request)
    {
        return $this->controller->updateSession($request);
    }

    public function confirm(Request $request)
    {
        return $this->controller->confirm($request);
    }

    public function detail(Request $request)
    {
        return $this->controller->detail($request);
    }

    public function history(Request $request)
    {
        return $this->controller->history($request);
    }

    public function check(Request $request)
    {
        return $this->controller->check($request);
    }
}
