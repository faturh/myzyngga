<?php

namespace App\Core\Infrastructure\Persistence\Eloquent;

use App\Models\Transaksi;
use App\Core\Domain\Repositories\TransaksiRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentTransaksiRepository implements TransaksiRepositoryInterface
{
    public function getAllByCabang(int $cabangId): Collection
    {
        return Transaksi::with(['pelanggan', 'pegawai', 'gamis', 'layananPrioritas'])
            ->where('cabang_id', $cabangId)
            ->orderBy('waktu', 'desc')
            ->get();
    }

    public function findById(int $id): ?Transaksi
    {
        return Transaksi::with(['pelanggan', 'pegawai', 'gamis', 'layananPrioritas'])->find($id);
    }

    public function create(array $data): Transaksi
    {
        return Transaksi::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return Transaksi::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return Transaksi::where('id', $id)->delete();
    }
}
