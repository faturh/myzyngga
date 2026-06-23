<?php

namespace Database\Seeders\layanan;

use App\Models\JenisParfum;
use Illuminate\Database\Seeder;

class JenisParfumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parfums = [
            ['nama' => 'Vanilla'],
            ['nama' => 'Lavender'],
            ['nama' => 'Sakura'],
            ['nama' => 'Ocean Fresh'],
            ['nama' => 'Bubblegum'],
            ['nama' => 'Baby Powder'],
        ];

        foreach ($parfums as $parfum) {
            JenisParfum::firstOrCreate($parfum);
        }
    }
}
