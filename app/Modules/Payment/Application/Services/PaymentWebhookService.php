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

    public function handleMidtransNotification(?array $mockPayload = null): void
    {
        if ($mockPayload !== null) {
            $notification = (object) $mockPayload;
        } else {
            try {
                $notification = new \Midtrans\Notification();
            } catch (\Exception $e) {
                Log::error('Midtrans Webhook Error: ' . $e->getMessage());
                return;
            }
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

    private function markAsPaid(string $orderId, object $notification): void
    {
        $order = $this->orderRepository->findById($orderId);
        if (!$order) return;

        // Process pending metadata if any to calculate real target amount
        $meta = json_decode($order->payment_metadata, true) ?? [];
        $targetAmount = (float) $order->total_bayar_akhir;
        if (isset($meta['pending_upgrade'])) {
            $targetAmount += (float) ($meta['pending_upgrade']['price_diff'] ?? 0);
        }
        if (isset($meta['pending_delivery'])) {
            $targetAmount += (float) ($meta['pending_delivery']['delivery_fee'] ?? 0);
        }

        // If it's already fully paid against the new target amount, skip
        if ((float)$order->bayar >= $targetAmount && $targetAmount > 0) {
            return;
        }

        $grossAmount = (float) $notification->gross_amount;
        $paymentType = $notification->payment_type;

        $newBayar = (float)$order->bayar + $grossAmount;
        
        $payload = [
            'bayar' => $newBayar,
            'jenis_pembayaran' => $paymentType === 'gopay' || $paymentType === 'qris' ? 'qris' : ($paymentType === 'credit_card' ? 'credit_card' : 'transfer'),
        ];

        // Process pending metadata if any
        $meta = json_decode($order->payment_metadata, true) ?? [];
        $metaChanged = false;

        if (isset($meta['pending_upgrade'])) {
            $payload['layanan_prioritas_id'] = $meta['pending_upgrade']['new_service_id'];
            $payload['total_biaya_prioritas'] = (float) $order->total_biaya_prioritas + (float) $meta['pending_upgrade']['price_diff'];
            $payload['total_bayar_akhir'] = (float) $order->total_bayar_akhir + (float) $meta['pending_upgrade']['price_diff'];
            
            // Record to upgrade history
            \App\Models\UpgradeLayanan::create([
                'transaksi_id' => $orderId,
                'layanan_asal_id' => $order->layanan_prioritas_id,
                'layanan_tujuan_id' => $meta['pending_upgrade']['new_service_id'],
                'biaya_upgrade' => (float) $meta['pending_upgrade']['price_diff'],
            ]);

            unset($meta['pending_upgrade']);
            $metaChanged = true;
        }

        if (isset($meta['pending_delivery'])) {
            $payload['pickup_address'] = $meta['pending_delivery']['address'];
            $payload['pickup_detail_address'] = $meta['pending_delivery']['detail_address'];
            $payload['is_roundtrip'] = true;
            unset($meta['pending_delivery']);
            $metaChanged = true;
        }

        if ($metaChanged) {
            $payload['payment_metadata'] = json_encode($meta);
        }

        // Check if fully paid against the potentially updated total_bayar_akhir
        $finalTotal = $payload['total_bayar_akhir'] ?? $order->total_bayar_akhir;
        if ($newBayar >= (float)$finalTotal) {
            $payload['payment_status'] = 'paid';
            $payload['paid_at'] = now();
        }

        $this->orderRepository->updatePaymentInformation($orderId, $payload);

        Log::info("Midtrans Webhook: Order $orderId marked as paid.");
    }
}
