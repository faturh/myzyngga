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
