<?php

namespace Tests\Feature\Pelanggan;

use App\Models\Cabang;
use App\Models\LayananPrioritas;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UpgradeApiTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
    }

    public function test_ajukan_upgrade_layanan_berhasil_menghitung_selisih_biaya(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $user->assignRole('customer');
        
        $pelanggan = Pelanggan::create([
            'user_id' => $user->id,
            'nama' => 'Test Customer',
            'telepon' => '081234567890',
            'alamat' => 'Alamat Asli',
            'jenis_kelamin' => 'L',
        ]);

        $admin = User::factory()->create([
            'username' => 'admin-test-up',
            'email' => 'admin-test-up@example.com',
            'role' => 'admin',
        ]);
        $admin->assignRole('admin');

        $cabang = Cabang::create([
            'nama' => 'Cabang Test',
            'alamat' => 'Alamat Cabang',
            'lokasi' => 'Jakarta',
        ]);

        // Layanan 1 (Reguler - prioritas 1, biaya 0)
        $lpReguler = LayananPrioritas::create([
            'nama' => 'Reguler',
            'harga' => 0,
            'prioritas' => 1,
            'cabang_id' => $cabang->id,
        ]);

        // Layanan 2 (Express - prioritas 3, biaya 15000)
        $lpExpress = LayananPrioritas::create([
            'nama' => 'Express',
            'harga' => 15000,
            'prioritas' => 3,
            'cabang_id' => $cabang->id,
        ]);

        $order = Transaksi::create([
            'pelanggan_id' => $pelanggan->id,
            'cabang_id' => $cabang->id,
            'layanan_prioritas_id' => $lpReguler->id,
            'pickup_address' => 'Jalan Alamat',
            'pickup_date' => now()->toDateString(),
            'pickup_time' => '10:00',
            'waktu' => now(),
            'status' => 'Baru',
            'payment_status' => 'pending',
            'jenis_pembayaran' => 'cash',
            'total_bayar_akhir' => 4850,
            'total_biaya_layanan' => 4850,
            'total_biaya_prioritas' => 0,
            'total_biaya_layanan_tambahan' => 0,
            'bayar' => 0,
            'kembalian' => 0,
            'nota' => 'ZYG-UP-TEST',
            'pegawai_id' => (string) $admin->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/orders/{$order->id}/upgrade", [
                'new_service_id' => $lpExpress->id,
                'payment_method' => 'qris',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        // Verifikasi selisih biaya (15000 - 0 = 15000) dicatat ke dalam payment_metadata
        $order = $order->fresh();
        $meta = json_decode($order->payment_metadata, true);
        
        $this->assertNotNull($meta);
        $this->assertArrayHasKey('pending_upgrade', $meta);
        $this->assertEquals(15000, $meta['pending_upgrade']['price_diff']);
    }
}
