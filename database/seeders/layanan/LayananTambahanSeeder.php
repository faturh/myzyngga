<?php

namespace Database\Seeders\layanan;

use Carbon\Carbon;
use App\Models\Cabang;
use App\Models\LayananTambahan;
use Illuminate\Database\Seeder;

class LayananTambahanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cabang = Cabang::where('nama', 'Cabang Pusat Pertama')->first();
        $cabang2 = Cabang::withTrashed()->where('nama', 'Cabang Kedua Uhuy')->first();

        //? Cabang 1
        LayananTambahan::create([
            'nama' => 'Antar',
            'harga' => 10000,
            'cabang_id' => $cabang->id,
        ]);
        LayananTambahan::create([
            'nama' => 'Jemput',
            'harga' => 10000,
            'cabang_id' => $cabang->id,
        ]);
        LayananTambahan::create([
            'nama' => 'Keamanan Ganda',
            'harga' => 5000,
            'cabang_id' => $cabang->id,
            'deleted_at' => Carbon::now(),
        ]);

        //? Cabang 2
        LayananTambahan::create([
            'nama' => 'Antar',
            'harga' => 10000,
            'cabang_id' => $cabang2->id,
        ]);
        LayananTambahan::create([
            'nama' => 'Jemput',
            'harga' => 10000,
            'cabang_id' => $cabang2->id,
        ]);
        LayananTambahan::create([
            'nama' => 'Keamanan Ganda',
            'harga' => 5000,
            'cabang_id' => $cabang2->id,
            'deleted_at' => Carbon::now(),
        ]);
    }
}
