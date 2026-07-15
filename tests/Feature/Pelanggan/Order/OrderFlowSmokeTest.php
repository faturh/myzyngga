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

class OrderFlowSmokeTest extends TestCase
{
    use DatabaseTransactions;

    public function test_web_order_flow_creates_customer_order_and_payment(): void
    {
        $this->createAdminUser();
        $customer = $this->createCustomerUser('web-order@example.com', 'web-order');

        $this->ensureOrderReferencesExist();

        $this->actingAs($customer);

        $this->get(route('dashboard'))
            ->assertOk();

        $this->get(route('profile'))
            ->assertOk();

        $this->get(route('order.pickup', ['service' => 'regular']))
            ->assertOk();

        $this->post(route('order.pickup.store'), [
            'service' => 'regular',
            'address' => 'Jalan Testing Nomor 1',
            'detail_address' => 'Rumah belakang pagar hitam',
            'lat' => -6.2,
            'lng' => 106.8,
        ])->assertRedirect(route('order.booking'));

        $this->get(route('order.booking'))
            ->assertOk();

        $response = $this->post(route('order.confirm'), [
            'service' => 'regular',
            'address' => 'Jalan Testing Nomor 1',
            'detail_address' => 'Rumah belakang pagar hitam',
            'lat' => -6.2,
            'lng' => 106.8,
            'selected_service_id' => 'regular',
            'pickup_date' => 'today',
            'pickup_time' => '09:00',
            'parfum' => 'lavender',
            'catatan' => 'Tolong dipisah pakaian putih',
            'payment' => 'cash',
        ]);

        $pelanggan = Pelanggan::query()->where('user_id', $customer->id)->first();

        $this->assertNotNull($pelanggan);

        $order = Transaksi::query()
            ->where('pelanggan_id', $pelanggan->id)
            ->latest('created_at')
            ->first();

        $this->assertNotNull($order);
        $response->assertRedirect(route('order.detail', ['id' => $order->nota]));
        $this->assertSame('cash', $order->jenis_pembayaran);
        $this->assertSame('pending', $order->payment_status);
        $this->assertSame('Jalan Testing Nomor 1', $order->pickup_address);
        $this->assertNotNull($order->payments()->first());

        // Verify detail page is accessible post-creation
        $this->get(route('order.detail', ['id' => $order->id]))
            ->assertOk();

        // Verify public receipt page loads correctly
        $this->get(route('public.cetak-struk', ['idOrNota' => $order->id]))
            ->assertOk();

        $this->get(route('order.history'))
            ->assertOk();

        // Request Delivery (Minta Pengantaran)
        $this->get(route('order.request.delivery', ['id' => $order->id, 'change' => 1]))
            ->assertOk();

        $this->get(route('order.request.delivery.confirm', [
            'id' => $order->id,
            'address' => 'Jalan Antar Baru',
            'detail_address' => 'Pagar Hijau',
            'lat' => -6.22,
            'lng' => 106.82,
        ]))->assertOk();

        $this->post(route('order.delivery.store', ['id' => $order->id]), [
            'address' => 'Jalan Antar Baru',
            'detail_address' => 'Pagar Hijau',
            'lat' => -6.22,
            'lng' => 106.82,
            'pickup_date' => 'today',
            'pickup_time' => '10:00',
            'catatan' => 'Tolong hati-hati',
        ])->assertRedirect(route('order.detail', ['id' => $order->id]));

        app(\App\Modules\Payment\Application\Services\PaymentWebhookService::class)->handleMidtransNotification([
            'transaction_status' => 'settlement',
            'payment_type' => 'qris',
            'order_id' => $order->id . '-' . time(),
            'fraud_status' => 'accept',
            'gross_amount' => $order->total_bayar_akhir,
        ]);

        $order->refresh();
        $this->assertTrue((bool)$order->is_roundtrip);
        $this->assertSame('Jalan Antar Baru', $order->pickup_address);

        // Complaint (Kirim Komplain)
        $this->get(route('order.complaint', ['id' => $order->id]))
            ->assertOk();

        $this->post(route('order.complaint.store', ['id' => $order->id]), [
            'content' => 'Baju saya luntur satu buah.',
        ])->assertRedirect(route('order.detail', ['id' => $order->id]));

        $this->assertDatabaseHas('complaints', [
            'transaksi_id' => $order->id,
            'pelanggan_id' => $pelanggan->id,
            'content' => 'Baju saya luntur satu buah.',
            'status' => 'pending',
        ]);
    }

    public function test_customer_and_order_api_flow_works_end_to_end(): void
    {
        $admin = $this->createAdminUser();
        $customer = $this->createCustomerUser('api-order@example.com', 'api-order');
        [$cabang, $layananPrioritas] = $this->ensureOrderReferencesExist();

        $this->actingAs($customer);

        $this->putJson('/api/v1/customer/address', [
            'label' => 'Rumah',
            'address' => 'Jalan API Nomor 2',
            'detail_address' => 'Samping mushola',
            'lat' => -6.21,
            'lng' => 106.81,
            'jenis_kelamin' => 'L',
            'telepon' => '08123456789',
        ])->assertOk()
            ->assertJsonPath('data.address.address', 'Jalan API Nomor 2');

        $this->putJson('/api/v1/customer/preferences', [
            'default_parfum' => 'ocean',
            'default_note' => 'Jangan pakai pemutih',
            'default_payment_method' => 'qris',
        ])->assertOk()
            ->assertJsonPath('data.preferences.default_payment_method', 'qris');

        $preferenceOwner = Pelanggan::query()->where('user_id', $customer->id)->first();
        $this->assertDatabaseHas('customer_preferences', [
            'pelanggan_id' => $preferenceOwner->id,
            'default_parfum' => 'ocean',
            'default_note' => 'Jangan pakai pemutih',
            'default_payment_method' => 'qris',
        ]);

        $profileResponse = $this->getJson('/api/v1/customer/profile')
            ->assertOk()
            ->assertJsonPath('data.profile.nama', 'api-order');

        $pelangganId = $profileResponse->json('data.profile.id');

        $this->getJson('/api/v1/payment/methods')
            ->assertOk()
            ->assertJsonCount(3, 'data.methods');

        $createOrderResponse = $this->postJson('/api/v1/orders', [
            'pelanggan_id' => $pelangganId,
            'cabang_id' => $cabang->id,
            'layanan_prioritas_id' => $layananPrioritas->id,
            'pickup_address' => 'Jalan API Nomor 2',
            'pickup_detail_address' => 'Samping mushola',
            'pickup_date' => now()->toDateString(),
            'pickup_time' => '10:30',
            'parfum' => 'ocean',
            'catatan' => 'Tes order API',
            'payment_method' => 'qris',
            'estimated_total' => 25000,
        ])->assertCreated()
            ->assertJsonPath('data.order.payment.method', 'qris');

        $orderId = $createOrderResponse->json('data.order.id');

        $this->getJson('/api/v1/orders/history')
            ->assertOk()
            ->assertJsonPath('data.orders.0.id', $orderId);

        $this->getJson("/api/v1/orders/{$orderId}")
            ->assertOk()
            ->assertJsonPath('data.order.id', $orderId);

        $this->actingAs($admin);

        $this->postJson("/api/v1/payments/{$orderId}/verify", [
            'method' => 'qris',
            'amount' => 25000,
            'notes' => 'Lunas via QRIS',
        ])->assertOk()
            ->assertJsonPath('data.payment.status', 'verified');

        $this->patchJson("/api/v1/orders/{$orderId}/status", [
            'status' => 'in_progress',
        ])->assertOk()
            ->assertJsonPath('data.order.status', 'in_progress');
    }

    public function test_operator_can_work_and_complete_order()
    {
        $admin = $this->createAdminUser();
        $this->actingAs($admin);
        
        $customer = $this->createCustomerUser('test-op@example.com', 'test-op');
        
        $pelanggan = Pelanggan::create([
            'user_id' => $customer->id,
            'nama' => 'Test Op Customer',
            'jenis_kelamin' => 'L',
            'telepon' => '081234567890',
            'alamat' => 'Jalan Test Op',
        ]);
        
        [$cabang, $lp] = $this->ensureOrderReferencesExist();
        
        $transaksi = Transaksi::create([
            'nota' => 'NOT-TEST-OP',
            'waktu' => now(),
            'total_biaya_layanan' => 10000,
            'total_biaya_prioritas' => 0,
            'total_biaya_layanan_tambahan' => 0,
            'total_bayar_akhir' => 10000,
            'jenis_pembayaran' => 'cash',
            'bayar' => 0,
            'kembalian' => 0,
            'status' => 'Perlu Dikerjakan',
            'layanan_prioritas_id' => $lp->id,
            'pelanggan_id' => $pelanggan->id,
            'pegawai_id' => (string)$admin->id,
            'cabang_id' => $cabang->id,
        ]);
        
        // Start working on transaction
        $this->post("/admin/riwayat-pesanan/{$transaksi->id}/kerjakan", [
            'pegawai_id' => $admin->id,
            'items' => [
                [
                    'nama_item' => 'Kaos',
                    'qty' => 5,
                ],
                [
                    'nama_item' => 'Celana Jeans',
                    'qty' => 2,
                ]
            ]
        ])->assertRedirect();
            
        $transaksi->refresh();
        $this->assertEquals('Proses Pengerjaan', $transaksi->status);
        $this->assertEquals(4, $transaksi->list_status_pengerjaan_id);

        // Check if customer can see the clothing details on the detail page
        $this->actingAs($customer);
        $response = $this->get(route('order.detail', ['id' => $transaksi->id]));
        $response->assertStatus(200);
        $response->assertSee('Rincian Pakaian');
        $response->assertSee('Kaos');
        $response->assertSee('5 pcs');
        $response->assertSee('Celana Jeans');
        $response->assertSee('2 pcs');

        // Restore admin auth for completing transaction
        $this->actingAs($admin);
        
        // Complete transaction
        $this->post("/admin/riwayat-pesanan/{$transaksi->id}/selesaikan")
            ->assertRedirect();
            
        $transaksi->refresh();
        // Since payment is pending, it should go to 'Menunggu Pembayaran'
        $this->assertEquals('Menunggu Pembayaran', $transaksi->status);
        $this->assertEquals(2, $transaksi->list_status_pengerjaan_id);

        // Verify history log creation
        $this->assertEquals(3, \App\Models\ListHistoryPengerjaan::where('transaksi_id', $transaksi->id)->count());
        $latestLog = \App\Models\ListHistoryPengerjaan::orderBy('id', 'desc')->first();
        $this->assertEquals(4, $latestLog->status_sebelumnya);
        $this->assertEquals(2, $latestLog->status_sesudahnya);
    }

    public function test_preferensi_tersimpan_dipakai_sebagai_default_saat_booking_dan_konfirmasi_order(): void
    {
        $this->createAdminUser();
        $customer = $this->createCustomerUser('preferensi@example.com', 'preferensi');
        [$cabang, $layananPrioritas] = $this->ensureOrderReferencesExist();

        $pelanggan = Pelanggan::create([
            'user_id' => $customer->id,
            'nama' => 'Preferensi Test',
            'telepon' => '081200001111',
            'alamat' => 'Alamat Awal',
            'jenis_kelamin' => 'L',
        ]);

        \App\Models\CustomerPreference::create([
            'pelanggan_id' => $pelanggan->id,
            'default_parfum' => 'Lavender',
            'default_note' => 'Tolong pisahkan baju putih',
            'default_payment_method' => 'transfer',
        ]);

        $this->actingAs($customer);

        $this->post(route('order.pickup.store'), [
            'service' => 'regular',
            'address' => 'Jalan Preferensi No. 1',
            'lat' => -6.2,
            'lng' => 106.8,
        ])->assertRedirect(route('order.booking'));

        // Halaman booking harus pre-fill dari preferensi tersimpan, bukan hardcode "Fresh"/kosong.
        $this->get(route('order.booking'))
            ->assertOk()
            ->assertSee('Lavender')
            ->assertSee('Tolong pisahkan baju putih');

        // Form booking tidak punya field pemilihan metode bayar sendiri — konfirmasi
        // tanpa mengirim 'payment' sama sekali, seperti yang benar-benar terjadi di form asli.
        $response = $this->post(route('order.confirm'), [
            'service' => 'regular',
            'address' => 'Jalan Preferensi No. 1',
            'lat' => -6.2,
            'lng' => 106.8,
            'selected_service_id' => 'regular',
            'pickup_date' => 'today',
            'pickup_time' => '09:00',
            'parfum' => 'Lavender',
            'catatan' => 'Tolong pisahkan baju putih',
        ]);

        $order = Transaksi::query()->where('pelanggan_id', $pelanggan->id)->latest('created_at')->first();

        $this->assertNotNull($order);
        $response->assertRedirect(route('order.detail', ['id' => $order->nota]));
        $this->assertSame(
            'transfer',
            $order->jenis_pembayaran,
            'Tanpa metode bayar eksplisit, order harus pakai default_payment_method dari preferensi tersimpan pelanggan, bukan hardcode qris.'
        );
    }

    public function test_dashboard_pesanan_terakhir_hanya_menampilkan_order_yang_sudah_selesai(): void
    {
        $this->createAdminUser();
        $customer = $this->createCustomerUser('dashboard-recent@example.com', 'dashboard-recent');
        [$cabang, $layananPrioritas] = $this->ensureOrderReferencesExist();

        $pelanggan = Pelanggan::query()->where('user_id', $customer->id)->first();
        if (!$pelanggan) {
            $pelanggan = Pelanggan::create([
                'user_id' => $customer->id,
                'nama' => 'Dashboard Recent',
                'telepon' => '081200002222',
                'alamat' => 'Alamat Awal',
                'jenis_kelamin' => 'L',
            ]);
        }

        // Order lama, sudah selesai (list_status_pengerjaan_id = 5).
        $oldFinished = Transaksi::create([
            'nota' => 'ZYG-OLD-DONE',
            'waktu' => now()->subDays(5),
            'total_biaya_layanan' => 10000,
            'total_biaya_prioritas' => 0,
            'total_biaya_layanan_tambahan' => 0,
            'total_bayar_akhir' => 10000,
            'jenis_pembayaran' => 'cash',
            'bayar' => 10000,
            'kembalian' => 0,
            'layanan_prioritas_id' => $layananPrioritas->id,
            'pelanggan_id' => $pelanggan->id,
            'pegawai_id' => '0',
            'cabang_id' => $cabang->id,
            'payment_status' => 'paid',
        ]);
        $oldFinished->pending_status_id = 5;
        $oldFinished->save();

        // Order paling baru, TAPI masih diproses (belum status "Selesai").
        $newInProgress = Transaksi::create([
            'nota' => 'ZYG-NEW-PROGRESS',
            'waktu' => now(),
            'total_biaya_layanan' => 15000,
            'total_biaya_prioritas' => 0,
            'total_biaya_layanan_tambahan' => 0,
            'total_bayar_akhir' => 15000,
            'jenis_pembayaran' => 'cash',
            'bayar' => 0,
            'kembalian' => 0,
            'layanan_prioritas_id' => $layananPrioritas->id,
            'pelanggan_id' => $pelanggan->id,
            'pegawai_id' => '0',
            'cabang_id' => $cabang->id,
            'payment_status' => 'pending',
        ]);

        // "Pesanan Terakhir" cuma dipakai untuk tombol "Ulangi Pesanan", yang
        // cuma masuk akal untuk pesanan yang sudah tuntas (dikonfirmasi dengan
        // Syihan) — jadi order yang masih diproses TIDAK boleh muncul di sana,
        // walaupun itu order paling baru. Order yang masih diproses tetap
        // ditampilkan lewat "Pesanan Aktif", bukan "Pesanan Terakhir".
        $data = app(\App\Modules\Order\Application\Services\OrderWebService::class)->dashboardData($customer);

        $this->assertNotNull($data['latestOrder']);
        $this->assertSame('ZYG-OLD-DONE', $data['latestOrder']['nota_layanan']);

        $this->assertNotNull($data['activeOrder']);
        $this->assertSame('ZYG-NEW-PROGRESS', $data['activeOrder']['nota_layanan']);

        $this->actingAs($customer)->get(route('dashboard'))->assertOk();
    }

    private function createAdminUser(): User
    {
        $this->ensureRoleExists('admin');

        $user = User::factory()->create([
            'username' => 'admin-smoke',
            'slug' => 'admin-smoke',
            'email' => 'admin-smoke@example.com',
            'password' => 'password',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $user->assignRole('admin');

        return $user;
    }

    private function createCustomerUser(string $email, string $username): User
    {
        $this->ensureRoleExists('customer');

        $user = User::factory()->create([
            'username' => $username,
            'slug' => $username,
            'email' => $email,
            'password' => 'password',
            'email_verified_at' => now(),
        ]);

        $user->assignRole('customer');

        return $user;
    }

    private function ensureRoleExists(string $name): void
    {
        Role::query()->firstOrCreate([
            'name' => $name,
            'guard_name' => 'web',
        ]);
    }

    /**
     * @return array{0: \App\Models\Cabang, 1: \App\Models\LayananPrioritas}
     */
    private function ensureOrderReferencesExist(): array
    {
        $cabang = Cabang::query()->firstOrCreate(
            ['nama' => 'Cabang Smoke'],
            [
                'slug' => 'cabang-smoke',
                'lokasi' => 'Bandung',
                'alamat' => 'Jalan Cabang Smoke',
            ],
        );

        $layananPrioritas = LayananPrioritas::query()->firstOrCreate(
            [
                'nama' => 'Regular Smoke',
                'cabang_id' => $cabang->id,
            ],
            [
                'deskripsi' => 'Layanan untuk smoke test',
                'harga' => 25000,
                'prioritas' => 1,
            ],
        );

        return [$cabang, $layananPrioritas];
    }
}
