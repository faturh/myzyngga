<?php

namespace App\Modules\Transaksi\Infrastructure\Persistence;

use App\Models\ProsesTransaksi;
use App\Models\ProsesTransaksiItem;
use App\Models\Transaksi;
use App\Modules\Transaksi\Domain\Repositories\ProsesTransaksiRepositoryInterface;
use Illuminate\Support\Facades\DB;

class EloquentProsesTransaksiRepository implements ProsesTransaksiRepositoryInterface
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
     * @return ProsesTransaksi
     */
    public function storeProsesTransaksi(array $prosesData, array $itemsData): ProsesTransaksi
    {
        return DB::transaction(function () use ($prosesData, $itemsData) {
            $proses = ProsesTransaksi::create($prosesData);

            foreach ($itemsData as $item) {
                $proses->items()->create([
                    'nama_item' => $item['nama_item'],
                    'qty' => $item['qty'],
                ]);
            }

            return $proses;
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

        return Transaksi::where('id', $id)->update([
            'status' => $status,
            'total_biaya_layanan' => $totalBiayaLayanan,
            'total_bayar_akhir' => $totalBayarAkhir,
        ]);
    }
}
