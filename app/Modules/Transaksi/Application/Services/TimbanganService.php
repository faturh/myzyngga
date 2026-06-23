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

        if (!in_array($transaksi->status, ['Baru', 'created'])) {
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

        if (!in_array($transaksi->status, ['Baru', 'created'])) {
            throw new DomainException('Hanya pesanan berstatus baru yang dapat diproses.', 422);
        }

        $actualWeight = (double) ($data['actual_weight'] ?? 0);
        $minimumWeight = (double) ($data['minimum_weight'] ?? 3.0);
        $pricePerKg = (double) ($data['price_per_kg'] ?? 0);

        // Validation
        if ($actualWeight <= 0) {
            throw new DomainException('Berat timbangan harus lebih besar dari 0 kg.', 422);
        }

        if ($pricePerKg < 0) {
            throw new DomainException('Harga per kg tidak boleh bernilai negatif.', 422);
        }

        // Formula calculations
        $chargedWeight = max($minimumWeight, $actualWeight);
        $totalPrice = $chargedWeight * $pricePerKg;

        $prosesData = [
            'transaksi_id' => $transaksi->id,
            'nota' => $transaksi->nota,
            'actual_weight' => $actualWeight,
            'minimum_weight' => $minimumWeight,
            'price_per_kg' => $pricePerKg,
            'charged_weight' => $chargedWeight,
            'total_price' => $totalPrice,
        ];

        $itemsData = [];
        if (!empty($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                if (!empty($item['nama_item']) && isset($item['qty'])) {
                    // Resolve nama_item to jenis_pakaian_id
                    $jenisPakaian = JenisPakaian::firstOrCreate([
                        'nama' => trim($item['nama_item'])
                    ]);

                    $itemsData[] = [
                        'jenis_pakaian_id' => $jenisPakaian->id,
                        'qty' => (int) $item['qty'],
                    ];
                }
            }
        }

        // Store timbangan records
        $proses = $this->repository->storeTimbangan($prosesData, $itemsData);

        // Update transaction status and total bayar akhir
        $this->repository->updateTransaksiStatusAndTotal($transaksi->id, 'Proses', $totalPrice);

        return $proses;
    }
}
