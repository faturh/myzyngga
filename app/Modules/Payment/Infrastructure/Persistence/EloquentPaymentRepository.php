<?php

namespace App\Modules\Payment\Infrastructure\Persistence;

use App\Models\Payment;
use App\Modules\Payment\Domain\Repositories\PaymentRepositoryInterface;

class EloquentPaymentRepository implements PaymentRepositoryInterface
{
    public function findByOrderId(string $orderId): ?Payment
    {
        return Payment::query()->where('transaksi_id', $orderId)->latest()->first();
    }

    public function upsertForOrder(string $orderId, array $payload): Payment
    {
        return Payment::query()->updateOrCreate(
            ['transaksi_id' => $orderId],
            $payload,
        );
    }
}
