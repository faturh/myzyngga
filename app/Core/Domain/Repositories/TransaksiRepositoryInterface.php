<?php

namespace App\Core\Domain\Repositories;

use App\Models\Transaksi;
use Illuminate\Support\Collection;

interface TransaksiRepositoryInterface
{
    public function getAllByCabang(int $cabangId): Collection;
    public function findById(int $id): ?Transaksi;
    public function create(array $data): Transaksi;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
