<?php

namespace Database\Seeders\layanan;

use App\Models\JenisPakaian;
use Illuminate\Database\Seeder;

class JenisPakaianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JenisPakaian::create([
            'nama' => 'Kemeja',
        ]);
        JenisPakaian::create([
            'nama' => 'Kaos',
        ]);
        JenisPakaian::create([
            'nama' => 'Jeans',
        ]);
        JenisPakaian::create([
            'nama' => 'Jas',
        ]);
    }
}
