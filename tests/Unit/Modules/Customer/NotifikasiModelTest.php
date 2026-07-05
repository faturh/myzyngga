<?php

namespace Tests\Unit\Modules\Customer;

use App\Models\Notifikasi;
use App\Models\NotifikasiRead;
use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * WHITEBOX – Model Notifikasi
 *
 * Menguji logika internal method isReadBy(), relasi reads(),
 * dan konstanta jenis notifikasi.
 */
class NotifikasiModelTest extends TestCase
{
    use RefreshDatabase;

    private function pelanggan(): Pelanggan
    {
        $user = User::factory()->create();
        return Pelanggan::factory()->create(['user_id' => $user->id]);
    }

    private function broadcast(string $pesan = 'broadcast'): Notifikasi
    {
        return Notifikasi::create([
            'jenis'        => Notifikasi::JENIS_JAM_OPERASIONAL,
            'pesan'        => $pesan,
            'pelanggan_id' => null,
        ]);
    }

    private function personal(int $pelangganId, bool $isRead = false): Notifikasi
    {
        return Notifikasi::create([
            'jenis'        => Notifikasi::JENIS_JAM_OPERASIONAL,
            'pesan'        => 'personal',
            'pelanggan_id' => $pelangganId,
            'is_read'      => $isRead,
        ]);
    }

    // ── WB-NM-01  konstanta jenis terdefinisi ─────────────────────────────────

    /** @test */
    public function semua_konstanta_jenis_terdefinisi(): void
    {
        $this->assertSame('status',           Notifikasi::JENIS_STATUS);
        $this->assertSame('kurir_jemput',     Notifikasi::JENIS_KURIR_JEMPUT);
        $this->assertSame('kurir_antar',      Notifikasi::JENIS_KURIR_ANTAR);
        $this->assertSame('selesai',          Notifikasi::JENIS_SELESAI);
        $this->assertSame('jam_operasional',  Notifikasi::JENIS_JAM_OPERASIONAL);
    }

    // ── WB-NM-02  isReadBy broadcast — belum ada record ─────────────────────

    /** @test */
    public function isReadBy_broadcast_false_sebelum_ada_read_record(): void
    {
        $notif = $this->broadcast();
        $pelanggan = $this->pelanggan();

        $this->assertFalse($notif->isReadBy($pelanggan->id));
    }

    // ── WB-NM-03  isReadBy broadcast — setelah record dibuat ─────────────────

    /** @test */
    public function isReadBy_broadcast_true_setelah_notifikasi_reads_dibuat(): void
    {
        $notif = $this->broadcast();
        $pelanggan = $this->pelanggan();

        NotifikasiRead::create([
            'notifikasi_id' => $notif->id,
            'pelanggan_id'  => $pelanggan->id,
            'read_at'       => now(),
        ]);

        $this->assertTrue($notif->isReadBy($pelanggan->id));
    }

    // ── WB-NM-04  isReadBy broadcast — pelanggan A baca, B belum ─────────────

    /** @test */
    public function isReadBy_broadcast_false_untuk_pelanggan_yang_belum_baca(): void
    {
        $notif = $this->broadcast();
        $pelangganA = $this->pelanggan();
        $pelangganB = $this->pelanggan();

        NotifikasiRead::create([
            'notifikasi_id' => $notif->id,
            'pelanggan_id'  => $pelangganA->id,
            'read_at'       => now(),
        ]);

        $this->assertFalse($notif->isReadBy($pelangganB->id));
    }

    // ── WB-NM-05  isReadBy personal — pemilik, sudah dibaca ──────────────────

    /** @test */
    public function isReadBy_personal_true_untuk_pemilik_yang_sudah_baca(): void
    {
        $pelanggan = $this->pelanggan();
        $notif = $this->personal($pelanggan->id, isRead: true);

        $this->assertTrue($notif->isReadBy($pelanggan->id));
    }

    // ── WB-NM-06  isReadBy personal — pemilik, belum dibaca ──────────────────

    /** @test */
    public function isReadBy_personal_false_untuk_pemilik_yang_belum_baca(): void
    {
        $pelanggan = $this->pelanggan();
        $notif = $this->personal($pelanggan->id, isRead: false);

        $this->assertFalse($notif->isReadBy($pelanggan->id));
    }

    // ── WB-NM-07  isReadBy personal — pelanggan lain HARUS false (bug lama) ──

    /** @test */
    public function isReadBy_personal_false_untuk_pelanggan_bukan_pemilik(): void
    {
        $pelangganA = $this->pelanggan();
        $pelangganB = $this->pelanggan();

        // Notif milik A yang sudah dibaca
        $notif = $this->personal($pelangganA->id, isRead: true);

        // B menanyakan apakah dia sudah baca — harus FALSE, bukan meneruskan is_read milik A
        $this->assertFalse($notif->isReadBy($pelangganB->id));
    }

    // ── WB-NM-08  relasi reads() ──────────────────────────────────────────────

    /** @test */
    public function relasi_reads_mengambil_semua_notifikasi_reads(): void
    {
        $notif = $this->broadcast();
        $p1 = $this->pelanggan();
        $p2 = $this->pelanggan();

        NotifikasiRead::create(['notifikasi_id' => $notif->id, 'pelanggan_id' => $p1->id, 'read_at' => now()]);
        NotifikasiRead::create(['notifikasi_id' => $notif->id, 'pelanggan_id' => $p2->id, 'read_at' => now()]);

        $this->assertCount(2, $notif->reads);
    }

    // ── WB-NM-09  unique constraint mencegah duplikat read ───────────────────

    /** @test */
    public function unique_constraint_mencegah_duplikat_notifikasi_reads(): void
    {
        $notif = $this->broadcast();
        $pelanggan = $this->pelanggan();

        NotifikasiRead::create(['notifikasi_id' => $notif->id, 'pelanggan_id' => $pelanggan->id]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        NotifikasiRead::create(['notifikasi_id' => $notif->id, 'pelanggan_id' => $pelanggan->id]);
    }

    // ── WB-NM-10  firstOrCreate aman terhadap duplikat (idempotent) ───────────

    /** @test */
    public function firstOrCreate_tidak_melempar_exception_jika_sudah_ada(): void
    {
        $notif = $this->broadcast();
        $pelanggan = $this->pelanggan();

        NotifikasiRead::firstOrCreate(
            ['notifikasi_id' => $notif->id, 'pelanggan_id' => $pelanggan->id],
            ['read_at' => now()]
        );
        // Panggil kedua kali — tidak boleh exception
        $result = NotifikasiRead::firstOrCreate(
            ['notifikasi_id' => $notif->id, 'pelanggan_id' => $pelanggan->id],
            ['read_at' => now()]
        );

        $this->assertDatabaseCount('notifikasi_reads', 1);
        $this->assertInstanceOf(NotifikasiRead::class, $result);
    }

    // ── WB-NM-11  cast is_read ke boolean ────────────────────────────────────

    /** @test */
    public function kolom_is_read_di_cast_ke_boolean(): void
    {
        // Sertakan is_read eksplisit karena default DB tidak otomatis di-load
        // ke model Eloquent setelah create() tanpa fresh()
        $notif = Notifikasi::create([
            'jenis'   => Notifikasi::JENIS_STATUS,
            'pesan'   => 'test',
            'is_read' => false,
        ]);

        $this->assertIsBool($notif->is_read);
        $this->assertFalse($notif->is_read);

        $notif->update(['is_read' => true]);
        $this->assertIsBool($notif->fresh()->is_read);
        $this->assertTrue($notif->fresh()->is_read);
    }

    // ── WB-NM-12  relasi pelanggan dan transaksi ada di model ────────────────

    /** @test */
    public function model_memiliki_relasi_belongsTo_pelanggan_dan_transaksi(): void
    {
        $notif = new Notifikasi();
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
            $notif->pelanggan()
        );
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
            $notif->transaksi()
        );
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\HasMany::class,
            $notif->reads()
        );
    }
}
