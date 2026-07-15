<?php

namespace Tests\Feature\Pelanggan;

use App\Models\Cabang;
use App\Models\Complaint;
use App\Models\LayananPrioritas;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ComplaintApiTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
    }

    private function setupOrderData(): array
    {
        $user = User::factory()->create(['role' => 'customer']);
        $user->assignRole('customer');
        
        $pelanggan = Pelanggan::create([
            'user_id' => $user->id,
            'nama' => 'Test Customer',
            'telepon' => '081234567890',
            'alamat' => 'Jalan Sultan Iskandar Muda No. 10',
            'jenis_kelamin' => 'L',
        ]);

        $admin = User::factory()->create([
            'username' => 'admin-test-comp',
            'email' => 'admin-test-comp@example.com',
            'role' => 'admin',
        ]);
        $admin->assignRole('admin');

        $cabang = Cabang::create([
            'nama' => 'Cabang Test Comp',
            'alamat' => 'Alamat Cabang',
            'lokasi' => 'Jakarta',
        ]);

        $layananPrioritas = LayananPrioritas::create([
            'nama' => 'Reguler',
            'harga' => 0,
            'prioritas' => 1,
            'cabang_id' => $cabang->id,
        ]);

        $order = Transaksi::create([
            'pelanggan_id' => $pelanggan->id,
            'cabang_id' => $cabang->id,
            'layanan_prioritas_id' => $layananPrioritas->id,
            'pickup_address' => 'Jalan Alamat',
            'pickup_date' => now()->toDateString(),
            'pickup_time' => '10:00',
            'waktu' => now(),
            'status' => 'Baru',
            'payment_status' => 'pending',
            'jenis_pembayaran' => 'cash',
            'total_bayar_akhir' => 10000,
            'total_biaya_layanan' => 10000,
            'total_biaya_prioritas' => 0,
            'total_biaya_layanan_tambahan' => 0,
            'bayar' => 0,
            'kembalian' => 0,
            'nota' => 'ZYG-COMP-TEST',
            'pegawai_id' => (string) $admin->id,
        ]);

        return [$user, $pelanggan, $order];
    }

    public function test_ajukan_komplain_berhasil_dengan_status_pending(): void
    {
        [$user, $pelanggan, $order] = $this->setupOrderData();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/orders/{$order->id}/complaint", [
                'issue_description' => 'Pakaian luntur parah',
                'issue_types' => ['pakaian_rusak'],
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('complaints', [
            'transaksi_id' => $order->id,
            'pelanggan_id' => $pelanggan->id,
            'status' => 'pending',
        ]);
    }

    public function test_ajukan_komplain_dengan_satu_foto_menyimpan_gambar_ke_image_path(): void
    {
        [$user, $pelanggan, $order] = $this->setupOrderData();

        \Illuminate\Support\Facades\Storage::fake('cloudinary');
        $file = \Illuminate\Http\UploadedFile::fake()->image('bukti.jpg');

        // Validasi backend mensyaratkan array: 'issue_image' => 'nullable|array|max:3'
        $response = $this->actingAs($user, 'sanctum')
            ->post("/api/v1/orders/{$order->id}/complaint", [
                'issue_description' => 'Pakaian robek, ada fotonya',
                'issue_types' => ['pakaian_rusak'],
                'issue_image' => [$file],
            ], ['Accept' => 'application/json']);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $complaint = Complaint::where('transaksi_id', $order->id)->where('pelanggan_id', $pelanggan->id)->firstOrFail();

        $this->assertNotNull(
            $complaint->image_path,
            'Foto komplain yang diunggah pelanggan harus tersimpan di image_path, bukan hilang diam-diam.'
        );
        $this->assertNotSame(
            '[]',
            $complaint->image_path,
            'image_path berisi array kosong — foto yang diunggah pelanggan tidak benar-benar tersimpan.'
        );
    }

    public function test_ajukan_komplain_dengan_issue_image_sebagai_file_tunggal_tetap_diterima(): void
    {
        [$user, $pelanggan, $order] = $this->setupOrderData();

        \Illuminate\Support\Facades\Storage::fake('cloudinary');
        $file = \Illuminate\Http\UploadedFile::fake()->image('bukti-single.jpg');

        // Klien yang tidak tahu field ini harus dibungkus array (kirim single file biasa)
        // seharusnya tetap diterima, bukan langsung 422.
        $response = $this->actingAs($user, 'sanctum')
            ->post("/api/v1/orders/{$order->id}/complaint", [
                'issue_description' => 'Pakaian robek, foto tunggal',
                'issue_types' => ['pakaian_rusak'],
                'issue_image' => $file,
            ], ['Accept' => 'application/json']);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $complaint = Complaint::where('transaksi_id', $order->id)->where('pelanggan_id', $pelanggan->id)->firstOrFail();
        $this->assertNotNull($complaint->image_path);
        $this->assertNotSame('[]', $complaint->image_path);
    }

    public function test_riwayat_komplain_mengembalikan_array_lintas_pesanan(): void
    {
        [$user, $pelanggan, $order] = $this->setupOrderData();

        Complaint::create([
            'transaksi_id' => $order->id,
            'pelanggan_id' => $pelanggan->id,
            'content' => 'Baju robek',
            'status' => 'pending',
            'issue_types' => ['pakaian_rusak'],
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/customer/complaints');

        $response->assertStatus(200)
            ->assertJsonPath('errors', null)
            ->assertJsonStructure([
                'data' => [
                    'complaints' => [
                        '*' => [
                            'id',
                            'transaksi_id',
                            'status',
                            'content',
                        ]
                    ]
                ]
            ]);
    }
}
