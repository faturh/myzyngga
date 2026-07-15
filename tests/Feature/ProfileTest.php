<?php

namespace Tests\Feature;

use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/profile');

        $response
            ->assertOk()
            ->assertSeeVolt('profile.update-password-form')
            ->assertSeeVolt('profile.delete-user-form');

        $responseAccount = $this->actingAs($user)->get('/profile/account');

        $responseAccount
            ->assertOk()
            ->assertSeeVolt('profile.update-profile-information-form');
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Volt::test('profile.update-profile-information-form')
            ->set('name', 'Test User')
            ->set('phone', '08123456789')
            ->call('updateProfileInformation');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('profile'));

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('08123456789', $user->phone);
    }

    public function test_profile_information_update_juga_menyinkronkan_data_pelanggan(): void
    {
        $user = User::factory()->create(['name' => 'Nama Lama']);
        $pelanggan = Pelanggan::create([
            'user_id' => $user->id,
            'nama' => 'Nama Lama',
            'jenis_kelamin' => 'L',
            'telepon' => '080000000000',
            'alamat' => null,
        ]);

        $this->actingAs($user);

        Volt::test('profile.update-profile-information-form')
            ->set('name', 'Nama Baru')
            ->set('phone', '08199998888')
            ->call('updateProfileInformation')
            ->assertHasNoErrors();

        $pelanggan->refresh();
        $this->assertSame(
            'Nama Baru',
            $pelanggan->nama,
            'Update nama profil harus tersinkron ke Pelanggan.nama — kalau tidak, riwayat pesanan, nota, dan komplain tetap menampilkan nama lama selamanya.'
        );
        $this->assertSame(
            '08199998888',
            $pelanggan->telepon,
            'Update telepon profil harus tersinkron ke Pelanggan.telepon.'
        );
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Volt::test('profile.update-profile-information-form')
            ->set('name', 'Test User')
            ->set('email', $user->email)
            ->call('updateProfileInformation');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('profile'));

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Volt::test('profile.delete-user-form')
            ->set('password', 'password')
            ->call('deleteUser');

        $component
            ->assertHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertSoftDeleted($user);
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Volt::test('profile.delete-user-form')
            ->set('password', 'wrong-password')
            ->call('deleteUser');

        $component
            ->assertHasErrors('password')
            ->assertNoRedirect();

        $this->assertNotNull($user->fresh());
    }
}
