<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\LayananPrioritas;
use Illuminate\Database\Seeder;
use App\Models\DetailLayananTransaksi;

class UpgradeLayananTestSeeder extends Seeder
{
    public function run(): void
    {
        // Delete previous test orders to prevent clutter if seeded multiple times
        Transaksi::where('nota_layanan', 'like', 'upgrade-test-%')->delete();

        $layanans = LayananPrioritas::where('cabang_id', 1)->get();
        $pelanggan = 1; // Assuming customer 1 exists
        $gamis = 1;
        
        $baseBiayaLayanan = 84000;

        foreach ($layanans as $index => $layanan) {
            $tanggal = Carbon::now()->subMinutes(10 * $index); // Created recently
            $jamNota = $tanggal->format('His');
            $tanggalNota = $tanggal->format('dmY');
            $nota = 'upgrade-test-' . $layanan->nama . '-' . $jamNota;

            $totalBiayaPrioritas = 24 * $layanan->harga; // assuming 24kg
            $totalAkhir = $baseBiayaLayanan + $totalBiayaPrioritas;

            $transaksi = Transaksi::create([
                'nota_layanan' => $nota,
                'nota_pelanggan' => 'plg-' . $nota,
                'waktu' => $tanggal,
                'total_biaya_layanan' => $baseBiayaLayanan,
                'total_biaya_prioritas' => $totalBiayaPrioritas,
                'total_bayar_akhir' => $totalAkhir,
                'total_biaya_layanan_tambahan' => 0,
                'jenis_pembayaran' => 'Tunai',
                'bayar' => $totalAkhir + 10000,
                'kembalian' => 10000,
                'status' => 'Proses', // Set to Proses so it shows up in "Sedang Berlangsung"
                'konfirmasi_upah_gamis' => 0,
                'layanan_prioritas_id' => $layanan->id,
                'pelanggan_id' => $pelanggan,
                'pegawai_id' => 9,
                'gamis_id' => $gamis,
                'cabang_id' => 1,
            ]);

            $transaksi->update([
                'is_roundtrip' => true,
                'payment_status' => 'pending',
                'pickup_address' => 'Jl. Testing Upgrade No. ' . ($index + 1),
                'pickup_date' => $tanggal->toDateString(),
            ]);

            \Illuminate\Support\Facades\DB::table('transaksi')
                ->where('id', $transaksi->id)
                ->update([
                    'created_at' => $tanggal,
                    'updated_at' => $tanggal,
                ]);

            $detail1 = DetailTransaksi::create([
                'total_pakaian' => 24,
                'harga_layanan_akhir' => 3500,
                'total_biaya_layanan' => $baseBiayaLayanan,
                'total_biaya_prioritas' => $totalBiayaPrioritas,
                'transaksi_id' => $transaksi->id,
            ]);

            DetailLayananTransaksi::create([
                'harga_jenis_layanan_id' => 3, // Reguler / Cuci Setrika usually
                'detail_transaksi_id' => $detail1->id,
            ]);
            
            DetailLayananTransaksi::create([
                'harga_jenis_layanan_id' => 4,
                'detail_transaksi_id' => $detail1->id,
            ]);
        }
    }
}
