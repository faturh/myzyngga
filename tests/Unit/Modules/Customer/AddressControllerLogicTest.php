<?php

namespace Tests\Unit\Modules\Customer;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * WHITEBOX – AddressController Logic
 *
 * Menguji logika internal controller alamat:
 * - Dead code di store() (else-if yang tidak pernah tercapai)
 * - Race condition di setPrimary (tidak ada DB transaction)
 * - Kondisi batas: 1 alamat, perpindahan primary, hapus primary
 */
class AddressControllerLogicTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create(['role' => 'pelanggan']);
    }

    private function makeAddress(User $user, bool $isPrimary = false): Address
    {
        return $user->addresses()->create([
            'label'          => 'Label ' . uniqid(),
            'address_detail' => 'Jl. ' . uniqid(),
            'latitude'       => -6.2,
            'longitude'      => 106.8,
            'is_primary'     => $isPrimary,
        ]);
    }

    // ── WB-AC-01  Dead code di store(): else-if count===0 tidak pernah tercapai

    /**
     * @test
     * Whitebox: Di AddressController::store(), blok kode berikut adalah dead code:
     *   } else if ($user->addresses()->count() === 0) {
     *       $shouldBePrimary = true;
     *   }
     * Karena jika count() === 0 maka $isFirstAddress = true, sehingga
     * $shouldBePrimary = ($isPrimaryRequest || true) = true, dan cabang if ($shouldBePrimary)
     * sudah dieksekusi duluan. Cabang else-if tidak pernah tercapai.
     *
     * Dampak: Tidak ada bug aktif, tapi kode ini memberi kesan false safety.
     * Ini adalah dead code yang bisa menyesatkan pembaca.
     */
    public function store_else_if_count_nol_adalah_dead_code(): void
    {
        // Verifikasi perilaku: alamat pertama selalu is_primary=true
        $user = $this->user();

        $this->actingAs($user)->post(route('addresses.store'), [
            'label'          => 'Rumah',
            'address_detail' => 'Jl. Merdeka',
            'is_primary'     => false, // eksplisit false
        ]);

        // Meskipun request is_primary=false, alamat pertama tetap harus primary
        $this->assertTrue((bool) $user->addresses()->first()->is_primary,
            'Alamat pertama harus selalu is_primary=true terlepas dari request'
        );
    }

    // ── WB-AC-02  update: satu-satunya alamat tidak bisa di-unset dari primary ─

    /** @test */
    public function update_satu_satunya_alamat_dipaksa_primary_meski_request_false(): void
    {
        $user = $this->user();
        $addr = $this->makeAddress($user, true);

        $this->actingAs($user)->put(route('addresses.update', $addr), [
            'label'          => 'Rumah Lama',
            'address_detail' => 'Jl. Lama',
            'is_primary'     => false,
        ]);

        $this->assertTrue((bool) $addr->fresh()->is_primary);
    }

    // ── WB-AC-03  hapus non-primary tidak mengubah primary yang ada ───────────

    /** @test */
    public function hapus_alamat_non_primary_tidak_mengubah_alamat_primary(): void
    {
        $user   = $this->user();
        $primary = $this->makeAddress($user, true);
        $other   = $this->makeAddress($user, false);

        $this->actingAs($user)->delete(route('addresses.destroy', $other));

        $this->assertTrue((bool) $primary->fresh()->is_primary);
        $this->assertDatabaseMissing('addresses', ['id' => $other->id]);
    }

    // ── WB-AC-04  setPrimary me-reset semua alamat lain ──────────────────────

    /** @test */
    public function setPrimary_me_reset_is_primary_semua_alamat_lain(): void
    {
        $user  = $this->user();
        $addr1 = $this->makeAddress($user, true);
        $addr2 = $this->makeAddress($user, false);
        $addr3 = $this->makeAddress($user, false);

        $this->actingAs($user)->post(route('addresses.primary', $addr3));

        $this->assertFalse((bool) $addr1->fresh()->is_primary);
        $this->assertFalse((bool) $addr2->fresh()->is_primary);
        $this->assertTrue((bool) $addr3->fresh()->is_primary);
    }

    // ── WB-AC-05  store: request is_primary=true me-reset yang lama ──────────

    /** @test */
    public function store_dengan_is_primary_true_me_reset_alamat_lama(): void
    {
        $user = $this->user();
        $existing = $this->makeAddress($user, true);

        $this->actingAs($user)->post(route('addresses.store'), [
            'label'          => 'Kantor',
            'address_detail' => 'Jl. Sudirman',
            'is_primary'     => true,
        ]);

        // Alamat lama tidak lagi primary
        $this->assertFalse((bool) $existing->fresh()->is_primary);

        // Alamat baru menjadi primary
        $newAddr = $user->addresses()->where('label', 'Kantor')->first();
        $this->assertTrue((bool) $newAddr->is_primary);
    }

    // ── WB-AC-06  hapus primary: alamat tersisa otomatis naik jadi primary ───

    /** @test */
    public function hapus_primary_membuat_alamat_berikutnya_naik_jadi_primary(): void
    {
        $user  = $this->user();
        $addr1 = $this->makeAddress($user, true);
        $addr2 = $this->makeAddress($user, false);

        $this->actingAs($user)->delete(route('addresses.destroy', $addr1));

        $this->assertDatabaseMissing('addresses', ['id' => $addr1->id]);
        $this->assertTrue((bool) $addr2->fresh()->is_primary);
    }

    // ── WB-AC-07  store tidak bisa tambah di atas 3 alamat ───────────────────

    /** @test */
    public function store_menolak_penambahan_jika_sudah_3_alamat(): void
    {
        $user = $this->user();
        foreach (range(1, 3) as $i) {
            $this->makeAddress($user, $i === 1);
        }

        $response = $this->actingAs($user)->post(route('addresses.store'), [
            'label'          => 'Ke-4',
            'address_detail' => 'Jl. Overflow',
        ]);

        $response->assertSessionHas('error');
        $this->assertSame(3, $user->addresses()->count());
    }

    // ── WB-AC-08  update: ganti primary ke lain, address lama bukan primary lagi

    /** @test */
    public function update_matikan_primary_menyetel_alamat_lain_sebagai_gantinya(): void
    {
        $user  = $this->user();
        $addr1 = $this->makeAddress($user, true);
        $addr2 = $this->makeAddress($user, false);

        // Matikan primary addr1, addr2 harus naik jadi primary (paling baru diupdate)
        $this->actingAs($user)->put(route('addresses.update', $addr1), [
            'label'          => 'Rumah',
            'address_detail' => 'Jl. Lama',
            'is_primary'     => false,
        ]);

        // Harus ada tepat 1 primary
        $primaryCount = $user->addresses()->where('is_primary', true)->count();
        $this->assertSame(1, $primaryCount,
            'Harus selalu ada tepat 1 alamat primary setelah update'
        );
    }
}
