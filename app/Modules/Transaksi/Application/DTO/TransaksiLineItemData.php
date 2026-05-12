<?php

namespace App\Modules\Transaksi\Application\DTO;

class TransaksiLineItemData
{
    /**
     * @param  array<int, int>  $jenisLayananIds
     */
    public function __construct(
        public readonly int $jenisPakaianId,
        public readonly array $jenisLayananIds,
        public readonly float $hargaLayananAkhir,
        public readonly int $totalPakaian,
    ) {}
}
