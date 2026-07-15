<?php

namespace Tests\Unit\Modules\Order;

use App\Models\Cabang;
use App\Models\LayananPrioritas;
use App\Models\Notifikasi;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\User;
use App\Modules\Order\Application\Services\OrderWebService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * WHITEBOX – OrderWebService Bug Regression Tests
 *
 * Menguji logika internal service yang rentan terhadap bug:
 * - delivery_fee tidak pernah dihitung (selalu 0) → FIXED
 * - gross_amount harus integer untuk Midtrans → FIXED
 * - checkPaymentStatus: kalkulasi target amount → FIXED (bergantung delivery_fee fix)
 * - updatePayment: blokir jika sudah selesai/lunas → FIXED (guard sudah ada, test aktif)
 */
class OrderWebServiceBugTest extends TestCase
{
    use RefreshDatabase;

    private function makeTransaksi(array $overrides = []): Transaksi
    {
        $user = User::factory()->create(['role' => 'pelanggan']);
        $pelanggan = Pelanggan::factory()->create(['user_id' => $user->id]);

        $cabang = Cabang::create([
            'nama'   => 'Cabang Test ' . uniqid(),
            'lokasi' => '-6.2,106.8',
            'alamat' => 'Jl. Test No. 1',
        ]);
        $layanan = LayananPrioritas::create([
            'nama'      => 'Reguler',
            'harga'     => 5000,
            'prioritas' => 1,
            'cabang_id' => $cabang->id,
        ]);

        return Transaksi::create(array_merge([
            'nota'                        => 'TEST-' . uniqid(),
            'pelanggan_id'                => $pelanggan->id,
            'cabang_id'                   => $cabang->id,
            'layanan_prioritas_id'        => $layanan->id,
            'pegawai_id'                  => '0',
            'status'                      => 'Baru',
            'jenis_pembayaran'            => 'cash',
            'total_biaya_layanan'         => 10000,
            'total_biaya_prioritas'       => 0,
            'total_biaya_layanan_tambahan' => 0,
            'total_bayar_akhir'           => 10000,
            'bayar'                       => 0,
            'kembalian'                   => 0,
            'waktu'                       => now(),
        ], $overrides));
    }

    // ── WB-OS-01  Fix: storeRequestDelivery menyimpan delivery_fee dari config ─

    /** @test */
    public function storeRequestDelivery_menyimpan_delivery_fee_server_side_ke_metadata(): void
    {
        $order = $this->makeTransaksi();
        $service = app(OrderWebService::class);

        // Delivery fee TIDAK dikirim dari client — diambil dari config server
        $request = Request::create('/', 'POST', [
            'address'        => 'Jl. Merdeka No. 1',
            'detail_address' => 'Lantai 2',
            'lat'            => -6.914744,
            'lng'            => 107.60981,
        ]);

        $service->storeRequestDelivery($request, $order->id);

        $meta = json_decode(Transaksi::find($order->id)->payment_metadata, true);

        $this->assertArrayHasKey('pending_delivery', $meta);
        // Nilai harus sama persis dengan config, bukan nilai dari request
        $this->assertEquals((float) config('laundry.delivery_fee', 0), $meta['pending_delivery']['delivery_fee']);
        $this->assertEquals('Jl. Merdeka No. 1', $meta['pending_delivery']['address']);
    }

    // ── WB-OS-01b delivery_fee dari client diabaikan sepenuhnya ─────────────

    /** @test */
    public function storeRequestDelivery_mengabaikan_delivery_fee_dari_client(): void
    {
        $order = $this->makeTransaksi();
        $service = app(OrderWebService::class);

        // Seandainya client mengirim delivery_fee yang dimanipulasi, harus diabaikan
        $request = Request::create('/', 'POST', [
            'address'      => 'Jl. Test No. 2',
            'delivery_fee' => 0,  // client manipulasi fee jadi 0
            'lat'          => -6.914744,
            'lng'          => 107.60981,
        ]);

        $service->storeRequestDelivery($request, $order->id);

        $meta = json_decode(Transaksi::find($order->id)->payment_metadata, true);
        // Nilai harus tetap dari config, bukan 0 dari client
        $expectedFee = (float) config('laundry.delivery_fee', 0);
        $this->assertEquals($expectedFee, $meta['pending_delivery']['delivery_fee']);
    }

    // ── WB-OS-02  Fix: gross_amount dibulatkan ke integer (bukan float) ────────

    /** @test */
    public function gross_amount_harus_integer_bukan_float(): void
    {
        // Verifikasi logika konversi: (int) round() menghasilkan integer
        $floatAmount = 15500.50;
        $intAmount = (int) round($floatAmount);

        $this->assertIsInt($intAmount);
        $this->assertEquals(15501, $intAmount);

        // Verifikasi fix ada di source code service
        $source = file_get_contents(
            app_path('Modules/Order/Application/Services/OrderWebService.php')
        );
        $this->assertStringContainsString('(int) round($unpaidAmount)', $source,
            'Fix gross_amount float→int harus ada di OrderWebService::processCoreApiPayment()'
        );
    }

    // ── WB-OS-03  Fix: checkPaymentStatus menghitung delivery_fee dalam target ─

    /** @test */
    public function checkPaymentStatus_memasukkan_delivery_fee_ke_target_amount(): void
    {
        // Order total_bayar_akhir = 10000, delivery_fee = 5000 → target = 15000
        // Set bayar = 15000 agar path 'paid' tercapai tanpa panggil Midtrans
        $order = $this->makeTransaksi([
            'total_bayar_akhir' => 10000,
            'bayar'             => 15000,
            'midtrans_order_id' => 'test-order-' . uniqid(),
        ]);
        $order->payment_metadata = json_encode([
            'pending_delivery' => [
                'address'      => 'Jl. Test',
                'delivery_fee' => 5000.0,
                'is_roundtrip' => true,
            ],
        ]);
        $order->save();

        $service = app(OrderWebService::class);
        $status = $service->checkPaymentStatus($order->id);

        // bayar (15000) >= target (10000 + 5000) → harus 'paid'
        $this->assertEquals('paid', $status);
    }

    // ── WB-OS-03b checkPaymentStatus tidak 'paid' jika bayar < total+delivery ─

    /** @test */
    public function checkPaymentStatus_bukan_paid_jika_bayar_kurang_dari_total_plus_delivery(): void
    {
        // Order total = 10000, delivery_fee = 5000, bayar hanya 10000 → belum cukup
        // Tanpa Midtrans dikonfigurasi → akan catch Exception dan return 'pending'
        $order = $this->makeTransaksi([
            'total_bayar_akhir' => 10000,
            'bayar'             => 10000,
            'midtrans_order_id' => 'test-order-' . uniqid(),
        ]);
        $order->payment_metadata = json_encode([
            'pending_delivery' => [
                'address'      => 'Jl. Test',
                'delivery_fee' => 5000.0,
                'is_roundtrip' => true,
            ],
        ]);
        $order->save();

        $service = app(OrderWebService::class);
        $status = $service->checkPaymentStatus($order->id);

        // bayar (10000) < target (15000) → tidak 'paid'
        $this->assertNotEquals('paid', $status);
    }

    // ── WB-OS-04  Fix: updatePayment melempar exception jika order sudah selesai

    /** @test */
    public function updatePayment_melempar_exception_jika_order_sudah_selesai(): void
    {
        $order = $this->makeTransaksi();

        // Tandai sebagai selesai langsung di ListPengerjaan (status_id=5 = Pesanan Selesai)
        // bypass setStatusAttribute() yang memerlukan payment_status=paid
        \Illuminate\Support\Facades\DB::table('list_pengerjaan')
            ->where('id', $order->list_pengerjaan_id)
            ->update(['list_status_pengerjaan_id' => 5]);

        $user = User::find(Pelanggan::find($order->pelanggan_id)->user_id);
        $service = app(OrderWebService::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('tidak dapat diubah');

        $service->updatePayment($order->id, 'cash', $user);
    }

    // ── WB-OS-04b updatePayment melempar exception jika order sudah lunas ──────

    /** @test */
    public function updatePayment_melempar_exception_jika_order_sudah_lunas(): void
    {
        $order = $this->makeTransaksi(['payment_status' => 'paid']);
        $user = User::find(
            Pelanggan::find($order->pelanggan_id)->user_id
        );

        $service = app(OrderWebService::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('tidak dapat diubah');

        $service->updatePayment($order->id, 'qris', $user);
    }

    // ── WB-OS-05  bookingData null jika session kosong ───────────────────────

    /** @test */
    public function bookingData_mengembalikan_null_jika_session_address_kosong(): void
    {
        $service = app(OrderWebService::class);

        session()->forget('order');

        $result = $service->bookingData();

        $this->assertNull($result);
    }

    // ── WB-OS-06  bookingData mengambil data dari session dengan benar ────────

    /** @test */
    public function bookingData_mengembalikan_data_dari_session(): void
    {
        $service = app(OrderWebService::class);

        session([
            'order.service'  => 'reguler',
            'order.address'  => 'Jl. Merdeka No. 1',
            'order.lat'      => '-6.2',
            'order.lng'      => '106.8',
        ]);

        $result = $service->bookingData();

        $this->assertNotNull($result);
        $this->assertSame('reguler', $result['service']);
        $this->assertSame('Jl. Merdeka No. 1', $result['address']);
    }

    // ── WB-OS-07  notificationData mengembalikan empty collection jika no pelanggan

    /** @test */
    public function notificationData_mengembalikan_empty_collection_jika_user_null(): void
    {
        $service = app(OrderWebService::class);

        $result = $service->notificationData(null);

        $this->assertArrayHasKey('notifications', $result);
        $this->assertCount(0, $result['notifications']);
    }

    // ── WB-OS-08  notificationData mengembalikan empty jika user tanpa pelanggan

    /** @test */
    public function notificationData_mengembalikan_empty_jika_user_belum_punya_profil_pelanggan(): void
    {
        $service = app(OrderWebService::class);
        $user = User::factory()->create(['role' => 'pelanggan']);

        $result = $service->notificationData($user);

        $this->assertArrayHasKey('notifications', $result);
        $this->assertCount(0, $result['notifications']);
    }

    // ── WB-OS-09  Fix: upgradeData tidak boleh kasih totalWeightKg = 0 kalau ──
    // ── detailTransaksi belum diisi (biaya upgrade jadi ke-nol-kan di halaman) ─

    /** @test */
    public function upgradeData_menghitung_total_weight_kg_estimasi_kalau_detail_transaksi_belum_ada(): void
    {
        $order = $this->makeTransaksi([
            'total_bayar_akhir' => 4850,
        ]);
        // Status sudah lewat "belum ditimbang" (mis. operator sudah proses status)
        // TAPI detailTransaksi (rincian per-item timbangan) belum pernah diisi —
        // skenario ini bisa terjadi kalau alur status & pengisian detail terpisah.
        $order->pending_status_id = 3;
        $order->save();
        $this->assertTrue($order->fresh()->detailTransaksi->isEmpty());

        LayananPrioritas::create([
            'nama'      => 'Quick',
            'harga'     => 6000,
            'prioritas' => 2,
            'cabang_id' => $order->cabang_id,
        ]);

        $service = app(OrderWebService::class);
        $data = $service->upgradeData($order->id, $order->pelanggan->user);

        $this->assertGreaterThan(
            0,
            $data['totalWeightKg'],
            'totalWeightKg tidak boleh 0 hanya karena detailTransaksi belum diisi — biaya upgrade di halaman upgrade jadi Rp0 kalau ini 0.'
        );
    }
}
