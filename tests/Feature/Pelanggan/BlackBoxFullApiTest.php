<?php

namespace Tests\Feature\Pelanggan;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Pelanggan;
use App\Models\CustomerAddress as AlamatPelanggan;
use App\Models\Cabang;
use App\Models\LayananPrioritas;
use App\Models\Transaksi;
use App\Models\Notifikasi;
use App\Models\Complaint;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BlackBoxFullApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'customer']);
        Role::firstOrCreate(['name' => 'admin']);

        Storage::fake('cloudinary');
    }

    private function createCustomerUser($email = 'test@domain.com')
    {
        $user = User::factory()->create([
            'name' => 'Test',
            'username' => 'test' . rand(1, 10000),
            'email' => $email,
            'role' => 'customer'
        ]);
        $user->assignRole('customer');
        Pelanggan::create([
            'user_id' => $user->id,
            'nama' => 'Test',
            'telepon' => '0812' . rand(1000, 9999) . rand(1000, 9999),
            'alamat' => 'Jalan Test',
            'jenis_kelamin' => 'L',
        ]);
        return $user;
    }

    private function createOrder($pelangganId, $userId, $nota = 'ZYG-TEST-123')
    {
        $cabang = Cabang::firstOrCreate(['nama' => 'Pusat'], ['lokasi' => 'Pusat', 'alamat' => 'Pusat']);
        $prio = LayananPrioritas::firstOrCreate(['nama' => 'Reguler', 'cabang_id' => $cabang->id], ['harga' => 0, 'prioritas' => 1]);

        return Transaksi::create([
            'id' => Str::uuid(),
            'nota' => $nota,
            'status' => 'completed',
            'pelanggan_id' => $pelangganId,
            'waktu' => now(),
            'total_biaya_layanan' => 10000,
            'total_biaya_prioritas' => 0,
            'total_biaya_layanan_tambahan' => 0,
            'bayar' => 0,
            'kembalian' => 0,
            'total_bayar_akhir' => 10000,
            'jenis_pembayaran' => 'cash',
            'layanan_prioritas_id' => $prio->id,
            'cabang_id' => $cabang->id,
            'pegawai_id' => (string) $userId,
            'payment_status' => 'pending',
            'is_roundtrip' => false,
        ]);
    }

    public function test_a_profile()
    {
        $user = $this->createCustomerUser('a@mail.com');

        $resNoToken = $this->getJson('/api/v1/customer/profile');
        $resNoToken->assertStatus(401);

        $resToken = $this->actingAs($user, 'sanctum')->getJson('/api/v1/customer/profile');
        $resToken->assertStatus(200)->assertJsonPath('data.profile.nama', 'Test');
    }

    public function test_b_addresses()
    {
        $user = $this->createCustomerUser('b@mail.com');
        $pelangganId = Pelanggan::where('user_id', $user->id)->first()->id;

        $resEmpty = $this->actingAs($user, 'sanctum')->getJson('/api/v1/customer/addresses');
        $resEmpty->assertStatus(200)->assertJsonPath('data.addresses', []);

        AlamatPelanggan::create([
            'pelanggan_id' => $pelangganId,
            'label' => 'Rumah',
            'address' => 'Jalan',
            'lat' => -6.123,
            'lng' => 106.123,
            'is_default' => true
        ]);

        $resFilled = $this->actingAs($user, 'sanctum')->getJson('/api/v1/customer/addresses');
        $resFilled->assertStatus(200)
            ->assertJsonCount(1, 'data.addresses')
            ->assertJsonPath('data.addresses.0.label', 'Rumah');
    }

    public function test_c_add_address()
    {
        $user = $this->createCustomerUser('c@mail.com');
        $pelangganId = Pelanggan::where('user_id', $user->id)->first()->id;

        AlamatPelanggan::create(['pelanggan_id' => $pelangganId, 'label' => 'A1', 'address' => 'A1', 'lat' => 1, 'lng' => 1, 'is_default' => true]);
        AlamatPelanggan::create(['pelanggan_id' => $pelangganId, 'label' => 'A2', 'address' => 'A2', 'lat' => 1, 'lng' => 1, 'is_default' => false]);

        $resValid = $this->actingAs($user, 'sanctum')->postJson('/api/v1/customer/addresses', [
            'label' => 'A3', 'address' => 'A3', 'lat' => 1, 'lng' => 1
        ]);
        $resValid->assertStatus(201);

        $resMax = $this->actingAs($user, 'sanctum')->postJson('/api/v1/customer/addresses', [
            'label' => 'A4', 'address' => 'A4', 'lat' => 1, 'lng' => 1
        ]);
        $resMax->assertStatus(422);
        $this->assertSame(3, AlamatPelanggan::where('pelanggan_id', $pelangganId)->count());

        $resNoCoord = $this->actingAs($user, 'sanctum')->postJson('/api/v1/customer/addresses', [
            'label' => 'A5', 'address' => 'A5'
        ]);
        $resNoCoord->assertStatus(422)->assertJsonValidationErrors(['lat', 'lng']);
    }

    public function test_d_primary_address()
    {
        $user = $this->createCustomerUser('d@mail.com');
        $user2 = $this->createCustomerUser('d2@mail.com');
        $p1 = Pelanggan::where('user_id', $user->id)->first()->id;
        $p2 = Pelanggan::where('user_id', $user2->id)->first()->id;

        $addrOwn = AlamatPelanggan::create(['pelanggan_id' => $p1, 'label' => 'A1', 'address' => 'A1', 'lat' => 1, 'lng' => 1, 'is_default' => false]);
        $addrPrimary = AlamatPelanggan::create(['pelanggan_id' => $p1, 'label' => 'A2', 'address' => 'A2', 'lat' => 1, 'lng' => 1, 'is_default' => true]);
        $addrOther = AlamatPelanggan::create(['pelanggan_id' => $p2, 'label' => 'A3', 'address' => 'A3', 'lat' => 1, 'lng' => 1, 'is_default' => false]);

        $resOwn = $this->actingAs($user, 'sanctum')->postJson("/api/v1/customer/addresses/{$addrOwn->id}/primary");
        $resOwn->assertStatus(200)->assertJsonPath('data.address.is_primary', true);
        $this->assertDatabaseHas('customer_addresses', ['id' => $addrOwn->id, 'is_default' => true]);
        $this->assertDatabaseHas('customer_addresses', ['id' => $addrPrimary->id, 'is_default' => false]);

        $resOther = $this->actingAs($user, 'sanctum')->postJson("/api/v1/customer/addresses/{$addrOther->id}/primary");
        $resOther->assertStatus(403);
        $this->assertDatabaseHas('customer_addresses', ['id' => $addrOther->id, 'is_default' => false]);

        $resAlreadyPrimary = $this->actingAs($user, 'sanctum')->postJson("/api/v1/customer/addresses/{$addrOwn->id}/primary");
        $resAlreadyPrimary->assertStatus(200);
    }

    public function test_e_edit_address()
    {
        $user = $this->createCustomerUser('e@mail.com');
        $user2 = $this->createCustomerUser('e2@mail.com');
        $p1 = Pelanggan::where('user_id', $user->id)->first()->id;
        $p2 = Pelanggan::where('user_id', $user2->id)->first()->id;

        $addrOwn = AlamatPelanggan::create(['pelanggan_id' => $p1, 'label' => 'A1', 'address' => 'A1', 'lat' => 1, 'lng' => 1, 'is_default' => false]);
        $addrOther = AlamatPelanggan::create(['pelanggan_id' => $p2, 'label' => 'A2', 'address' => 'A2', 'lat' => 1, 'lng' => 1, 'is_default' => false]);

        $resOwn = $this->actingAs($user, 'sanctum')->putJson("/api/v1/customer/addresses/{$addrOwn->id}", ['label' => 'A1 Edit', 'address' => 'A1', 'lat' => 1, 'lng' => 1]);
        $resOwn->assertStatus(200)->assertJsonPath('data.address.label', 'A1 Edit');

        $resOther = $this->actingAs($user, 'sanctum')->putJson("/api/v1/customer/addresses/{$addrOther->id}", ['label' => 'A2 Edit', 'address' => 'A2', 'lat' => 1, 'lng' => 1]);
        $resOther->assertStatus(403);
        $this->assertDatabaseHas('customer_addresses', ['id' => $addrOther->id, 'label' => 'A2']);

        $resNotExist = $this->actingAs($user, 'sanctum')->putJson("/api/v1/customer/addresses/99999", ['label' => 'A', 'address' => 'A', 'lat' => 1, 'lng' => 1]);
        $resNotExist->assertStatus(403);
    }

    public function test_f_delete_address()
    {
        $user = $this->createCustomerUser('f@mail.com');
        $user2 = $this->createCustomerUser('f2@mail.com');
        $p1 = Pelanggan::where('user_id', $user->id)->first()->id;
        $p2 = Pelanggan::where('user_id', $user2->id)->first()->id;

        $addrOwn = AlamatPelanggan::create(['pelanggan_id' => $p1, 'label' => 'A1', 'address' => 'A1', 'lat' => 1, 'lng' => 1, 'is_default' => false]);
        $addrPrimary = AlamatPelanggan::create(['pelanggan_id' => $p1, 'label' => 'A2', 'address' => 'A2', 'lat' => 1, 'lng' => 1, 'is_default' => true]);
        $addrOther = AlamatPelanggan::create(['pelanggan_id' => $p2, 'label' => 'A3', 'address' => 'A3', 'lat' => 1, 'lng' => 1, 'is_default' => false]);

        $resOwn = $this->actingAs($user, 'sanctum')->deleteJson("/api/v1/customer/addresses/{$addrOwn->id}");
        $resOwn->assertStatus(200);
        $this->assertDatabaseMissing('customer_addresses', ['id' => $addrOwn->id]);

        $resPrimary = $this->actingAs($user, 'sanctum')->deleteJson("/api/v1/customer/addresses/{$addrPrimary->id}");
        $resPrimary->assertStatus(422); // tidak bisa hapus alamat utama sebelum menetapkan alamat lain jadi utama
        $this->assertDatabaseHas('customer_addresses', ['id' => $addrPrimary->id]);

        $resOther = $this->actingAs($user, 'sanctum')->deleteJson("/api/v1/customer/addresses/{$addrOther->id}");
        $resOther->assertStatus(403);
        $this->assertDatabaseHas('customer_addresses', ['id' => $addrOther->id]);
    }

    public function test_g_create_order()
    {
        $user = $this->createCustomerUser('g@mail.com');
        $p1 = Pelanggan::where('user_id', $user->id)->first()->id;
        $cabang = Cabang::firstOrCreate(['nama' => 'C1'], ['lokasi' => 'L1', 'alamat' => 'D1']);
        $prio = LayananPrioritas::firstOrCreate(['nama' => 'P1', 'cabang_id' => $cabang->id], ['harga' => 0, 'prioritas' => 1]);

        // Order butuh admin/pegawai assignable (EloquentOrderRepository::firstAssignablePegawaiId).
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->assignRole('admin');

        $resLengkap = $this->actingAs($user, 'sanctum')->postJson('/api/v1/orders', [
            'pelanggan_id' => $p1,
            'pickup_address' => 'Jalan A',
            'pickup_date' => now()->toDateString(),
            'pickup_time' => '10:00',
            'payment_method' => 'cash',
            'cabang_id' => $cabang->id,
            'layanan_prioritas_id' => $prio->id,
            'estimated_total' => 50000,
        ]);
        $resLengkap->assertStatus(201);

        $resEmpty = $this->actingAs($user, 'sanctum')->postJson('/api/v1/orders', []);
        $resEmpty->assertStatus(422)->assertJsonValidationErrors([
            'pelanggan_id', 'cabang_id', 'layanan_prioritas_id', 'pickup_address',
            'pickup_date', 'pickup_time', 'payment_method', 'estimated_total',
        ]);

        $resInvalidCabang = $this->actingAs($user, 'sanctum')->postJson('/api/v1/orders', [
            'pelanggan_id' => $p1,
            'pickup_address' => 'Jalan A',
            'pickup_date' => now()->toDateString(),
            'pickup_time' => '10:00',
            'payment_method' => 'cash',
            'cabang_id' => 9999,
            'layanan_prioritas_id' => $prio->id,
            'estimated_total' => 50000,
        ]);
        $resInvalidCabang->assertStatus(422)->assertJsonValidationErrors('cabang_id');
    }

    public function test_h_order_history()
    {
        $user1 = $this->createCustomerUser('h1@mail.com');
        $user2 = $this->createCustomerUser('h2@mail.com');
        $p1 = Pelanggan::where('user_id', $user1->id)->first()->id;

        $this->createOrder($p1, $user1->id, 'ZYG-1');
        $this->createOrder($p1, $user1->id, 'ZYG-2');

        $resMany = $this->actingAs($user1, 'sanctum')->getJson('/api/v1/orders/history');
        $resMany->assertStatus(200)->assertJsonPath('meta.total', 2);

        $resNone = $this->actingAs($user2, 'sanctum')->getJson('/api/v1/orders/history');
        $resNone->assertStatus(200)->assertJsonPath('meta.total', 0);
    }

    public function test_i_track_order()
    {
        $user = $this->createCustomerUser('i@mail.com');
        $p1 = Pelanggan::where('user_id', $user->id)->first();
        $this->createOrder($p1->id, $user->id, 'ZYG-TRK');

        $phoneLast4 = substr($p1->telepon, -4);

        $resValid = $this->postJson('/api/v1/orders/track', ['query' => 'ZYG-TRK', 'phone_last_4' => $phoneLast4]);
        $resValid->assertStatus(200)->assertJsonPath('success', true);

        $resWrongPhone = $this->postJson('/api/v1/orders/track', ['query' => 'ZYG-TRK', 'phone_last_4' => '0000']);
        $resWrongPhone->assertStatus(404);

        $resNoNota = $this->postJson('/api/v1/orders/track', ['query' => 'ZYG-NOTFOUND', 'phone_last_4' => $phoneLast4]);
        $resNoNota->assertStatus(404);
    }

    public function test_j_delivery_request()
    {
        $user = $this->createCustomerUser('j@mail.com');
        $p1 = Pelanggan::where('user_id', $user->id)->first()->id;
        $order = $this->createOrder($p1, $user->id, 'ZYG-DEL');

        $resValid = $this->actingAs($user, 'sanctum')->postJson("/api/v1/orders/{$order->id}/delivery-request", [
            'address' => 'Jalan Kirim',
            'lat' => -6.1,
            'lng' => 106.1
        ]);
        $resValid->assertStatus(200)->assertJsonPath('success', true);
        $order->refresh();
        $meta = json_decode($order->payment_metadata, true) ?? [];
        $this->assertArrayHasKey('pending_delivery', $meta);

        $resNoCoord = $this->actingAs($user, 'sanctum')->postJson("/api/v1/orders/{$order->id}/delivery-request", [
            'address' => 'Jalan Kirim'
        ]);
        $resNoCoord->assertStatus(400);

        // Requesting again with a finished order should still succeed at the
        // metadata level — real-world "already delivered" guard lives elsewhere.
        $resAgain = $this->actingAs($user, 'sanctum')->postJson("/api/v1/orders/{$order->id}/delivery-request", [
            'address' => 'Jalan Kirim 2',
            'lat' => -6.1,
            'lng' => 106.1
        ]);
        $resAgain->assertStatus(200);
    }

    public function test_k_payment_methods()
    {
        $user = $this->createCustomerUser('k@mail.com');

        $res = $this->actingAs($user, 'sanctum')->getJson('/api/v1/payment/methods');
        $res->assertStatus(200)->assertJsonCount(3, 'data.methods');
    }

    public function test_l_midtrans_webhook()
    {
        // Webhook selalu balas 200 "success" ke Midtrans (kontrak wajib Midtrans),
        // terlepas order-nya ketemu atau tidak — kegagalan dicatat lewat Log, bukan
        // response HTTP. Order fiktif di sini memang tidak ada di DB.
        $resSettlement = $this->postJson('/api/v1/payment/notification', [
            'order_id' => '11111111-1111-1111-1111-111111111111',
            'transaction_status' => 'settlement',
            'gross_amount' => '10000.00',
            'signature_key' => 'fake_signature'
        ]);
        $resSettlement->assertStatus(200)->assertJson(['status' => 'success']);

        $resExpire = $this->postJson('/api/v1/payment/notification', [
            'order_id' => '22222222-2222-2222-2222-222222222222',
            'transaction_status' => 'expire',
            'gross_amount' => '10000.00',
            'signature_key' => 'fake_signature'
        ]);
        $resExpire->assertStatus(200)->assertJson(['status' => 'success']);

        $resInvalid = $this->postJson('/api/v1/payment/notification', [
            'order_id' => '33333333-3333-3333-3333-333333333333',
            'transaction_status' => 'settlement', // no signature
        ]);
        $resInvalid->assertStatus(200)->assertJson(['status' => 'success']);
    }

    public function test_m_payment_status()
    {
        $user1 = $this->createCustomerUser('m1@mail.com');
        $user2 = $this->createCustomerUser('m2@mail.com');
        $p1 = Pelanggan::where('user_id', $user1->id)->first()->id;

        $order = $this->createOrder($p1, $user1->id, 'ZYG-PAYSTAT');

        $resOwn = $this->actingAs($user1, 'sanctum')->getJson("/api/v1/orders/{$order->id}/payment-status");
        $resOwn->assertStatus(200)->assertJsonPath('data.nota', 'ZYG-PAYSTAT');

        $resOther = $this->actingAs($user2, 'sanctum')->getJson("/api/v1/orders/{$order->id}/payment-status");
        $resOther->assertStatus(403);

        $resNotFound = $this->actingAs($user1, 'sanctum')->getJson("/api/v1/orders/99999999-9999-9999-9999-999999999999/payment-status");
        $resNotFound->assertStatus(404);
    }

    public function test_n_notifications()
    {
        $user1 = $this->createCustomerUser('n1@mail.com');
        $user2 = $this->createCustomerUser('n2@mail.com');
        $p1 = Pelanggan::where('user_id', $user1->id)->first()->id;

        Notifikasi::create(['pelanggan_id' => $p1, 'judul' => 'N1', 'pesan' => 'M1', 'jenis' => 'personal']);

        $resMany = $this->actingAs($user1, 'sanctum')->getJson('/api/v1/customer/notifications');
        $resMany->assertStatus(200)->assertJsonCount(1, 'data.notifications');

        $resNone = $this->actingAs($user2, 'sanctum')->getJson('/api/v1/customer/notifications');
        $resNone->assertStatus(200)->assertJsonCount(0, 'data.notifications');
    }

    public function test_o_read_notification()
    {
        $user1 = $this->createCustomerUser('o1@mail.com');
        $user2 = $this->createCustomerUser('o2@mail.com');
        $p1 = Pelanggan::where('user_id', $user1->id)->first()->id;

        $notif = Notifikasi::create(['pelanggan_id' => $p1, 'judul' => 'N1', 'pesan' => 'M1', 'jenis' => 'personal']);

        $resOwn = $this->actingAs($user1, 'sanctum')->postJson("/api/v1/customer/notifications/{$notif->id}/read");
        $resOwn->assertStatus(200);
        $this->assertDatabaseHas('notifikasi', ['id' => $notif->id, 'is_read' => true]);

        $resOther = $this->actingAs($user2, 'sanctum')->postJson("/api/v1/customer/notifications/{$notif->id}/read");
        $resOther->assertStatus(403);

        $resNotFound = $this->actingAs($user1, 'sanctum')->postJson("/api/v1/customer/notifications/99999/read");
        $resNotFound->assertStatus(404);
    }

    public function test_p_upgrade_service()
    {
        $user = $this->createCustomerUser('p@mail.com');
        $p1 = Pelanggan::where('user_id', $user->id)->first()->id;
        $order = $this->createOrder($p1, $user->id, 'ZYG-UPG');
        $orderDone = $this->createOrder($p1, $user->id, 'ZYG-UPGDONE');
        $orderDone->pending_status_id = 5;
        $orderDone->save();

        $cabang = Cabang::first();
        $prioNew = LayananPrioritas::create(['nama' => 'Kilat', 'cabang_id' => $cabang->id, 'harga' => 10000, 'prioritas' => 2]);

        $resValid = $this->actingAs($user, 'sanctum')->postJson("/api/v1/orders/{$order->id}/upgrade", ['new_service_id' => $prioNew->id]);
        $resValid->assertStatus(200)->assertJsonPath('success', true);
        $order->refresh();
        $meta = json_decode($order->payment_metadata, true) ?? [];
        $this->assertArrayHasKey('pending_upgrade', $meta);

        $resEmpty = $this->actingAs($user, 'sanctum')->postJson("/api/v1/orders/{$order->id}/upgrade", []);
        $resEmpty->assertStatus(422)->assertJsonValidationErrors('new_service_id');

        $resNotExist = $this->actingAs($user, 'sanctum')->postJson("/api/v1/orders/{$order->id}/upgrade", ['new_service_id' => 9999]);
        $resNotExist->assertStatus(400);

        $this->assertSame('Pesanan Selesai', $orderDone->fresh()->status);
        $resDone = $this->actingAs($user, 'sanctum')->postJson("/api/v1/orders/{$orderDone->id}/upgrade", ['new_service_id' => $prioNew->id]);
        $resDone->assertStatus(400)->assertJsonPath('success', false);
    }

    public function test_q_complaint()
    {
        $user = $this->createCustomerUser('q@mail.com');
        $p1 = Pelanggan::where('user_id', $user->id)->first()->id;
        $order = $this->createOrder($p1, $user->id, 'ZYG-COMP-ALL');

        $fileImg = UploadedFile::fake()->image('bukti.jpg');
        $filePdf = UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf');

        $resImg = $this->actingAs($user, 'sanctum')->post("/api/v1/orders/{$order->id}/complaint", [
            'content' => 'Rusak', 'issue_types' => ['lainnya'], 'issue_image' => $fileImg
        ], ['Accept' => 'application/json']);
        $resImg->assertStatus(200)->assertJsonPath('success', true);
        $this->assertDatabaseHas('complaints', ['id' => $resImg->json('complaint_id'), 'content' => 'Rusak']);

        $resNoPhoto = $this->actingAs($user, 'sanctum')->post("/api/v1/orders/{$order->id}/complaint", [
            'content' => 'Rusak', 'issue_types' => ['lainnya']
        ], ['Accept' => 'application/json']);
        $resNoPhoto->assertStatus(200)->assertJsonPath('success', true);

        $resPdf = $this->actingAs($user, 'sanctum')->post("/api/v1/orders/{$order->id}/complaint", [
            'content' => 'Rusak', 'issue_types' => ['lainnya'], 'issue_image' => $filePdf
        ], ['Accept' => 'application/json']);
        $resPdf->assertStatus(422);

        $resEmptyDesc = $this->actingAs($user, 'sanctum')->post("/api/v1/orders/{$order->id}/complaint", [
            'issue_types' => ['lainnya'], 'issue_image' => $fileImg
        ], ['Accept' => 'application/json']);
        $resEmptyDesc->assertStatus(400)->assertJsonPath('success', false);

        $this->assertSame(2, Complaint::where('transaksi_id', $order->id)->count());
    }

    public function test_r_complaint_history()
    {
        $user1 = $this->createCustomerUser('r1@mail.com');
        $user2 = $this->createCustomerUser('r2@mail.com');

        $p1 = Pelanggan::where('user_id', $user1->id)->first()->id;
        $order = $this->createOrder($p1, $user1->id, 'ZYG-COMP-HIST');

        $fileImg = UploadedFile::fake()->image('bukti.jpg');
        $this->actingAs($user1, 'sanctum')->post("/api/v1/orders/{$order->id}/complaint", [
            'content' => 'Rusak', 'issue_types' => ['lainnya'], 'issue_image' => $fileImg
        ], ['Accept' => 'application/json']);

        $resMany = $this->actingAs($user1, 'sanctum')->getJson('/api/v1/customer/complaints');
        $resMany->assertStatus(200)->assertJsonCount(1, 'data.complaints');

        $resNone = $this->actingAs($user2, 'sanctum')->getJson('/api/v1/customer/complaints');
        $resNone->assertStatus(200)->assertJsonCount(0, 'data.complaints');
    }
}
