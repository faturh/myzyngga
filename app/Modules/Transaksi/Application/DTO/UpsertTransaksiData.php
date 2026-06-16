<?php

namespace App\Modules\Transaksi\Application\DTO;

use App\Enums\StatusTransaksi;
use App\Shared\Exceptions\DomainException;
use Illuminate\Support\Str;

class UpsertTransaksiData
{
    /**
     * @param  array<int, int>  $layananTambahanIds
     * @param  array<int, TransaksiLineItemData>  $lineItems
     */
    public function __construct(
        public readonly int $pelangganId,
        public readonly float $totalBiayaLayanan,
        public readonly float $totalBiayaPrioritas,
        public readonly float $totalBiayaLayananTambahan,
        public readonly float $totalBayarAkhir,
        public readonly string $jenisPembayaran,
        public readonly float $bayar,
        public readonly float $kembalian,
        public readonly int $layananPrioritasId,
        public readonly array $layananTambahanIds,
        public readonly array $lineItems,
        public readonly ?string $status = null,
    ) {}

    public static function fromArray(array $validated): self
    {
        $lineItems = [];
        $jenisPakaianIds = $validated['jenis_pakaian_id'] ?? [];
        $jenisLayananIds = $validated['jenis_layanan_id'] ?? [];
        $hargaJenisLayanan = $validated['harga_jenis_layanan_id'] ?? [];
        $totalPakaian = $validated['total_pakaian'] ?? [];

        $lineItemCount = count($jenisPakaianIds);
        if (
            $lineItemCount === 0
            || $lineItemCount !== count($jenisLayananIds)
            || $lineItemCount !== count($hargaJenisLayanan)
            || $lineItemCount !== count($totalPakaian)
        ) {
            throw new DomainException('Payload detail transaksi tidak valid.', 422);
        }

        if ($lineItemCount > 1) {
            throw new DomainException('Sistem saat ini membatasi maksimal 1 jenis layanan per transaksi.', 422);
        }

        foreach ($jenisPakaianIds as $index => $jenisPakaianId) {
            $selectedJenisLayanan = array_map('intval', $jenisLayananIds[$index] ?? []);
            if ($selectedJenisLayanan === []) {
                throw new DomainException('Setiap detail transaksi wajib memiliki minimal satu jenis layanan.', 422);
            }

            $lineItems[] = new TransaksiLineItemData(
                jenisPakaianId: (int) $jenisPakaianId,
                jenisLayananIds: $selectedJenisLayanan,
                hargaLayananAkhir: (float) ($hargaJenisLayanan[$index] ?? 0),
                totalPakaian: (int) ($totalPakaian[$index] ?? 0),
            );
        }

        return new self(
            pelangganId: (int) $validated['pelanggan_id'],
            totalBiayaLayanan: (float) $validated['total_biaya_layanan'],
            totalBiayaPrioritas: (float) $validated['total_biaya_prioritas'],
            totalBiayaLayananTambahan: (float) $validated['total_biaya_layanan_tambahan'],
            totalBayarAkhir: (float) $validated['total_bayar_akhir'],
            jenisPembayaran: (string) $validated['jenis_pembayaran'],
            bayar: (float) $validated['bayar'],
            kembalian: (float) $validated['kembalian'],
            layananPrioritasId: (int) $validated['layanan_prioritas_id'],
            layananTambahanIds: array_map('intval', $validated['layanan_tambahan_id'] ?? []),
            lineItems: $lineItems,
            status: $validated['status'] ?? null,
        );
    }

    public function toStorePayload(int $cabangId, int $pegawaiId): array
    {
        $now = now();
        $notaSuffix = strtoupper(substr(str_replace('-', '', (string) Str::uuid()), 0, 10));

        return [
            'nota' => 'pelanggan-'.$notaSuffix,
            'waktu' => $now,
            'total_biaya_layanan' => $this->totalBiayaLayanan,
            'total_biaya_prioritas' => $this->totalBiayaPrioritas,
            'total_biaya_layanan_tambahan' => $this->totalBiayaLayananTambahan,
            'total_bayar_akhir' => $this->totalBayarAkhir,
            'jenis_pembayaran' => $this->jenisPembayaran,
            'bayar' => $this->bayar,
            'kembalian' => $this->kembalian,
            'status' => $this->status ?? StatusTransaksi::BARU->value,
            'cabang_id' => $cabangId,
            'pegawai_id' => $pegawaiId,
            'pelanggan_id' => $this->pelangganId,
            'layanan_prioritas_id' => $this->layananPrioritasId,
        ];
    }

    public function toUpdatePayload(): array
    {
        return [
            'total_biaya_layanan' => $this->totalBiayaLayanan,
            'total_biaya_prioritas' => $this->totalBiayaPrioritas,
            'total_biaya_layanan_tambahan' => $this->totalBiayaLayananTambahan,
            'total_bayar_akhir' => $this->totalBayarAkhir,
            'jenis_pembayaran' => $this->jenisPembayaran,
            'bayar' => $this->bayar,
            'kembalian' => $this->kembalian,
            'status' => $this->status,
            'pelanggan_id' => $this->pelangganId,
            'layanan_prioritas_id' => $this->layananPrioritasId,
        ];
    }
}
