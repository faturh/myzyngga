<?php

namespace Tests\Feature\Pelanggan;

use App\Models\Cabang;
use App\Models\Complaint;
use App\Models\CustomerAddress;
use App\Models\LayananPrioritas;
use App\Models\Notifikasi;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BlackBoxApiTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    }

    private function createCustomerUser(string $email = 'blackbox_tester@domain.com'): array
    {
        $user = User::factory()->create([
            'email' => $email,
            'password' => bcrypt('password123'),
            'role' => 'customer',
        ]);
        $user->assignRole('customer');

        $pelanggan = Pelanggan::create([
            'user_id' => $user->id,
            'nama' => 'Black Box Tester',
            'telepon' => '0812' . rand(1000, 9999) . rand(1000, 9999),
            'alamat' => 'Jalan Tester No. 1',
            'jenis_kelamin' => 'L',
        ]);

        return [$user, $pelanggan];
    }

    private function createOrderReferences(): array
    {
        $admin = User::factory()->create([
            'username' => 'admin-bb-' . Str::random(6),
            'email' => 'admin-bb-' . Str::random(6) . '@domain.com',
            'role' => 'admin',
        ]);
        $admin->assignRole('admin');

        $cabang = Cabang::create([
            'nama' => 'Cabang Pusat ' . Str::random(4),
            'alamat' => 'Jalan Utama No. 1',
            'lokasi' => 'Jakarta',
        ]);

        $layananPrioritas = LayananPrioritas::create([
            'nama' => 'Reguler',
            'harga' => 0,
            'prioritas' => 1,
            'cabang_id' => $cabang->id,
        ]);

        return [$cabang, $layananPrioritas, $admin];
    }

    private function createOrder(Pelanggan $pelanggan, Cabang $cabang, LayananPrioritas $lp, User $admin, string $nota = 'ZYG-BB-TEST'): Transaksi
    {
        return Transaksi::create([
            'id' => (string) Str::uuid(),
            'nota' => $nota,
            'status' => 'created',
            'pelanggan_id' => $pelanggan->id,
            'waktu' => now(),
            'total_biaya_layanan' => 10000,
            'total_biaya_prioritas' => 0,
            'total_biaya_layanan_tambahan' => 0,
            'bayar' => 0,
            'kembalian' => 0,
            'total_bayar_akhir' => 10000,
            'jenis_pembayaran' => 'cash',
            'layanan_prioritas_id' => $lp->id,
            'cabang_id' => $cabang->id,
            'pegawai_id' => (string) $admin->id,
            'payment_status' => 'pending',
            'is_roundtrip' => false,
            'pickup_address' => 'Jalan Alamat',
            'pickup_date' => now()->toDateString(),
            'pickup_time' => '10:00',
        ]);
    }

    // ── A. Lihat Profil (2 Skenario) ─────────────────────────────────────────

    public function test_a1_lihat_profil_tanpa_token(): void
    {
        $response = $this->getJson('/api/v1/customer/profile');
        $response->assertStatus(401);
    }

    public function test_a2_lihat_profil_dengan_token_valid(): void
    {
        [$user] = $this->createCustomerUser('user_a2@domain.com');
        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/customer/profile');
        $response->assertStatus(200);
    }

    // ── B. Lihat Daftar Alamat (2 Skenario) ──────────────────────────────────

    public function test_b1_lihat_daftar_alamat_pelanggan_0_alamat(): void
    {
        [$user] = $this->createCustomerUser('user_b1@domain.com');
        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/customer/addresses');
        $response->assertStatus(200)->assertJsonPath('data.addresses', []);
    }

    public function test_b2_lihat_daftar_alamat_pelanggan_punya_alamat(): void
    {
        [$user, $pelanggan] = $this->createCustomerUser('user_b2@domain.com');
        CustomerAddress::create(['pelanggan_id' => $pelanggan->id, 'label' => 'Rumah', 'address' => 'Jl. Merdeka No. 1']);
        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/customer/addresses');
        $response->assertStatus(200);
        $this->assertNotEmpty($response->json('data.addresses'));
    }

    // ── C. Tambah Alamat (3 Skenario) ────────────────────────────────────────

    public function test_c1_tambah_alamat_ke_3_valid(): void
    {
        [$user, $pelanggan] = $this->createCustomerUser('user_c1@domain.com');
        CustomerAddress::create(['pelanggan_id' => $pelanggan->id, 'label' => 'A1', 'address' => 'Jl 1']);
        CustomerAddress::create(['pelanggan_id' => $pelanggan->id, 'label' => 'A2', 'address' => 'Jl 2']);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/customer/addresses', [
            'label' => 'A3',
            'address' => 'Jl 3',
            'lat' => -6.2,
            'lng' => 106.8,
        ]);
        $response->assertStatus(201);
    }

    public function test_c2_tambah_alamat_ke_4_melebihi_batas(): void
    {
        [$user, $pelanggan] = $this->createCustomerUser('user_c2@domain.com');
        for ($i = 1; $i <= 3; $i++) {
            CustomerAddress::create(['pelanggan_id' => $pelanggan->id, 'label' => "A$i", 'address' => "Jl $i"]);
        }

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/customer/addresses', [
            'label' => 'A4',
            'address' => 'Jl 4',
            'lat' => -6.2,
            'lng' => 106.8,
        ]);
        $response->assertStatus(422);
    }

    public function test_c3_tambah_alamat_koordinat_kosong(): void
    {
        [$user] = $this->createCustomerUser('user_c3@domain.com');
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/customer/addresses', [
            'label' => 'A1',
            'address' => 'Jl 1',
            'lat' => null,
            'lng' => null,
        ]);
        $response->assertStatus(422);
    }

    // ── D. Tetapkan Alamat Utama (2 Skenario) ────────────────────────────────

    public function test_d1_tetapkan_alamat_utama_milik_sendiri(): void
    {
        [$user, $pelanggan] = $this->createCustomerUser('user_d1@domain.com');
        $addr = CustomerAddress::create(['pelanggan_id' => $pelanggan->id, 'label' => 'A1', 'address' => 'Jl 1']);

        $response = $this->actingAs($user, 'sanctum')->postJson("/api/v1/customer/addresses/{$addr->id}/primary");
        $response->assertStatus(200);
    }

    public function test_d2_tetapkan_alamat_utama_milik_orang_lain(): void
    {
        [$userA] = $this->createCustomerUser('user_d2a@domain.com');
        [$userB, $pelangganB] = $this->createCustomerUser('user_d2b@domain.com');
        $addrB = CustomerAddress::create(['pelanggan_id' => $pelangganB->id, 'label' => 'B1', 'address' => 'Jl B']);

        $response = $this->actingAs($userA, 'sanctum')->postJson("/api/v1/customer/addresses/{$addrB->id}/primary");
        $response->assertStatus(403);
    }

    // ── E. Edit Alamat (2 Skenario) ──────────────────────────────────────────

    public function test_e1_edit_alamat_data_valid_milik_sendiri(): void
    {
        [$user, $pelanggan] = $this->createCustomerUser('user_e1@domain.com');
        $addr = CustomerAddress::create(['pelanggan_id' => $pelanggan->id, 'label' => 'Old', 'address' => 'Old Address']);

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/v1/customer/addresses/{$addr->id}", [
            'label' => 'New Label',
            'address' => 'New Address',
            'lat' => -6.2,
            'lng' => 106.8,
        ]);
        $response->assertStatus(200);
    }

    public function test_e2_edit_alamat_id_tidak_ditemukan(): void
    {
        [$user] = $this->createCustomerUser('user_e2@domain.com');
        $response = $this->actingAs($user, 'sanctum')->putJson('/api/v1/customer/addresses/99999', [
            'label' => 'New',
            'address' => 'New',
            'lat' => -6.2,
            'lng' => 106.8,
        ]);
        $response->assertStatus(403);
    }

    // ── F. Hapus Alamat (3 Skenario) ─────────────────────────────────────────

    public function test_f1_hapus_alamat_biasa(): void
    {
        [$user, $pelanggan] = $this->createCustomerUser('user_f1@domain.com');
        $addr1 = CustomerAddress::create(['pelanggan_id' => $pelanggan->id, 'label' => 'Utama', 'address' => 'Jl 1', 'is_default' => true]);
        $addr2 = CustomerAddress::create(['pelanggan_id' => $pelanggan->id, 'label' => 'Biasa', 'address' => 'Jl 2', 'is_default' => false]);

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/v1/customer/addresses/{$addr2->id}");
        $response->assertStatus(200);
    }

    public function test_f2_hapus_alamat_utama(): void
    {
        [$user, $pelanggan] = $this->createCustomerUser('user_f2@domain.com');
        $addr = CustomerAddress::create(['pelanggan_id' => $pelanggan->id, 'label' => 'Utama', 'address' => 'Jl Utama', 'is_default' => true]);

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/v1/customer/addresses/{$addr->id}");
        $response->assertStatus(422);
    }

    public function test_f3_hapus_alamat_milik_orang_lain(): void
    {
        [$userA] = $this->createCustomerUser('user_f3a@domain.com');
        [$userB, $pelangganB] = $this->createCustomerUser('user_f3b@domain.com');
        $addrB = CustomerAddress::create(['pelanggan_id' => $pelangganB->id, 'label' => 'B', 'address' => 'Jl B', 'is_default' => false]);

        $response = $this->actingAs($userA, 'sanctum')->deleteJson("/api/v1/customer/addresses/{$addrB->id}");
        $response->assertStatus(403);
    }

    // ── G. Buat Pesanan Baru (2 Skenario) ────────────────────────────────────

    public function test_g1_buat_pesanan_baru_input_lengkap(): void
    {
        [$user, $pelanggan] = $this->createCustomerUser('user_g1@domain.com');
        $cabang = Cabang::create(['nama' => 'Cabang G1', 'lokasi' => 'Pusat', 'alamat' => 'Pusat']);
        $lp = LayananPrioritas::create(['nama' => 'Reguler', 'cabang_id' => $cabang->id, 'harga' => 0, 'prioritas' => 1]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/orders', [
            'pelanggan_id' => $pelanggan->id,
            'cabang_id' => $cabang->id,
            'layanan_prioritas_id' => $lp->id,
            'pickup_address' => 'Jalan Lengkap',
            'pickup_date' => now()->toDateString(),
            'pickup_time' => '10:00',
            'payment_method' => 'cash',
            'estimated_total' => 50000,
        ]);
        $response->assertStatus(201);
    }

    public function test_g2_buat_pesanan_baru_payload_kosong(): void
    {
        [$user] = $this->createCustomerUser('user_g2@domain.com');
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/orders', []);
        $response->assertStatus(422);
    }

    // ── H. Riwayat Pesanan (2 Skenario) ──────────────────────────────────────

    public function test_h1_riwayat_pesanan_ada_riwayat(): void
    {
        [$user, $pelanggan] = $this->createCustomerUser('user_h1@domain.com');
        [$cabang, $lp, $admin] = $this->createOrderReferences();
        $this->createOrder($pelanggan, $cabang, $lp, $admin, 'ZYG-H1');

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/orders/history');
        $response->assertStatus(200);
        $this->assertNotEmpty($response->json('data.orders'));
    }

    public function test_h2_riwayat_pesanan_tanpa_riwayat(): void
    {
        [$user] = $this->createCustomerUser('user_h2@domain.com');
        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/orders/history');
        $response->assertStatus(200);
        $this->assertEmpty($response->json('data.orders'));
    }

    // ── I. Cek Nota Non-Login (3 Skenario) ───────────────────────────────────

    public function test_i1_cek_nota_non_login_nota_telepon_cocok(): void
    {
        [$user, $pelanggan] = $this->createCustomerUser('user_i1@domain.com');
        $pelanggan->update(['telepon' => '081299998888']);
        [$cabang, $lp, $admin] = $this->createOrderReferences();
        $this->createOrder($pelanggan, $cabang, $lp, $admin, 'ZYG-I1-MATCH');

        $response = $this->postJson('/api/v1/orders/track', [
            'query' => 'ZYG-I1-MATCH',
            'phone_last_4' => '8888',
        ]);
        $response->assertStatus(200)->assertJsonPath('success', true);
    }

    public function test_i2_cek_nota_non_login_telepon_salah(): void
    {
        [$user, $pelanggan] = $this->createCustomerUser('user_i2@domain.com');
        [$cabang, $lp, $admin] = $this->createOrderReferences();
        $this->createOrder($pelanggan, $cabang, $lp, $admin, 'ZYG-I2-FAIL');

        $response = $this->postJson('/api/v1/orders/track', [
            'query' => 'ZYG-I2-FAIL',
            'phone_last_4' => '0000',
        ]);
        $response->assertStatus(404);
    }

    public function test_i3_cek_nota_non_login_nota_tidak_ada(): void
    {
        $response = $this->postJson('/api/v1/orders/track', [
            'query' => 'ZYG-NON-EXISTENT-999',
            'phone_last_4' => '8888',
        ]);
        $response->assertStatus(404);
    }

    // ── J. Ajukan Layanan Antar (2 Skenario) ─────────────────────────────────

    public function test_j1_ajukan_layanan_antar_input_lengkap(): void
    {
        [$user, $pelanggan] = $this->createCustomerUser('user_j1@domain.com');
        [$cabang, $lp, $admin] = $this->createOrderReferences();
        $order = $this->createOrder($pelanggan, $cabang, $lp, $admin, 'ZYG-J1');

        $response = $this->actingAs($user, 'sanctum')->postJson("/api/v1/orders/{$order->id}/delivery-request", [
            'address' => 'Jalan Antar Baru',
            'detail_address' => 'Detail Pagar',
            'lat' => -6.2,
            'lng' => 106.8,
        ]);
        $response->assertStatus(200)->assertJsonPath('success', true);
    }

    public function test_j2_ajukan_layanan_antar_lat_kosong(): void
    {
        [$user, $pelanggan] = $this->createCustomerUser('user_j2@domain.com');
        [$cabang, $lp, $admin] = $this->createOrderReferences();
        $order = $this->createOrder($pelanggan, $cabang, $lp, $admin, 'ZYG-J2');

        $response = $this->actingAs($user, 'sanctum')->postJson("/api/v1/orders/{$order->id}/delivery-request", [
            'address' => 'Jalan Antar Baru',
            'lat' => null,
        ]);
        $response->assertStatus(400);
    }

    // ── K. Daftar Metode Pembayaran (1 Skenario) ─────────────────────────────

    public function test_k1_daftar_metode_pembayaran_token_valid(): void
    {
        [$user] = $this->createCustomerUser('user_k1@domain.com');
        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/payment/methods');
        $response->assertStatus(200);
    }

    // ── L. Notifikasi Pembayaran/Webhook (3 Skenario) ────────────────────────

    public function test_l1_notifikasi_pembayaran_settlement(): void
    {
        [$user, $pelanggan] = $this->createCustomerUser('user_l1@domain.com');
        [$cabang, $lp, $admin] = $this->createOrderReferences();
        $order = $this->createOrder($pelanggan, $cabang, $lp, $admin, 'ZYG-L1');

        $response = $this->postJson('/api/v1/payment/notification', [
            'order_id' => "{$order->id}-12345",
            'transaction_status' => 'settlement',
            'gross_amount' => '10000.00',
        ]);
        $response->assertStatus(200);
    }

    public function test_l2_notifikasi_pembayaran_expire(): void
    {
        [$user, $pelanggan] = $this->createCustomerUser('user_l2@domain.com');
        [$cabang, $lp, $admin] = $this->createOrderReferences();
        $order = $this->createOrder($pelanggan, $cabang, $lp, $admin, 'ZYG-L2');

        $response = $this->postJson('/api/v1/payment/notification', [
            'order_id' => "{$order->id}-12345",
            'transaction_status' => 'expire',
            'gross_amount' => '10000.00',
        ]);
        $response->assertStatus(200);
    }

    public function test_l3_notifikasi_pembayaran_order_id_tidak_ditemukan(): void
    {
        $response = $this->postJson('/api/v1/payment/notification', [
            'order_id' => 'non-existent-uuid-12345',
            'transaction_status' => 'settlement',
            'gross_amount' => '10000.00',
        ]);
        $response->assertStatus(200);
    }

    // ── M. Cek Status Pembayaran (2 Skenario) ────────────────────────────────

    public function test_m1_cek_status_pembayaran_milik_sendiri(): void
    {
        [$user, $pelanggan] = $this->createCustomerUser('user_m1@domain.com');
        [$cabang, $lp, $admin] = $this->createOrderReferences();
        $order = $this->createOrder($pelanggan, $cabang, $lp, $admin, 'ZYG-M1');

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/v1/orders/{$order->id}/payment-status");
        $response->assertStatus(200);
    }

    public function test_m2_cek_status_pembayaran_tidak_ada(): void
    {
        [$user] = $this->createCustomerUser('user_m2@domain.com');
        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/orders/99999999-9999-9999-9999-999999999999/payment-status');
        $response->assertStatus(404);
    }

    // ── N. Lihat Daftar Notifikasi (2 Skenario) ──────────────────────────────

    public function test_n1_lihat_daftar_notifikasi_ada_notifikasi(): void
    {
        [$user, $pelanggan] = $this->createCustomerUser('user_n1@domain.com');
        Notifikasi::create(['pelanggan_id' => $pelanggan->id, 'jenis' => 'status', 'pesan' => 'Notif N1', 'is_read' => false]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/customer/notifications');
        $response->assertStatus(200);
        $this->assertNotEmpty($response->json('data.notifications'));
    }

    public function test_n2_lihat_daftar_notifikasi_tanpa_notifikasi(): void
    {
        [$user] = $this->createCustomerUser('user_n2@domain.com');
        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/customer/notifications');
        $response->assertStatus(200);
        $this->assertEmpty($response->json('data.notifications'));
    }

    // ── O. Perbarui Status Baca Notifikasi (2 Skenario SAJA) ─────────────────

    public function test_o1_perbarui_status_baca_notifikasi_milik_sendiri(): void
    {
        [$user, $pelanggan] = $this->createCustomerUser('user_o1@domain.com');
        $notif = Notifikasi::create(['pelanggan_id' => $pelanggan->id, 'jenis' => 'status', 'pesan' => 'Notif O1', 'is_read' => false]);

        $response = $this->actingAs($user, 'sanctum')->postJson("/api/v1/customer/notifications/{$notif->id}/read");
        $response->assertStatus(200);
    }

    public function test_o2_perbarui_status_baca_notifikasi_id_tidak_valid(): void
    {
        [$user] = $this->createCustomerUser('user_o2@domain.com');
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/customer/notifications/9999/read');
        $response->assertStatus(404);
    }

    // ── P. Ajukan Upgrade Layanan (3 Skenario) ───────────────────────────────

    public function test_p1_ajukan_upgrade_layanan_valid(): void
    {
        [$user, $pelanggan] = $this->createCustomerUser('user_p1@domain.com');
        [$cabang, $lpReguler, $admin] = $this->createOrderReferences();
        $lpExpress = LayananPrioritas::create(['nama' => 'Express P1', 'harga' => 15000, 'prioritas' => 3, 'cabang_id' => $cabang->id]);
        $order = $this->createOrder($pelanggan, $cabang, $lpReguler, $admin, 'ZYG-P1');

        $response = $this->actingAs($user, 'sanctum')->postJson("/api/v1/orders/{$order->id}/upgrade", [
            'new_service_id' => $lpExpress->id,
            'payment_method' => 'qris',
        ]);
        $response->assertStatus(200)->assertJsonPath('success', true);
    }

    public function test_p2_ajukan_upgrade_layanan_new_service_id_kosong(): void
    {
        [$user, $pelanggan] = $this->createCustomerUser('user_p2@domain.com');
        [$cabang, $lp, $admin] = $this->createOrderReferences();
        $order = $this->createOrder($pelanggan, $cabang, $lp, $admin, 'ZYG-P2');

        $response = $this->actingAs($user, 'sanctum')->postJson("/api/v1/orders/{$order->id}/upgrade", [
            'payment_method' => 'qris',
        ]);
        $response->assertStatus(422);
    }

    public function test_p3_ajukan_upgrade_layanan_tidak_terdaftar(): void
    {
        [$user, $pelanggan] = $this->createCustomerUser('user_p3@domain.com');
        [$cabang, $lp, $admin] = $this->createOrderReferences();
        $order = $this->createOrder($pelanggan, $cabang, $lp, $admin, 'ZYG-P3');

        $response = $this->actingAs($user, 'sanctum')->postJson("/api/v1/orders/{$order->id}/upgrade", [
            'new_service_id' => 99999,
            'payment_method' => 'qris',
        ]);
        $response->assertStatus(400);
    }

    // ── Q. Ajukan Komplain (3 Skenario) ──────────────────────────────────────

    public function test_q1_ajukan_komplain_dengan_foto_jpg(): void
    {
        [$user, $pelanggan] = $this->createCustomerUser('user_q1@domain.com');
        [$cabang, $lp, $admin] = $this->createOrderReferences();
        $order = $this->createOrder($pelanggan, $cabang, $lp, $admin, 'ZYG-Q1');
        Storage::fake('cloudinary');
        $file = UploadedFile::fake()->image('bukti.jpg');

        $response = $this->actingAs($user, 'sanctum')->post("/api/v1/orders/{$order->id}/complaint", [
            'content' => 'Baju robek ada foto',
            'issue_types' => ['pakaian_rusak'],
            'issue_image' => [$file],
        ], ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJsonPath('success', true);
    }

    public function test_q2_ajukan_komplain_tanpa_foto(): void
    {
        [$user, $pelanggan] = $this->createCustomerUser('user_q2@domain.com');
        [$cabang, $lp, $admin] = $this->createOrderReferences();
        $order = $this->createOrder($pelanggan, $cabang, $lp, $admin, 'ZYG-Q2');

        $response = $this->actingAs($user, 'sanctum')->postJson("/api/v1/orders/{$order->id}/complaint", [
            'content' => 'Baju robek tanpa foto',
            'issue_types' => ['pakaian_rusak'],
        ]);

        $response->assertStatus(200)->assertJsonPath('success', true);
    }

    public function test_q3_ajukan_komplain_foto_pdf_ditolak(): void
    {
        [$user, $pelanggan] = $this->createCustomerUser('user_q3@domain.com');
        [$cabang, $lp, $admin] = $this->createOrderReferences();
        $order = $this->createOrder($pelanggan, $cabang, $lp, $admin, 'ZYG-Q3');
        Storage::fake('cloudinary');
        $filePdf = UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf');

        $response = $this->actingAs($user, 'sanctum')->post("/api/v1/orders/{$order->id}/complaint", [
            'content' => 'File pdf ditolak',
            'issue_types' => ['pakaian_rusak'],
            'issue_image' => [$filePdf],
        ], ['Accept' => 'application/json']);

        $response->assertStatus(422);
    }

    // ── R. Riwayat Komplain (2 Skenario) ─────────────────────────────────────

    public function test_r1_riwayat_komplain_ada_komplain(): void
    {
        [$user, $pelanggan] = $this->createCustomerUser('user_r1@domain.com');
        [$cabang, $lp, $admin] = $this->createOrderReferences();
        $order = $this->createOrder($pelanggan, $cabang, $lp, $admin, 'ZYG-R1');

        Complaint::create([
            'transaksi_id' => $order->id,
            'pelanggan_id' => $pelanggan->id,
            'content' => 'Komplain R1',
            'status' => 'pending',
            'issue_types' => ['pakaian_rusak'],
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/customer/complaints');
        $response->assertStatus(200);
        $this->assertNotEmpty($response->json('data.complaints'));
    }

    public function test_r2_riwayat_komplain_tanpa_komplain(): void
    {
        [$user] = $this->createCustomerUser('user_r2@domain.com');

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/customer/complaints');
        $response->assertStatus(200);
        $this->assertEmpty($response->json('data.complaints'));
    }
}
