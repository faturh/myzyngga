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
            HargaJenisLayananSeeder::class,
            LayananPrioritasSeeder::class,

            //? Data Transaksi
            TransaksiSeeder::class,
        ]);
    }
}
