<?php

namespace Tests\Feature\Routes;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class TransaksiWebRoutesTest extends TestCase
{
    public function test_transaksi_dashboard_routes_are_registered(): void
    {
        $this->assertTrue(Route::has('transaksi'));
        $this->assertTrue(Route::has('transaksi.jadwal'));
        $this->assertTrue(Route::has('transaksi.view'));
        $this->assertTrue(Route::has('transaksi.view.layanan'));
        $this->assertTrue(Route::has('transaksi.create'));
        $this->assertTrue(Route::has('transaksi.store'));
        $this->assertTrue(Route::has('transaksi.edit'));
        $this->assertTrue(Route::has('transaksi.update'));
        $this->assertTrue(Route::has('transaksi.delete'));
        $this->assertTrue(Route::has('transaksi.edit.status'));
        $this->assertTrue(Route::has('transaksi.update.status'));
        $this->assertTrue(Route::has('transaksi.cetak-struk'));
    }

    public function test_transaksi_lurah_and_gamis_routes_are_registered(): void
    {
        $this->assertTrue(Route::has('transaksi.lurah'));
        $this->assertTrue(Route::has('transaksi.lurah.cabang'));
        $this->assertTrue(Route::has('transaksi.lurah.cabang.jadwal'));
        $this->assertTrue(Route::has('transaksi.lurah.view'));
        $this->assertTrue(Route::has('transaksi.lurah.view.layanan'));
        $this->assertTrue(Route::has('transaksi.lurah.cabang.create'));
        $this->assertTrue(Route::has('transaksi.lurah.cabang.store'));
        $this->assertTrue(Route::has('transaksi.lurah.cabang.edit'));
        $this->assertTrue(Route::has('transaksi.lurah.cabang.update'));
        $this->assertTrue(Route::has('transaksi.lurah.cabang.delete'));
        $this->assertTrue(Route::has('transaksi.lurah.cabang.edit.status'));
        $this->assertTrue(Route::has('transaksi.lurah.cabang.update.status'));
        $this->assertTrue(Route::has('transaksi-gamis'));
        $this->assertTrue(Route::has('transaksi-gamis.semua'));
        $this->assertTrue(Route::has('transaksi-gamis.view'));
        $this->assertTrue(Route::has('transaksi-gamis.view.layanan'));
    }
}
