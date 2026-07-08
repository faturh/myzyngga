<?php

namespace Tests\Feature\Operator\Keuangan;

use App\Models\Cabang;
use App\Models\LayananPrioritas;
use App\Models\Transaksi;
use App\Models\User;
use App\Models\Pelanggan;
use App\Models\KeuanganToko;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PostmanFlowsTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private User $manager;
    private User $pegawai;
    private Cabang $cabang;
    private LayananPrioritas $layanan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cabang = Cabang::query()->create([
            'nama' => 'Cabang Test Postman',
            'slug' => 'cabang-test-postman',
            'lokasi' => 'Bandung',
            'alamat' => 'Jalan Ganesha No 10',
        ]);

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'manajer_laundry', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'pegawai_laundry', 'guard_name' => 'web']);

        $this->admin = User::factory()->create([
            'username' => 'admin-test-flow',
            'slug' => 'admin-test-flow',
            'email' => 'admin-test-flow@example.com',
            'password' => 'password',
            'role' => 'admin',
            'cabang_id' => $this->cabang->id,
            'email_verified_at' => now(),
        ]);
        $this->admin->assignRole('admin');

        $this->manager = User::factory()->create([
            'username' => 'manager-test-flow',
            'slug' => 'manager-test-flow',
            'email' => 'manager-test-flow@example.com',
            'password' => 'password',
            'role' => 'manajer_laundry',
            'cabang_id' => $this->cabang->id,
            'email_verified_at' => now(),
        ]);
        $this->manager->assignRole('manajer_laundry');

        $this->pegawai = User::factory()->create([
            'username' => 'pegawai-test-flow',
            'slug' => 'pegawai-test-flow',
            'email' => 'pegawai-test-flow@example.com',
            'password' => 'password',
            'role' => 'pegawai_laundry',
            'cabang_id' => $this->cabang->id,
            'email_verified_at' => now(),
        ]);
        $this->pegawai->assignRole('pegawai_laundry');

        // Create standard prioritas
        $this->layanan = LayananPrioritas::firstOrCreate(
            ['id' => 1],
            [
                'nama' => 'Reguler',
                'harga' => 4850,
                'prioritas' => 1,
                'cabang_id' => $this->cabang->id
            ]
        );
    }

    public function test_post_manual_order_flow_as_json(): void
    {
        $this->actingAs($this->admin);

        // 1. Tambah Pesanan Manual (New Customer)
        $response = $this->postJson(route('admin.riwayat-pesanan.store'), [
            'pelanggan_option' => 'new',
            'customer_name' => 'John Walkin Test',
            'customer_phone' => '081234567890',
            'customer_address' => 'Dago 123',
            'layanan_prioritas_id' => $this->layanan->id,
            'jenis_pembayaran' => 'cash',
            'payment_status' => 'paid',
            'pegawai_id' => $this->pegawai->id,
            'parfum' => 'Sakura',
            'catatan' => 'Cuci rapi',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => ['id', 'nota', 'status'],
            'message',
            'status'
        ]);

        $transaksiId = $response->json('data.id');
        $transaksiNota = $response->json('data.nota');

        // 2. Proses Timbangan (using legacy "berat" input parameter)
        $responseProses = $this->postJson(route('admin.riwayat-pesanan.proses', $transaksiId), [
            'berat' => 3.5,
            'jenis_pembayaran' => 'cash',
        ]);

        $responseProses->assertStatus(200);
        $responseProses->assertJsonPath('status', 200);
        $responseProses->assertJsonStructure([
            'data',
            'message',
            'status'
        ]);

        // 3. Mulai Pengerjaan
        $responseKerjakan = $this->postJson(route('admin.riwayat-pesanan.kerjakan', $transaksiId), [
            'pegawai_id' => $this->pegawai->id,
            'items' => [
                ['nama_item' => 'Kaos', 'qty' => 5],
                ['nama_item' => 'Celana Jeans', 'qty' => 2],
            ]
        ]);

        $responseKerjakan->assertStatus(200);
        $responseKerjakan->assertJsonPath('status', 200);

        // 4. Selesaikan Pengerjaan
        $responseSelesai = $this->postJson(route('admin.riwayat-pesanan.selesaikan', $transaksiId));
        $responseSelesai->assertStatus(200);
        $responseSelesai->assertJsonPath('status', 200);

        // 5. Batalkan Transaksi
        $responseBatal = $this->postJson(route('admin.riwayat-pesanan.batal', $transaksiId));
        $responseBatal->assertStatus(200);
        $responseBatal->assertJsonPath('status', 200);
    }

    public function test_financial_manual_flow_as_json(): void
    {
        $this->actingAs($this->admin);

        // 1. Store manual cash record
        $responseStore = $this->postJson(route('admin.keuangan.store'), [
            'tanggal' => date('Y-m-d'),
            'tipe' => 'pengeluaran',
            'kategori' => 'Pencairan Dana',
            'nominal' => 200000,
            'keterangan' => 'Pencairan dana owner',
            'cabang_id' => $this->cabang->id,
        ]);

        $responseStore->assertStatus(200);
        $responseStore->assertJsonPath('status', 200);
        $responseStore->assertJsonStructure(['data' => ['id', 'nominal'], 'message', 'status']);
        
        $recordId = $responseStore->json('data.id');

        // 2. Delete manual cash record
        $responseDelete = $this->deleteJson(route('admin.keuangan.destroy', $recordId));
        $responseDelete->assertStatus(200);
        $responseDelete->assertJsonPath('status', 200);
    }

    public function test_user_management_flow_as_json(): void
    {
        $this->actingAs($this->admin);

        // 1. Simpan User
        $responseSimpan = $this->postJson(route('user.store'), [
            'username' => 'karyawan_postman_test',
            'email' => 'karyawan_postman_test@zyngga.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'cabang_id' => $this->cabang->id,
            'nama' => 'Karyawan Postman',
            'telepon' => '08129999999',
            'gaji' => 2000,
            'role' => 'pegawai_laundry',
        ]);

        $responseSimpan->assertStatus(200);
        $responseSimpan->assertJsonPath('status', 200);
        
        $userId = $responseSimpan->json('data.id');
        $userSlug = $responseSimpan->json('data.slug');

        // 2. Ubah User
        $responseUbah = $this->postJson(route('user.update', $userSlug), [
            'username' => 'karyawan_postman_edit',
            'email' => 'karyawan_postman_edit@zyngga.com',
            'cabang_id' => $this->cabang->id,
            'nama' => 'Karyawan Postman Edit',
            'telepon' => '08129999988',
            'gaji' => 2500,
            'role' => 'pegawai_laundry',
        ]);

        $responseUbah->assertStatus(200);
        $responseUbah->assertJsonPath('status', 200);
        $editedSlug = $responseUbah->json('data.slug');

        // 3. Ubah Password User
        $responsePassword = $this->postJson(route('user.update.password', $editedSlug), [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);
        $responsePassword->assertStatus(200);
        $responsePassword->assertJsonPath('status', 200);

        // 4. Hapus User
        $responseHapus = $this->postJson(route('user.delete'), [
            'slug' => $editedSlug,
        ]);
        $responseHapus->assertStatus(200);
        $responseHapus->assertJsonPath('status', 200);
    }

    public function test_employee_salary_actions_flow_as_json(): void
    {
        $this->actingAs($this->admin);

        // 1. Update Tarif Gaji
        $responseRate = $this->postJson(route('admin.gaji-karyawan.update-tarif'), [
            'pegawai_id' => $this->pegawai->id,
            'gaji' => 4500,
        ]);
        $responseRate->assertStatus(200);
        $responseRate->assertJsonPath('status', 200);
        
        $this->assertEquals(4500, $this->pegawai->refresh()->gaji);

        // 2. Bayar Gaji (Record Keuangan)
        $responsePay = $this->postJson(route('admin.gaji-karyawan.bayar'), [
            'pegawai_id' => $this->pegawai->id,
            'nominal' => 150000,
            'tanggal' => now()->toDateString(),
            'keterangan' => 'Pembayaran Gaji Test',
        ]);
        $responsePay->assertStatus(200);
        $responsePay->assertJsonPath('status', 200);

        // Verify KeuanganToko entry exists
        $this->assertDatabaseHas('keuangan_toko', [
            'tipe' => 'pengeluaran',
            'kategori' => 'Gaji',
            'nominal' => 150000.0,
            'keterangan' => 'Pembayaran Gaji Test',
            'cabang_id' => $this->cabang->id,
        ]);
    }
}
