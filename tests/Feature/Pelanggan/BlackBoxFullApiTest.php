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
        
        \Illuminate\Http\UploadedFile::macro('storeOnCloudinary', function ($path = null) {
            return new class {
                public function getSecurePath() {
                    return 'https://res.cloudinary.com/demo/image/upload/sample.jpg';
                }
            };
        });
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
        $cabang = Cabang::firstOrCreate(['nama' => 'Pusat'], ['lokasi' => 'Pusat', 'deskripsi' => 'Pusat']);
        $prio = LayananPrioritas::firstOrCreate(['nama' => 'Reguler', 'cabang_id' => $cabang->id], ['harga' => 0, 'prioritas' => 1]);

        return Transaksi::create([
            'id' => Str::uuid(),
            'nota' => $nota,
            'status' => 'completed',
            'pelanggan_id' => $pelangganId,
            'waktu' => now(),
            'total_biaya_layanan' => 10000,
            'total_biaya_prioritas' => 0,
            'total_biaya_antar' => 0,
            'total_biaya_layanan_tambahan' => 0,
            'total_biaya_penjemputan' => 0,
            'bayar' => 0,
            'sisa_bayar' => 0,
            'kembalian' => 0,
            'total_bayar_akhir' => 10000,
            'jenis_pembayaran' => 'cash',
            'layanan_prioritas_id' => $prio->id,
            'cabang_id' => $cabang->id,
            'pegawai_id' => $userId,
            'payment_status' => 'pending',
            'is_roundtrip' => false,
        ]);
    }

    public function test_a_profile()
    {
        $user = $this->createCustomerUser('a@mail.com');

        $resNoToken = $this->getJson('/api/v1/customer/profile');
        dump('--- A. Profil Pelanggan (Tanpa Token) ---', $resNoToken->status(), json_encode($resNoToken->json()));

        $resToken = $this->actingAs($user, 'sanctum')->getJson('/api/v1/customer/profile');
        dump('--- A. Profil Pelanggan (Dengan Token) ---', $resToken->status(), json_encode($resToken->json()));

        $this->assertTrue(true);
    }

    public function test_b_addresses()
    {
        $user = $this->createCustomerUser('b@mail.com');
        $pelangganId = Pelanggan::where('user_id', $user->id)->first()->id;

        $resEmpty = $this->actingAs($user, 'sanctum')->getJson('/api/v1/customer/addresses');
        dump('--- B. Daftar Alamat (Tanpa Alamat) ---', $resEmpty->status(), json_encode($resEmpty->json()));

        AlamatPelanggan::create([
            'pelanggan_id' => $pelangganId,
            'label' => 'Rumah',
            'address' => 'Jalan',
            'lat' => -6.123,
            'lng' => 106.123,
            'is_primary' => true
        ]);

        $resFilled = $this->actingAs($user, 'sanctum')->getJson('/api/v1/customer/addresses');
        dump('--- B. Daftar Alamat (Ada Alamat) ---', $resFilled->status(), json_encode($resFilled->json()));

        $this->assertTrue(true);
    }

    public function test_c_add_address()
    {
        $user = $this->createCustomerUser('c@mail.com');
        $pelangganId = Pelanggan::where('user_id', $user->id)->first()->id;

        AlamatPelanggan::create(['pelanggan_id' => $pelangganId, 'label' => 'A1', 'address' => 'A1', 'lat' => 1, 'lng' => 1, 'is_primary' => true]);
        AlamatPelanggan::create(['pelanggan_id' => $pelangganId, 'label' => 'A2', 'address' => 'A2', 'lat' => 1, 'lng' => 1, 'is_primary' => false]);

        $resValid = $this->actingAs($user, 'sanctum')->postJson('/api/v1/customer/addresses', [
            'label' => 'A3', 'address' => 'A3', 'lat' => 1, 'lng' => 1
        ]);
        dump('--- C. Tambah Alamat (Valid ke-3) ---', $resValid->status(), json_encode($resValid->json()));

        $resMax = $this->actingAs($user, 'sanctum')->postJson('/api/v1/customer/addresses', [
            'label' => 'A4', 'address' => 'A4', 'lat' => 1, 'lng' => 1
        ]);
        dump('--- C. Tambah Alamat (Batas Tercapai) ---', $resMax->status(), json_encode($resMax->json()));

        $resNoCoord = $this->actingAs($user, 'sanctum')->postJson('/api/v1/customer/addresses', [
            'label' => 'A5', 'address' => 'A5'
        ]);
        dump('--- C. Tambah Alamat (Koordinat Kosong) ---', $resNoCoord->status(), json_encode($resNoCoord->json()));

        $this->assertTrue(true);
    }

    public function test_d_primary_address()
    {
        $user = $this->createCustomerUser('d@mail.com');
        $user2 = $this->createCustomerUser('d2@mail.com');
        $p1 = Pelanggan::where('user_id', $user->id)->first()->id;
        $p2 = Pelanggan::where('user_id', $user2->id)->first()->id;

        $addrOwn = AlamatPelanggan::create(['pelanggan_id' => $p1, 'label' => 'A1', 'address' => 'A1', 'lat' => 1, 'lng' => 1, 'is_primary' => false]);
        $addrPrimary = AlamatPelanggan::create(['pelanggan_id' => $p1, 'label' => 'A2', 'address' => 'A2', 'lat' => 1, 'lng' => 1, 'is_primary' => true]);
        $addrOther = AlamatPelanggan::create(['pelanggan_id' => $p2, 'label' => 'A3', 'address' => 'A3', 'lat' => 1, 'lng' => 1, 'is_primary' => false]);

        $resOwn = $this->actingAs($user, 'sanctum')->postJson("/api/v1/customer/addresses/{$addrOwn->id}/primary");
        dump('--- D. Tetapkan Alamat Utama (Milik Sendiri) ---', $resOwn->status(), json_encode($resOwn->json()));

        $resOther = $this->actingAs($user, 'sanctum')->postJson("/api/v1/customer/addresses/{$addrOther->id}/primary");
        dump('--- D. Tetapkan Alamat Utama (Milik Orang Lain) ---', $resOther->status(), json_encode($resOther->json()));

        $resAlreadyPrimary = $this->actingAs($user, 'sanctum')->postJson("/api/v1/customer/addresses/{$addrPrimary->id}/primary");
        dump('--- D. Tetapkan Alamat Utama (Sudah Utama) ---', $resAlreadyPrimary->status(), json_encode($resAlreadyPrimary->json()));

        $this->assertTrue(true);
    }

    public function test_e_edit_address()
    {
        $user = $this->createCustomerUser('e@mail.com');
        $user2 = $this->createCustomerUser('e2@mail.com');
        $p1 = Pelanggan::where('user_id', $user->id)->first()->id;
        $p2 = Pelanggan::where('user_id', $user2->id)->first()->id;

        $addrOwn = AlamatPelanggan::create(['pelanggan_id' => $p1, 'label' => 'A1', 'address' => 'A1', 'lat' => 1, 'lng' => 1, 'is_primary' => false]);
        $addrOther = AlamatPelanggan::create(['pelanggan_id' => $p2, 'label' => 'A2', 'address' => 'A2', 'lat' => 1, 'lng' => 1, 'is_primary' => false]);

        $resOwn = $this->actingAs($user, 'sanctum')->putJson("/api/v1/customer/addresses/{$addrOwn->id}", ['label' => 'A1 Edit', 'address' => 'A1', 'lat' => 1, 'lng' => 1]);
        dump('--- E. Edit Alamat (Milik Sendiri) ---', $resOwn->status(), json_encode($resOwn->json()));

        $resOther = $this->actingAs($user, 'sanctum')->putJson("/api/v1/customer/addresses/{$addrOther->id}", ['label' => 'A2 Edit', 'address' => 'A2', 'lat' => 1, 'lng' => 1]);
        dump('--- E. Edit Alamat (Milik Orang Lain) ---', $resOther->status(), json_encode($resOther->json()));

        $resNotExist = $this->actingAs($user, 'sanctum')->putJson("/api/v1/customer/addresses/99999", ['label' => 'A', 'address' => 'A', 'lat' => 1, 'lng' => 1]);
        dump('--- E. Edit Alamat (ID Tidak Ada) ---', $resNotExist->status(), json_encode($resNotExist->json()));

        $this->assertTrue(true);
    }

    public function test_f_delete_address()
    {
        $user = $this->createCustomerUser('f@mail.com');
        $user2 = $this->createCustomerUser('f2@mail.com');
        $p1 = Pelanggan::where('user_id', $user->id)->first()->id;
        $p2 = Pelanggan::where('user_id', $user2->id)->first()->id;

        $addrOwn = AlamatPelanggan::create(['pelanggan_id' => $p1, 'label' => 'A1', 'address' => 'A1', 'lat' => 1, 'lng' => 1, 'is_primary' => false]);
        $addrPrimary = AlamatPelanggan::create(['pelanggan_id' => $p1, 'label' => 'A2', 'address' => 'A2', 'lat' => 1, 'lng' => 1, 'is_primary' => true]);
        $addrOther = AlamatPelanggan::create(['pelanggan_id' => $p2, 'label' => 'A3', 'address' => 'A3', 'lat' => 1, 'lng' => 1, 'is_primary' => false]);

        $resOwn = $this->actingAs($user, 'sanctum')->deleteJson("/api/v1/customer/addresses/{$addrOwn->id}");
        dump('--- F. Hapus Alamat (Biasa) ---', $resOwn->status(), json_encode($resOwn->json()));

        $resPrimary = $this->actingAs($user, 'sanctum')->deleteJson("/api/v1/customer/addresses/{$addrPrimary->id}");
        dump('--- F. Hapus Alamat (Utama) ---', $resPrimary->status(), json_encode($resPrimary->json()));

        $resOther = $this->actingAs($user, 'sanctum')->deleteJson("/api/v1/customer/addresses/{$addrOther->id}");
        dump('--- F. Hapus Alamat (Milik Orang Lain) ---', $resOther->status(), json_encode($resOther->json()));

        $this->assertTrue(true);
    }

    public function test_g_create_order()
    {
        $user = $this->createCustomerUser('g@mail.com');
        $p1 = Pelanggan::where('user_id', $user->id)->first()->id;
        $cabang = Cabang::firstOrCreate(['nama' => 'C1'], ['lokasi' => 'L1', 'deskripsi' => 'D1']);
        $prio = LayananPrioritas::firstOrCreate(['nama' => 'P1', 'cabang_id' => $cabang->id], ['harga' => 0, 'prioritas' => 1]);

        $resLengkap = $this->actingAs($user, 'sanctum')->postJson('/api/v1/orders', [
            'pelanggan_id' => $p1,
            'pickup_address' => 'Jalan A',
            'pickup_date' => '2026-10-10',
            'pickup_time' => '10:00',
            'payment_method' => 'cash',
            'cabang_id' => $cabang->id,
            'layanan_prioritas_id' => $prio->id,
            'estimated_total' => 50000,
        ]);
        dump('--- G. Buat Pesanan (Lengkap) ---', $resLengkap->status(), json_encode($resLengkap->json()));

        $resEmpty = $this->actingAs($user, 'sanctum')->postJson('/api/v1/orders', []);
        dump('--- G. Buat Pesanan (Field Wajib Kosong) ---', $resEmpty->status(), json_encode($resEmpty->json()));

        $resInvalidCabang = $this->actingAs($user, 'sanctum')->postJson('/api/v1/orders', [
            'pelanggan_id' => $p1,
            'pickup_address' => 'Jalan A',
            'pickup_date' => '2026-10-10',
            'pickup_time' => '10:00',
            'payment_method' => 'cash',
            'cabang_id' => 9999,
            'layanan_prioritas_id' => $prio->id,
            'estimated_total' => 50000,
        ]);
        dump('--- G. Buat Pesanan (Cabang Tidak Valid) ---', $resInvalidCabang->status(), json_encode($resInvalidCabang->json()));

        $this->assertTrue(true);
    }

    public function test_h_order_history()
    {
        $user1 = $this->createCustomerUser('h1@mail.com');
        $user2 = $this->createCustomerUser('h2@mail.com');
        $p1 = Pelanggan::where('user_id', $user1->id)->first()->id;

        $this->createOrder($p1, $user1->id, 'ZYG-1');
        $this->createOrder($p1, $user1->id, 'ZYG-2');

        $resMany = $this->actingAs($user1, 'sanctum')->getJson('/api/v1/orders/history');
        dump('--- H. Riwayat Pesanan (Ada Pesanan) ---', $resMany->status(), json_encode($resMany->json()));

        $resNone = $this->actingAs($user2, 'sanctum')->getJson('/api/v1/orders/history');
        dump('--- H. Riwayat Pesanan (Tidak Ada Pesanan) ---', $resNone->status(), json_encode($resNone->json()));

        $this->assertTrue(true);
    }

    public function test_i_track_order()
    {
        $user = $this->createCustomerUser('i@mail.com');
        $p1 = Pelanggan::where('user_id', $user->id)->first();
        $this->createOrder($p1->id, $user->id, 'ZYG-TRK');
        
        $phone_last_4 = substr($p1->telepon, -4);

        $resValid = $this->postJson('/api/v1/orders/track', ['query' => 'ZYG-TRK', 'phone_last_4' => $phone_last_4]);
        dump('--- I. Cek Nota Non-Login (Valid) ---', $resValid->status(), json_encode($resValid->json()));

        $resWrongPhone = $this->postJson('/api/v1/orders/track', ['query' => 'ZYG-TRK', 'phone_last_4' => '0000']);
        dump('--- I. Cek Nota Non-Login (Telepon Salah) ---', $resWrongPhone->status(), json_encode($resWrongPhone->json()));

        $resNoNota = $this->postJson('/api/v1/orders/track', ['query' => 'ZYG-NOTFOUND', 'phone_last_4' => $phone_last_4]);
        dump('--- I. Cek Nota Non-Login (Nota Tidak Ada) ---', $resNoNota->status(), json_encode($resNoNota->json()));

        $this->assertTrue(true);
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
        dump('--- J. Ajukan Layanan Antar (Alamat Valid) ---', $resValid->status(), json_encode($resValid->json()));

        $resNoCoord = $this->actingAs($user, 'sanctum')->postJson("/api/v1/orders/{$order->id}/delivery-request", [
            'address' => 'Jalan Kirim'
        ]);
        dump('--- J. Ajukan Layanan Antar (Koordinat Kosong) ---', $resNoCoord->status(), json_encode($resNoCoord->json()));

        $order->update(['status' => 'delivered']); // Assume it's handled or something
        $resAlready = $this->actingAs($user, 'sanctum')->postJson("/api/v1/orders/{$order->id}/delivery-request", [
            'address' => 'Jalan Kirim',
            'lat' => -6.1,
            'lng' => 106.1
        ]);
        dump('--- J. Ajukan Layanan Antar (Sudah Ada Delivery) ---', $resAlready->status(), json_encode($resAlready->json()));

        $this->assertTrue(true);
    }

    public function test_k_payment_methods()
    {
        $user = $this->createCustomerUser('k@mail.com');

        $res = $this->actingAs($user, 'sanctum')->getJson('/api/v1/payment/methods');
        dump('--- K. Daftar Metode Pembayaran (Valid Token) ---', $res->status(), json_encode($res->json()));

        $this->assertTrue(true);
    }

    public function test_l_midtrans_webhook()
    {
        $resSettlement = $this->postJson('/api/v1/payment/notification', [
            'order_id' => 'ZYG-PAY-1',
            'transaction_status' => 'settlement',
            'gross_amount' => '10000.00',
            'signature_key' => 'fake_signature'
        ]);
        dump('--- L. Midtrans Webhook (Settlement) ---', $resSettlement->status(), json_encode($resSettlement->json()));

        $resExpire = $this->postJson('/api/v1/payment/notification', [
            'order_id' => 'ZYG-PAY-2',
            'transaction_status' => 'expire',
            'gross_amount' => '10000.00',
            'signature_key' => 'fake_signature'
        ]);
        dump('--- L. Midtrans Webhook (Expire) ---', $resExpire->status(), json_encode($resExpire->json()));

        $resInvalid = $this->postJson('/api/v1/payment/notification', [
            'order_id' => 'ZYG-PAY-3',
            'transaction_status' => 'settlement' // no signature
        ]);
        dump('--- L. Midtrans Webhook (Signature Invalid) ---', $resInvalid->status(), json_encode($resInvalid->json()));

        $this->assertTrue(true);
    }

    public function test_m_payment_status()
    {
        $user1 = $this->createCustomerUser('m1@mail.com');
        $user2 = $this->createCustomerUser('m2@mail.com');
        $p1 = Pelanggan::where('user_id', $user1->id)->first()->id;
        
        $order = $this->createOrder($p1, $user1->id, 'ZYG-PAYSTAT');

        $resOwn = $this->actingAs($user1, 'sanctum')->getJson("/api/v1/orders/{$order->id}/payment-status");
        dump('--- M. Cek Status Pembayaran (Milik Sendiri) ---', $resOwn->status(), json_encode($resOwn->json()));

        $resOther = $this->actingAs($user2, 'sanctum')->getJson("/api/v1/orders/{$order->id}/payment-status");
        dump('--- M. Cek Status Pembayaran (Milik Orang Lain) ---', $resOther->status(), json_encode($resOther->json()));

        $resNotFound = $this->actingAs($user1, 'sanctum')->getJson("/api/v1/orders/99999999-9999-9999-9999-999999999999/payment-status");
        dump('--- M. Cek Status Pembayaran (Order Tidak Ada) ---', $resNotFound->status(), json_encode($resNotFound->json()));

        $this->assertTrue(true);
    }

    public function test_n_notifications()
    {
        $user1 = $this->createCustomerUser('n1@mail.com');
        $user2 = $this->createCustomerUser('n2@mail.com');
        $p1 = Pelanggan::where('user_id', $user1->id)->first()->id;

        Notifikasi::create(['pelanggan_id' => $p1, 'judul' => 'N1', 'pesan' => 'M1', 'jenis' => 'personal']);

        $resMany = $this->actingAs($user1, 'sanctum')->getJson('/api/v1/customer/notifications');
        dump('--- N. Daftar Notifikasi (Ada Notifikasi) ---', $resMany->status(), json_encode($resMany->json()));

        $resNone = $this->actingAs($user2, 'sanctum')->getJson('/api/v1/customer/notifications');
        dump('--- N. Daftar Notifikasi (Tanpa Notifikasi) ---', $resNone->status(), json_encode($resNone->json()));

        $this->assertTrue(true);
    }

    public function test_o_read_notification()
    {
        $user1 = $this->createCustomerUser('o1@mail.com');
        $user2 = $this->createCustomerUser('o2@mail.com');
        $p1 = Pelanggan::where('user_id', $user1->id)->first()->id;

        $notif = Notifikasi::create(['pelanggan_id' => $p1, 'judul' => 'N1', 'pesan' => 'M1', 'jenis' => 'personal']);

        $resOwn = $this->actingAs($user1, 'sanctum')->postJson("/api/v1/customer/notifications/{$notif->id}/read");
        dump('--- O. Status Baca Notifikasi (Milik Sendiri) ---', $resOwn->status(), json_encode($resOwn->json()));

        $resOther = $this->actingAs($user2, 'sanctum')->postJson("/api/v1/customer/notifications/{$notif->id}/read");
        dump('--- O. Status Baca Notifikasi (Milik Orang Lain) ---', $resOther->status(), json_encode($resOther->json()));

        $resNotFound = $this->actingAs($user1, 'sanctum')->postJson("/api/v1/customer/notifications/99999/read");
        dump('--- O. Status Baca Notifikasi (Tidak Ada) ---', $resNotFound->status(), json_encode($resNotFound->json()));

        $this->assertTrue(true);
    }

    public function test_p_upgrade_service()
    {
        $user = $this->createCustomerUser('p@mail.com');
        $p1 = Pelanggan::where('user_id', $user->id)->first()->id;
        $order = $this->createOrder($p1, $user->id, 'ZYG-UPG');
        $orderDone = $this->createOrder($p1, $user->id, 'ZYG-UPGDONE');
        $orderDone->update(['status' => 'completed']);

        $cabang = Cabang::first();
        $prioNew = LayananPrioritas::create(['nama' => 'Kilat', 'cabang_id' => $cabang->id, 'harga' => 10000, 'prioritas' => 2]);

        $resValid = $this->actingAs($user, 'sanctum')->postJson("/api/v1/orders/{$order->id}/upgrade", ['new_service_id' => $prioNew->id]);
        dump('--- P. Upgrade Layanan (Valid) ---', $resValid->status(), json_encode($resValid->json()));

        $resEmpty = $this->actingAs($user, 'sanctum')->postJson("/api/v1/orders/{$order->id}/upgrade", []);
        dump('--- P. Upgrade Layanan (Kosong) ---', $resEmpty->status(), json_encode($resEmpty->json()));

        $resNotExist = $this->actingAs($user, 'sanctum')->postJson("/api/v1/orders/{$order->id}/upgrade", ['new_service_id' => 9999]);
        dump('--- P. Upgrade Layanan (Tidak Ada di DB) ---', $resNotExist->status(), json_encode($resNotExist->json()));

        $resDone = $this->actingAs($user, 'sanctum')->postJson("/api/v1/orders/{$orderDone->id}/upgrade", ['new_service_id' => $prioNew->id]);
        dump('--- P. Upgrade Layanan (Order Selesai) ---', $resDone->status(), json_encode($resDone->json()));

        $this->assertTrue(true);
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
        dump('--- Q. Ajukan Komplain (Dengan Foto JPG) ---', $resImg->status(), json_encode($resImg->json()));

        $resNoPhoto = $this->actingAs($user, 'sanctum')->post("/api/v1/orders/{$order->id}/complaint", [
            'content' => 'Rusak', 'issue_types' => ['lainnya']
        ], ['Accept' => 'application/json']);
        dump('--- Q. Ajukan Komplain (Tanpa Foto) ---', $resNoPhoto->status(), json_encode($resNoPhoto->json()));

        $resPdf = $this->actingAs($user, 'sanctum')->post("/api/v1/orders/{$order->id}/complaint", [
            'content' => 'Rusak', 'issue_types' => ['lainnya'], 'issue_image' => $filePdf
        ], ['Accept' => 'application/json']);
        dump('--- Q. Ajukan Komplain (Foto PDF) ---', $resPdf->status(), json_encode($resPdf->json()));

        $resEmptyDesc = $this->actingAs($user, 'sanctum')->post("/api/v1/orders/{$order->id}/complaint", [
            'issue_types' => ['lainnya'], 'issue_image' => $fileImg
        ], ['Accept' => 'application/json']);
        dump('--- Q. Ajukan Komplain (Deskripsi Kosong) ---', $resEmptyDesc->status(), json_encode($resEmptyDesc->json()));

        $this->assertTrue(true);
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
        dump('--- R. Riwayat Komplain (Ada Komplain) ---', $resMany->status(), json_encode($resMany->json()));

        $resNone = $this->actingAs($user2, 'sanctum')->getJson('/api/v1/customer/complaints');
        dump('--- R. Riwayat Komplain (Tanpa Komplain) ---', $resNone->status(), json_encode($resNone->json()));

        $this->assertTrue(true);
    }
}
