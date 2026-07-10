<?php

namespace Tests\Feature\Pelanggan;

use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProfileApiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_lihat_profil_mengembalikan_status_200_dan_data_lengkap(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $pelanggan = Pelanggan::create([
            'user_id' => $user->id,
            'nama' => 'Fatur Rahman Al-Fath',
            'telepon' => '081234567890',
            'alamat' => 'Jalan Sultan Iskandar Muda No. 10',
            'jenis_kelamin' => 'L',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/customer/profile');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'profile' => [
                        'id',
                        'nama',
                        'telepon',
                        'alamat',
                    ]
                ]
            ]);
    }
}
