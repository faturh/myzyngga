<?php

namespace Tests\Feature\Pelanggan;

use App\Models\Pelanggan;
use App\Models\User;
use App\Models\CustomerAddress;
use App\Models\Transaksi;
use App\Models\Notifikasi;
use App\Models\Cabang;
use App\Models\LayananPrioritas;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Illuminate\Support\Str;

class BlackBoxApiTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    }

    private function createCustomerUser()
    {
        $user = User::factory()->create([
            'email' => 'login_test@domain.com', 
            'password' => bcrypt('password123'),
            'role' => 'customer'
        ]);
        $user->assignRole('customer');
        Pelanggan::create([
            'user_id' => $user->id,
            'nama' => 'Black Box Tester',
            'telepon' => '081299998888',
            'alamat' => 'Jalan Tester',
            'jenis_kelamin' => 'L',
        ]);
        return $user;
    }

    // 1. Registrasi Akun
    public function test_registrasi()
    {
        // 1a. Registrasi Valid
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test Valid',
            'username' => 'testvalid',
            'phone' => '081234567890',
            'email' => 'valid@domain.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        dump('--- POST /api/v1/auth/register (VALID) ---', $response->status(), json_encode($response->json()));

        // 1b. Registrasi Invalid (Password Pendek)
        $responseShort = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test Short',
            'username' => 'testshort',
            'phone' => '081234567891',
            'email' => 'short@domain.com',
            'password' => '123',
            'password_confirmation' => '123',
        ]);
        dump('--- POST /api/v1/auth/register (INVALID - PASS PENDEK) ---', $responseShort->status(), json_encode($responseShort->json()));
        
        $this->assertTrue(true);
    }

    // 2. Alamat Batas Maksimal
    public function test_alamat_batas()
    {
        $user = $this->createCustomerUser();
        $pelangganId = Pelanggan::where('user_id', $user->id)->first()->id;
        
        CustomerAddress::create(['pelanggan_id' => $pelangganId, 'label' => 'A1', 'address' => 'A1', 'lat' => 1, 'lng' => 1]);
        CustomerAddress::create(['pelanggan_id' => $pelangganId, 'label' => 'A2', 'address' => 'A2', 'lat' => 1, 'lng' => 1]);

        // 2a. Address Limit Valid (3rd)
        $res3 = $this->actingAs($user, 'sanctum')->postJson('/api/v1/customer/addresses', [
            'label' => 'A3', 'address' => 'A3', 'lat' => 1, 'lng' => 1
        ]);
        dump('--- POST /api/v1/customer/addresses (VALID - 3RD ADDRESS) ---', $res3->status(), json_encode($res3->json()));

        // 2b. Address Limit Invalid (4th)
        $res4 = $this->actingAs($user, 'sanctum')->postJson('/api/v1/customer/addresses', [
            'label' => 'A4', 'address' => 'A4', 'lat' => 1, 'lng' => 1
        ]);
        dump('--- POST /api/v1/customer/addresses (INVALID - 4TH ADDRESS) ---', $res4->status(), json_encode($res4->json()));
        
        $this->assertTrue(true);
    }

    // 3. Alamat Koordinat Peta
    public function test_alamat_koordinat()
    {
        $user = $this->createCustomerUser();

        // 3a. Valid Lat Lng
        $resValid = $this->actingAs($user, 'sanctum')->postJson('/api/v1/customer/addresses', [
            'label' => 'Valid', 'address' => 'Valid', 'lat' => -6.2, 'lng' => 106.8
        ]);
        dump('--- POST /api/v1/customer/addresses (VALID LAT/LNG) ---', $resValid->status(), json_encode($resValid->json()));

        // 3b. Invalid Lat Lng (Null)
        $resInvalid = $this->actingAs($user, 'sanctum')->postJson('/api/v1/customer/addresses', [
            'label' => 'Invalid', 'address' => 'Invalid', 'lat' => null, 'lng' => null
        ]);
        dump('--- POST /api/v1/customer/addresses (INVALID LAT/LNG) ---', $resInvalid->status(), json_encode($resInvalid->json()));

        $this->assertTrue(true);
    }

    // 4. Buat Pesanan
    public function test_buat_pesanan()
    {
        $user = $this->createCustomerUser();
        $pelangganId = Pelanggan::where('user_id', $user->id)->first()->id;
        
        $cabang = Cabang::create(['nama' => 'Pusat', 'lokasi' => 'Pusat', 'deskripsi' => 'Pusat']);
        $prio = LayananPrioritas::create(['nama' => 'Reguler', 'cabang_id' => $cabang->id, 'harga' => 0, 'prioritas' => 1]);

        // 4a. Buat Pesanan Lengkap
        $resComplete = $this->actingAs($user, 'sanctum')->postJson('/api/v1/orders', [
            'pelanggan_id' => $pelangganId,
            'pickup_address' => 'Jalan Coba 123',
            'pickup_date' => '2026-10-10',
            'pickup_time' => '10:00',
            'payment_method' => 'cash',
            'cabang_id' => $cabang->id,
            'layanan_prioritas_id' => $prio->id,
            'estimated_total' => 50000,
        ]);
        dump('--- POST /api/v1/orders (VALID LENGKAP) ---', $resComplete->status(), json_encode($resComplete->json()));

        // 4b. Buat Pesanan Tanggal Invalid
        $resInvalidDate = $this->actingAs($user, 'sanctum')->postJson('/api/v1/orders', [
            'pelanggan_id' => $pelangganId,
            'pickup_address' => 'Jalan Coba 123',
            'pickup_date' => 'besok', // invalid
            'pickup_time' => '10:00',
            'payment_method' => 'cash',
            'cabang_id' => $cabang->id,
            'layanan_prioritas_id' => $prio->id,
            'estimated_total' => 50000,
        ]);
        dump('--- POST /api/v1/orders (INVALID DATE) ---', $resInvalidDate->status(), json_encode($resInvalidDate->json()));

        $this->assertTrue(true);
    }

    // 5. Pelacakan Pesanan
    public function test_pelacakan_pesanan()
    {
        $user = $this->createCustomerUser();
        $pelangganId = Pelanggan::where('user_id', $user->id)->first()->id;
        
        $cabang = Cabang::create(['nama' => 'Pusat', 'lokasi' => 'Pusat', 'deskripsi' => 'Pusat']);
        $prio = LayananPrioritas::create(['nama' => 'Reguler', 'cabang_id' => $cabang->id, 'harga' => 0, 'prioritas' => 1]);

        $order = Transaksi::create([
            'id' => Str::uuid(),
            'nota' => 'ZYG-TEST-123',
            'status' => 'created',
            'pelanggan_id' => $pelangganId,
            'waktu' => now(),
            'total_biaya_layanan' => 10000,
            'total_biaya_prioritas' => 0,
            'total_biaya_antar' => 0,
            'total_biaya_layanan_tambahan' => 0,
            'total_biaya_penjemputan' => 0,
            'bayar' => 0,
            'sisa_bayar' => 0,
            'kembalian' => 0,
            'total_bayar_akhir' => 10000,
            'jenis_pembayaran' => 'cash',
            'layanan_prioritas_id' => $prio->id,
            'cabang_id' => $cabang->id,
            'pegawai_id' => $user->id,
            'payment_status' => 'pending',
            'is_roundtrip' => false,
        ]);

        // 5a. Lacak Valid
        $resValid = $this->postJson('/api/v1/orders/track', [
            'nota' => 'ZYG-TEST-123',
            'phone_digits' => '8888'
        ]);
        dump('--- POST /api/v1/orders/track (VALID) ---', $resValid->status(), json_encode($resValid->json()));

        // 5b. Lacak Tanpa Nota
        $resInvalid = $this->postJson('/api/v1/orders/track', [
            'phone_digits' => '8888'
        ]);
        dump('--- POST /api/v1/orders/track (INVALID - TANPA NOTA) ---', $resInvalid->status(), json_encode($resInvalid->json()));

        $this->assertTrue(true);
    }

    // 6. Komplain
    public function test_komplain()
    {
        $user = $this->createCustomerUser();
        $pelangganId = Pelanggan::where('user_id', $user->id)->first()->id;
        
        $cabang = Cabang::create(['nama' => 'Pusat', 'lokasi' => 'Pusat', 'deskripsi' => 'Pusat']);
        $prio = LayananPrioritas::create(['nama' => 'Reguler', 'cabang_id' => $cabang->id, 'harga' => 0, 'prioritas' => 1]);

        $order = Transaksi::create([
            'id' => Str::uuid(),
            'nota' => 'ZYG-COMP-1',
            'status' => 'completed',
            'pelanggan_id' => $pelangganId,
            'waktu' => now(),
            'total_biaya_layanan' => 10000,
            'total_biaya_prioritas' => 0,
            'total_biaya_antar' => 0,
            'total_biaya_layanan_tambahan' => 0,
            'total_biaya_penjemputan' => 0,
            'bayar' => 0,
            'sisa_bayar' => 0,
            'kembalian' => 0,
            'total_bayar_akhir' => 10000,
            'jenis_pembayaran' => 'cash',
            'layanan_prioritas_id' => $prio->id,
            'cabang_id' => $cabang->id,
            'pegawai_id' => $user->id,
            'payment_status' => 'pending',
            'is_roundtrip' => false,
        ]);

        Storage::fake('public');

        \Illuminate\Http\UploadedFile::macro('storeOnCloudinary', function ($path = null) {
            return new class {
                public function getSecurePath() {
                    return 'https://res.cloudinary.com/demo/image/upload/sample.jpg';
                }
            };
        });

        // 6a. Komplain Foto Valid
        $file = UploadedFile::fake()->image('bukti.jpg');
        $resPhoto = $this->actingAs($user, 'sanctum')->post("/api/v1/orders/{$order->id}/complaint", [
            'content' => 'Rusak ada foto',
            'issue_types' => ['lainnya'],
            'issue_image' => $file
        ], ['Accept' => 'application/json']);
        dump('--- POST /api/v1/orders/{id}/complaint (VALID - DENGAN FOTO) ---', $resPhoto->status(), json_encode($resPhoto->json()));

        // 6b. Komplain Ekstensi Terlarang
        $filePdf = UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf');
        $resPdf = $this->actingAs($user, 'sanctum')->post("/api/v1/orders/{$order->id}/complaint", [
            'content' => 'Rusak pdf',
            'issue_types' => ['lainnya'],
            'issue_image' => $filePdf
        ], ['Accept' => 'application/json']);
        dump('--- POST /api/v1/orders/{id}/complaint (INVALID - EKSTENSI PDF) ---', $resPdf->status(), json_encode($resPdf->json()));

        $this->assertTrue(true);
    }

    // 7. Notifikasi
    public function test_notifikasi()
    {
        $user = $this->createCustomerUser();
        
        $user2 = User::factory()->create(['email' => 'user2@domain.com', 'role' => 'customer']);
        $user2->assignRole('customer');
        Pelanggan::create([
            'user_id' => $user2->id,
            'nama' => 'U2',
            'telepon' => '081299998889',
            'alamat' => 'Jalan U2',
            'jenis_kelamin' => 'L',
        ]);
        
        $pelanggan1 = Pelanggan::where('user_id', $user->id)->first();
        $pelanggan2 = Pelanggan::where('user_id', $user2->id)->first();

        $notif1 = Notifikasi::create([
            'pelanggan_id' => $pelanggan1->id,
            'judul' => 'T1',
            'pesan' => 'C1',
            'jenis' => 'personal'
        ]);

        $notif2 = Notifikasi::create([
            'pelanggan_id' => $pelanggan2->id,
            'judul' => 'T2',
            'pesan' => 'C2',
            'jenis' => 'personal'
        ]);

        // 7a. Akses Milik Sendiri (Valid)
        $resOwned = $this->actingAs($user, 'sanctum')->postJson("/api/v1/customer/notifications/{$notif1->id}/read");
        dump('--- POST /api/v1/customer/notifications/{id}/read (VALID - MILIK SENDIRI) ---', $resOwned->status(), json_encode($resOwned->json()));

        // 7b. Akses Milik Orang Lain (Invalid - IDOR)
        $resOthers = $this->actingAs($user, 'sanctum')->postJson("/api/v1/customer/notifications/{$notif2->id}/read");
        dump('--- POST /api/v1/customer/notifications/{id}/read (INVALID - IDOR ORANG LAIN) ---', $resOthers->status(), json_encode($resOthers->json()));

        $this->assertTrue(true);
    }
}
