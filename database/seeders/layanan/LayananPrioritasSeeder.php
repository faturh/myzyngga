<?php

namespace Database\Seeders\layanan;

use Carbon\Carbon;
use App\Models\Cabang;
use Illuminate\Database\Seeder;
use App\Models\LayananPrioritas;

class LayananPrioritasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cabang = Cabang::where('nama', 'Cabang Pusat Pertama')->first();
        $cabang2 = Cabang::withTrashed()->where('nama', 'Cabang Kedua Uhuy')->first();

        //? Cabang 1
        LayananPrioritas::create([
            'nama' => 'Reguler',
            'harga' => 0,
            'prioritas' => 1,
            'cabang_id' => $cabang->id,
        ]);
        LayananPrioritas::create([
            'nama' => 'Quick',
            'harga' => 1150,
            'prioritas' => 2,
            'cabang_id' => $cabang->id,
        ]);
        LayananPrioritas::create([
            'nama' => 'Express',
            'harga' => 1400,
            'prioritas' => 3,
            'cabang_id' => $cabang->id,
        ]);
        LayananPrioritas::create([
            'nama' => 'Kilat',
            'harga' => 3000,
            'prioritas' => 99,
            'cabang_id' => $cabang->id,
        ]);

        //? Cabang 2
        LayananPrioritas::create([
            'nama' => 'Reguler',
            'harga' => 0,
            'prioritas' => 1,
            'cabang_id' => $cabang2->id,
        ]);
        LayananPrioritas::create([
            'nama' => 'Kilat',
            'harga' => 3000,
            'prioritas' => 99,
            'cabang_id' => $cabang2->id,
        ]);
    }
}
