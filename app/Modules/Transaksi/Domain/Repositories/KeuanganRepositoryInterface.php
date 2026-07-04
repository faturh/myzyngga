<?php

namespace App\Modules\Transaksi\Domain\Repositories;

use App\Models\KeuanganToko;
use Illuminate\Support\Collection;

interface KeuanganRepositoryInterface
{
    /**
     * Get all financial records (manual and paid transaction income) based on filters.
     *
     * @param int|null $cabangId
     * @param string|null $filterType (daily, weekly, monthly)
     * @param string|null $startDate
     * @param string|null $endDate
     * @return Collection
     */
    public function allByCabang(?int $cabangId, ?string $filterType, ?string $startDate, ?string $endDate): Collection;

    /**
     * Save a manual financial record.
     *
     * @param array $data
     * @return KeuanganToko
     */
    public function save(array $data): KeuanganToko;

    /**
     * Delete a manual financial record.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Calculate dynamic store balance.
     *
     * @param int|null $cabangId
     * @return float
     */
    public function getStoreBalance(?int $cabangId): float;
}
