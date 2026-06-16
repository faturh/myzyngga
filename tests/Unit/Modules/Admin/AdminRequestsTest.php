<?php

namespace Tests\Unit\Modules\Admin;

use App\Modules\Admin\Presentation\Http\Requests\StoreCabangRequest;
use App\Modules\Admin\Presentation\Http\Requests\StoreJenisLayananRequest;
use App\Modules\Admin\Presentation\Http\Requests\StoreTransaksiManualRequest;
use PHPUnit\Framework\TestCase;

class AdminRequestsTest extends TestCase
{
    public function test_store_cabang_rules_exist(): void
    {
        $rules = (new StoreCabangRequest())->rules();
        $this->assertArrayHasKey('nama', $rules);
        $this->assertArrayHasKey('lokasi', $rules);
    }

    public function test_store_jenis_layanan_rules_exist(): void
    {
        $rules = (new StoreJenisLayananRequest())->rules();
        $this->assertArrayHasKey('nama', $rules);
        $this->assertArrayHasKey('cabang_id', $rules);
    }

    public function test_store_transaksi_manual_rules_exist(): void
    {
        $rules = (new StoreTransaksiManualRequest())->rules();
        $this->assertArrayHasKey('pelanggan_id', $rules);
        $this->assertArrayHasKey('total_bayar_akhir', $rules);
    }
}
