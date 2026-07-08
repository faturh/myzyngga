<?php

namespace App\Modules\Transaksi\Application\Services;

use App\Models\Timbangan;
use App\Models\Transaksi;
use App\Modules\Transaksi\Domain\Repositories\TimbanganRepositoryInterface;
use App\Shared\Exceptions\DomainException;
use App\Models\JenisPakaian;

class TimbanganService
{
    public function __construct(
        private readonly TimbanganRepositoryInterface $repository
    ) {}

    /**
     * Retrieve transaction details for the processing form.
     */
    public function getProsesFormData(string $id): Transaksi
    {
        $transaksi = $this->repository->findTransaksiById($id);

        if (!in_array($transaksi->status, ['Baru', 'created', 'Perlu Diproses'])) {
            throw new DomainException('Hanya pesanan berstatus baru yang dapat diproses.', 422);
        }

        return $transaksi;
    }

    /**
     * Process transaction by saving items and actual scale weight.
     */
    public function prosesTransaksi(string $id, array $data): Timbangan
    {
        $transaksi = $this->repository->findTransaksiById($id);

        if (!in_array($transaksi->status, ['Baru', 'created', 'Perlu Diproses'])) {
            throw new DomainException('Hanya pesanan berstatus baru yang dapat diproses.', 422);
        }

        $tipeLayanan = $data['tipe_layanan'] ?? 'kiloan';

        $priorityCharge = (double) ($transaksi->layananPrioritas->harga ?? 0);
        $totalSatuanPrice = 0;
        $satuanItemsData = [];

        if (!empty($data['satuan_items']) && is_array($data['satuan_items'])) {
            foreach ($data['satuan_items'] as $item) {
                if (!empty($item['kategori_pakaian_satuan_id']) && isset($item['jumlah']) && $item['jumlah'] > 0) {
                    $kat = \App\Models\KategoriPakaianSatuan::find($item['kategori_pakaian_satuan_id']);
                    if ($kat) {
                        $basePrice = (double) $kat->harga;
                        $hargaAkhir = ($basePrice + $priorityCharge) * (int) $item['jumlah'];
                        $totalSatuanPrice += $hargaAkhir;
                        $satuanItemsData[] = [
                            'kategori_pakaian_satuan_id' => $kat->id,
                            'jumlah' => (int) $item['jumlah'],
                            'harga_akhir' => $hargaAkhir
                        ];
                    }
                }
            }
        }

        $actualWeight = (double) ($data['actual_weight'] ?? 0);
        $minimumWeight = (double) ($data['minimum_weight'] ?? 3.0);
        $pricePerKg = (double) ($data['price_per_kg'] ?? 0);

        if ($actualWeight <= 0) {
            throw new DomainException('Berat timbangan harus lebih besar dari 0 kg.', 422);
        }

        if ($pricePerKg < 0) {
            throw new DomainException('Harga per kg tidak boleh bernilai negatif.', 422);
        }

        if ($actualWeight > 0) {
            $chargedWeight = max($minimumWeight, $actualWeight);
            $totalKiloanPrice = $chargedWeight * $pricePerKg;
        } else {
            $chargedWeight = 0;
            $totalKiloanPrice = 0;
        }

        $totalPrice = $totalKiloanPrice + $totalSatuanPrice;

        // Save Satuan items in tambahan table if any
        if (!empty($satuanItemsData)) {
            $nextTambahanId = (\Illuminate\Support\Facades\DB::table('tambahan')->max('tambahan_id') ?? 0) + 1;
            
            foreach ($satuanItemsData as $itemData) {
                \Illuminate\Support\Facades\DB::table('tambahan')->insert([
                    'tambahan_id' => $nextTambahanId,
                    'kategori_pakaian_satuan_id' => $itemData['kategori_pakaian_satuan_id'],
                    'jumlah' => $itemData['jumlah'],
                    'harga_akhir' => $itemData['harga_akhir'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Set transaction fk_tambahan
            $transaksi->fk_tambahan = $nextTambahanId;
            $transaksi->save();
        }

        $prosesData = [
            'transaksi_id' => $transaksi->id,
            'nota' => $transaksi->nota,
            'actual_weight' => $actualWeight,
            'minimum_weight' => $minimumWeight,
            'price_per_kg' => $pricePerKg,
            'charged_weight' => $chargedWeight,
            'total_price' => $totalPrice,
        ];

        // Store timbangan records (list_pakaian_timbangan is empty for now as it will be filled in pekerjaan stage)
        $proses = $this->repository->storeTimbangan($prosesData, []);

        // Update transaction status and total bayar akhir
        $this->repository->updateTransaksiStatusAndTotal($transaksi->id, 'Proses', $totalPrice);

        return $proses;
    }
}
