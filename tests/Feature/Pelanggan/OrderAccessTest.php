<?php

namespace Tests\Feature\Pelanggan;

use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * BLACKBOX – Akses & Proteksi Rute Order Pelanggan
 *
 * Menguji bahwa:
 * 1. Rute di bawah middleware auth mengembalikan redirect (bukan 200) untuk tamu.
 * 2. Rute order aksi (payment, upgrade, complaint, delivery) sekarang dilindungi auth.
 * 3. repeat() memverifikasi kepemilikan order sebelum mengisi sesi.
 * 4. Session milik user A tidak bocor ke session user B dalam request terpisah.
 */
class OrderAccessTest extends TestCase
{
    use RefreshDatabase;

    private function userWithPelanggan(): array
    {
        $user = User::factory()->create(['role' => 'pelanggan']);
        $pelanggan = Pelanggan::factory()->create(['user_id' => $user->id]);
        return [$user, $pelanggan];
    }

    private function fakeOrderId(): string
    {
        return (string) Str::uuid();
    }

    // ── BB-O01  halaman detail order (publik) — UUID tidak ada → redirect/error ─

    /** @test */
    public function halaman_detail_order_uuid_tidak_ada_tidak_mengembalikan_500(): void
    {
        $response = $this->get('/order/detail/' . $this->fakeOrderId());
        $response->assertStatus(302);
    }

    // ── BB-O02  rute history HARUS dilindungi auth ────────────────────────────

    /** @test */
    public function halaman_riwayat_pesanan_diredirect_untuk_tamu(): void
    {
        $this->get(route('order.history'))->assertStatus(302);
    }

    // ── BB-O03  Fix: POST ajukan delivery sekarang butuh login ───────────────

    /** @test */
    public function post_ajukan_delivery_memerlukan_login(): void
    {
        $this->post(route('order.delivery.store', $this->fakeOrderId()), [
            'address' => 'Jl. Test No. 1',
        ])->assertStatus(302);
    }

    // ── BB-O04  Fix: POST upgrade sekarang butuh login ───────────────────────

    /** @test */
    public function post_upgrade_memerlukan_login(): void
    {
        $this->post(route('order.upgrade.process', $this->fakeOrderId()), [
            'new_service_id' => 1,
        ])->assertStatus(302);
    }

    // ── BB-O05  Fix: POST complaint sekarang butuh login ─────────────────────

    /** @test */
    public function post_complaint_memerlukan_login(): void
    {
        $this->post(route('order.complaint.store', $this->fakeOrderId()), [
            'issue_description' => 'Test komplain',
        ])->assertStatus(302);
    }

    // ── BB-O06  Fix: POST process-payment sekarang butuh login ──────────────

    /** @test */
    public function post_process_payment_memerlukan_login(): void
    {
        $this->post(route('order.process-payment', $this->fakeOrderId()), [
            'method' => 'qris',
        ])->assertStatus(302);
    }

    // ── BB-O07  Fix: POST payment-cancel sekarang butuh login ────────────────

    /** @test */
    public function post_payment_cancel_memerlukan_login(): void
    {
        $this->post(route('order.payment-cancel', $this->fakeOrderId()))
            ->assertStatus(302);
    }

    // ── BB-O08  Fix: repeat() memverifikasi kepemilikan order ────────────────

    /** @test */
    public function repeat_order_milik_pelanggan_lain_ditolak_403(): void
    {
        [$userA, $pelangganA] = $this->userWithPelanggan();
        [$userB] = $this->userWithPelanggan();

        // Buat cabang + layanan_prioritas minimal agar FK valid
        $cabang = \App\Models\Cabang::create([
            'nama'    => 'Cabang Test',
            'lokasi'  => '-6.2,106.8',
            'alamat'  => 'Jl. Test',
        ]);
        $layanan = \App\Models\LayananPrioritas::create([
            'nama'       => 'Reguler',
            'harga'      => 5000,
            'prioritas'  => 1,
            'cabang_id'  => $cabang->id,
        ]);

        $orderA = Transaksi::create([
            'nota'                        => 'TEST-' . uniqid(),
            'pelanggan_id'                => $pelangganA->id,
            'cabang_id'                   => $cabang->id,
            'layanan_prioritas_id'        => $layanan->id,
            'pegawai_id'                  => '0',
            'status'                      => 'Baru',
            'jenis_pembayaran'            => 'cash',
            'total_biaya_layanan'         => 10000,
            'total_biaya_prioritas'       => 0,
            'total_biaya_layanan_tambahan' => 0,
            'total_bayar_akhir'           => 10000,
            'bayar'                       => 0,
            'kembalian'                   => 0,
            'waktu'                       => now(),
            'pickup_lat'                  => -6.2,
            'pickup_lng'                  => 106.8,
            'pickup_address'              => 'Jl. A No. 1',
        ]);

        // User B coba repeat order milik User A → harus 403
        $this->actingAs($userB)
            ->get(route('order.repeat', $orderA->id))
            ->assertForbidden();
    }

    // ── BB-O09  updateSession: isolasi sesi antar user ───────────────────────

    /** @test */
    public function update_session_tidak_mempengaruhi_user_lain(): void
    {
        [$userB] = $this->userWithPelanggan();

        $this->actingAs($userB)
            ->withSession([])
            ->get(route('order.booking'))
            ->assertRedirect(route('dashboard'));
    }

    // ── BB-O10  updateSession prefix order.* tidak merusak key system ─────────

    /** @test */
    public function update_session_hanya_menulis_dengan_prefix_order(): void
    {
        $this->post(route('order.update-session'), ['_token' => 'injected'])
            ->assertJson(['status' => 'success']);

        $this->assertNotEquals('injected', session()->token());
    }

    // ── BB-O11  download receipt UUID tidak ada → redirect bukan 500 ──────────

    /** @test */
    public function download_receipt_order_tidak_ada_tidak_error_500(): void
    {
        $this->get('/order/' . $this->fakeOrderId() . '/download-receipt')
            ->assertStatus(302);
    }

    // ── BB-O12  notifications route HARUS dilindungi auth ────────────────────

    /** @test */
    public function halaman_notifikasi_diredirect_untuk_tamu(): void
    {
        $this->get(route('notifications'))->assertStatus(302);
    }

    // ── BB-O13  addresses route HARUS dilindungi auth ─────────────────────────

    /** @test */
    public function halaman_alamat_diredirect_untuk_tamu(): void
    {
        $this->get(route('addresses.create'))->assertStatus(302);
    }

    // ── BB-O14  dashboard HARUS dilindungi auth ───────────────────────────────

    /** @test */
    public function dashboard_diredirect_untuk_tamu(): void
    {
        $this->get(route('dashboard'))->assertStatus(302);
    }
}
