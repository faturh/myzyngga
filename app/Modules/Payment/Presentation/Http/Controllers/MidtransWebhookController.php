<?php

namespace App\Modules\Payment\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Payment\Application\Services\PaymentWebhookService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MidtransWebhookController extends Controller
{
    public function __construct(
        private readonly PaymentWebhookService $webhookService
    ) {
    }

    public function handle(Request $request): JsonResponse
    {
        // Midtrans configuration setup
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

        $payload = app()->environment('testing') ? $request->all() : null;
        $this->webhookService->handleMidtransNotification($payload);

        return response()->json(['status' => 'success']);
    }
}
