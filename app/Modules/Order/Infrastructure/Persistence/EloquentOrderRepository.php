<?php

namespace App\Modules\Order\Infrastructure\Persistence;

use App\Models\Cabang;
use App\Models\LayananPrioritas;
use App\Models\Transaksi;
use App\Models\User;
use App\Modules\Order\Domain\Repositories\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class EloquentOrderRepository implements OrderRepositoryInterface
{
    private const ORDER_RELATIONS = [
        'pelanggan',
        'payments',
        'layananPrioritas',
        'cabang',
        'detailTransaksi.detailLayananTransaksi.hargaJenisLayanan.jenisLayanan',
        'detailTransaksi.detailLayananTransaksi.hargaJenisLayanan.jenisPakaian',
    ];

    public function create(array $payload): Transaksi
    {
        return Transaksi::query()->create($payload);
    }

    public function findById(string $id): ?Transaksi
    {
        return Transaksi::query()->with(self::ORDER_RELATIONS)->find($id);
    }

    public function findByNotaPelanggan(string $nota): ?Transaksi
    {
        return Transaksi::query()
            ->with(self::ORDER_RELATIONS)
            ->where('nota', $nota)
            ->first();
    }

    public function latestByPelangganId(int $pelangganId, ?bool $finished = null): ?Transaksi
    {
        return Transaksi::query()
            ->with(self::ORDER_RELATIONS)
            ->where('pelanggan_id', $pelangganId)
            ->when($finished === true, fn (Builder $query) => $query->where('list_status_pengerjaan_id', 5))
            ->when($finished === false, fn (Builder $query) => $query->where('list_status_pengerjaan_id', '!=', 5))
            ->latest('waktu')
            ->first();
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
            ->where(function (Builder $query) {
                $query->role('admin')
                      ->orWhere('role', 'admin');
            })
            ->orderBy('id')
            ->value('id');

        if ($adminId !== null) {
            return (int) $adminId;
        }

        $staffId = User::query()
            ->where(function (Builder $query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', '!=', 'customer');
                })
                ->orWhere(function ($q) {
                    $q->whereNotNull('role')
                      ->where('role', '!=', 'customer');
                });
            })
            ->orderBy('id')
            ->value('id');

        return $staffId !== null ? (int) $staffId : null;
    }

    public function paginateByPelangganId(int $pelangganId, int $perPage = 10): LengthAwarePaginator
    {
        return Transaksi::query()
            ->with(self::ORDER_RELATIONS)
            ->where('pelanggan_id', $pelangganId)
            ->latest('waktu')
            ->paginate($perPage);
    }

    public function searchForPublicCheck(string $query, string $phoneLast4, int $limit = 5): Collection
    {
        $normalizedQuery = trim($query);

        return Transaksi::query()
            ->with(self::ORDER_RELATIONS)
            ->whereHas('pelanggan', function (Builder $query) use ($phoneLast4) {
                $query->where('telepon', 'like', '%'.$phoneLast4)
                      ->orWhereHas('user', function (Builder $userQuery) use ($phoneLast4) {
                          $userQuery->where('phone', 'like', '%'.$phoneLast4);
                      });
            })
            ->where(function (Builder $query) use ($normalizedQuery) {
                if (\Illuminate\Support\Str::isUuid($normalizedQuery)) {
                    $query->where('id', $normalizedQuery);
                }
                $query
                    ->orWhere('nota', 'like', '%'.$normalizedQuery.'%')
                    ->orWhereHas('pelanggan', function (Builder $customerQuery) use ($normalizedQuery) {
                        $customerQuery->where('nama', 'like', '%'.$normalizedQuery.'%')
                                      ->orWhereHas('user', function (Builder $userQuery) use ($normalizedQuery) {
                                          $userQuery->where('name', 'like', '%'.$normalizedQuery.'%');
                                      });
                    });
            })
            ->latest('waktu')
            ->limit($limit)
            ->get();
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

        return $transaksi->refresh()->load(self::ORDER_RELATIONS);
    }
}
