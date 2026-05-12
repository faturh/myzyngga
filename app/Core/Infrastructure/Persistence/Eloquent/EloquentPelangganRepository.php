<?php

namespace App\Core\Infrastructure\Persistence\Eloquent;

use App\Models\Pelanggan;
use App\Core\Domain\Repositories\PelangganRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentPelangganRepository implements PelangganRepositoryInterface
{
    public function getAll(): Collection
    {
        return Pelanggan::orderBy('created_at', 'asc')->get();
    }

    public function findById(int $id): ?Pelanggan
    {
        return Pelanggan::find($id);
    }

    public function create(array $data): Pelanggan
    {
        return Pelanggan::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return Pelanggan::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return Pelanggan::where('id', $id)->delete();
    }
}
