<?php

namespace Tests\Feature\Pelanggan;

use App\Models\CustomerAddress;
use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AddressApiTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
    }

    private function setupCustomer(): array
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

        return [$user, $pelanggan];
    }

    public function test_lihat_daftar_alamat_mengembalikan_array_dengan_status_primary(): void
    {
        [$user, $pelanggan] = $this->setupCustomer();

        $address = CustomerAddress::create([
            'pelanggan_id' => $pelanggan->id,
            'label' => 'Rumah',
            'address' => 'Jl. Merdeka No. 1',
            'is_default' => true,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/customer/addresses');

        $response->assertStatus(200)
            ->assertJsonPath('errors', null)
            ->assertJsonStructure([
                'data' => [
                    'addresses' => [
                        '*' => [
                            'id',
                            'label',
                            'address',
                            'is_primary',
                        ]
                    ]
                ]
            ]);
    }

    public function test_tambah_alamat_berhasil_dengan_status_201(): void
    {
        [$user, $pelanggan] = $this->setupCustomer();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/customer/addresses', [
                'label' => 'Kantor Baru',
                'address' => 'Jalan Kebayoran Lama No. 5',
                'detail_address' => 'Gedung A lantai 4',
                'lat' => -6.21,
                'lng' => 106.81,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'address' => [
                        'id',
                        'address',
                        'lat',
                        'lng',
                    ]
                ]
            ]);
    }

    public function test_tambah_alamat_ditolak_saat_melebihi_batas_maksimal(): void
    {
        [$user, $pelanggan] = $this->setupCustomer();

        // Buat 3 alamat
        for ($i = 0; $i < 3; $i++) {
            CustomerAddress::create([
                'pelanggan_id' => $pelanggan->id,
                'label' => 'Alamat ' . ($i + 1),
                'address' => 'Jalan Test ' . ($i + 1),
                'is_default' => $i === 0,
            ]);
        }

        // Post alamat ke-4
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/customer/addresses', [
                'label' => 'Alamat 4',
                'address' => 'Jalan Test 4',
            ]);

        $response->assertStatus(422);
    }

    public function test_tetapkan_alamat_utama_mengubah_status_is_primary(): void
    {
        [$user, $pelanggan] = $this->setupCustomer();

        $addr1 = CustomerAddress::create([
            'pelanggan_id' => $pelanggan->id,
            'label' => 'Rumah',
            'address' => 'Jl. Rumah',
            'is_default' => true,
        ]);

        $addr2 = CustomerAddress::create([
            'pelanggan_id' => $pelanggan->id,
            'label' => 'Kantor',
            'address' => 'Jl. Kantor',
            'is_default' => false,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/customer/addresses/{$addr2->id}/primary");

        $response->assertStatus(200);

        $this->assertTrue((bool)$addr2->fresh()->is_default);
        $this->assertFalse((bool)$addr1->fresh()->is_default);
    }

    public function test_edit_alamat_berhasil_memperbarui_data(): void
    {
        [$user, $pelanggan] = $this->setupCustomer();

        $address = CustomerAddress::create([
            'pelanggan_id' => $pelanggan->id,
            'label' => 'Rumah',
            'address' => 'Jl. Rumah',
            'is_default' => true,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/v1/customer/addresses/{$address->id}", [
                'label' => 'Apartemen',
                'address' => 'Jl. Apartemen',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.address.label', 'Apartemen');
    }

    public function test_edit_alamat_ditolak_jika_bukan_milik_pelanggan(): void
    {
        [$user, $pelanggan] = $this->setupCustomer();

        // Pelanggan lain
        $otherUser = User::factory()->create(['role' => 'customer']);
        $otherUser->assignRole('customer');
        
        $otherPelanggan = Pelanggan::create([
            'user_id' => $otherUser->id,
            'nama' => 'Other Customer',
            'telepon' => '081234567891',
            'alamat' => 'Jalan Lain',
            'jenis_kelamin' => 'P',
        ]);

        $otherAddress = CustomerAddress::create([
            'pelanggan_id' => $otherPelanggan->id,
            'label' => 'Alamat Orang Lain',
            'address' => 'Jl. Orang Lain',
            'is_default' => true,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/v1/customer/addresses/{$otherAddress->id}", [
                'label' => 'Mencoba Edit',
                'address' => 'Jl. Mencoba Edit',
            ]);

        $response->assertStatus(403);
    }

    public function test_hapus_alamat_berhasil_menghapus_data(): void
    {
        [$user, $pelanggan] = $this->setupCustomer();

        $address1 = CustomerAddress::create([
            'pelanggan_id' => $pelanggan->id,
            'label' => 'Rumah',
            'address' => 'Jl. Rumah',
            'is_default' => true,
        ]);

        $address2 = CustomerAddress::create([
            'pelanggan_id' => $pelanggan->id,
            'label' => 'Kantor',
            'address' => 'Jl. Kantor',
            'is_default' => false,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/v1/customer/addresses/{$address2->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('customer_addresses', ['id' => $address2->id]);
    }

    public function test_hapus_alamat_ditolak_jika_berstatus_utama(): void
    {
        [$user, $pelanggan] = $this->setupCustomer();

        $address = CustomerAddress::create([
            'pelanggan_id' => $pelanggan->id,
            'label' => 'Rumah Utama',
            'address' => 'Jl. Rumah',
            'is_default' => true,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/v1/customer/addresses/{$address->id}");

        $response->assertStatus(422);
    }
}
