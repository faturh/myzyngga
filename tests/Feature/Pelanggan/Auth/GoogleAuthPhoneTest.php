<?php

namespace Tests\Feature\Pelanggan\Auth;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GoogleAuthPhoneTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
    }

    public function test_submit_phone_menyimpan_user_dan_pelanggan_baru(): void
    {
        $this->withSession([
            'pending_google_registration' => [
                'name' => 'Google Test User',
                'email' => 'google-test-user@example.com',
                'google_id' => 'google-1234567890',
                'google_token' => 'fake-google-token',
            ],
        ])->post(route('auth.google.phone.submit'), [
            'phone' => '081298765432',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'google-test-user@example.com',
            'google_id' => 'google-1234567890',
        ]);

        $user = \App\Models\User::where('email', 'google-test-user@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('customer'));

        $this->assertDatabaseHas('pelanggan', [
            'user_id' => $user->id,
            'telepon' => '081298765432',
        ]);

        // users.phone juga harus terisi — halaman profil (Ubah Profil) baca dari
        // User::phone, bukan Pelanggan::telepon. Kalau ini kosong, nomor WhatsApp
        // yang baru saja dimasukkan saat registrasi Google tidak muncul di profil,
        // sampai user logout-login lagi dan dipaksa isi ulang lewat register/phone.
        $this->assertSame(
            '081298765432',
            $user->phone,
            'users.phone tidak ke-set saat registrasi Google — nomor WhatsApp hilang dari halaman profil.'
        );

        $this->assertAuthenticatedAs($user);
    }

    public function test_submit_phone_ditolak_tanpa_pending_registration(): void
    {
        $this->post(route('auth.google.phone.submit'), [
            'phone' => '081298765432',
        ])->assertRedirect(route('login'));

        $this->assertDatabaseMissing('users', [
            'email' => 'google-test-user@example.com',
        ]);
    }
}
