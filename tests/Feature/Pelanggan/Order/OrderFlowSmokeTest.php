<?php

namespace Tests\Feature\Order;

use App\Models\Cabang;
use App\Models\LayananPrioritas;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OrderFlowSmokeTest extends TestCase
{
    use DatabaseTransactions;

    public function test_web_order_flow_creates_customer_order_and_payment(): void
    {
        $this->createAdminUser();
        $customer = $this->createCustomerUser('web-order@example.com', 'web-order');

        $this->ensureOrderReferencesExist();

        $this->actingAs($customer);

        $this->get(route('dashboard'))
            ->assertOk();

        $this->get(route('profile'))
            ->assertOk();

        $this->get(route('order.pickup', ['service' => 'regular']))
            ->assertOk();

        $this->post(route('order.pickup.store'), [
            'service' => 'regular',
            'address' => 'Jalan Testing Nomor 1',
            'detail_address' => 'Rumah belakang pagar hitam',
            'lat' => -6.2,
            'lng' => 106.8,
        ])->assertRedirect(route('order.booking'));

        $this->get(route('order.booking'))
            ->assertOk();

        $this->get(route('order.detail'))
            ->assertOk();

        $this->get(route('order.history'))
            ->assertOk();

        $this->post(route('order.confirm'), [
            'service' => 'regular',
            'address' => 'Jalan Testing Nomor 1',
            'detail_address' => 'Rumah belakang pagar hitam',
            'lat' => -6.2,
            'lng' => 106.8,
            'selected_service_id' => 'regular',
            'pickup_date' => 'today',
            'pickup_time' => '09:00',
            'parfum' => 'lavender',
            'catatan' => 'Tolong dipisah pakaian putih',
            'payment' => 'cash',
        ])->assertRedirect(route('dashboard'));

        $pelanggan = Pelanggan::query()->where('user_id', $customer->id)->first();

        $this->assertNotNull($pelanggan);

        $order = Transaksi::query()
            ->where('pelanggan_id', $pelanggan->id)
            ->latest('created_at')
            ->first();

        $this->assertNotNull($order);
        $this->assertSame('cash', $order->jenis_pembayaran);
        $this->assertSame('pending', $order->payment_status);
        $this->assertSame('Jalan Testing Nomor 1', $order->pickup_address);
        $this->assertNotNull($order->payments()->first());
    }

    public function test_customer_and_order_api_flow_works_end_to_end(): void
    {
        $admin = $this->createAdminUser();
        $customer = $this->createCustomerUser('api-order@example.com', 'api-order');
        [$cabang, $layananPrioritas] = $this->ensureOrderReferencesExist();

        $this->actingAs($customer);

        $this->putJson('/api/v1/customer/address', [
            'label' => 'Rumah',
            'address' => 'Jalan API Nomor 2',
            'detail_address' => 'Samping mushola',
            'lat' => -6.21,
            'lng' => 106.81,
            'jenis_kelamin' => 'L',
            'telepon' => '08123456789',
        ])->assertOk()
            ->assertJsonPath('data.address.address', 'Jalan API Nomor 2');

        $this->putJson('/api/v1/customer/preferences', [
            'default_parfum' => 'ocean',
            'default_note' => 'Jangan pakai pemutih',
            'default_payment_method' => 'qris',
        ])->assertOk()
            ->assertJsonPath('data.preferences.default_payment_method', 'qris');

        $profileResponse = $this->getJson('/api/v1/customer/profile')
            ->assertOk()
            ->assertJsonPath('data.profile.nama', 'api-order');

        $pelangganId = $profileResponse->json('data.profile.id');

        $this->getJson('/api/v1/payment/methods')
            ->assertOk()
            ->assertJsonCount(3, 'data.methods');

        $createOrderResponse = $this->postJson('/api/v1/orders', [
            'pelanggan_id' => $pelangganId,
            'cabang_id' => $cabang->id,
            'layanan_prioritas_id' => $layananPrioritas->id,
            'pickup_address' => 'Jalan API Nomor 2',
            'pickup_detail_address' => 'Samping mushola',
            'pickup_date' => now()->toDateString(),
            'pickup_time' => '10:30',
            'parfum' => 'ocean',
            'catatan' => 'Tes order API',
            'payment_method' => 'qris',
            'estimated_total' => 25000,
        ])->assertCreated()
            ->assertJsonPath('data.order.payment.method', 'qris');

        $orderId = $createOrderResponse->json('data.order.id');

        $this->getJson('/api/v1/orders/history')
            ->assertOk()
            ->assertJsonPath('data.orders.0.id', $orderId);

        $this->getJson("/api/v1/orders/{$orderId}")
            ->assertOk()
            ->assertJsonPath('data.order.id', $orderId);

        $this->actingAs($admin);

        $this->postJson("/api/v1/payments/{$orderId}/verify", [
            'method' => 'qris',
            'amount' => 25000,
            'notes' => 'Lunas via QRIS',
        ])->assertOk()
            ->assertJsonPath('data.payment.status', 'verified');

        $this->patchJson("/api/v1/orders/{$orderId}/status", [
            'status' => 'in_progress',
        ])->assertOk()
            ->assertJsonPath('data.order.status', 'in_progress');
    }

    private function createAdminUser(): User
    {
        $this->ensureRoleExists('admin');

        $user = User::factory()->create([
            'username' => 'admin-smoke',
            'slug' => 'admin-smoke',
            'email' => 'admin-smoke@example.com',
            'password' => 'password',
            'email_verified_at' => now(),
        ]);

        $user->assignRole('admin');

        return $user;
    }

    private function createCustomerUser(string $email, string $username): User
    {
        $this->ensureRoleExists('customer');

        $user = User::factory()->create([
            'username' => $username,
            'slug' => $username,
            'email' => $email,
            'password' => 'password',
            'email_verified_at' => now(),
        ]);

        $user->assignRole('customer');

        return $user;
    }

    private function ensureRoleExists(string $name): void
    {
        Role::query()->firstOrCreate([
            'name' => $name,
            'guard_name' => 'web',
        ]);
    }

    /**
     * @return array{0: \App\Models\Cabang, 1: \App\Models\LayananPrioritas}
     */
    private function ensureOrderReferencesExist(): array
    {
        $cabang = Cabang::query()->firstOrCreate(
            ['nama' => 'Cabang Smoke'],
            [
                'slug' => 'cabang-smoke',
                'lokasi' => 'Bandung',
                'alamat' => 'Jalan Cabang Smoke',
            ],
        );

        $layananPrioritas = LayananPrioritas::query()->firstOrCreate(
            [
                'nama' => 'Regular Smoke',
                'cabang_id' => $cabang->id,
            ],
            [
                'deskripsi' => 'Layanan untuk smoke test',
                'harga' => 25000,
                'prioritas' => 1,
            ],
        );

        return [$cabang, $layananPrioritas];
    }
}
