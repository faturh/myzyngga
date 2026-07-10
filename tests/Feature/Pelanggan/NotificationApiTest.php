<?php

namespace Tests\Feature\Pelanggan;

use App\Models\Notifikasi;
use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class NotificationApiTest extends TestCase
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

    public function test_lihat_daftar_notifikasi_mengembalikan_array_dengan_is_read(): void
    {
        [$user, $pelanggan] = $this->setupCustomer();

        Notifikasi::create([
            'pelanggan_id' => $pelanggan->id,
            'jenis' => 'status',
            'pesan' => 'Notifikasi 1',
            'is_read' => false,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/customer/notifications');

        $response->assertStatus(200)
            ->assertJsonPath('errors', null)
            ->assertJsonStructure([
                'data' => [
                    'notifications' => [
                        '*' => [
                            'id',
                            'pesan',
                            'is_read',
                        ]
                    ]
                ]
            ]);
    }

    public function test_perbarui_status_baca_berhasil_untuk_notifikasi_sendiri(): void
    {
        [$user, $pelanggan] = $this->setupCustomer();

        $notif = Notifikasi::create([
            'pelanggan_id' => $pelanggan->id,
            'jenis' => 'status',
            'pesan' => 'Notifikasi 1',
            'is_read' => false,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/customer/notifications/{$notif->id}/read");

        $response->assertStatus(200)
            ->assertJsonPath('errors', null);
            
        $this->assertTrue((bool)$notif->fresh()->is_read);
    }

    public function test_perbarui_status_baca_ditolak_untuk_notifikasi_milik_orang_lain(): void
    {
        [$user, $pelanggan] = $this->setupCustomer();

        // Buat pelanggan lain
        $otherUser = User::factory()->create(['role' => 'customer']);
        $otherUser->assignRole('customer');
        
        $otherPelanggan = Pelanggan::create([
            'user_id' => $otherUser->id,
            'nama' => 'Other Customer',
            'telepon' => '081234567891',
            'alamat' => 'Jalan Lain',
            'jenis_kelamin' => 'P',
        ]);

        $otherNotif = Notifikasi::create([
            'pelanggan_id' => $otherPelanggan->id,
            'jenis' => 'status',
            'pesan' => 'Notifikasi Orang Lain',
            'is_read' => false,
        ]);

        // Coba mark as read menggunakan user pertama
        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/customer/notifications/{$otherNotif->id}/read");

        // Sistem wajib menolak dengan status 403 (IDOR protection)
        $response->assertStatus(403);
        $this->assertFalse((bool)$otherNotif->fresh()->is_read);
    }
}
