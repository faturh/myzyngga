<?php

namespace App\Modules\Order\Application\Services;

use App\Models\Transaksi;
use App\Models\User;
use App\Modules\Customer\Domain\Repositories\CustomerRepositoryInterface;
use App\Modules\Order\Application\DTO\CreateOrderData;
use App\Modules\Order\Domain\Repositories\OrderRepositoryInterface;
use App\Modules\Payment\Domain\Repositories\PaymentRepositoryInterface;
use App\Shared\Exceptions\DomainException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly PaymentRepositoryInterface $paymentRepository,
        private readonly CustomerRepositoryInterface $customerRepository,
    ) {}

    public function createOrder(CreateOrderData $data): Transaksi
    {
        return DB::transaction(function () use ($data) {
            $transaksi = $this->orderRepository->create(
                $data->toPersistencePayload($this->resolvePegawaiId($data))
            );

            $this->paymentRepository->upsertForOrder($transaksi->id, [
                'transaksi_id' => $transaksi->id,
                'method' => $data->paymentMethod,
                'amount' => $data->estimatedTotal,
                'status' => 'pending',
            ]);

            return $this->orderRepository->findById($transaksi->id)
                ?? $transaksi->load(['pelanggan', 'payments']);
        });
    }

    public function getOrder(string $orderId): Transaksi
    {
        $order = $this->orderRepository->findById($orderId);
        if (! $order) {
            throw new DomainException('Order tidak ditemukan.', 404);
        }

        return $order;
    }

    public function historyForUser(User $user, int $perPage = 10): LengthAwarePaginator
    {
        $pelanggan = $this->customerRepository->findByUser($user);
        if (! $pelanggan) {
            throw new DomainException('Data pelanggan untuk user ini belum tersedia.', 404);
        }

        return $this->orderRepository->paginateByPelangganId($pelanggan->id, $perPage);
    }

    public function updateOrderStatus(string $orderId, string $status): Transaksi
    {
        $order = $this->getOrder($orderId);

        return $this->orderRepository->updateStatus($order, $status);
    }

    private function resolvePegawaiId(CreateOrderData $data): int
    {
        $pegawaiId = $data->pegawaiId ?? $this->orderRepository->firstAssignablePegawaiId();

        if ($pegawaiId === null) {
            throw new DomainException('Belum ada petugas yang bisa ditugaskan untuk order baru.', 422);
        }

        return (int) $pegawaiId;
    }
}
