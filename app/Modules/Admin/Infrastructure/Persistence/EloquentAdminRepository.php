<?php

namespace App\Modules\Admin\Infrastructure\Persistence;

use App\Models\Cabang;
use App\Models\JenisLayanan;
use App\Models\Transaksi;
use App\Modules\Admin\Domain\Repositories\AdminRepositoryInterface;
use Illuminate\Support\Str;

class EloquentAdminRepository implements AdminRepositoryInterface
{
    public function createCabang(array $payload): Cabang
    {
        return Cabang::query()->create([
            'nama' => $payload['nama'],
            'slug' => Str::slug($payload['nama']),
            'lokasi' => $payload['lokasi'],
            'alamat' => $payload['alamat'] ?? null,
        ]);
    }

    public function createJenisLayanan(array $payload): JenisLayanan
    {
        return JenisLayanan::query()->create($payload);
    }

    public function createManualTransaksi(array $payload): Transaksi
    {
        return Transaksi::query()->create($payload);
    }

    public function dashboardSummary(): array
    {
        return [
            'total_transaksi' => Transaksi::query()->count(),
            'transaksi_hari_ini' => Transaksi::query()->whereDate('created_at', today())->count(),
            'order_pending_payment' => Transaksi::query()->where('payment_status', 'pending')->count(),
            'order_in_progress' => Transaksi::query()->whereIn('status', ['created', 'picked_up', 'in_progress'])->count(),
            'total_cabang' => Cabang::query()->count(),
            'total_jenis_layanan' => JenisLayanan::query()->count(),
        ];
    }
}
