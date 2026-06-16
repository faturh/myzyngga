<?php

namespace Database\Seeders;

use App\Models\UMR;
use Illuminate\Database\Seeder;

class UMRSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UMR::updateOrCreate(
            ['tahun' => 2024, 'regional' => 'Surabaya'],
            [
                'upah' => 4725479,
                'is_used' => true,
            ]
        );
        UMR::updateOrCreate(
            ['tahun' => 2023, 'regional' => 'Surabaya'],
            [
                'upah' => 4525479,
                'is_used' => false,
            ]
        );
    }
}
