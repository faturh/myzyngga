<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Transaksi;
use App\Models\Timbangan;
use App\Models\Pelanggan;
use App\Models\Cabang;
use App\Models\LayananPrioritas;
use App\Models\ListPengerjaan;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

class GajiKaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds for Gaji Karyawan testing.
     */
    public function run(): void
    {
        // 1. Ensure Roles Exist
        Role::firstOrCreate(['name' => 'pegawai_laundry', 'guard_name' => 'web']);

        // 2. Fetch or Create Cabang
        $cabang = Cabang::query()->first();
        if (!$cabang) {
            $cabang = Cabang::query()->create([
                'nama' => 'Cabang Pusat',
                'slug' => 'cabang-pusat',
                'lokasi' => 'Bandung',
                'alamat' => 'Jalan Ganesha No. 10',
            ]);
        }

        // 3. Create Employees with unique slugs and salary rates
        $employeeA = User::updateOrCreate(
            ['email' => 'budi.pegawai@example.com'],
            [
                'name' => 'Budi Gaji Seeder',
                'username' => 'budi_laundry_gaji',
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                'role' => 'pegawai_laundry',
                'slug' => 'budi-laundry-gaji',
                'cabang_id' => $cabang->id,
                'gaji' => 3000, // Rp 3000 / kg
                'bank' => 'BCA',
                'nomor_rekening' => '0012345678',
            ]
        );
        $employeeA->assignRole('pegawai_laundry');

        $employeeB = User::updateOrCreate(
            ['email' => 'siti.pegawai@example.com'],
            [
                'name' => 'Siti Gaji Seeder',
                'username' => 'siti_laundry_gaji',
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                'role' => 'pegawai_laundry',
                'slug' => 'siti-laundry-gaji',
                'cabang_id' => $cabang->id,
                'gaji' => 4000, // Rp 4000 / kg
                'bank' => 'Mandiri',
                'nomor_rekening' => '88123456789',
            ]
        );
        $employeeB->assignRole('pegawai_laundry');

        // 4. Fetch or Create Pelanggan
        $pelanggan = Pelanggan::query()->first();
        if (!$pelanggan) {
            $customerUser = User::factory()->create([
                'username' => 'cust-gaji-seeder',
                'email' => 'cust-gaji-seeder@example.com',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]);
            $pelanggan = Pelanggan::query()->create([
                'user_id' => $customerUser->id,
                'nama' => 'Pelanggan Gaji Seeder',
                'telepon' => '081234567890',
                'jenis_kelamin' => 'L',
            ]);
        }

        // 5. Fetch or Create Layanan Prioritas
        $lp = LayananPrioritas::query()->first();
        if (!$lp) {
            $lp = LayananPrioritas::query()->create([
                'nama' => 'Reguler',
                'harga' => 5000,
                'prioritas' => 1,
                'cabang_id' => $cabang->id,
            ]);
        }

        // 6. Create Completed Transactions for Current Month
        $this->createCompletedTransactionWithTimbangan(
            'TX-GAJI-CURR-1',
            $pelanggan->id,
            $employeeA->id,
            $cabang->id,
            $lp->id,
            now()->startOfMonth()->addDays(2)->toDateTimeString(), // Current Month
            5.0 // 5kg
        );

        $this->createCompletedTransactionWithTimbangan(
            'TX-GAJI-CURR-2',
            $pelanggan->id,
            $employeeB->id,
            $cabang->id,
            $lp->id,
            now()->startOfMonth()->addDays(5)->toDateTimeString(), // Current Month
            8.0 // 8kg
        );

        // 7. Create Completed Transactions for Previous Month (e.g. 35 days ago)
        $this->createCompletedTransactionWithTimbangan(
            'TX-GAJI-PREV-1',
            $pelanggan->id,
            $employeeA->id,
            $cabang->id,
            $lp->id,
            now()->subMonth()->startOfMonth()->addDays(2)->toDateTimeString(), // Previous Month
            4.0 // 4kg
        );

        $this->createCompletedTransactionWithTimbangan(
            'TX-GAJI-PREV-2',
            $pelanggan->id,
            $employeeB->id,
            $cabang->id,
            $lp->id,
            now()->subMonth()->startOfMonth()->addDays(10)->toDateTimeString(), // Previous Month
            6.5 // 6.5kg
        );
    }

    /**
     * Helper to create a completed transaction with timbangan weight.
     */
    private function createCompletedTransactionWithTimbangan(
        string $nota,
        int $pelangganId,
        int $pegawaiId,
        int $cabangId,
        int $layananPrioritasId,
        string $waktu,
        float $weight
    ): void {
        $listPengerjaan = ListPengerjaan::create([
            'list_status_pengerjaan_id' => 5, // Selesai
        ]);

        $tx = Transaksi::query()->create([
            'nota' => $nota,
            'pelanggan_id' => $pelangganId,
            'pegawai_id' => (string) $pegawaiId,
            'cabang_id' => $cabangId,
            'layanan_prioritas_id' => $layananPrioritasId,
            'list_pengerjaan_id' => $listPengerjaan->id,
            'waktu' => $waktu,
            'pickup_address' => 'Jl. Testing Seeder No. 8',
            'pickup_lat' => -6.2,
            'pickup_lng' => 106.8,
            'total_biaya_layanan' => 5000 * $weight,
            'total_biaya_prioritas' => 0,
            'total_biaya_layanan_tambahan' => 0,
            'total_bayar_akhir' => 5000 * $weight,
            'jenis_pembayaran' => 'cash',
            'payment_status' => 'paid',
            'bayar' => 5000 * $weight,
            'kembalian' => 0,
            'gaji_dibayar' => 0,
        ]);

        // Explicitly set text status
        $tx->status = 'Pesanan Selesai';
        $tx->save();

        Timbangan::create([
            'transaksi_id' => $tx->id,
            'nota' => $nota,
            'actual_weight' => $weight,
            'minimum_weight' => 3.0,
            'price_per_kg' => 5000,
            'charged_weight' => $weight,
            'total_price' => 5000 * $weight,
        ]);

        // Override database timestamps to match $waktu
        \Illuminate\Support\Facades\DB::table('transaksi')
            ->where('id', $tx->id)
            ->update([
                'waktu' => $waktu,
                'created_at' => $waktu,
                'updated_at' => $waktu,
            ]);
    }
}
