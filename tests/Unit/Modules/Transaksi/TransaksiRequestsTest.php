<?php

namespace Tests\Unit\Modules\Transaksi;

use App\Modules\Transaksi\Presentation\Web\Requests\DeleteTransaksiCabangRequest;
use App\Modules\Transaksi\Presentation\Web\Requests\StoreTransaksiCabangRequest;
use App\Modules\Transaksi\Presentation\Web\Requests\UpdateStatusTransaksiCabangRequest;
use App\Modules\Transaksi\Presentation\Web\Requests\UpdateTransaksiCabangRequest;
use PHPUnit\Framework\TestCase;

class TransaksiRequestsTest extends TestCase
{
    public function test_store_transaksi_rules_exist(): void
    {
        $rules = (new StoreTransaksiCabangRequest)->rules();

        $this->assertArrayHasKey('pelanggan_id', $rules);
        $this->assertArrayHasKey('jenis_pakaian_id', $rules);
        $this->assertArrayHasKey('jenis_layanan_id.*.*', $rules);
        $this->assertArrayHasKey('total_pakaian.*', $rules);
    }

    public function test_update_transaksi_rules_exist(): void
    {
        $rules = (new UpdateTransaksiCabangRequest)->rules();

        $this->assertArrayHasKey('status', $rules);
        $this->assertArrayHasKey('detail_transaksi_id', $rules);
        $this->assertArrayHasKey('layanan_tambahan_id.*', $rules);
    }

    public function test_update_status_rules_exist(): void
    {
        $rules = (new UpdateStatusTransaksiCabangRequest)->rules();

        $this->assertArrayHasKey('id', $rules);
        $this->assertArrayHasKey('status', $rules);
    }

    public function test_delete_transaksi_rules_exist(): void
    {
        $rules = (new DeleteTransaksiCabangRequest)->rules();

        $this->assertArrayHasKey('transaksi_id', $rules);
    }
}
