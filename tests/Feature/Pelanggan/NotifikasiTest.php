<?php

namespace Tests\Feature\Pelanggan;

use App\Models\Notifikasi;
use App\Models\NotifikasiRead;
use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * BLACKBOX – Fitur Notifikasi Pelanggan
 *
 * Menguji perilaku endpoint notifikasi dari sudut pandang pengguna:
 * tampilan halaman, isolasi antar pelanggan, dan proteksi akses.
 */
class NotifikasiTest extends TestCase
{
    use RefreshDatabase;

    // ── helpers ──────────────────────────────────────────────────────────────

    private function makeUserWithPelanggan(): array
    {
        $user = User::factory()->create(['role' => 'pelanggan']);
        $pelanggan = Pelanggan::factory()->create(['user_id' => $user->id]);
        return [$user, $pelanggan];
    }

    private function broadcast(string $pesan = 'Jam operasional berubah.'): Notifikasi
    {
        return Notifikasi::create([
            'jenis'        => Notifikasi::JENIS_JAM_OPERASIONAL,
            'pesan'        => $pesan,
            'pelanggan_id' => null,
        ]);
    }

    private function personalNotif(int $pelangganId, string $pesan = 'Notif personal.'): Notifikasi
    {
        return Notifikasi::create([
            'jenis'        => Notifikasi::JENIS_JAM_OPERASIONAL,
            'pesan'        => $pesan,
            'pelanggan_id' => $pelangganId,
            'is_read'      => false,
        ]);
    }

    // ── BB-01  akses tanpa login ──────────────────────────────────────────────

    /** @test */
    public function halaman_notifikasi_redirect_untuk_tamu(): void
    {
        // Middleware auth mengembalikan redirect (konfigurasi app tidak selalu ke /login)
        $this->get(route('notifications'))->assertStatus(302);
    }

    /** @test */
    public function mark_as_read_redirect_untuk_tamu(): void
    {
        $notif = $this->broadcast();
        $this->post(route('notifications.read', $notif))->assertStatus(302);
    }

    // ── BB-02  halaman muncul saat login ─────────────────────────────────────

    /** @test */
    public function halaman_notifikasi_200_saat_login(): void
    {
        [$user] = $this->makeUserWithPelanggan();
        $this->actingAs($user)->get(route('notifications'))->assertOk();
    }

    // ── BB-03  broadcast muncul sebelum dibaca ────────────────────────────────

    /** @test */
    public function broadcast_muncul_di_halaman_sebelum_dibaca(): void
    {
        [$user, $pelanggan] = $this->makeUserWithPelanggan();
        $notif = $this->broadcast('Libur lebaran 2 hari.');

        $this->actingAs($user)
            ->get(route('notifications'))
            ->assertOk()
            ->assertSee('Libur lebaran 2 hari.');
    }

    // ── BB-04  broadcast hilang setelah dibaca oleh pelanggan yg sama ─────────

    /** @test */
    public function broadcast_hilang_setelah_pelanggan_mark_as_read(): void
    {
        [$user, $pelanggan] = $this->makeUserWithPelanggan();
        $notif = $this->broadcast('Libur 1 hari.');

        $this->actingAs($user)->post(route('notifications.read', $notif));

        $this->actingAs($user)
            ->get(route('notifications'))
            ->assertOk()
            ->assertDontSee('Libur 1 hari.');
    }

    // ── BB-05  isolasi broadcast: setelah A baca, B masih lihat ──────────────

    /** @test */
    public function broadcast_masih_muncul_untuk_pelanggan_lain_setelah_satu_membaca(): void
    {
        [$userA, $pelangganA] = $this->makeUserWithPelanggan();
        [$userB, $pelangganB] = $this->makeUserWithPelanggan();
        $notif = $this->broadcast('Jam buka berubah.');

        // A baca
        $this->actingAs($userA)->post(route('notifications.read', $notif));

        // B masih lihat
        $this->actingAs($userB)
            ->get(route('notifications'))
            ->assertOk()
            ->assertSee('Jam buka berubah.');
    }

    // ── BB-06  notif personal TIDAK muncul untuk pelanggan lain ──────────────

    /** @test */
    public function notif_personal_tidak_muncul_untuk_pelanggan_lain(): void
    {
        [$userA, $pelangganA] = $this->makeUserWithPelanggan();
        [$userB, $pelangganB] = $this->makeUserWithPelanggan();

        $this->personalNotif($pelangganA->id, 'Pesan khusus untuk A.');

        $this->actingAs($userB)
            ->get(route('notifications'))
            ->assertOk()
            ->assertDontSee('Pesan khusus untuk A.');
    }

    // ── BB-07  IDOR: pelanggan tidak boleh mark-as-read notif personal orang lain

    /** @test */
    public function mark_as_read_notif_personal_milik_orang_lain_ditolak_403(): void
    {
        [$userA, $pelangganA] = $this->makeUserWithPelanggan();
        [$userB]              = $this->makeUserWithPelanggan();

        $notifA = $this->personalNotif($pelangganA->id);

        // B coba tandai notifikasi milik A
        $this->actingAs($userB)
            ->post(route('notifications.read', $notifA))
            ->assertForbidden();

        // is_read tetap false
        $this->assertFalse($notifA->fresh()->is_read);
    }

    // ── BB-08  broadcast mark-as-read hanya catat ke notifikasi_reads ─────────

    /** @test */
    public function mark_as_read_broadcast_menulis_ke_notifikasi_reads_bukan_is_read(): void
    {
        [$user, $pelanggan] = $this->makeUserWithPelanggan();
        $notif = $this->broadcast();

        $this->actingAs($user)->post(route('notifications.read', $notif));

        // Baris di pivot ada
        $this->assertDatabaseHas('notifikasi_reads', [
            'notifikasi_id' => $notif->id,
            'pelanggan_id'  => $pelanggan->id,
        ]);

        // Kolom is_read di tabel notifikasi TIDAK berubah
        $this->assertFalse($notif->fresh()->is_read);
    }

    // ── BB-09  mark-as-read idempotent (tidak error jika dipanggil 2×) ────────

    /** @test */
    public function mark_as_read_broadcast_idempotent(): void
    {
        [$user, $pelanggan] = $this->makeUserWithPelanggan();
        $notif = $this->broadcast();

        $this->actingAs($user)->post(route('notifications.read', $notif));
        // Panggil kedua kali – unique constraint tidak boleh throw exception
        // markAsRead() mengembalikan back() → 302, bukan 2xx
        $this->actingAs($user)->post(route('notifications.read', $notif))->assertStatus(302);

        $this->assertDatabaseCount('notifikasi_reads', 1);
    }

    // ── BB-10  user tanpa profil pelanggan tidak lihat broadcast (tidak error) -

    /** @test */
    public function user_tanpa_profil_pelanggan_tidak_error_di_halaman_notifikasi(): void
    {
        // User terdaftar tapi BELUM punya profil pelanggan
        $user = User::factory()->create(['role' => 'pelanggan']);
        $this->broadcast('Pesan broadcast.');

        $response = $this->actingAs($user)->get(route('notifications'));
        $response->assertOk();
        // Broadcast tidak ditampilkan karena pelanggan null
        $response->assertDontSee('Pesan broadcast.');
    }

    // ── BB-11  notif personal hilang setelah di-mark-as-read ─────────────────

    /** @test */
    public function notif_personal_hilang_setelah_mark_as_read(): void
    {
        [$user, $pelanggan] = $this->makeUserWithPelanggan();
        $notif = $this->personalNotif($pelanggan->id, 'Pesan personal X.');

        $this->actingAs($user)->post(route('notifications.read', $notif));

        $this->actingAs($user)
            ->get(route('notifications'))
            ->assertOk()
            ->assertDontSee('Pesan personal X.');

        // is_read berubah jadi true di tabel notifikasi
        $this->assertTrue($notif->fresh()->is_read);
    }

    // ── BB-12  cascade delete: hapus notifikasi → notifikasi_reads ikut hapus -

    /** @test */
    public function hapus_notifikasi_cascade_ke_notifikasi_reads(): void
    {
        [$user, $pelanggan] = $this->makeUserWithPelanggan();
        $notif = $this->broadcast();
        NotifikasiRead::create([
            'notifikasi_id' => $notif->id,
            'pelanggan_id'  => $pelanggan->id,
            'read_at'       => now(),
        ]);

        $notif->delete();

        $this->assertDatabaseMissing('notifikasi_reads', ['notifikasi_id' => $notif->id]);
    }
}
