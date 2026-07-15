<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response
            ->assertOk()
            ->assertSeeVolt('pages.auth.register');
    }

    public function test_new_users_can_register(): void
    {
        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('whatsapp', '08123456789')
            ->set('password', 'password');

        $component->call('register');

        $component->assertRedirect(route('home', absolute: false));

        $this->assertAuthenticated();

        // Halaman profil (Ubah Profil) baca nomor WhatsApp dari users.phone,
        // bukan pelanggan.telepon — kalau ini kosong, nomor yang baru saja
        // dimasukkan saat daftar tidak muncul di profil.
        $user = \App\Models\User::where('email', 'test@example.com')->first();
        $this->assertSame(
            '08123456789',
            $user->phone,
            'users.phone tidak ke-set saat registrasi — nomor WhatsApp hilang dari halaman profil.'
        );
    }
}
