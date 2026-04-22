<?php

namespace App\Modules\Admin\Domain\Repositories;

use App\Models\Cabang;
use App\Models\JenisLayanan;
use App\Models\Transaksi;

interface AdminRepositoryInterface
{
    public function createCabang(array $payload): Cabang;

    public function createJenisLayanan(array $payload): JenisLayanan;

    public function createManualTransaksi(array $payload): Transaksi;

    public function dashboardSummary(): array;
}
