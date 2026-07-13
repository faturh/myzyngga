<?php

namespace Tests\Feature\Operator\Gaji;

use App\Models\Cabang;
use App\Models\HistoryGaji;
use App\Models\KeuanganToko;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GajiKaryawanTest extends TestCase
{
    use DatabaseTransactions;

    private function createAdminUser(): User
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'pegawai_laundry', 'guard_name' => 'web']);

        $cabang = Cabang::query()->create([
            'nama' => 'Cabang Test Gaji',
            'slug' => 'cabang-test-gaji',
            'lokasi' => 'Bandung',
            'alamat' => 'Jalan Gaji 1',
        ]);

        $user = User::factory()->create([
            'username' => 'admin-test',
            'slug' => 'admin-test',
            'email' => 'admin-test@example.com',
            'password' => 'password',
            'role' => 'admin',
            'cabang_id' => $cabang->id,
            'email_verified_at' => now(),
        ]);

        $user->assignRole('admin');

        return $user;
    }

    public function test_can_manage_bank_details_for_employee(): void
    {
        $admin = $this->createAdminUser();
        $this->actingAs($admin);

        // 1. Create employee with bank details
        $response = $this->post(route('user.store'), [
            'username' => 'pegawai-bank-test',
            'email' => 'pegawai-bank-test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'nama' => 'Pegawai Bank Test',
            'telepon' => '08987654321',
            'role' => 'pegawai_laundry',
            'gaji' => 2000,
            'bank' => 'BCA',
            'nomor_rekening' => '1234567890',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'username' => 'pegawai-bank-test',
            'bank' => 'BCA',
            'nomor_rekening' => '1234567890',
            'gaji' => 2000,
        ]);

        // 2. Update employee bank details
        $employee = User::where('username', 'pegawai-bank-test')->firstOrFail();
        $response = $this->post(route('user.update', $employee->slug), [
            'username' => 'pegawai-bank-test',
            'email' => 'pegawai-bank-test@example.com',
            'nama' => 'Pegawai Bank Test Updated',
            'telepon' => '08987654321',
            'role' => 'pegawai_laundry',
            'gaji' => 2500,
            'bank' => 'Mandiri',
            'nomor_rekening' => '0987654321',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $employee->id,
            'bank' => 'Mandiri',
            'nomor_rekening' => '0987654321',
            'gaji' => 2500,
        ]);
    }

    public function test_can_calculate_salary_only_for_unpaid_transactions_and_record_history(): void
    {
        $admin = $this->createAdminUser();
        $this->actingAs($admin);

        $employee = User::factory()->create([
            'username' => 'pegawai-laundry-test',
            'slug' => 'pegawai-laundry-test',
            'email' => 'pegawai-laundry-test@example.com',
            'password' => 'password',
            'role' => 'pegawai_laundry',
            'cabang_id' => $admin->cabang_id,
            'gaji' => 3000, // Rp 3000 / kg
            'bank' => 'BNI',
            'nomor_rekening' => '88889999',
        ]);
        $employee->assignRole('pegawai_laundry');

        // Create completed transaction
        $customerUser = User::factory()->create([
            'username' => 'cust-test-gaji',
            'slug' => 'cust-test-gaji',
            'email' => 'cust-test-gaji@example.com',
            'password' => 'password',
        ]);
        $pelanggan = \App\Models\Pelanggan::query()->create([
            'user_id' => $customerUser->id,
            'nama' => 'Pelanggan Gaji',
            'telepon' => '081234567890',
            'jenis_kelamin' => 'L',
        ]);

        $lp = \App\Models\LayananPrioritas::query()->firstOrCreate(
            ['nama' => 'Regular', 'cabang_id' => $admin->cabang_id],
            ['deskripsi' => 'Regular service', 'harga' => 5000, 'prioritas' => 1]
        );

        $listPengerjaan = \App\Models\ListPengerjaan::create([
            'list_status_pengerjaan_id' => 5, // Selesai
        ]);

        $tx = Transaksi::query()->create([
            'nota' => 'TX-GAJI-1',
            'pelanggan_id' => $pelanggan->id,
            'pegawai_id' => (string) $employee->id,
            'cabang_id' => $admin->cabang_id,
            'layanan_prioritas_id' => $lp->id,
            'list_pengerjaan_id' => $listPengerjaan->id,
            'waktu' => now()->toDateTimeString(),
            'pickup_address' => 'Test Address',
            'pickup_lat' => -6.2,
            'pickup_lng' => 106.8,
            'total_biaya_layanan' => 25000,
            'total_biaya_prioritas' => 0,
            'total_biaya_layanan_tambahan' => 0,
            'total_bayar_akhir' => 25000,
            'jenis_pembayaran' => 'cash',
            'payment_status' => 'paid',
            'bayar' => 25000,
            'kembalian' => 0,
            'gaji_dibayar' => 0, // Unpaid
        ]);

        // Explicitly override the status in the DB bypassing saving booted override if any, or update it
        $tx->status = 'Pesanan Selesai';
        $tx->save();

        \App\Models\Timbangan::create([
            'transaksi_id' => $tx->id,
            'nota' => 'TX-GAJI-1',
            'actual_weight' => 5.0, // 5kg
            'minimum_weight' => 3.0,
            'price_per_kg' => 5000,
            'charged_weight' => 5.0,
            'total_price' => 25000,
        ]);

        // 1. Assert calculated salary in dashboard (using JSON response)
        $response = $this->getJson(route('admin.gaji-karyawan'));
        $response->assertOk();
        $response->assertJsonFragment([
            'id' => $employee->id,
            'total_gaji' => 15000,
            'total_kg' => 5,
        ]);

        // 2. Pay salary
        $response = $this->post(route('admin.gaji-karyawan.bayar'), [
            'pegawai_id' => $employee->id,
            'nominal' => 15000,
            'tanggal' => date('Y-m-d'),
            'keterangan' => 'Bayar gaji test',
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->toDateString(),
        ]);

        $response->assertRedirect();

        // 3. Verify database updates
        $this->assertDatabaseHas('history_gaji', [
            'pegawai_id' => $employee->id,
            'nominal' => 15000,
            'bank' => 'BNI',
            'nomor_rekening' => '88889999',
            'keterangan' => 'Bayar gaji test',
        ]);

        $this->assertDatabaseHas('keuangan_toko', [
            'tipe' => 'pengeluaran',
            'kategori' => 'Gaji',
            'nominal' => 15000,
        ]);

        $tx->refresh();
        $this->assertEquals(1, $tx->gaji_dibayar); // Now marked as paid

        // 4. Assert calculated salary is reset to 0
        $response = $this->getJson(route('admin.gaji-karyawan'));
        $response->assertOk();
        $response->assertJsonFragment([
            'id' => $employee->id,
            'total_gaji' => 0,
            'total_kg' => 0,
        ]);
    }

    public function test_can_add_kas_manual_pemasukan_and_pengeluaran(): void
    {
        $admin = $this->createAdminUser();
        $this->actingAs($admin);

        // 1. Post manual pemasukan
        $this->post(route('admin.keuangan.store'), [
            'tanggal' => date('Y-m-d'),
            'tipe' => 'pemasukan',
            'kategori' => 'Kas Masuk Lain',
            'nominal' => 75000,
            'keterangan' => 'Manual income test',
        ])->assertRedirect();

        $this->assertDatabaseHas('keuangan_toko', [
            'tipe' => 'pemasukan',
            'kategori' => 'Kas Masuk Lain',
            'nominal' => 75000,
            'keterangan' => 'Manual income test',
        ]);

        // 2. Post manual pengeluaran
        $this->post(route('admin.keuangan.store'), [
            'tanggal' => date('Y-m-d'),
            'tipe' => 'pengeluaran',
            'kategori' => 'Konsumsi Pegawai',
            'nominal' => 20000,
            'keterangan' => 'Manual expense test',
        ])->assertRedirect();

        $this->assertDatabaseHas('keuangan_toko', [
            'tipe' => 'pengeluaran',
            'kategori' => 'Konsumsi Pegawai',
            'nominal' => 20000,
            'keterangan' => 'Manual expense test',
        ]);
    }
}
