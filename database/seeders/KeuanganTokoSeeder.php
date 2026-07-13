<?php

namespace Database\Seeders;

use App\Models\Cabang;
use App\Models\KeuanganToko;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class KeuanganTokoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cabangs = Cabang::all();
        if ($cabangs->isEmpty()) {
            $cabangId = 1;
        } else {
            $cabangId = $cabangs->first()->id;
        }

        $now = Carbon::now('Asia/Jakarta');

        $data = [
            // --- Hari Ini (Daily Filter Today) ---
            [
                'tanggal' => $now->toDateString(),
                'tipe' => 'pemasukan',
                'kategori' => 'Pendapatan Laundry',
                'nominal' => 350000,
                'keterangan' => 'Pemasukan laundry kiloan hari ini',
                'cabang_id' => $cabangId,
            ],
            [
                'tanggal' => $now->toDateString(),
                'tipe' => 'pengeluaran',
                'kategori' => 'Listrik',
                'nominal' => 85000,
                'keterangan' => 'Pembayaran token listrik mesin cuci',
                'cabang_id' => $cabangId,
            ],
            [
                'tanggal' => $now->toDateString(),
                'tipe' => 'pemasukan',
                'kategori' => 'Kas Masuk',
                'nominal' => 100000,
                'keterangan' => 'Setoran kas awal hari ini',
                'cabang_id' => $cabangId,
            ],

            // --- Kemarin / Minggu Ini ---
            [
                'tanggal' => $now->copy()->subDays(1)->toDateString(),
                'tipe' => 'pemasukan',
                'kategori' => 'Pendapatan Laundry',
                'nominal' => 450000,
                'keterangan' => 'Pemasukan laundry kemarin',
                'cabang_id' => $cabangId,
            ],
            [
                'tanggal' => $now->copy()->subDays(2)->toDateString(),
                'tipe' => 'pengeluaran',
                'kategori' => 'Peralatan',
                'nominal' => 120000,
                'keterangan' => 'Beli parfum lavender premium 5 Liter',
                'cabang_id' => $cabangId,
            ],
            [
                'tanggal' => $now->copy()->subDays(4)->toDateString(),
                'tipe' => 'pemasukan',
                'kategori' => 'Pendapatan Satuan',
                'nominal' => 200000,
                'keterangan' => 'Pencucian karpet dan jaket kulit',
                'cabang_id' => $cabangId,
            ],

            // --- Bulan Ini (tapi beda minggu) ---
            [
                'tanggal' => $now->copy()->subDays(10)->toDateString(),
                'tipe' => 'pemasukan',
                'kategori' => 'Pendapatan Laundry',
                'nominal' => 600000,
                'keterangan' => 'Pemasukan tengah bulan',
                'cabang_id' => $cabangId,
            ],
            [
                'tanggal' => $now->copy()->subDays(12)->toDateString(),
                'tipe' => 'pengeluaran',
                'kategori' => 'Operasional',
                'nominal' => 75000,
                'keterangan' => 'Beli plastik packing tebal',
                'cabang_id' => $cabangId,
            ],

            // --- Bulan Kemarin ---
            [
                'tanggal' => $now->copy()->subMonths(1)->day(10)->toDateString(),
                'tipe' => 'pemasukan',
                'kategori' => 'Pendapatan Laundry',
                'nominal' => 2500000,
                'keterangan' => 'Rekap pendapatan bulanan lalu',
                'cabang_id' => $cabangId,
            ],
            [
                'tanggal' => $now->copy()->subMonths(1)->day(28)->toDateString(),
                'tipe' => 'pengeluaran',
                'kategori' => 'Gaji',
                'nominal' => 1500000,
                'keterangan' => 'Gaji bulanan pegawai laundry',
                'cabang_id' => $cabangId,
            ],

            // --- Dua Bulan Kemarin ---
            [
                'tanggal' => $now->copy()->subMonths(2)->day(15)->toDateString(),
                'tipe' => 'pemasukan',
                'kategori' => 'Pendapatan Laundry',
                'nominal' => 3100000,
                'keterangan' => 'Pemasukan 2 bulan lalu',
                'cabang_id' => $cabangId,
            ],
            [
                'tanggal' => $now->copy()->subMonths(2)->day(20)->toDateString(),
                'tipe' => 'pengeluaran',
                'kategori' => 'Peralatan',
                'nominal' => 500000,
                'keterangan' => 'Servis mesin pengering laundry',
                'cabang_id' => $cabangId,
            ],
        ];

        foreach ($data as $row) {
            // Check if we already have this record to avoid duplicates on re-seed
            KeuanganToko::create($row);
        }
    }
}
