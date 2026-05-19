<?php

namespace App\Modules\Order\Presentation\Web\Controllers;

use App\Modules\Order\Domain\Repositories\OrderRepositoryInterface;
use Illuminate\Http\Request;

class PublicNotaLookupController
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
    ) {
    }

    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'nota' => ['required', 'string'],
        ]);

        $order = $this->orders->findByNotaPelanggan($validated['nota']);

        abort_unless($order, 404, 'Pesanan tidak ditemukan.');

        $detailLayanan = $order->detailTransaksi->map(function ($detail) {
            $firstService = $detail->detailLayananTransaksi->first();

            return [
                'pakaian' => $firstService?->hargaJenisLayanan?->jenisPakaian?->nama ?? '-',
                'total' => $detail->total_pakaian,
                'layanan' => $detail->detailLayananTransaksi
                    ->map(fn ($layanan) => $layanan->hargaJenisLayanan?->jenisLayanan?->nama)
                    ->filter()
                    ->values(),
            ];
        })->values();

        return [
            [
                'id' => $order->id,
                'nota_pelanggan' => $order->nota_pelanggan,
                'tanggal' => optional($order->waktu)->toDateString(),
                'cabang_nama' => $order->cabang->nama ?? '-',
                'pelanggan_nama' => $order->pelanggan->nama ?? '-',
                'jenis_pembayaran' => $order->jenis_pembayaran,
                'total_bayar_akhir' => $order->total_bayar_akhir,
                'bayar' => $order->bayar,
                'kembalian' => $order->kembalian,
                'status' => $order->status,
            ],
            $detailLayanan,
            [],
        ];
    }
}
