<?php

namespace App\Core\Domain\Repositories;

use App\Models\Pelanggan;
use Illuminate\Support\Collection;

interface PelangganRepositoryInterface
{
    public function getAll(): Collection;
    public function findById(int $id): ?Pelanggan;
    public function create(array $data): Pelanggan;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
