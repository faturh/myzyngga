<?php

namespace App\Modules\Order\Presentation\Http\Controllers;

use App\Modules\Order\Application\Services\OrderService;
use App\Modules\Order\Presentation\Http\Requests\UpdateOrderStatusRequest;
use App\Modules\Order\Presentation\Http\Resources\OrderResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Support\Facades\Gate;

class UpdateOrderStatusController
{
    public function __construct(
        private readonly OrderService $service,
    ) {
    }

    public function __invoke(UpdateOrderStatusRequest $request, string $orderId)
    {
        Gate::authorize('manage-order-status');
        $order = $this->service->updateOrderStatus($orderId, $request->validated('status'));

        $order->load(['layananPrioritas', 'timbangan.items.jenisPakaian', 'pegawai', 'pelanggan', 'listPengerjaan']);

        $statusName = $order->status;
        $pegawaiName = optional($order->pegawai)->name ?? 'Siti Aminah';

        if (in_array(strtolower($statusName), ['proses pengerjaan', 'proses_pengerjaan', 'in_progress', 'proses'])) {
            $message = 'Pesanan #' . $order->nota . ' mulai dikerjakan oleh ' . $pegawaiName . '.';
        } elseif (in_array(strtolower($statusName), ['selesai', 'completed', 'pesanan selesai', 'pesanan_selesai'])) {
            $message = 'Pengerjaan pesanan #' . $order->nota . ' telah selesai. Pembayaran sudah lunas, status menjadi Selesai.';
        } else {
            $message = 'Status pesanan #' . $order->nota . ' berhasil diperbarui menjadi ' . $statusName . '.';
        }

        return response()->json([
            'data' => [
                'order' => new OrderResource($order)
            ],
            'message' => $message,
            'status' => 200
        ], 200);
    }
}
