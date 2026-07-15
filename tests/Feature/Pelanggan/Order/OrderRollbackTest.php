<?php

namespace Tests\Feature\Pelanggan\Order;

use App\Models\Cabang;
use App\Models\LayananPrioritas;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OrderRollbackTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
    }

    public function test_rollback_delivery_mengembalikan_data_lama_ke_database(): void
    {
        [$user, $order] = $this->makeCustomerOrder('rollback-delivery@example.com', 'rollback-delivery');

        $originalAddress = $order->pickup_address;

        $this->actingAs($user)
            ->post(route('order.delivery.store', ['id' => $order->id]), [
                'address' => 'Alamat Pengantaran Baru',
                'detail_address' => 'Detail Baru',
                'lat' => -6.3,
                'lng' => 106.9,
                'pickup_date' => now()->toDateString(),
                'pickup_time' => '11:00',
                'catatan' => 'Test',
            ])->assertRedirect(route('order.detail', ['id' => $order->id]));

        $order->refresh();
        $metaAfterRequest = json_decode($order->payment_metadata, true) ?? [];
        $this->assertArrayHasKey('pending_delivery', $metaAfterRequest, 'storeRequestDelivery harus menyimpan pending_delivery ke payment_metadata.');

        // Batalkan (rollback) sebelum pembayaran — panggil endpoint rollback
        $this->actingAs($user)
            ->post(route('order.delivery.rollback', ['id' => $order->id]))
            ->assertOk()
            ->assertJson(['success' => true]);

        $order->refresh();
        $this->assertSame($originalAddress, $order->pickup_address, 'Rollback delivery harus mengembalikan pickup_address ke database.');

        $metaAfterRollback = json_decode($order->payment_metadata, true) ?? [];
        $this->assertArrayNotHasKey(
            'pending_delivery',
            $metaAfterRollback,
            'Rollback delivery harus menghapus pending_delivery dari payment_metadata, jika tidak perubahan yang sudah dibatalkan akan tetap diterapkan saat webhook pembayaran settlement diterima.'
        );

        // Buktikan webhook pembayaran TIDAK lagi menerapkan alamat baru yang sudah dibatalkan
        app(\App\Modules\Payment\Application\Services\PaymentWebhookService::class)->handleMidtransNotification([
            'transaction_status' => 'settlement',
            'payment_type' => 'qris',
            'order_id' => $order->id . '-' . time(),
            'fraud_status' => 'accept',
            'gross_amount' => $order->total_bayar_akhir,
        ]);

        $order->refresh();
        $this->assertSame($originalAddress, $order->pickup_address, 'Setelah rollback, settlement pembayaran tidak boleh menerapkan alamat pengantaran yang sudah dibatalkan.');
    }

    public function test_rollback_upgrade_mengembalikan_data_lama_ke_database(): void
    {
        [$user, $order, $cabang] = $this->makeCustomerOrder('rollback-upgrade@example.com', 'rollback-upgrade');

        $newService = LayananPrioritas::create([
            'nama' => 'Kilat',
            'harga' => 20000,
            'prioritas' => 5,
            'cabang_id' => $cabang->id,
        ]);

        $originalServiceId = $order->layanan_prioritas_id;
        $originalTotal = $order->total_bayar_akhir;

        $this->actingAs($user)
            ->postJson(route('order.upgrade.process', ['id' => $order->id]), [
                'new_service_id' => $newService->id,
                'payment_method' => 'qris',
            ])->assertOk()
            ->assertJson(['success' => true]);

        // processUpgrade menyimpan perubahan sebagai pending_upgrade di payment_metadata
        // (baru diterapkan permanen ke layanan_prioritas_id saat pembayaran settlement).
        $order->refresh();
        $metaAfterUpgrade = json_decode($order->payment_metadata, true) ?? [];
        $this->assertArrayHasKey('pending_upgrade', $metaAfterUpgrade, 'processUpgrade harus menyimpan pending_upgrade ke payment_metadata.');
        $this->assertEquals($newService->id, $metaAfterUpgrade['pending_upgrade']['new_service_id']);

        $this->actingAs($user)
            ->post(route('order.upgrade.rollback', ['id' => $order->id]))
            ->assertOk()
            ->assertJson(['success' => true]);

        $order->refresh();
        $this->assertEquals($originalServiceId, $order->layanan_prioritas_id, 'Rollback upgrade harus mengembalikan layanan_prioritas_id ke database.');
        $this->assertEquals($originalTotal, $order->total_bayar_akhir, 'Rollback upgrade harus mengembalikan total_bayar_akhir ke database.');

        $metaAfterRollback = json_decode($order->payment_metadata, true) ?? [];
        $this->assertArrayNotHasKey(
            'pending_upgrade',
            $metaAfterRollback,
            'Rollback upgrade harus menghapus pending_upgrade dari payment_metadata, jika tidak upgrade yang sudah dibatalkan akan tetap diterapkan saat webhook pembayaran settlement diterima.'
        );

        // Buktikan webhook pembayaran TIDAK lagi menerapkan upgrade yang sudah dibatalkan
        app(\App\Modules\Payment\Application\Services\PaymentWebhookService::class)->handleMidtransNotification([
            'transaction_status' => 'settlement',
            'payment_type' => 'qris',
            'order_id' => $order->id . '-' . time(),
            'fraud_status' => 'accept',
            'gross_amount' => $order->total_bayar_akhir,
        ]);

        $order->refresh();
        $this->assertEquals($originalServiceId, $order->layanan_prioritas_id, 'Setelah rollback, settlement pembayaran tidak boleh menerapkan upgrade layanan yang sudah dibatalkan.');
    }

    public function test_batalkan_popup_pembayaran_tidak_menghapus_pending_upgrade_yang_belum_dibatalkan(): void
    {
        [$user, $order] = $this->makeCustomerOrder('cancel-payment@example.com', 'cancel-payment');

        // Simulasikan pending_upgrade aktif (sudah minta upgrade, belum bayar) DAN
        // sedang di tengah percobaan charge Midtrans (midtrans_order_id ke-set).
        $order->update([
            'midtrans_order_id' => $order->id . '-123456',
            'payment_metadata' => json_encode([
                'pending_upgrade' => ['new_service_id' => 99, 'price_diff' => 15000],
                'payment_type' => 'qris',
                'transaction_status' => 'pending',
            ]),
        ]);

        $this->actingAs($user)
            ->post(route('order.payment-cancel', ['id' => $order->id]))
            ->assertOk()
            ->assertJson(['success' => true]);

        $order->refresh();
        $this->assertNull($order->midtrans_order_id, 'midtrans_order_id harus dibersihkan setelah pembayaran dibatalkan.');

        $meta = json_decode($order->payment_metadata, true) ?? [];
        $this->assertArrayHasKey(
            'pending_upgrade',
            $meta,
            'Membatalkan popup pembayaran tidak boleh ikut menghapus pending_upgrade yang belum pernah dibatalkan pelanggan lewat rollback.'
        );
        $this->assertEquals(15000, $meta['pending_upgrade']['price_diff']);
    }

    /**
     * @return array{0: User, 1: Transaksi, 2: Cabang}
     */
    private function makeCustomerOrder(string $email, string $username): array
    {
        $user = User::factory()->create([
            'username' => $username,
            'slug' => $username,
            'email' => $email,
            'password' => 'password',
            'email_verified_at' => now(),
        ]);
        $user->assignRole('customer');

        $admin = User::factory()->create([
            'username' => $username . '-admin',
            'slug' => $username . '-admin',
            'email' => $username . '-admin@example.com',
            'role' => 'admin',
        ]);
        $admin->assignRole('admin');

        $pelanggan = Pelanggan::create([
            'user_id' => $user->id,
            'nama' => 'Test Customer',
            'telepon' => '081234567890',
            'alamat' => 'Alamat Asli',
            'jenis_kelamin' => 'L',
        ]);

        $cabang = Cabang::create([
            'nama' => 'Cabang Rollback ' . $username,
            'alamat' => 'Alamat Cabang',
            'lokasi' => 'Jakarta',
        ]);

        $layananPrioritas = LayananPrioritas::create([
            'nama' => 'Reguler',
            'harga' => 0,
            'prioritas' => 1,
            'cabang_id' => $cabang->id,
        ]);

        $order = Transaksi::create([
            'pelanggan_id' => $pelanggan->id,
            'cabang_id' => $cabang->id,
            'layanan_prioritas_id' => $layananPrioritas->id,
            'pickup_address' => 'Jalan Alamat Asli',
            'pickup_date' => now()->toDateString(),
            'pickup_time' => '10:00',
            'waktu' => now(),
            'status' => 'Baru',
            'payment_status' => 'pending',
            'jenis_pembayaran' => 'cash',
            'total_bayar_akhir' => 10000,
            'total_biaya_layanan' => 10000,
            'total_biaya_prioritas' => 0,
            'total_biaya_layanan_tambahan' => 0,
            'bayar' => 0,
            'kembalian' => 0,
            'nota' => 'ZYG-RB-' . strtoupper(substr(md5($email), 0, 8)),
            'pegawai_id' => (string) $admin->id,
        ]);

        return [$user, $order, $cabang];
    }
}
