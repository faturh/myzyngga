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
            KeuanganTokoSeeder::class,
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

        $pelanggan = \App\Models\Pelanggan::updateOrCreate(
            ['user_id' => $userPelanggan->id],
            [
                'nama' => 'Fatur Rahman Al-Fath',
                'telepon' => '081234567890',
                'alamat' => 'Jalan Sultan Iskandar Muda No. 10, Jakarta Selatan',
                'jenis_kelamin' => 'L',
            ]
        );

        // Buat Notifikasi Test Personal (milik fatur)
        \App\Models\Notifikasi::create([
            'pelanggan_id' => $pelanggan->id,
            'jenis' => 'status',
            'pesan' => 'Pesanan laundry Anda dengan nota ZYG-TEST-123 sedang dalam proses pengerjaan.',
            'is_read' => false,
        ]);

        // Buat Notifikasi Test Personal Milik Pelanggan Lain (untuk test IDOR)
        $pelangganLain = \App\Models\Pelanggan::where('id', '!=', $pelanggan->id)->first();
        if ($pelangganLain) {
            \App\Models\Notifikasi::create([
                'pelanggan_id' => $pelangganLain->id,
                'jenis' => 'status',
                'pesan' => 'Pesanan laundry milik orang lain (jangan sampai bisa dibaca/diakses oleh pelanggan lain).',
                'is_read' => false,
            ]);
        }

        // Buat Notifikasi Test Broadcast
        \App\Models\Notifikasi::create([
            'pelanggan_id' => null,
            'jenis' => 'jam_operasional',
            'pesan' => 'Pengumuman: Zyngga Laundry buka 24 jam selama libur lebaran!',
            'is_read' => false,
        ]);
    }
}
