<?php

namespace App\Modules\Payment\Application\Services;

use App\Models\Payment;
use App\Models\User;
use App\Modules\Order\Domain\Repositories\OrderRepositoryInterface;
use App\Modules\Payment\Domain\Repositories\PaymentRepositoryInterface;
use App\Shared\Exceptions\DomainException;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(
        private readonly PaymentRepositoryInterface $repository,
        private readonly OrderRepositoryInterface $orderRepository,
    ) {}

    public function paymentMethods(): array
    {
        return [
            ['id' => 'cash', 'label' => 'Cash'],
            ['id' => 'qris', 'label' => 'QRIS'],
            ['id' => 'transfer', 'label' => 'Transfer Bank'],
        ];
    }

    public function verifyPayment(string $orderId, User $verifier, array $payload): Payment
    {
        $order = $this->orderRepository->findById($orderId);
        if (! $order) {
            throw new DomainException('Order tidak ditemukan.', 404);
        }

        return DB::transaction(function () use ($orderId, $verifier, $payload, $order) {
            $verifiedAt = now();
            $amount = (float) $payload['amount'];

            $payment = $this->repository->upsertForOrder($orderId, [
                'method' => $payload['method'],
                'amount' => $amount,
                'status' => 'verified',
                'verified_at' => $verifiedAt,
                'verified_by' => $verifier->id,
                'notes' => $payload['notes'] ?? null,
            ]);

            $orderPayload = [
                'jenis_pembayaran' => $payload['method'],
                'payment_status' => 'paid',
                'paid_at' => $verifiedAt,
                'bayar' => $amount,
                'kembalian' => max(0, $amount - (float) $order->total_bayar_akhir),
            ];

            // If the transaction is currently in "Menunggu Pembayaran" (list_status_pengerjaan_id = 2)
            $currentStatusId = DB::table('list_pengerjaan')
                ->where('id', $order->list_pengerjaan_id)
                ->value('list_status_pengerjaan_id');

            if ($currentStatusId == 2) {
                $listPengerjaan = new \App\Models\ListPengerjaan();
                $listPengerjaan->list_status_pengerjaan_id = 5;
                $listPengerjaan->save();

                $orderPayload['list_pengerjaan_id'] = $listPengerjaan->id;
                $orderPayload['status'] = 'Pesanan Selesai';

                $history = new \App\Models\ListHistoryPengerjaan();
                $history->transaksi_id = $order->id;
                $history->status_sebelumnya = 2;
                $history->status_sesudahnya = 5;
                $history->operator_id = $verifier->id;
                $history->keterangan = "Pembayaran diverifikasi oleh operator. Status menjadi Selesai.";
                $history->save();

                $listPengerjaan->list_history_pengerjaan_id = $history->id;
                $listPengerjaan->saveQuietly();
            }

            $updatedOrder = $this->orderRepository->updatePaymentInformation($orderId, $orderPayload);

            if (! $updatedOrder) {
                throw new DomainException('Order tidak ditemukan.', 404);
            }

            return $payment;
        });
    }
}
