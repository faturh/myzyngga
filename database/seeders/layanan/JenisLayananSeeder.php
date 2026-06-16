<?php

namespace Database\Seeders\layanan;

use App\Models\Cabang;
use App\Models\JenisLayanan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class JenisLayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cabang = Cabang::where('id', 1)->first();
        $cabang2 = Cabang::withTrashed()->where('id', 2)->first();

        if ($cabang) {
            //? Cabang 1
            JenisLayanan::updateOrCreate(
                ['nama' => 'Cuci', 'cabang_id' => $cabang->id],
                ['deskripsi' => 'Layanan Cuci']
            );
            JenisLayanan::updateOrCreate(
                ['nama' => 'Setrika', 'cabang_id' => $cabang->id],
                ['deskripsi' => 'Layanan Setrika']
            );
            JenisLayanan::updateOrCreate(
                ['nama' => 'Parfum', 'cabang_id' => $cabang->id],
                ['deleted_at' => Carbon::now(), 'deskripsi' => 'Layanan Parfum']
            );
        }

        if ($cabang2) {
            //? Cabang 2
            JenisLayanan::withTrashed()->updateOrCreate(
                ['nama' => 'Cuci', 'cabang_id' => $cabang2->id],
                ['deskripsi' => 'Layanan Cuci']
            );
            JenisLayanan::withTrashed()->updateOrCreate(
                ['nama' => 'Setrika', 'cabang_id' => $cabang2->id],
                ['deskripsi' => 'Layanan Setrika']
            );
            JenisLayanan::withTrashed()->updateOrCreate(
                ['nama' => 'Parfum', 'cabang_id' => $cabang2->id],
                ['deleted_at' => Carbon::now(), 'deskripsi' => 'Layanan Parfum']
            );
        }
    }
}
