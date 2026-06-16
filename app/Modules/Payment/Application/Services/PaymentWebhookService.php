<?php

namespace App\Modules\Payment\Application\Services;

use App\Modules\Order\Domain\Repositories\OrderRepositoryInterface;
use Illuminate\Support\Facades\Log;

class PaymentWebhookService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository
    ) {
    }

    public function handleMidtransNotification(): void
    {
        try {
            $notification = new \Midtrans\Notification();
        } catch (\Exception $e) {
            Log::error('Midtrans Webhook Error: ' . $e->getMessage());
            return;
        }

        $transaction = $notification->transaction_status;
        $type = $notification->payment_type;
        $orderIdRaw = $notification->order_id;
        $fraud = $notification->fraud_status;

        // Our order_id was sent as {$order->id}-{time()}
        // We need to extract the real order ID
        $orderIdParts = explode('-', $orderIdRaw);
        if (count($orderIdParts) > 5) {
            array_pop($orderIdParts);
        }
        $realOrderId = implode('-', $orderIdParts);

        $order = $this->orderRepository->findById($realOrderId);
        if (! $order) {
            Log::warning("Midtrans Webhook: Order $realOrderId not found.");
            return;
        }

        if ($transaction == 'capture') {
            if ($type == 'credit_card') {
                if ($fraud == 'challenge') {
                    // TODO set payment status in merchant's database to 'Challenge by FDS'
                    Log::info("Transaction order_id: " . $realOrderId ." is challenged by FDS");
                }
                else {
                    $this->markAsPaid($realOrderId, $notification);
                }
            }
        }
        else if ($transaction == 'settlement') {
            $this->markAsPaid($realOrderId, $notification);
        }
        else if ($transaction == 'pending') {
            Log::info("Waiting customer to finish transaction order_id: " . $realOrderId);
        }
        else if ($transaction == 'deny') {
            Log::info("Payment using " . $type . " for transaction order_id: " . $realOrderId . " is denied.");
        }
        else if ($transaction == 'expire') {
            Log::info("Payment using " . $type . " for transaction order_id: " . $realOrderId . " is expired.");
        }
        else if ($transaction == 'cancel') {
            Log::info("Payment using " . $type . " for transaction order_id: " . $realOrderId . " is canceled.");
        }
    }

    private function markAsPaid(string $orderId, \Midtrans\Notification $notification): void
    {
        $order = $this->orderRepository->findById($orderId);
        if (!$order) return;

        // If it's already fully paid, skip
        if ((float)$order->bayar >= (float)$order->total_bayar_akhir) {
            return;
        }

        $grossAmount = (float) $notification->gross_amount;
        $paymentType = $notification->payment_type;

        $newBayar = (float)$order->bayar + $grossAmount;
        
        $payload = [
            'bayar' => $newBayar,
            'jenis_pembayaran' => $paymentType === 'gopay' || $paymentType === 'qris' ? 'qris' : ($paymentType === 'credit_card' ? 'credit_card' : 'transfer'),
        ];

        if ($newBayar >= (float)$order->total_bayar_akhir) {
            $payload['payment_status'] = 'paid';
            $payload['paid_at'] = now();
        }

        $this->orderRepository->updatePaymentInformation($orderId, $payload);

        Log::info("Midtrans Webhook: Order $orderId marked as paid.");
    }
}
