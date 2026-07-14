<?php

namespace Tests\Feature\Auth;

use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * BLACKBOX - Google OAuth: akun baru harus melalui langkah input nomor telepon
 * sebelum akun benar-benar dibuat, sesuai Activity Diagram Login via Google.
 */
class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::query()->firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
    }

    private function mockSocialiteUser(string $googleId, string $email, string $name = 'Jane Doe'): void
    {
        $socialiteUser = new SocialiteUser();
        $socialiteUser->id = $googleId;
        $socialiteUser->name = $name;
        $socialiteUser->email = $email;
        $socialiteUser->token = 'fake-google-token';

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('stateless')->andReturnSelf();
        $provider->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
    }

    /** @test */
    public function google_callback_dengan_email_baru_diarahkan_ke_form_nomor_telepon_bukan_langsung_membuat_akun(): void
    {
        $this->mockSocialiteUser('google-new-123', 'akun.baru@example.com');

        $response = $this->get(route('api.google.callback'));

        $response->assertRedirect(route('auth.google.phone'));
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'akun.baru@example.com']);
        $this->assertEquals(
            'akun.baru@example.com',
            session('pending_google_registration')['email']
        );
    }

    /** @test */
    public function form_nomor_telepon_tidak_bisa_diakses_tanpa_sesi_pending_registration(): void
    {
        $this->get(route('auth.google.phone'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function submit_nomor_telepon_membuat_akun_baru_dan_login_otomatis(): void
    {
        $this->mockSocialiteUser('google-new-456', 'akun.baru2@example.com', 'Budi Santoso');
        $this->get(route('api.google.callback'));

        $response = $this->post(route('auth.google.phone.submit'), [
            'phone' => '081234567890',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', 'akun.baru2@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('google-new-456', $user->google_id);
        $this->assertEquals('Budi Santoso', $user->name);

        $pelanggan = Pelanggan::where('user_id', $user->id)->first();
        $this->assertNotNull($pelanggan);
        $this->assertEquals('081234567890', $pelanggan->telepon);

        $this->assertNull(session('pending_google_registration'));
    }

    /** @test */
    public function submit_nomor_telepon_wajib_diisi(): void
    {
        $this->mockSocialiteUser('google-new-789', 'akun.baru3@example.com');
        $this->get(route('api.google.callback'));

        $this->post(route('auth.google.phone.submit'), ['phone' => ''])
            ->assertSessionHasErrors('phone');

        $this->assertDatabaseMissing('users', ['email' => 'akun.baru3@example.com']);
    }

    /** @test */
    public function google_callback_dengan_email_yang_sudah_terdaftar_langsung_login_tanpa_form_telepon(): void
    {
        $existingUser = User::factory()->create([
            'email' => 'sudah.ada@example.com',
            'phone' => '081234567890',
        ]);

        $this->mockSocialiteUser('google-existing-1', 'sudah.ada@example.com');

        $response = $this->get(route('api.google.callback'));

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($existingUser);
    }
}
