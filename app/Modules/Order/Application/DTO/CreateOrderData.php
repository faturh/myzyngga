<?php

namespace App\Modules\Order\Application\DTO;

use Illuminate\Support\Str;

class CreateOrderData
{
    public function __construct(
        public readonly int $pelangganId,
        public readonly int $cabangId,
        public readonly int $layananPrioritasId,
        public readonly string $pickupAddress,
        public readonly ?string $pickupDetailAddress,
        public readonly string $pickupDate,
        public readonly string $pickupTime,
        public readonly ?string $parfum,
        public readonly ?string $catatan,
        public readonly string $paymentMethod,
        public readonly float $estimatedTotal,
        public readonly bool $isRoundtrip = false,
        public readonly ?int $pegawaiId = null,
    ) {}

    public function toPersistencePayload(int $pegawaiId): array
    {
        $now = now();
        $notaSuffix = strtoupper(substr(str_replace('-', '', (string) Str::uuid()), 0, 8));

        return [
            'nota' => 'PLG-'.$notaSuffix,
            'waktu' => $now,
            'pickup_address' => $this->pickupAddress,
            'pickup_detail_address' => $this->pickupDetailAddress,
            'pickup_date' => $this->pickupDate,
            'pickup_time' => $this->pickupTime,
            'parfum' => $this->parfum,
            'catatan' => $this->catatan,
            'is_roundtrip' => $this->isRoundtrip,
            'total_biaya_layanan' => $this->estimatedTotal,
            'total_biaya_prioritas' => 0,
            'total_biaya_layanan_tambahan' => 0,
            'total_bayar_akhir' => $this->estimatedTotal,
            'jenis_pembayaran' => $this->paymentMethod,
            'payment_status' => 'pending',
            'bayar' => 0,
            'kembalian' => 0,
            'status' => 'created',
            'konfirmasi_upah_gamis' => false,
            'layanan_prioritas_id' => $this->layananPrioritasId,
            'pelanggan_id' => $this->pelangganId,
            'pegawai_id' => $this->pegawaiId ?? $pegawaiId,
            'gamis_id' => null,
            'cabang_id' => $this->cabangId,
        ];
    }
}
