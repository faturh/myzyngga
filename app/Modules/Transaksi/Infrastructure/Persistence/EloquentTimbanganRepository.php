<?php

namespace App\Modules\Transaksi\Infrastructure\Persistence;

use App\Models\Timbangan;
use App\Models\ListPakaianTimbangan;
use App\Models\Transaksi;
use App\Modules\Transaksi\Domain\Repositories\TimbanganRepositoryInterface;
use Illuminate\Support\Facades\DB;

class EloquentTimbanganRepository implements TimbanganRepositoryInterface
{
    /**
     * Find a transaction by its UUID.
     *
     * @param string $id
     * @return Transaksi
     */
    public function findTransaksiById(string $id): Transaksi
    {
        return Transaksi::with(['pelanggan.user', 'layananPrioritas'])->findOrFail($id);
    }

    /**
     * Store processing details and its items, and update transaction status.
     *
     * @param array $prosesData
     * @param array $itemsData
     * @return Timbangan
     */
    public function storeTimbangan(array $prosesData, array $itemsData): Timbangan
    {
        return DB::transaction(function () use ($prosesData, $itemsData) {
            $timbangan = Timbangan::create($prosesData);

            foreach ($itemsData as $item) {
                $timbangan->items()->create([
                    'jenis_pakaian_id' => $item['jenis_pakaian_id'],
                    'qty' => $item['qty'],
                    'harga' => $item['harga'] ?? 0,
                ]);
            }

            return $timbangan;
        });
    }

    /**
     * Update transaction status, total_biaya_layanan, and recalculate total_bayar_akhir.
     *
     * @param string $id
     * @param string $status
     * @param float $totalPrice
     * @return int
     */
    public function updateTransaksiStatusAndTotal(string $id, string $status, float $totalPrice): int
    {
        $transaksi = Transaksi::findOrFail($id);

        $totalBiayaLayanan = $totalPrice;
        $totalBayarAkhir = $totalBiayaLayanan 
            + (double) ($transaksi->total_biaya_prioritas ?? 0) 
            + (double) ($transaksi->total_biaya_layanan_tambahan ?? 0);

        $transaksi->status = $status;
        $transaksi->total_biaya_layanan = $totalBiayaLayanan;
        $transaksi->total_bayar_akhir = $totalBayarAkhir;
        $transaksi->save();

        return 1;
    }
}
