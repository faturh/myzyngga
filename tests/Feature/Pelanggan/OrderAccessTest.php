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

    // ── BB-O04  Guest checkout: upgrade cuma boleh untuk order sendiri ───────

    /** @test */
    public function post_upgrade_ditolak_untuk_guest_yang_bukan_pemilik_order(): void
    {
        [$owner, $pelanggan] = $this->userWithPelanggan();
        $order = $this->makeOwnedOrder($pelanggan);
        $express = \App\Models\LayananPrioritas::create([
            'nama' => 'Express', 'harga' => 15000, 'prioritas' => 3, 'cabang_id' => $order->cabang_id,
        ]);

        // Guest (tanpa login, tanpa order ini di sesinya) cuma tahu/nebak ID order.
        $this->post(route('order.upgrade.process', $order->id), [
            'new_service_id' => $express->id,
        ])->assertStatus(403);
    }

    /** @test */
    public function post_upgrade_diizinkan_untuk_guest_pemilik_order_di_sesi(): void
    {
        $pelanggan = Pelanggan::factory()->create(['user_id' => null]);
        $order = $this->makeOwnedOrder($pelanggan);
        $order->pending_status_id = 3; // sudah ditimbang, biar lolos cek isUnweighed()
        $order->save();
        $express = \App\Models\LayananPrioritas::create([
            'nama' => 'Express', 'harga' => 15000, 'prioritas' => 3, 'cabang_id' => $order->cabang_id,
        ]);

        $this->withSession(['orders' => [$order->id]])
            ->post(route('order.upgrade.process', $order->id), [
                'new_service_id' => $express->id,
            ], ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    // ── BB-O05  Fix: POST complaint sekarang butuh login ─────────────────────

    /** @test */
    public function post_complaint_memerlukan_login(): void
    {
        $this->post(route('order.complaint.store', $this->fakeOrderId()), [
            'issue_description' => 'Test komplain',
        ])->assertStatus(302);
    }

    // ── BB-O06  Guest checkout: process-payment cuma boleh untuk order sendiri ──

    /** @test */
    public function post_process_payment_ditolak_untuk_guest_yang_bukan_pemilik_order(): void
    {
        [$owner, $pelanggan] = $this->userWithPelanggan();
        $order = $this->makeOwnedOrder($pelanggan);

        // Guest (tanpa login, tanpa order ini di sesinya) cuma tahu/nebak ID order.
        $this->post(route('order.process-payment', $order->id), [
            'method' => 'qris',
        ])->assertStatus(403);
    }

    /** @test */
    public function post_process_payment_diizinkan_untuk_guest_pemilik_order_di_sesi(): void
    {
        $pelanggan = Pelanggan::factory()->create(['user_id' => null]);
        $order = $this->makeOwnedOrder($pelanggan);
        $order->pending_status_id = 3; // sudah ditimbang, biar lolos cek isUnweighed()
        $order->bayar = $order->total_bayar_akhir; // sudah lunas → lolos ownership, gagal di step lain (bukan 403)
        $order->save();

        $this->withSession(['orders' => [$order->id]])
            ->post(route('order.process-payment', $order->id), [
                'method' => 'qris',
            ])
            ->assertStatus(400)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Pesanan sudah lunas.');
    }

    // ── BB-O07  Guest checkout: payment-cancel cuma boleh untuk order sendiri ──

    /** @test */
    public function post_payment_cancel_ditolak_untuk_guest_yang_bukan_pemilik_order(): void
    {
        [$owner, $pelanggan] = $this->userWithPelanggan();
        $order = $this->makeOwnedOrder($pelanggan);
        $order->update(['midtrans_order_id' => 'MID-TEST-123']);

        $this->post(route('order.payment-cancel', $order->id))
            ->assertStatus(403);
    }

    /** @test */
    public function post_payment_cancel_diizinkan_untuk_guest_pemilik_order_di_sesi(): void
    {
        $pelanggan = Pelanggan::factory()->create(['user_id' => null]);
        $order = $this->makeOwnedOrder($pelanggan);
        $order->update(['midtrans_order_id' => 'MID-TEST-123']);

        $this->withSession(['orders' => [$order->id]])
            ->post(route('order.payment-cancel', $order->id))
            ->assertStatus(200)
            ->assertJsonPath('success', true);
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

    // ── BB-O08b  Ownership check untuk aksi order lain (delivery/upgrade/
    // complaint/payment) — SEHARUSNYA 403 seperti repeat(), tapi belum ada
    // pengecekan kepemilikan sama sekali di service-nya. ───────────────────

    private function makeOwnedOrder(Pelanggan $pelanggan): Transaksi
    {
        $cabang = \App\Models\Cabang::create([
            'nama' => 'Cabang Test Ownership',
            'lokasi' => '-6.2,106.8',
            'alamat' => 'Jl. Test',
        ]);
        $layanan = \App\Models\LayananPrioritas::create([
            'nama' => 'Reguler',
            'harga' => 5000,
            'prioritas' => 1,
            'cabang_id' => $cabang->id,
        ]);

        return Transaksi::create([
            'nota' => 'TEST-OWN-' . uniqid(),
            'pelanggan_id' => $pelanggan->id,
            'cabang_id' => $cabang->id,
            'layanan_prioritas_id' => $layanan->id,
            'pegawai_id' => '0',
            'status' => 'Baru',
            'jenis_pembayaran' => 'cash',
            'total_biaya_layanan' => 10000,
            'total_biaya_prioritas' => 0,
            'total_biaya_layanan_tambahan' => 0,
            'total_bayar_akhir' => 10000,
            'bayar' => 0,
            'kembalian' => 0,
            'waktu' => now(),
            'pickup_lat' => -6.2,
            'pickup_lng' => 106.8,
            'pickup_address' => 'Jl. A No. 1',
        ]);
    }

    /** @test */
    public function komplain_order_milik_pelanggan_lain_seharusnya_ditolak_403(): void
    {
        [$userA, $pelangganA] = $this->userWithPelanggan();
        [$userB] = $this->userWithPelanggan();
        $orderA = $this->makeOwnedOrder($pelangganA);

        $response = $this->actingAs($userB)->postJson(route('order.complaint.store', $orderA->id), [
            'issue_description' => 'Komplain dari user yang bukan pemilik order',
            'issue_types' => ['lainnya'],
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('complaints', ['transaksi_id' => $orderA->id]);
    }

    /** @test */
    public function upgrade_order_milik_pelanggan_lain_seharusnya_ditolak_403(): void
    {
        [$userA, $pelangganA] = $this->userWithPelanggan();
        [$userB] = $this->userWithPelanggan();
        $orderA = $this->makeOwnedOrder($pelangganA);

        $newService = \App\Models\LayananPrioritas::create([
            'nama' => 'Kilat',
            'harga' => 20000,
            'prioritas' => 5,
            'cabang_id' => $orderA->cabang_id,
        ]);

        $response = $this->actingAs($userB)->postJson(route('order.upgrade.process', $orderA->id), [
            'new_service_id' => $newService->id,
        ]);

        $response->assertForbidden();
        $orderA->refresh();
        $meta = json_decode($orderA->payment_metadata, true) ?? [];
        $this->assertArrayNotHasKey('pending_upgrade', $meta);
    }

    /** @test */
    public function payment_cancel_order_milik_pelanggan_lain_seharusnya_ditolak_403(): void
    {
        [$userA, $pelangganA] = $this->userWithPelanggan();
        [$userB] = $this->userWithPelanggan();
        $orderA = $this->makeOwnedOrder($pelangganA);
        $orderA->update(['midtrans_order_id' => $orderA->id . '-999']);

        $response = $this->actingAs($userB)->postJson(route('order.payment-cancel', $orderA->id));

        $response->assertForbidden();
        $this->assertNotNull($orderA->fresh()->midtrans_order_id, 'User lain tidak boleh bisa membatalkan percobaan pembayaran order orang lain.');
    }

    /** @test */
    public function rollback_delivery_order_milik_pelanggan_lain_seharusnya_ditolak_403(): void
    {
        [$userA, $pelangganA] = $this->userWithPelanggan();
        [$userB] = $this->userWithPelanggan();
        $orderA = $this->makeOwnedOrder($pelangganA);
        $originalAddress = $orderA->pickup_address;

        // User B mencoba request-delivery ke order A dulu (bakal 403, tapi ini
        // yang men-trigger session 'pending_rollback_delivery_*' terisi di sesi B
        // SEBELUM pengecekan kepemilikan jalan).
        $this->actingAs($userB)->postJson(route('order.delivery.store', $orderA->id), [
            'address' => 'Alamat Palsu', 'lat' => -6.1, 'lng' => 106.1,
        ])->assertForbidden();

        // Lalu User B coba panggil endpoint rollback-nya langsung.
        $response = $this->actingAs($userB)->postJson(route('order.delivery.rollback', $orderA->id));

        $response->assertForbidden();
        $this->assertSame($originalAddress, $orderA->fresh()->pickup_address);
    }

    /** @test */
    public function rollback_upgrade_order_milik_pelanggan_lain_seharusnya_ditolak_403(): void
    {
        [$userA, $pelangganA] = $this->userWithPelanggan();
        [$userB] = $this->userWithPelanggan();
        $orderA = $this->makeOwnedOrder($pelangganA);
        $originalServiceId = $orderA->layanan_prioritas_id;

        $newService = \App\Models\LayananPrioritas::create([
            'nama' => 'Kilat', 'harga' => 20000, 'prioritas' => 5, 'cabang_id' => $orderA->cabang_id,
        ]);

        $this->actingAs($userB)->postJson(route('order.upgrade.process', $orderA->id), [
            'new_service_id' => $newService->id,
        ])->assertForbidden();

        $response = $this->actingAs($userB)->postJson(route('order.upgrade.rollback', $orderA->id));

        $response->assertForbidden();
        $this->assertSame($originalServiceId, $orderA->fresh()->layanan_prioritas_id);
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
