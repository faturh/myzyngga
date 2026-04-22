<?php

namespace App\Modules\Order\Domain\Repositories;

use App\Models\Transaksi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    public function create(array $payload): Transaksi;

    public function findById(string $id): ?Transaksi;

    public function firstAvailableCabangId(): ?int;

    public function firstAvailableLayananPrioritasId(): ?int;

    public function firstAssignablePegawaiId(): ?int;

    public function paginateByPelangganId(int $pelangganId, int $perPage = 10): LengthAwarePaginator;

    public function updateStatus(Transaksi $transaksi, string $status): Transaksi;

    public function updatePaymentInformation(string $orderId, array $payload): ?Transaksi;
}
