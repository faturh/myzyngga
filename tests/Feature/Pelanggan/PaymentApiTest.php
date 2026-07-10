<?php

namespace Tests\Feature\Pelanggan;

use App\Models\Cabang;
use App\Models\LayananPrioritas;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PaymentApiTest extends TestCase
{
    use DatabaseTransactions;

    private function setupOrderData(): array
    {
        $user = User::factory()->create(['role' => 'customer']);
        $pelanggan = Pelanggan::create([
            'user_id' => $user->id,
            'nama' => 'Test Customer',
            'telepon' => '081234567890',
            'alamat' => 'Jalan Sultan Iskandar Muda No. 10',
            'jenis_kelamin' => 'L',
        ]);

        $admin = User::factory()->create([
            'username' => 'admin-test-pay',
            'email' => 'admin-test-pay@example.com',
            'role' => 'admin',
        ]);

        $cabang = Cabang::create([
            'nama' => 'Cabang Test Pay',
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
            'nota' => 'ZYG-PAY-TEST',
            'pegawai_id' => (string) $admin->id,
        ]);

        return [$user, $pelanggan, $order];
    }

    public function test_daftar_metode_pembayaran_mengembalikan_array(): void
    {
        [$user] = $this->setupOrderData();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/payment/methods');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'methods' => [
                        '*' => [
                            'code',
                            'name',
                        ]
                    ]
                ]
            ]);
    }

    public function test_webhook_midtrans_memperbarui_status_pembayaran(): void
    {
        [$user, $pelanggan, $order] = $this->setupOrderData();

        $response = $this->postJson('/api/v1/payment/notification', [
            'transaction_time' => '2026-07-08 10:30:00',
            'transaction_status' => 'settlement',
            'transaction_id' => 'abc123-midtrans-uuid-def456',
            'status_message' => 'midtrans payment notification',
            'status_code' => '200',
            'signature_key' => 'dummy_signature',
            'settlement_time' => '2026-07-08 10:35:00',
            'payment_type' => 'qris',
            'order_id' => "{$order->id}-12345678",
            'gross_amount' => '10000.00',
            'fraud_status' => 'accept',
        ]);

        $response->assertStatus(200);
        $this->assertEquals('paid', $order->fresh()->payment_status);
    }

    public function test_cek_status_pembayaran_berhasil_untuk_pemilik_order(): void
    {
        [$user, $pelanggan, $order] = $this->setupOrderData();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/v1/orders/{$order->id}/payment-status");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'order_id',
                    'nota',
                    'payment_status',
                ]
            ]);
    }

    public function test_cek_status_pembayaran_ditolak_untuk_bukan_pemilik(): void
    {
        [$user, $pelanggan, $order] = $this->setupOrderData();

        // Akun pelanggan lain
        $otherUser = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($otherUser, 'sanctum')
            ->getJson("/api/v1/orders/{$order->id}/payment-status");

        $response->assertStatus(403);
    }
}
