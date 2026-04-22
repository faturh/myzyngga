<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modules\Order\Presentation\Web\Controllers\OrderPageController;

class OrderController extends Controller
{
    // @deprecated gunakan App\Modules\Order\Presentation\Web\Controllers\OrderPageController
    public function __construct(
        private readonly OrderPageController $controller,
    ) {
    }

    /**
     * Show the pickup location selection page.
     */
    public function pickupLocation(Request $request, string $service)
    {
        return $this->controller->pickupLocation($request, $service);
    }

    /**
     * Store pickup location and redirect to booking page.
     */
    public function storePickupLocation(Request $request)
    {
        return $this->controller->storePickupLocation($request);
    }

    /**
     * Show the full booking form (Figma 77:301).
     */
    public function booking(Request $request)
    {
        return $this->controller->booking($request);
    }

    /**
     * Confirm and place the order.
     */
    public function confirm(Request $request)
    {
        return $this->controller->confirm($request);
    }

    /**
     * Show order detail page (Figma 95:10 & 221:719).
     */
    public function detail(Request $request)
    {
        return $this->controller->detail($request);
    }

    /**
     * Show order history page (Figma 110:15).
     */
    public function history(Request $request)
    {
        return $this->controller->history($request);
    }
}
