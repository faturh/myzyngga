<?php

namespace Tests\Feature\Pelanggan;

use App\Models\Cabang;
use App\Models\LayananPrioritas;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
    }

    private function setupOrderData(): array
    {
        $user = User::factory()->create(['role' => 'customer']);
        $user->assignRole('customer');
        
        $pelanggan = Pelanggan::create([
            'user_id' => $user->id,
            'nama' => 'Test Customer',
            'telepon' => '081234567890',
            'alamat' => 'Jalan Sultan Iskandar Muda No. 10',
            'jenis_kelamin' => 'L',
        ]);

        $admin = User::factory()->create([
            'username' => 'admin-test-order',
            'email' => 'admin-test-order@example.com',
            'role' => 'admin',
        ]);
        $admin->assignRole('admin');

        $cabang = Cabang::create([
            'nama' => 'Cabang Test Order',
            'alamat' => 'Alamat Cabang',
            'lokasi' => 'Jakarta',
        ]);

        $layananPrioritas = LayananPrioritas::create([
            'nama' => 'Reguler',
            'harga' => 0,
            'prioritas' => 1,
            'cabang_id' => $cabang->id,
        ]);

        return [$user, $pelanggan, $cabang, $layananPrioritas, $admin];
    }

    public function test_buat_pesanan_berhasil_dengan_status_created(): void
    {
        [$user, $pelanggan, $cabang, $lp] = $this->setupOrderData();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/orders', [
                'pelanggan_id' => $pelanggan->id,
                'cabang_id' => $cabang->id,
                'layanan_prioritas_id' => $lp->id,
                'pickup_address' => 'Jl. Kebayoran Lama No. 10',
                'pickup_date' => now()->toDateString(),
                'pickup_time' => '10:00 - 12:00',
                'payment_method' => 'cash',
                'estimated_total' => 20000,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.order.status', 'created');
    }

    public function test_buat_pesanan_dengan_pelanggan_id_milik_orang_lain_harus_ditolak(): void
    {
        [$userA, $pelangganA, $cabang, $lp] = $this->setupOrderData();

        $userB = User::factory()->create(['role' => 'customer']);
        $userB->assignRole('customer');
        $pelangganB = Pelanggan::create([
            'user_id' => $userB->id,
            'nama' => 'Customer B (Korban)',
            'telepon' => '089876543210',
            'alamat' => 'Alamat B',
            'jenis_kelamin' => 'P',
        ]);

        // User A login, tapi kirim pelanggan_id milik User B
        $response = $this->actingAs($userA, 'sanctum')
            ->postJson('/api/v1/orders', [
                'pelanggan_id' => $pelangganB->id,
                'cabang_id' => $cabang->id,
                'layanan_prioritas_id' => $lp->id,
                'pickup_address' => 'Jl. Palsu No. 1',
                'pickup_date' => now()->toDateString(),
                'pickup_time' => '10:00 - 12:00',
                'payment_method' => 'cash',
                'estimated_total' => 20000,
            ]);

        $response->assertStatus(422);

        $this->assertDatabaseMissing('transaksi', [
            'pelanggan_id' => $pelangganB->id,
            'pickup_address' => 'Jl. Palsu No. 1',
        ]);
    }

    public function test_riwayat_pesanan_mengembalikan_array_dengan_pagination(): void
    {
        [$user, $pelanggan, $cabang, $lp, $admin] = $this->setupOrderData();

        Transaksi::create([
            'pelanggan_id' => $pelanggan->id,
            'cabang_id' => $cabang->id,
            'layanan_prioritas_id' => $lp->id,
            'pickup_address' => 'Jalan Alamat',
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
            'nota' => 'ZYG-TEST-HIST',
            'pegawai_id' => (string) $admin->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/orders/history');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'orders' => [
                        '*' => [
                            'id',
                            'nota',
                            'status',
                        ]
                    ]
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ]
            ]);
    }

    public function test_riwayat_pesanan_per_page_dibatasi_maksimal_100(): void
    {
        [$user] = $this->setupOrderData();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/orders/history?per_page=999999');

        $response->assertStatus(200)
            ->assertJsonPath('meta.per_page', 100);
    }

    public function test_cek_nota_non_login_berhasil_dengan_data_valid(): void
    {
        [$user, $pelanggan, $cabang, $lp, $admin] = $this->setupOrderData();

        Transaksi::create([
            'pelanggan_id' => $pelanggan->id,
            'cabang_id' => $cabang->id,
            'layanan_prioritas_id' => $lp->id,
            'pickup_address' => 'Jalan Alamat',
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
            'nota' => 'ZYG-TRACK-OK',
            'pegawai_id' => (string) $admin->id,
        ]);

        $response = $this->postJson('/api/v1/orders/track', [
            'query' => 'ZYG-TRACK-OK',
            'phone_last_4' => '7890', // dari 081234567890
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_cek_nota_non_login_ditolak_dengan_telepon_salah(): void
    {
        [$user, $pelanggan, $cabang, $lp, $admin] = $this->setupOrderData();

        Transaksi::create([
            'pelanggan_id' => $pelanggan->id,
            'cabang_id' => $cabang->id,
            'layanan_prioritas_id' => $lp->id,
            'pickup_address' => 'Jalan Alamat',
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
            'nota' => 'ZYG-TRACK-FAIL',
            'pegawai_id' => (string) $admin->id,
        ]);

        $response = $this->postJson('/api/v1/orders/track', [
            'query' => 'ZYG-TRACK-FAIL',
            'phone_last_4' => '9999', // salah nomor HP
        ]);

        $response->assertStatus(404)
            ->assertJsonPath('success', false);
    }

    public function test_cek_nota_dibatasi_rate_limit_untuk_cegah_brute_force_phone_last_4(): void
    {
        [$user, $pelanggan, $cabang, $lp, $admin] = $this->setupOrderData();

        Transaksi::create([
            'pelanggan_id' => $pelanggan->id,
            'cabang_id' => $cabang->id,
            'layanan_prioritas_id' => $lp->id,
            'pickup_address' => 'Jalan Alamat',
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
            'nota' => 'ZYG-TRACK-THROTTLE',
            'pegawai_id' => (string) $admin->id,
        ]);

        // phone_last_4 cuma 4 digit (10.000 kemungkinan) — tanpa rate limit,
        // penyerang yang tahu nama pelanggan bisa brute-force dalam hitungan detik.
        for ($i = 0; $i < 10; $i++) {
            $this->postJson('/api/v1/orders/track', [
                'query' => 'Test Customer',
                'phone_last_4' => str_pad((string) $i, 4, '0', STR_PAD_LEFT),
            ])->assertStatus(404);
        }

        $this->postJson('/api/v1/orders/track', [
            'query' => 'Test Customer',
            'phone_last_4' => '0010',
        ])->assertStatus(429);
    }
}
