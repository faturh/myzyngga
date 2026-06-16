<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\LayananPrioritas;
use Illuminate\Database\Seeder;
use App\Models\DetailLayananTransaksi;

class SelesaiOrderSeeder extends Seeder
{
    public function run(): void
    {
        // Do not delete to avoid foreign key constraints

        $layanan = LayananPrioritas::where('cabang_id', 1)->first();
        if (!$layanan) return;
        
        $pelanggan = 1; // Assuming customer 1 exists
        $gamis = 1;
        
        $baseBiayaLayanan = 48500;

        $tanggal = Carbon::now()->subDays(2); // Created 2 days ago
        $jamNota = $tanggal->format('His');
        $nota = 'selesai-test-' . $jamNota;

        $totalBiayaPrioritas = 10 * $layanan->harga; // assuming 10kg
        $totalAkhir = $baseBiayaLayanan + $totalBiayaPrioritas;

        $transaksi = Transaksi::create([
            'nota_layanan' => $nota,
            'nota_pelanggan' => 'plg-' . $nota,
            'waktu' => $tanggal,
            'total_biaya_layanan' => $baseBiayaLayanan,
            'total_biaya_prioritas' => $totalBiayaPrioritas,
            'total_bayar_akhir' => $totalAkhir,
            'total_biaya_layanan_tambahan' => 0,
            'jenis_pembayaran' => 'QRIS',
            'bayar' => $totalAkhir,
            'kembalian' => 0,
            'status' => 'Selesai', // Set to Selesai
            'konfirmasi_upah_gamis' => 0,
            'layanan_prioritas_id' => $layanan->id,
            'pelanggan_id' => $pelanggan,
            'pegawai_id' => 9,
            'gamis_id' => $gamis,
            'cabang_id' => 1,
        ]);

        $transaksi->update([
            'is_roundtrip' => false,
            'payment_status' => 'Lunas',
            'pickup_address' => 'Jl. Testing Selesai No. 1',
            'pickup_date' => $tanggal->toDateString(),
        ]);

        \Illuminate\Support\Facades\DB::table('transaksi')
            ->where('id', $transaksi->id)
            ->update([
                'created_at' => $tanggal,
                'updated_at' => Carbon::now()->subHour(),
            ]);

        $detail1 = DetailTransaksi::create([
            'total_pakaian' => 10,
            'harga_layanan_akhir' => 4850,
            'total_biaya_layanan' => $baseBiayaLayanan,
            'total_biaya_prioritas' => $totalBiayaPrioritas,
            'transaksi_id' => $transaksi->id,
        ]);

        DetailLayananTransaksi::create([
            'harga_jenis_layanan_id' => 3, // Reguler / Cuci Setrika usually
            'detail_transaksi_id' => $detail1->id,
        ]);
    }
}
