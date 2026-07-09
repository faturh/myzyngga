<?php

namespace Database\Seeders;

use App\Models\Pelanggan;
use Illuminate\Database\Seeder;
use Database\Seeders\TransaksiSeeder;
use Database\Seeders\layanan\JenisLayananSeeder;
use Database\Seeders\layanan\JenisPakaianSeeder;
use Database\Seeders\layanan\LayananTambahanSeeder;
use Database\Seeders\layanan\LayananPrioritasSeeder;
use Database\Seeders\layanan\HargaJenisLayananSeeder;
use Database\Seeders\layanan\JenisParfumSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Pelanggan::factory(10)->create();
        $this->call([
            PermissionSeeder::class,
            //? Data Master
            CabangSeeder::class,

            //? Data Akun
            RoleSeeder::class,

            //? Data Layanan
            JenisLayananSeeder::class,
            LayananTambahanSeeder::class,
            JenisPakaianSeeder::class,
            JenisParfumSeeder::class,
            HargaJenisLayananSeeder::class,
            LayananPrioritasSeeder::class,
            \Database\Seeders\layanan\KategoriPakaianSatuanSeeder::class,

            //? Data Transaksi
            TransaksiSeeder::class,
        ]);

        // Buat Akun Pelanggan Skripsi Default untuk Postman
        $userPelanggan = \App\Models\User::updateOrCreate(
            ['email' => 'fatur.rahman.laundry@example.com'],
            [
                'name' => 'Fatur Rahman Al-Fath',
                'username' => 'faturrahman99',
                'password' => \Illuminate\Support\Facades\Hash::make('password12345'),
                'role' => 'customer',
                'slug' => 'fatur-rahman',
            ]
        );
        $userPelanggan->assignRole('customer');

        \App\Models\Pelanggan::updateOrCreate(
            ['user_id' => $userPelanggan->id],
            [
                'nama' => 'Fatur Rahman Al-Fath',
                'telepon' => '081234567890',
                'alamat' => 'Jalan Sultan Iskandar Muda No. 10, Jakarta Selatan',
                'jenis_kelamin' => 'L',
            ]
        );
    }
}
