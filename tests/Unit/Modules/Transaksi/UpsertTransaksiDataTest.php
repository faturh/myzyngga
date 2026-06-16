<?php

namespace Tests\Unit\Modules\Transaksi;

use App\Enums\StatusTransaksi;
use App\Modules\Transaksi\Application\DTO\UpsertTransaksiData;
use App\Shared\Exceptions\DomainException;
use PHPUnit\Framework\TestCase;

class UpsertTransaksiDataTest extends TestCase
{
    public function test_from_array_maps_line_items_and_payload(): void
    {
        $dto = UpsertTransaksiData::fromArray([
            'pelanggan_id' => 10,
            'total_biaya_layanan' => 12000,
            'total_biaya_prioritas' => 3000,
            'total_biaya_layanan_tambahan' => 2000,
            'total_bayar_akhir' => 17000,
            'jenis_pembayaran' => 'Tunai',
            'bayar' => 20000,
            'kembalian' => 3000,
            'layanan_prioritas_id' => 2,
            'layanan_tambahan_id' => ['8', '9'],
            'jenis_pakaian_id' => ['3'],
            'jenis_layanan_id' => [['5', '6']],
            'harga_jenis_layanan_id' => ['12000'],
            'total_pakaian' => ['2'],
            'status' => StatusTransaksi::PROSES->value,
        ]);

        $this->assertSame(10, $dto->pelangganId);
        $this->assertSame([8, 9], $dto->layananTambahanIds);
        $this->assertCount(1, $dto->lineItems);
        $this->assertSame([5, 6], $dto->lineItems[0]->jenisLayananIds);
        $this->assertSame(StatusTransaksi::PROSES->value, $dto->toUpdatePayload()['status']);
    }

    public function test_from_array_rejects_misaligned_detail_payload(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Payload detail transaksi tidak valid.');

        UpsertTransaksiData::fromArray([
            'pelanggan_id' => 10,
            'total_biaya_layanan' => 12000,
            'total_biaya_prioritas' => 3000,
            'total_biaya_layanan_tambahan' => 2000,
            'total_bayar_akhir' => 17000,
            'jenis_pembayaran' => 'Tunai',
            'bayar' => 20000,
            'kembalian' => 3000,
            'layanan_prioritas_id' => 2,
            'layanan_tambahan_id' => [],
            'jenis_pakaian_id' => ['3'],
            'jenis_layanan_id' => [],
            'harga_jenis_layanan_id' => ['12000'],
            'total_pakaian' => ['2'],
        ]);
    }
}
