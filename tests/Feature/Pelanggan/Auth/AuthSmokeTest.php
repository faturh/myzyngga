<?php

namespace Tests\Feature\Pelanggan\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthSmokeTest extends TestCase
{
    use DatabaseTransactions;

    public function test_register_page_is_accessible(): void
    {
        $this->get(route('register'))
            ->assertOk();
    }

    public function test_user_can_register_from_livewire_page(): void
    {
        Role::query()->firstOrCreate([
            'name' => 'customer',
            'guard_name' => 'web',
        ]);

        Livewire::test('pages.auth.register')
            ->set('name', 'Faturh Testing')
            ->set('whatsapp', '08123456789')
            ->set('email', 'faturh-testing@example.com')
            ->set('password', 'password')
            ->call('register')
            ->assertRedirect(route('home', absolute: false));

        $user = User::query()->where('email', 'faturh-testing@example.com')->first();

        $this->assertNotNull($user);
        $this->assertSame('08123456789', $user->username);
        $this->assertNotEmpty($user->slug);
        $this->assertTrue($user->hasRole('customer'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_login_and_open_dashboard(): void
    {
        $user = User::factory()->create([
            'username' => 'customer-smoke',
            'slug' => 'customer-smoke',
            'email' => 'customer-smoke@example.com',
            'password' => 'password',
        ]);

        Role::query()->firstOrCreate([
            'name' => 'customer',
            'guard_name' => 'web',
        ]);
        $user->assignRole('customer');

        Livewire::test('pages.auth.login')
            ->set('form.email', 'customer-smoke@example.com')
            ->set('form.password', 'password')
            ->set('form.remember', true)
            ->call('login')
            ->assertRedirect(route('home', absolute: false));

        $this->assertAuthenticatedAs($user);

        $this->get(route('dashboard'))
            ->assertOk();
    }
}
