<?php

namespace App\Modules\Order\Infrastructure\Persistence;

use App\Models\Cabang;
use App\Models\LayananPrioritas;
use App\Models\Transaksi;
use App\Models\User;
use App\Modules\Order\Domain\Repositories\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function create(array $payload): Transaksi
    {
        return Transaksi::query()->create($payload);
    }

    public function findById(string $id): ?Transaksi
    {
        return Transaksi::query()->with(['pelanggan', 'payments'])->find($id);
    }

    public function firstAvailableCabangId(): ?int
    {
        return Cabang::query()->orderBy('id')->value('id');
    }

    public function firstAvailableLayananPrioritasId(): ?int
    {
        return LayananPrioritas::query()->orderBy('id')->value('id');
    }

    public function firstAssignablePegawaiId(): ?int
    {
        $adminId = User::query()
            ->where('role', 'admin')
            ->orderBy('id')
            ->value('id');

        if ($adminId !== null) {
            return (int) $adminId;
        }

        $staffId = User::query()
            ->where('role', '!=', 'customer')
            ->orderBy('id')
            ->value('id');

        return $staffId !== null ? (int) $staffId : null;
    }

    public function paginateByPelangganId(int $pelangganId, int $perPage = 10): LengthAwarePaginator
    {
        return Transaksi::query()
            ->where('pelanggan_id', $pelangganId)
            ->latest('waktu')
            ->paginate($perPage);
    }

    public function updateStatus(Transaksi $transaksi, string $status): Transaksi
    {
        $transaksi->status = $status;
        $transaksi->save();

        return $transaksi->refresh();
    }

    public function updatePaymentInformation(string $orderId, array $payload): ?Transaksi
    {
        $transaksi = Transaksi::query()->find($orderId);
        if (! $transaksi) {
            return null;
        }

        $transaksi->fill($payload);
        $transaksi->save();

        return $transaksi->refresh()->load(['pelanggan', 'payments']);
    }
}
