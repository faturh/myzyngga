<?php

namespace Database\Seeders\layanan;

use App\Models\KategoriPakaianSatuan;
use Illuminate\Database\Seeder;

class KategoriPakaianSatuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['nama_pakaian' => 'Selimut', 'harga' => 10000],
            ['nama_pakaian' => 'Bed Cover', 'harga' => 15000],
            ['nama_pakaian' => 'Sprei', 'harga' => 8000],
            ['nama_pakaian' => 'Boneka', 'harga' => 12000],
            ['nama_pakaian' => 'Sepatu', 'harga' => 15000],
            ['nama_pakaian' => 'Tas', 'harga' => 20000],
            ['nama_pakaian' => 'Helm/Topi', 'harga' => 10000],
            ['nama_pakaian' => 'Jeans', 'harga' => 5000],
            ['nama_pakaian' => 'Kemeja', 'harga' => 5000],
            ['nama_pakaian' => 'Jaket / Sweeter', 'harga' => 8000],
            ['nama_pakaian' => 'Mukena / Sarung', 'harga' => 6000],
        ];

        foreach ($items as $item) {
            KategoriPakaianSatuan::updateOrCreate(
                ['nama_pakaian' => $item['nama_pakaian']],
                ['harga' => $item['harga']]
            );
        }
    }
}
