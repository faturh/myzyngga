<?php

namespace Tests\Feature\Pelanggan\Order;

use App\Models\Cabang;
use App\Models\LayananPrioritas;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OrderApiEstimatedFinishedTest extends TestCase
{
    use DatabaseTransactions;

    public function test_api_endpoints_return_estimated_finished(): void
    {
        // 1. Setup Data
        $user = User::factory()->create();
        $pelanggan = Pelanggan::create([
            'user_id' => $user->id,
            'nama' => 'Test Customer',
            'telepon' => '081234567890',
            'alamat' => 'Alamat Asli',
            'jenis_kelamin' => 'L',
        ]);

        $admin = User::factory()->create([
            'username' => 'admin-test-eta',
            'email' => 'admin-test-eta@example.com',
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
            'nota' => 'ZYG-TEST-123',
            'pegawai_id' => (string) $admin->id,
        ]);

        $this->actingAs($user, 'sanctum');

        // 2. Test GET /api/v1/orders/{id}
        $getResponse = $this->getJson("/api/v1/orders/{$order->id}");
        $getResponse->assertOk();
        
        $getData = $getResponse->json('data.order');
        $this->assertArrayHasKey('estimated_finished', $getData);
        dump("GET /api/v1/orders/{orderId} Response JSON:", json_encode($getData, JSON_PRETTY_PRINT));

        // 3. Test POST /api/v1/orders/{id}/delivery-request
        $postResponse = $this->postJson("/api/v1/orders/{$order->id}/delivery-request", [
            'address' => 'Jalan Antar Baru',
            'detail_address' => 'Pagar Hijau',
            'lat' => -6.123456,
            'lng' => 106.123456,
        ]);
        $postResponse->assertOk();
        
        $postData = $postResponse->json();
        $this->assertArrayHasKey('estimated_finished', $postData);
        dump("POST /api/v1/orders/{orderId}/delivery-request Response JSON:", json_encode($postData, JSON_PRETTY_PRINT));
    }
}
