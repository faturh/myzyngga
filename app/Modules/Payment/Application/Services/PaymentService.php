<?php

namespace App\Modules\Payment\Application\Services;

use App\Models\Payment;
use App\Models\User;
use App\Modules\Order\Domain\Repositories\OrderRepositoryInterface;
use App\Modules\Payment\Domain\Repositories\PaymentRepositoryInterface;
use App\Shared\Exceptions\DomainException;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(
        private readonly PaymentRepositoryInterface $repository,
        private readonly OrderRepositoryInterface $orderRepository,
    ) {}

    public function paymentMethods(): array
    {
        return [
            ['id' => 'cash', 'label' => 'Cash'],
            ['id' => 'qris', 'label' => 'QRIS'],
            ['id' => 'transfer', 'label' => 'Transfer Bank'],
        ];
    }

    public function verifyPayment(string $orderId, User $verifier, array $payload): Payment
    {
        $order = $this->orderRepository->findById($orderId);
        if (! $order) {
            throw new DomainException('Order tidak ditemukan.', 404);
        }

        return DB::transaction(function () use ($orderId, $verifier, $payload, $order) {
            $verifiedAt = now();
            $amount = (float) $payload['amount'];

            $payment = $this->repository->upsertForOrder($orderId, [
                'method' => $payload['method'],
                'amount' => $amount,
                'status' => 'verified',
                'verified_at' => $verifiedAt,
                'verified_by' => $verifier->id,
                'notes' => $payload['notes'] ?? null,
            ]);

            $updatedOrder = $this->orderRepository->updatePaymentInformation($orderId, [
                'jenis_pembayaran' => $payload['method'],
                'payment_status' => 'paid',
                'paid_at' => $verifiedAt,
                'bayar' => $amount,
                'kembalian' => max(0, $amount - (float) $order->total_bayar_akhir),
            ]);

            if (! $updatedOrder) {
                throw new DomainException('Order tidak ditemukan.', 404);
            }

            return $payment;
        });
    }
}
