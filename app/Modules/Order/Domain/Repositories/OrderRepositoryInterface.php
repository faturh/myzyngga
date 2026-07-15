<?php

namespace App\Modules\Order\Domain\Repositories;

use App\Models\Transaksi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface OrderRepositoryInterface
{
    public function create(array $payload): Transaksi;

    public function findById(string $id): ?Transaksi;

    public function lockById(string $id): ?Transaksi;

    public function findByNotaPelanggan(string $nota): ?Transaksi;

    public function latestByPelangganId(int $pelangganId, ?bool $finished = null): ?Transaksi;

    public function firstAvailableCabangId(): ?int;

    public function firstAvailableLayananPrioritasId(): ?int;

    public function firstAssignablePegawaiId(): ?int;

    public function paginateByPelangganId(int $pelangganId, int $perPage = 10): LengthAwarePaginator;

    public function searchForPublicCheck(string $query, string $phoneLast4, int $limit = 5): Collection;

    public function updateStatus(Transaksi $transaksi, string $status): Transaksi;

    public function updatePaymentInformation(string $orderId, array $payload): ?Transaksi;
}
