<?php

namespace Database\Seeders;

use App\Models\Cabang;
use Illuminate\Database\Seeder;

class CabangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cabang::updateOrCreate(
            ['nama' => 'Cabang Pusat Pertama'],
            [
                'lokasi' => 'Surabaya',
                'alamat' => 'Kelurahan Simokerto, Surabaya',
            ]
        );

        $cabang2 = Cabang::withTrashed()->where('nama', 'Cabang Kedua Uhuy')->first();
        if ($cabang2) {
            $cabang2->restore();
            $cabang2->update([
                'lokasi' => 'Surabaya',
                'alamat' => 'Kelurahan Simokerto, Surabaya',
                'deleted_at' => '2024-05-15 18:14:23',
            ]);
        } else {
            Cabang::create([
                'nama' => 'Cabang Kedua Uhuy',
                'lokasi' => 'Surabaya',
                'alamat' => 'Kelurahan Simokerto, Surabaya',
                'deleted_at' => '2024-05-15 18:14:23',
            ]);
        }

        Cabang::updateOrCreate(
            ['nama' => 'Cabang Ketiga Spontan'],
            [
                'lokasi' => 'Surabaya',
                'alamat' => 'Kelurahan Simokerto, Surabaya',
            ]
        );

        $cabang4 = Cabang::withTrashed()->where('nama', 'Cabang Keempat Luar')->first();
        if ($cabang4) {
            $cabang4->restore();
            $cabang4->update([
                'lokasi' => 'Surabaya',
                'alamat' => 'Kelurahan Simokerto, Surabaya',
                'deleted_at' => '2024-06-15 18:14:23',
            ]);
        } else {
            Cabang::create([
                'nama' => 'Cabang Keempat Luar',
                'lokasi' => 'Surabaya',
                'alamat' => 'Kelurahan Simokerto, Surabaya',
                'deleted_at' => '2024-06-15 18:14:23',
            ]);
        }
    }
}
