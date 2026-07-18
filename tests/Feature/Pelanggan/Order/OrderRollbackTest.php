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

    public function test_ajukan_delivery_dengan_nota_bukan_uuid_tidak_error(): void
    {
        // Order pelanggan (dibuat via booking sendiri) pakai nota berformat
        // "PLG-XXXXXXXX", bukan UUID. Endpoint ajukan-delivery sempat query
        // Transaksi::find($id) langsung pakai nota ini tanpa fallback ke kolom
        // nota dulu — di Postgres (tipe kolom id = uuid) ini bikin request
        // AJAX gagal dengan SQLSTATE 22P02 (invalid input syntax for type uuid).
        [$user, $order] = $this->makeCustomerOrder('ajukan-delivery-nota@example.com', 'ajukan-delivery-nota');
        $order->update(['nota' => 'PLG-' . strtoupper(substr(md5($order->id), 0, 8))]);

        $response = $this->actingAs($user)
            ->postJson(route('order.delivery.store', ['id' => $order->nota]), [
                'address' => 'Alamat Pengantaran Baru',
                'detail_address' => 'Detail Baru',
                'lat' => -6.3,
                'lng' => 106.9,
            ]);

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertNotSame(
            '-',
            $response->json('estimated_finished'),
            'estimated_finished harus terisi dari order yang berhasil ditemukan lewat nota, bukan fallback kosong karena lookup id gagal.'
        );
    }

    public function test_status_pengerjaan_diproses_muncul_di_log_pesanan_pelanggan(): void
    {
        // getStatusName() menghasilkan 'Proses Pengerjaan', 'Perlu Dikerjakan', dst —
        // bukan literal 'Proses'. mapOrderLogs() sempat cuma cek in_array($status,
        // ['Proses', 'Selesai']) sehingga log "Pesanan sedang diproses" TIDAK PERNAH
        // muncul walau progress sudah 56% (admin sudah mulai mengerjakan).
        [$user, $order] = $this->makeCustomerOrder('status-log@example.com', 'status-log');

        $order->pending_status_id = 4; // 'Proses Pengerjaan'
        $order->save();

        $data = app(\App\Modules\Order\Application\Services\OrderWebService::class)->detailData($order->id, $user);

        $notes = array_column($data['logs'], 'note');
        $this->assertContains(
            'Pesanan sedang diproses',
            $notes,
            'Log status pesanan harus menampilkan "Pesanan sedang diproses" begitu admin mulai mengerjakan pesanan (status Proses Pengerjaan), bukan diam di "Pesanan diterima" saja.'
        );
    }

    public function test_rincian_pakaian_yang_diisi_admin_muncul_di_detail_pesanan_pelanggan(): void
    {
        [$user, $order, $cabang] = $this->makeCustomerOrder('rincian-pakaian@example.com', 'rincian-pakaian');
        $admin = User::factory()->create([
            'username' => 'rincian-pakaian-admin2',
            'slug' => 'rincian-pakaian-admin2',
            'email' => 'rincian-pakaian-admin2@example.com',
            'role' => 'admin',
        ]);
        $admin->assignRole('admin');

        // Simulasikan alur admin sungguhan: timbang dulu, baru isi rincian pakaian
        // lewat endpoint "mulai kerjakan" — persis seperti UI admin sebenarnya.
        $this->actingAs($admin)->postJson(route('admin.riwayat-pesanan.proses', $order->id), [
            'berat' => 5.6,
        ])->assertStatus(200);

        $this->actingAs($admin)->postJson(route('admin.riwayat-pesanan.kerjakan', $order->id), [
            'pegawai_id' => $admin->id,
            'items' => [
                ['nama_item' => 'Kaos', 'qty' => 3],
                ['nama_item' => 'Celana', 'qty' => 2],
            ],
        ])->assertStatus(200);

        $data = app(\App\Modules\Order\Application\Services\OrderWebService::class)->detailData($order->id, $user);

        $this->assertNotEmpty(
            $data['clothing_items'],
            'Rincian pakaian yang sudah diisi admin lewat endpoint kerjakan harus muncul di halaman detail pesanan pelanggan.'
        );
        $this->assertEqualsCanonicalizing(
            ['Kaos', 'Celana'],
            array_column($data['clothing_items'], 'name')
        );
    }

    public function test_email_pesanan_selesai_terkirim_saat_admin_menyelesaikan_pesanan(): void
    {
        // getStatusName(5) menghasilkan 'Pesanan Selesai', bukan literal 'Selesai' —
        // pengecekan lama di Transaksi::booted() (saved hook) memakai
        // $transaksi->status === 'Selesai' yang tidak pernah cocok, sehingga
        // OrderFinishedMail tidak pernah benar-benar terkirim ke pelanggan.
        \Illuminate\Support\Facades\Mail::fake();

        [$user, $order] = $this->makeCustomerOrder('email-selesai@example.com', 'email-selesai');
        $order->update(['payment_status' => 'paid']);

        $order->status = 'Selesai';
        $order->save();

        \Illuminate\Support\Facades\Mail::assertSent(
            \App\Mail\OrderFinishedMail::class,
            fn ($mail) => $mail->transaksi->id === $order->id
        );
    }

    public function test_ajukan_delivery_pada_order_lunas_tidak_menambah_biaya_dan_status_tetap_lunas(): void
    {
        // "Gratis pickup dan delivery" dijanjikan di Syarat & Ketentuan halaman
        // detail pesanan. config('laundry.delivery_fee') dulu default Rp5.000,
        // yang diam-diam menambah Total pesanan yang sudah Lunas tanpa pernah
        // mengubah payment_status atau menyediakan cara untuk melunasinya.
        [$user, $order] = $this->makeCustomerOrder('delivery-gratis@example.com', 'delivery-gratis');
        $order->update(['payment_status' => 'paid']);
        $originalTotal = $order->total_bayar_akhir;

        $this->actingAs($user)->postJson(route('order.delivery.store', ['id' => $order->id]), [
            'address' => 'Alamat Pengantaran Baru',
            'lat' => -6.3,
            'lng' => 106.9,
        ])->assertOk()->assertJson(['success' => true]);

        $data = app(\App\Modules\Order\Application\Services\OrderWebService::class)->detailData($order->id, $user);

        $this->assertSame(0.0, $data['delivery_fee'], 'Biaya pengiriman harus Rp0 karena delivery memang gratis.');
        $this->assertEquals($originalTotal, $data['total'], 'Total tidak boleh bertambah oleh biaya delivery yang seharusnya gratis.');
        $this->assertSame('Lunas', $data['payment_status']);
    }

    public function test_bukti_timbangan_yang_diunggah_admin_muncul_di_galeri_pelanggan(): void
    {
        // mapOrderDetail() sempat tidak pernah mengisi key 'gallery' sama sekali
        // (blade selalu menampilkan "Tidak ada Gambar"), dan endpoint upload bukti
        // timbangan bahkan belum terdaftar sebagai route — jadi fitur Galeri
        // 100% tidak bisa dipakai dari ujung ke ujung.
        [$user, $order] = $this->makeCustomerOrder('galeri@example.com', 'galeri');
        $admin = User::factory()->create([
            'username' => 'galeri-admin2',
            'slug' => 'galeri-admin2',
            'email' => 'galeri-admin2@example.com',
            'role' => 'admin',
        ]);
        $admin->assignRole('admin');

        \Illuminate\Support\Facades\Storage::fake('cloudinary');
        $file = \Illuminate\Http\UploadedFile::fake()->image('timbangan.jpg');

        $this->actingAs($admin)->post(route('admin.riwayat-pesanan.bukti-timbangan', $order->id), [
            'bukti_timbangan' => $file,
        ])->assertRedirect();

        $order->refresh();
        $this->assertNotNull($order->bukti_timbangan, 'Upload bukti timbangan harus tersimpan ke kolom bukti_timbangan.');

        $data = app(\App\Modules\Order\Application\Services\OrderWebService::class)->detailData($order->id, $user);

        $this->assertNotEmpty(
            $data['gallery'],
            'Foto bukti timbangan yang sudah diunggah admin harus muncul di Galeri halaman detail pesanan pelanggan.'
        );
        $this->assertSame($order->bukti_timbangan, $data['gallery'][0]);
    }

    public function test_detail_pesanan_nota_tidak_ada_menampilkan_404_bukan_crash_500(): void
    {
        // Kolom id di Postgres bertipe uuid. Kalau nota tidak ketemu, kode lama
        // fallback ke Transaksi::find($nota) — string non-UUID bikin driver pgsql
        // lempar QueryException (SQLSTATE 22P02) alih-alih return null seperti
        // MySQL, sehingga halaman crash 500 alih-alih redirect "tidak ditemukan".
        $user = User::factory()->create([
            'username' => 'nota-hilang',
            'slug' => 'nota-hilang',
            'email' => 'nota-hilang@example.com',
            'password' => 'password',
        ]);
        $user->assignRole('customer');

        $response = $this->actingAs($user)->get(route('order.detail', ['id' => 'PLG-TIDAK-ADA']));

        $response->assertRedirect(route('order.history'));
    }

    public function test_guest_yang_bayar_dapat_email_notifikasi_pembayaran(): void
    {
        // Form booking guest MEMINTA email (customer_email), tapi confirmOrder()
        // sempat cuma validasi field ini lalu buang begitu saja — tidak pernah
        // disimpan ke mana pun. Akibatnya guest yang bayar tidak pernah dapat
        // email konfirmasi karena tidak ada email tersimpan sama sekali untuk
        // Pelanggan mereka (guest tidak punya akun User).
        \Illuminate\Support\Facades\Mail::fake();

        [$cabang, $layananPrioritas] = $this->ensureOrderReferencesExist();

        $response = $this->post(route('order.confirm'), [
            'service' => 'regular',
            'address' => 'Jalan Guest No. 1',
            'lat' => -6.2,
            'lng' => 106.8,
            'selected_service_id' => 'regular',
            'pickup_date' => 'today',
            'pickup_time' => '09:00',
            'customer_name' => 'Guest Bayar',
            'customer_phone' => '081200003333',
            'customer_email' => 'guest-bayar@example.com',
            'payment' => 'cash',
        ]);

        $pelanggan = \App\Models\Pelanggan::where('telepon', '081200003333')->first();
        $this->assertNotNull($pelanggan);
        $this->assertSame(
            'guest-bayar@example.com',
            $pelanggan->email,
            'Email yang diisi guest saat checkout harus tersimpan ke Pelanggan supaya bisa dikirimi notifikasi.'
        );

        $order = \App\Models\Transaksi::where('pelanggan_id', $pelanggan->id)->latest('created_at')->first();
        $order->payment_status = 'paid';
        $order->save();

        \Illuminate\Support\Facades\Mail::assertSent(
            \App\Mail\PaymentConfirmedMail::class,
            fn ($mail) => $mail->transaksi->id === $order->id
        );
    }

    private function ensureOrderReferencesExist(): array
    {
        $this->ensureRoleExists('admin');
        $this->ensureRoleExists('customer');
        $admin = User::query()->where('username', 'admin-guest-email')->first() ?? User::factory()->create([
            'username' => 'admin-guest-email',
            'slug' => 'admin-guest-email',
            'email' => 'admin-guest-email@example.com',
            'role' => 'admin',
        ]);
        $admin->assignRole('admin');
        $cabang = Cabang::firstOrCreate(['nama' => 'Cabang Guest Email'], ['alamat' => 'Alamat', 'lokasi' => 'Jakarta']);
        $layananPrioritas = LayananPrioritas::firstOrCreate(
            ['nama' => 'Reguler', 'cabang_id' => $cabang->id],
            ['harga' => 0, 'prioritas' => 1]
        );

        return [$cabang, $layananPrioritas];
    }

    private function ensureRoleExists(string $name): void
    {
        Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
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
