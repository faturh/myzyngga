<?php

namespace Tests\Feature\Pelanggan;

use App\Models\Cabang;
use App\Models\LayananPrioritas;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DeliveryApiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_ajukan_layanan_antar_mengembalikan_estimated_finished(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $pelanggan = Pelanggan::create([
            'user_id' => $user->id,
            'nama' => 'Test Customer',
            'telepon' => '081234567890',
            'alamat' => 'Alamat Asli',
            'jenis_kelamin' => 'L',
        ]);

        $admin = User::factory()->create([
            'username' => 'admin-test-del',
            'email' => 'admin-test-del@example.com',
            'role' => 'admin',
        ]);

        $cabang = Cabang::create([
            'nama' => 'Cabang Test',
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
            'nota' => 'ZYG-DEL-TEST',
            'pegawai_id' => (string) $admin->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/orders/{$order->id}/delivery-request", [
                'address' => 'Jalan Antar Baru',
                'detail_address' => 'Pagar Hijau',
                'lat' => -6.2443872,
                'lng' => 106.7835241,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'snap_token',
                'redirect',
                'estimated_finished',
            ]);
    }
}
