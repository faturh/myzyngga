<?php

namespace App\Modules\Payment\Domain\Repositories;

use App\Models\Payment;

interface PaymentRepositoryInterface
{
    public function findByOrderId(string $orderId): ?Payment;

    public function upsertForOrder(string $orderId, array $payload): Payment;
}
