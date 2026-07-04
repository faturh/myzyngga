<?php

namespace Tests\Feature\Operator\Keuangan;

use App\Models\Cabang;
use App\Models\KeuanganToko;
use App\Models\Transaksi;
use App\Models\User;
use App\Modules\Transaksi\Domain\Repositories\KeuanganRepositoryInterface;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KeuanganFlowTest extends TestCase
{
    use DatabaseTransactions;

    private function createAdminUser(): User
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $user = User::factory()->create([
            'username' => 'admin-keuangan',
            'slug' => 'admin-keuangan',
            'email' => 'admin-keuangan@example.com',
            'password' => 'password',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $user->assignRole('admin');

        return $user;
    }

    public function test_admin_can_access_keuangan_page_and_calculate_balance(): void
    {
        $admin = $this->createAdminUser();
        $this->actingAs($admin);

        $cabang = Cabang::query()->create([
            'nama' => 'Cabang Keuangan Test',
            'slug' => 'cabang-keuangan-test',
            'lokasi' => 'Jakarta',
            'alamat' => 'Jalan Keuangan Test 1',
        ]);

        // Resolve dynamic initial balance based on existing seed data
        $repo = app(KeuanganRepositoryInterface::class);
        $initialBalance = $repo->getStoreBalance(null);
        $initialFormatted = number_format($initialBalance, 0, ',', '.');

        // 1. Initial balance check
        $response = $this->get(route('admin.keuangan'));
        $response->assertOk();
        $response->assertSee('Rp ' . $initialFormatted);

        // 2. Add manual pemasukan
        $this->post(route('admin.keuangan.store'), [
            'tanggal' => date('Y-m-d'),
            'tipe' => 'pemasukan',
            'kategori' => 'Petty Cash',
            'nominal' => 150000,
            'keterangan' => 'Suntikan modal awal kasir',
        ])->assertRedirect();

        // Verify manual record is created
        $this->assertDatabaseHas('keuangan_toko', [
            'tipe' => 'pemasukan',
            'nominal' => 150000,
        ]);

        // Balance should be initial + 150,000
        $expected1 = number_format($initialBalance + 150000, 0, ',', '.');
        $response = $this->get(route('admin.keuangan'));
        $response->assertSee('Rp ' . $expected1);

        // 4. Add manual pengeluaran
        $this->post(route('admin.keuangan.store'), [
            'tanggal' => date('Y-m-d'),
            'tipe' => 'pengeluaran',
            'kategori' => 'Pembelian Deterjen',
            'nominal' => 30000,
            'keterangan' => 'Beli Deterjen Sakura 1 Liter',
        ])->assertRedirect();

        // Balance should now be initial + 150,000 - 30,000 = initial + 120,000
        $expected2 = number_format($initialBalance + 120000, 0, ',', '.');
        $response = $this->get(route('admin.keuangan'));
        $response->assertSee('Rp ' . $expected2);

        // 5. Simulate paid transaction
        $customerUser = User::factory()->create([
            'username' => 'customer-test-k',
            'slug' => 'customer-test-k',
            'email' => 'customer-test-k@example.com',
            'password' => 'password',
        ]);
        $pelanggan = \App\Models\Pelanggan::query()->create([
            'user_id' => $customerUser->id,
            'nama' => 'Pelanggan Test Keuangan',
            'telepon' => '081234567890',
            'jenis_kelamin' => 'L',
        ]);

        Transaksi::query()->create([
            'nota' => 'TX-TEST-KEUANGAN',
            'pelanggan_id' => $pelanggan->id,
            'pegawai_id' => $admin->id,
            'cabang_id' => $cabang->id,
            'layanan_prioritas_id' => 1, // regular
            'list_pengerjaan_id' => 1,
            'waktu' => now()->toDateTimeString(),
            'pickup_address' => 'Test Address',
            'pickup_lat' => -6.2,
            'pickup_lng' => 106.8,
            'total_biaya_layanan' => 10000,
            'total_biaya_prioritas' => 0,
            'total_biaya_layanan_tambahan' => 0,
            'total_bayar_akhir' => 10000,
            'jenis_pembayaran' => 'cash',
            'payment_status' => 'paid',
            'bayar' => 10000,
            'kembalian' => 0,
        ]);

        // Balance should now be initial + 120,000 + 10,000 = initial + 130,000
        $expected3 = number_format($initialBalance + 130000, 0, ',', '.');
        $response = $this->get(route('admin.keuangan'));
        $response->assertSee('Rp ' . $expected3);

        // 6. Test delete manual record
        $record = KeuanganToko::query()
            ->where('tipe', 'pengeluaran')
            ->where('nominal', 30000)
            ->first();
        $this->assertNotNull($record);

        $this->delete(route('admin.keuangan.destroy', $record->id))
            ->assertRedirect();

        // Since the 30,000 expense is deleted, balance should be initial + 150,000 + 10,000 = initial + 160,000
        $expected4 = number_format($initialBalance + 160000, 0, ',', '.');
        $response = $this->get(route('admin.keuangan'));
        $response->assertSee('Rp ' . $expected4);
    }
}
