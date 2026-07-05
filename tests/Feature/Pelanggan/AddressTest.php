<?php

namespace Tests\Feature\Pelanggan;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * BLACKBOX – Manajemen Alamat Pelanggan
 *
 * Menguji CRUD alamat, proteksi kepemilikan (IDOR),
 * dan logika penetapan alamat utama.
 */
class AddressTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create(['role' => 'pelanggan']);
    }

    private function addressFor(User $user, bool $isPrimary = false): Address
    {
        return $user->addresses()->create([
            'label'          => 'Rumah',
            'address_detail' => 'Jl. Merdeka No. 1',
            'latitude'       => -6.2,
            'longitude'      => 106.8,
            'is_primary'     => $isPrimary,
        ]);
    }

    // ── BB-A01  tamu tidak bisa akses endpoint alamat ─────────────────────────

    /** @test */
    public function tamu_tidak_bisa_melihat_halaman_buat_alamat(): void
    {
        $this->get(route('addresses.create'))->assertStatus(302);
    }

    /** @test */
    public function tamu_tidak_bisa_menyimpan_alamat(): void
    {
        $this->post(route('addresses.store'), ['label' => 'X'])->assertStatus(302);
    }

    // ── BB-A02  simpan alamat pertama otomatis jadi utama ─────────────────────

    /** @test */
    public function alamat_pertama_otomatis_menjadi_utama(): void
    {
        $user = $this->user();

        $this->actingAs($user)->post(route('addresses.store'), [
            'label'          => 'Kantor',
            'address_detail' => 'Jl. Sudirman No. 5',
            'latitude'       => -6.21,
            'longitude'      => 106.81,
        ])->assertRedirect(route('profile'));

        $this->assertDatabaseHas('addresses', [
            'user_id'    => $user->id,
            'label'      => 'Kantor',
            'is_primary' => true,
        ]);
    }

    // ── BB-A03  maksimal 3 alamat per user ───────────────────────────────────

    /** @test */
    public function tidak_bisa_simpan_lebih_dari_3_alamat(): void
    {
        $user = $this->user();
        foreach (range(1, 3) as $i) {
            $this->addressFor($user, $i === 1);
        }

        $this->actingAs($user)->post(route('addresses.store'), [
            'label'          => 'Gudang',
            'address_detail' => 'Jl. Industri No. 9',
        ])->assertSessionHas('error');

        $this->assertSame(3, $user->addresses()->count());
    }

    // ── BB-A04  IDOR: edit alamat milik orang lain ditolak ───────────────────

    /** @test */
    public function edit_alamat_milik_pelanggan_lain_ditolak_403(): void
    {
        $owner  = $this->user();
        $attacker = $this->user();
        $addr   = $this->addressFor($owner, true);

        $this->actingAs($attacker)
            ->get(route('addresses.edit', $addr))
            ->assertForbidden();
    }

    // ── BB-A05  IDOR: update alamat milik orang lain ditolak ─────────────────

    /** @test */
    public function update_alamat_milik_pelanggan_lain_ditolak_403(): void
    {
        $owner    = $this->user();
        $attacker = $this->user();
        $addr     = $this->addressFor($owner, true);

        $this->actingAs($attacker)
            ->put(route('addresses.update', $addr), [
                'label'          => 'Diretas',
                'address_detail' => 'Jl. Hack No. 0',
            ])
            ->assertForbidden();
    }

    // ── BB-A06  IDOR: hapus alamat milik orang lain ditolak ──────────────────

    /** @test */
    public function hapus_alamat_milik_pelanggan_lain_ditolak_403(): void
    {
        $owner    = $this->user();
        $attacker = $this->user();
        $addr     = $this->addressFor($owner, true);

        $this->actingAs($attacker)
            ->delete(route('addresses.destroy', $addr))
            ->assertForbidden();

        $this->assertDatabaseHas('addresses', ['id' => $addr->id]);
    }

    // ── BB-A07  setPrimary: hanya boleh dilakukan oleh pemilik ───────────────

    /** @test */
    public function set_primary_alamat_milik_orang_lain_ditolak_403(): void
    {
        $owner    = $this->user();
        $attacker = $this->user();
        $addr     = $this->addressFor($owner, true);

        $this->actingAs($attacker)
            ->post(route('addresses.primary', $addr))
            ->assertForbidden();
    }

    // ── BB-A08  setPrimary me-reset alamat utama lama ─────────────────────────

    /** @test */
    public function set_primary_menyetel_yang_lama_menjadi_bukan_utama(): void
    {
        $user  = $this->user();
        $addr1 = $this->addressFor($user, true);
        $addr2 = $this->addressFor($user, false);

        $this->actingAs($user)->post(route('addresses.primary', $addr2));

        $this->assertFalse((bool) $addr1->fresh()->is_primary);
        $this->assertTrue((bool) $addr2->fresh()->is_primary);
    }

    // ── BB-A09  hapus alamat utama: alamat lain naik jadi utama ──────────────

    /** @test */
    public function hapus_alamat_utama_membuat_alamat_lain_menjadi_utama(): void
    {
        $user  = $this->user();
        $addr1 = $this->addressFor($user, true);
        $addr2 = $this->addressFor($user, false);

        $this->actingAs($user)->delete(route('addresses.destroy', $addr1));

        $this->assertTrue((bool) $addr2->fresh()->is_primary);
    }

    // ── BB-A10  update satu-satunya alamat tetap is_primary=true ─────────────

    /** @test */
    public function update_satu_satunya_alamat_tetap_primary(): void
    {
        $user = $this->user();
        $addr = $this->addressFor($user, true);

        $this->actingAs($user)->put(route('addresses.update', $addr), [
            'label'          => 'Rumah Baru',
            'address_detail' => 'Jl. Baru No. 2',
            'is_primary'     => false,  // paksa false — tidak boleh berhasil
        ]);

        $this->assertTrue((bool) $addr->fresh()->is_primary);
    }
}
