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

        // 2. Add manual pengeluaran (Pencairan Dana)
        $this->post(route('admin.keuangan.store'), [
            'tanggal' => date('Y-m-d'),
            'tipe' => 'pengeluaran',
            'kategori' => 'Pencairan Dana',
            'nominal' => 50000,
            'keterangan' => 'Tarik dana oleh owner',
        ])->assertRedirect();

        // Verify manual record is created
        $this->assertDatabaseHas('keuangan_toko', [
            'tipe' => 'pengeluaran',
            'kategori' => 'Pencairan Dana',
            'nominal' => 50000,
        ]);

        // Balance should be initial - 50,000
        $expected1 = number_format($initialBalance - 50000, 0, ',', '.');
        $response = $this->get(route('admin.keuangan'));
        $response->assertSee('Rp ' . $expected1);

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

        $lp = \App\Models\LayananPrioritas::query()->firstOrCreate(
            ['nama' => 'Regular Smoke', 'cabang_id' => $cabang->id],
            ['deskripsi' => 'Regular service', 'harga' => 5000, 'prioritas' => 1]
        );

        $listPengerjaan = \App\Models\ListPengerjaan::create([
            'list_status_pengerjaan_id' => 1,
        ]);

        Transaksi::query()->create([
            'nota' => 'TX-TEST-KEUANGAN',
            'pelanggan_id' => $pelanggan->id,
            'pegawai_id' => $admin->id,
            'cabang_id' => $cabang->id,
            'layanan_prioritas_id' => $lp->id,
            'list_pengerjaan_id' => $listPengerjaan->id,
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

        // Balance should now be initial - 50,000 + 10,000 = initial - 40,000
        $expected3 = number_format($initialBalance - 40000, 0, ',', '.');
        $response = $this->get(route('admin.keuangan'));
        $response->assertSee('Rp ' . $expected3);

        // 6. Test delete manual record
        $record = KeuanganToko::query()
            ->where('kategori', 'Pencairan Dana')
            ->where('nominal', 50000)
            ->first();
        $this->assertNotNull($record);

        $this->delete(route('admin.keuangan.destroy', $record->id))
            ->assertRedirect();

        // Since the 50,000 expense is deleted, balance should be initial + 10,000
        $expected4 = number_format($initialBalance + 10000, 0, ',', '.');
        $response = $this->get(route('admin.keuangan'));
        $response->assertSee('Rp ' . $expected4);
    }
}
