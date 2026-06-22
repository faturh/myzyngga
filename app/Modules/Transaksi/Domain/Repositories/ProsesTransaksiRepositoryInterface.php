<?php

namespace App\Modules\Transaksi\Domain\Repositories;

use App\Models\ProsesTransaksi;
use App\Models\Transaksi;

interface ProsesTransaksiRepositoryInterface
{
    /**
     * Find a transaction by its UUID.
     *
     * @param string $id
     * @return Transaksi
     */
    public function findTransaksiById(string $id): Transaksi;

    /**
     * Store processing details and its items, and update transaction status.
     *
     * @param array $prosesData
     * @param array $itemsData
     * @return ProsesTransaksi
     */
    public function storeProsesTransaksi(array $prosesData, array $itemsData): ProsesTransaksi;

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
