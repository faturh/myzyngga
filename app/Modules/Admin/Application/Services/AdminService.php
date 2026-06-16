<?php

namespace App\Modules\Admin\Application\Services;

use App\Modules\Admin\Domain\Repositories\AdminRepositoryInterface;
use Illuminate\Support\Str;

class AdminService
{
    public function __construct(
        private readonly AdminRepositoryInterface $repository,
    ) {
    }

    public function dashboardSummary(): array
    {
        return $this->repository->dashboardSummary();
    }

    public function createCabang(array $payload): \App\Models\Cabang
    {
        return $this->repository->createCabang($payload);
    }

    public function createJenisLayanan(array $payload): \App\Models\JenisLayanan
    {
        return $this->repository->createJenisLayanan($payload);
    }

    public function createManualTransaksi(array $payload): \App\Models\Transaksi
    {
        $suffix = strtoupper(substr(str_replace('-', '', (string) Str::uuid()), 0, 8));

        return $this->repository->createManualTransaksi([
            'nota' => 'ZYG-'.$suffix,
            'waktu' => now(),
            'pickup_address' => $payload['pickup_address'] ?? null,
            'pickup_detail_address' => $payload['pickup_detail_address'] ?? null,
            'pickup_date' => $payload['pickup_date'] ?? now()->toDateString(),
            'pickup_time' => $payload['pickup_time'] ?? '10:00',
            'parfum' => $payload['parfum'] ?? null,
            'catatan' => $payload['catatan'] ?? null,
            'total_biaya_layanan' => (float) $payload['total_biaya_layanan'],
            'total_biaya_prioritas' => (float) ($payload['total_biaya_prioritas'] ?? 0),
            'total_biaya_layanan_tambahan' => (float) ($payload['total_biaya_layanan_tambahan'] ?? 0),
            'total_bayar_akhir' => (float) $payload['total_bayar_akhir'],
            'jenis_pembayaran' => $payload['jenis_pembayaran'],
            'payment_status' => $payload['payment_status'] ?? 'pending',
            'paid_at' => ($payload['payment_status'] ?? 'pending') === 'paid' ? now() : null,
            'bayar' => (float) ($payload['bayar'] ?? 0),
            'kembalian' => (float) ($payload['kembalian'] ?? 0),
            'status' => $payload['status'] ?? 'created',
            'layanan_prioritas_id' => (int) $payload['layanan_prioritas_id'],
            'pelanggan_id' => (int) $payload['pelanggan_id'],
            'pegawai_id' => (int) $payload['pegawai_id'],
            'cabang_id' => (int) $payload['cabang_id'],
        ]);
    }
}
