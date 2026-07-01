<?php

namespace Tests\Unit\Modules\Transaksi;

use Tests\TestCase;
use App\Models\Transaksi;
use App\Models\LayananPrioritas;
use App\Models\Pelanggan;
use App\Models\User;
use App\Models\Cabang;
use App\Models\JenisPakaian;
use App\Modules\Transaksi\Application\Services\TimbanganService;
use App\Shared\Exceptions\DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TimbanganServiceTest extends TestCase
{
    use RefreshDatabase;

    private TimbanganService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(TimbanganService::class);
    }

    public function test_proses_transaksi_calculates_correct_totals_when_actual_weight_is_above_minimum()
    {
        // Arrange
        $user = User::factory()->create();
        $cabang = Cabang::create([
            'nama' => 'Cabang Test',
            'alamat' => 'Alamat Test',
            'slug' => 'cabang-test',
            'lokasi' => 'Lokasi Test',
        ]);
        $pelanggan = Pelanggan::create([
            'nama' => 'Pelanggan Test',
            'slug' => 'pelanggan-test',
            'jenis_kelamin' => 'L',
            'telepon' => '08123456789',
            'alamat' => 'Alamat Pelanggan',
        ]);
        $prioritas = LayananPrioritas::create([
            'nama' => 'Reguler',
            'harga' => 0,
            'prioritas' => 1,
            'cabang_id' => $cabang->id,
        ]);
        $transaksi = Transaksi::create([
            'nota' => 'NOTATEST01',
            'waktu' => now(),
            'pickup_address' => 'Alamat Pick',
            'total_biaya_layanan' => 15000,
            'total_biaya_prioritas' => 0,
            'total_biaya_layanan_tambahan' => 0,
            'total_bayar_akhir' => 15000,
            'jenis_pembayaran' => 'cash',
            'bayar' => 0,
            'kembalian' => 0,
            'status' => 'Baru',
            'layanan_prioritas_id' => $prioritas->id,
            'pelanggan_id' => $pelanggan->id,
            'pegawai_id' => $user->id,
            'cabang_id' => $cabang->id,
        ]);

        $data = [
            'actual_weight' => 4.5,
            'minimum_weight' => 3.0,
            'price_per_kg' => 5000,
            'items' => [
                ['nama_item' => 'Baju Kaos', 'qty' => 5],
                ['nama_item' => 'Celana Jeans', 'qty' => 2],
            ],
        ];

        // Act
        $proses = $this->service->prosesTransaksi($transaksi->id, $data);

        // Assert
        $this->assertDatabaseHas('timbangan', [
            'transaksi_id' => $transaksi->id,
            'actual_weight' => 4.5,
            'minimum_weight' => 3.0,
            'price_per_kg' => 5000,
            'charged_weight' => 4.5, // max(3.0, 4.5) = 4.5
            'total_price' => 22500, // 4.5 * 5000 = 22500
        ]);

        $kaos = JenisPakaian::where('nama', 'Baju Kaos')->firstOrFail();
        $jeans = JenisPakaian::where('nama', 'Celana Jeans')->firstOrFail();

        $this->assertDatabaseHas('list_pakaian_timbangan', [
            'timbangan_id' => $proses->id,
            'jenis_pakaian_id' => $kaos->id,
            'qty' => 5,
        ]);

        $this->assertDatabaseHas('list_pakaian_timbangan', [
            'timbangan_id' => $proses->id,
            'jenis_pakaian_id' => $jeans->id,
            'qty' => 2,
        ]);

        $this->assertDatabaseHas('transaksi', [
            'id' => $transaksi->id,
            'status' => 'Perlu Dikerjakan',
            'total_biaya_layanan' => 22500,
            'total_bayar_akhir' => 22500,
        ]);
    }

    public function test_proses_transaksi_calculates_correct_totals_when_actual_weight_is_below_minimum()
    {
        // Arrange
        $user = User::factory()->create();
        $cabang = Cabang::create([
            'nama' => 'Cabang Test',
            'alamat' => 'Alamat Test',
            'slug' => 'cabang-test',
            'lokasi' => 'Lokasi Test',
        ]);
        $pelanggan = Pelanggan::create([
            'nama' => 'Pelanggan Test',
            'slug' => 'pelanggan-test',
            'jenis_kelamin' => 'L',
            'telepon' => '08123456789',
            'alamat' => 'Alamat Pelanggan',
        ]);
        $prioritas = LayananPrioritas::create([
            'nama' => 'Reguler',
            'harga' => 0,
            'prioritas' => 1,
            'cabang_id' => $cabang->id,
        ]);
        $transaksi = Transaksi::create([
            'nota' => 'NOTATEST02',
            'waktu' => now(),
            'pickup_address' => 'Alamat Pick',
            'total_biaya_layanan' => 15000,
            'total_biaya_prioritas' => 0,
            'total_biaya_layanan_tambahan' => 0,
            'total_bayar_akhir' => 15000,
            'jenis_pembayaran' => 'cash',
            'bayar' => 0,
            'kembalian' => 0,
            'status' => 'Baru',
            'layanan_prioritas_id' => $prioritas->id,
            'pelanggan_id' => $pelanggan->id,
            'pegawai_id' => $user->id,
            'cabang_id' => $cabang->id,
        ]);

        $data = [
            'actual_weight' => 2.0,
            'minimum_weight' => 3.0,
            'price_per_kg' => 5000,
            'items' => [
                ['nama_item' => 'Jaket', 'qty' => 3],
            ],
        ];

        // Act
        $proses = $this->service->prosesTransaksi($transaksi->id, $data);

        // Assert
        $this->assertDatabaseHas('timbangan', [
            'transaksi_id' => $transaksi->id,
            'actual_weight' => 2.0,
            'minimum_weight' => 3.0,
            'price_per_kg' => 5000,
            'charged_weight' => 3.0, // max(3.0, 2.0) = 3.0
            'total_price' => 15000, // 3.0 * 5000 = 15000
        ]);

        $this->assertDatabaseHas('transaksi', [
            'id' => $transaksi->id,
            'status' => 'Perlu Dikerjakan',
            'total_biaya_layanan' => 15000,
            'total_bayar_akhir' => 15000,
        ]);
    }

    public function test_throws_exception_if_actual_weight_is_zero_or_negative()
    {
        // Arrange
        $user = User::factory()->create();
        $cabang = Cabang::create([
            'nama' => 'Cabang Test',
            'alamat' => 'Alamat Test',
            'slug' => 'cabang-test',
            'lokasi' => 'Lokasi Test',
        ]);
        $pelanggan = Pelanggan::create([
            'nama' => 'Pelanggan Test',
            'slug' => 'pelanggan-test',
            'jenis_kelamin' => 'L',
            'telepon' => '08123456789',
            'alamat' => 'Alamat Pelanggan',
        ]);
        $prioritas = LayananPrioritas::create([
            'nama' => 'Reguler',
            'harga' => 0,
            'prioritas' => 1,
            'cabang_id' => $cabang->id,
        ]);
        $transaksi = Transaksi::create([
            'nota' => 'NOTATEST03',
            'waktu' => now(),
            'pickup_address' => 'Alamat Pick',
            'total_biaya_layanan' => 15000,
            'total_biaya_prioritas' => 0,
            'total_biaya_layanan_tambahan' => 0,
            'total_bayar_akhir' => 15000,
            'jenis_pembayaran' => 'cash',
            'bayar' => 0,
            'kembalian' => 0,
            'status' => 'Baru',
            'layanan_prioritas_id' => $prioritas->id,
            'pelanggan_id' => $pelanggan->id,
            'pegawai_id' => $user->id,
            'cabang_id' => $cabang->id,
        ]);

        $data = [
            'actual_weight' => 0.0,
            'minimum_weight' => 3.0,
            'price_per_kg' => 5000,
            'items' => [
                ['nama_item' => 'Jaket', 'qty' => 3],
            ],
        ];

        // Expect Exception
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Berat timbangan harus lebih besar dari 0 kg.');

        // Act
        $this->service->prosesTransaksi($transaksi->id, $data);
    }
}
