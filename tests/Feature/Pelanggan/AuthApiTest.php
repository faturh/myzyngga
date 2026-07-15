<?php

namespace Tests\Feature\Pelanggan;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test register API.
     */
    public function test_register_api(): void
    {
        \Spatie\Permission\Models\Role::firstOrCreate([
            'name' => 'customer',
            'guard_name' => 'web',
        ]);

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test User API',
            'username' => 'testuserapi',
            'email' => 'testuserapi@example.com',
            'phone' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'access_token',
                'token_type',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'username',
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'testuserapi@example.com',
            'username' => 'testuserapi',
        ]);
    }

    public function test_register_dengan_telepon_milik_guest_order_orang_lain_tidak_mewarisi_data_mereka(): void
    {
        \Spatie\Permission\Models\Role::firstOrCreate([
            'name' => 'customer',
            'guard_name' => 'web',
        ]);

        // Simulasikan korban yang sudah pernah pesan sebagai guest (tanpa akun) —
        // Pelanggan-nya berisi data pribadi asli (alamat, riwayat order).
        $victimPelanggan = \App\Models\Pelanggan::create([
            'user_id' => null,
            'nama' => 'Korban Asli',
            'jenis_kelamin' => 'L',
            'telepon' => '081299998888',
            'alamat' => 'Alamat Rahasia Korban',
        ]);
        $victimOrder = \App\Models\Transaksi::create([
            'nota' => 'ZYG-VICTIM-ORDER',
            'waktu' => now(),
            'total_biaya_layanan' => 10000,
            'total_biaya_prioritas' => 0,
            'total_biaya_layanan_tambahan' => 0,
            'total_bayar_akhir' => 10000,
            'jenis_pembayaran' => 'cash',
            'bayar' => 0,
            'kembalian' => 0,
            'status' => 'Selesai',
            'layanan_prioritas_id' => \App\Models\LayananPrioritas::create([
                'nama' => 'Reguler', 'harga' => 0, 'prioritas' => 1,
                'cabang_id' => \App\Models\Cabang::create(['nama' => 'C', 'lokasi' => 'L', 'alamat' => 'A'])->id,
            ])->id,
            'pelanggan_id' => $victimPelanggan->id,
            'pegawai_id' => '0',
            'cabang_id' => \App\Models\Cabang::first()->id,
        ]);

        // Penyerang cuma tahu nomor telepon korban (tidak diverifikasi kepemilikannya)
        // dan daftar akun baru pakai nomor itu.
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Penyerang',
            'username' => 'penyerang',
            'email' => 'penyerang@example.com',
            'phone' => '081299998888',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201);
        $attackerUserId = $response->json('data.id');

        // Akun penyerang HARUS dapat Pelanggan baru yang kosong, bukan ter-link ke
        // profil korban (yang berisi alamat & riwayat order asli) — kalau ter-link,
        // penyerang bisa lihat data pribadi korban lewat akunnya sendiri.
        $attackerPelanggan = \App\Models\Pelanggan::where('user_id', $attackerUserId)->first();
        $this->assertNotNull($attackerPelanggan);
        $this->assertNotSame(
            $victimPelanggan->id,
            $attackerPelanggan->id,
            'Registrasi TIDAK BOLEH menyambungkan akun baru ke Pelanggan orang lain hanya berdasarkan nomor telepon yang belum diverifikasi — ini celah account-takeover.'
        );
        $this->assertNull($attackerPelanggan->alamat, 'Penyerang tidak boleh mewarisi alamat milik korban.');

        // Profil & riwayat order korban harus tetap milik korban, tidak berubah.
        $this->assertNull($victimPelanggan->fresh()->user_id);
        $this->assertSame($victimPelanggan->id, $victimOrder->fresh()->pelanggan_id);
    }

    /**
     * Test login API.
     */
    public function test_login_api(): void
    {
        // Create user
        $user = User::factory()->create([
            'email' => 'testloginapi@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'testloginapi@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'access_token',
                'token_type',
            ]);
    }

    public function test_login_api_dibatasi_rate_limit_untuk_cegah_brute_force_password(): void
    {
        $user = User::factory()->create([
            'email' => 'bruteforce-target@example.com',
            'password' => bcrypt('password-asli'),
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/auth/login', [
                'email' => $user->email,
                'password' => 'tebakan-salah-'.$i,
            ])->assertStatus(401);
        }

        $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'tebakan-salah-lagi',
        ])->assertStatus(429);
    }

    /**
     * Test Google Redirect route exists.
     */
    public function test_google_redirect_route(): void
    {
        $response = $this->get('/api/v1/auth/google');
        
        // It should redirect to google.com/o/oauth2/...
        $response->assertStatus(302);
        $this->assertStringContainsString('accounts.google.com', $response->headers->get('Location'));
    }

    /**
     * Test guest track order API.
     */
    public function test_track_order_api(): void
    {
        $response = $this->postJson('/api/v1/orders/track', [
            'query' => 'NOT-EXIST',
            'phone_last_4' => '9999',
        ]);

        // It should return 404 since order doesn't exist
        $response->assertStatus(404)
            ->assertJsonPath('success', false);
    }
}
