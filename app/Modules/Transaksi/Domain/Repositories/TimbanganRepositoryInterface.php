<?php

namespace App\Modules\Transaksi\Domain\Repositories;

use App\Models\Timbangan;
use App\Models\Transaksi;

interface TimbanganRepositoryInterface
{
    /**
     * Find a transaction by its UUID.
     *
     * @param string $id
     * @return Transaksi
     */
    public function findTransaksiById(string $id): Transaksi;

    /**
     * Store timbangan details and its items, and update transaction status.
     *
     * @param array $prosesData
     * @param array $itemsData
     * @return Timbangan
     */
    public function storeTimbangan(array $prosesData, array $itemsData): Timbangan;

    /**
     * Update transaction status, total_biaya_layanan, and recalculate total_bayar_akhir.
     *
     * @param string $id
     * @param string $status
     * @param float $totalPrice
     * @return int
     */
    public function updateTransaksiStatusAndTotal(string $id, string $status, float $totalPrice): int;
}
