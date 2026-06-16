<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Illuminate\Database\Seeder;
use App\Models\DetailLayananTransaksi;
use App\Models\LayananTambahanTransaksi;

class TransaksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 12 unique combinations to ensure coverage, plus 8 random ones = 20 total
        $conditions = [
            // Baru
            ['status' => 'Baru', 'payment_status' => 'pending', 'is_roundtrip' => false],
            ['status' => 'Baru', 'payment_status' => 'pending', 'is_roundtrip' => true],
            ['status' => 'Baru', 'payment_status' => 'paid', 'is_roundtrip' => false],
            ['status' => 'Baru', 'payment_status' => 'paid', 'is_roundtrip' => true],
            
            // Proses
            ['status' => 'Proses', 'payment_status' => 'pending', 'is_roundtrip' => false],
            ['status' => 'Proses', 'payment_status' => 'pending', 'is_roundtrip' => true],
            ['status' => 'Proses', 'payment_status' => 'paid', 'is_roundtrip' => false],
            ['status' => 'Proses', 'payment_status' => 'paid', 'is_roundtrip' => true],
            
            // Selesai
            ['status' => 'Selesai', 'payment_status' => 'pending', 'is_roundtrip' => false],
            ['status' => 'Selesai', 'payment_status' => 'pending', 'is_roundtrip' => true],
            ['status' => 'Selesai', 'payment_status' => 'paid', 'is_roundtrip' => false],
            ['status' => 'Selesai', 'payment_status' => 'paid', 'is_roundtrip' => true],
        ];

        // Pad to 20
        for ($i = count($conditions); $i < 20; $i++) {
            $conditions[] = $conditions[array_rand($conditions)];
        }

        // Shuffle to distribute them randomly in time
        shuffle($conditions);

        $jmlPelanggan = 10;
        
        foreach ($conditions as $index => $cond) {
            $pelanggan = ($index % $jmlPelanggan) + 1;
            $gamis = fake()->randomElement([1,2]);
            // Orders closer to now for 'Baru', further for 'Selesai'
            if ($cond['status'] === 'Baru') {
                $tanggal = fake()->dateTimeBetween('-2 hours', 'now', 'Asia/Jakarta');
            } elseif ($cond['status'] === 'Proses') {
                $tanggal = fake()->dateTimeBetween('-5 days', '-1 days', 'Asia/Jakarta');
            } else {
                $tanggal = fake()->dateTimeBetween('-14 days', '-4 days', 'Asia/Jakarta');
            }

            $jamNota = $tanggal->format('His');
            $tanggalNota = $tanggal->format('dmY');
            
            $type = fake()->randomElement(['reguler', 'kilat', 'cahaya', 'tambahan']);
            
            $slaHours = 72;
            if ($type === 'kilat') {
                $slaHours = 24;
            } elseif ($type === 'cahaya') {
                $slaHours = 12;
            }
            
            $waktuPesan = Carbon::parse($tanggal);
            $waktuSelesai = $waktuPesan->copy()->addHours($slaHours);

            $transaksi = null;
            if ($type === 'reguler') {
                $transaksi = $this->createTransaksiReguler($pelanggan, $gamis, $cond['status'], $jamNota, $tanggalNota, $tanggal, $cond['status'] === 'Selesai' ? 1 : 0);
            } elseif ($type === 'kilat') {
                $transaksi = $this->createTransaksiKilat($pelanggan, $gamis, $cond['status'], $jamNota, $tanggalNota, $tanggal, $cond['status'] === 'Selesai' ? 1 : 0);
            } elseif ($type === 'cahaya') {
                $transaksi = $this->createTransaksiCahaya($pelanggan, $gamis, $cond['status'], $jamNota, $tanggalNota, $tanggal, $cond['status'] === 'Selesai' ? 1 : 0);
            } else {
                $transaksi = $this->createTransaksiRegulerTambahan($pelanggan, $gamis, $cond['status'], $jamNota, $tanggalNota, $tanggal, $cond['status'] === 'Selesai' ? 1 : 0);
            }
            
            if ($transaksi) {
                $updatedAt = $waktuPesan->copy();
                if ($cond['status'] === 'Selesai') {
                    $updatedAt = $waktuSelesai;
                } elseif ($cond['status'] === 'Proses') {
                    $updatedAt = $waktuPesan->copy()->addHours(fake()->numberBetween(1, max(1, $slaHours - 1)));
                }

                $transaksi->update([
                    'is_roundtrip' => $cond['is_roundtrip'],
                    'payment_status' => $cond['payment_status'],
                    'pickup_address' => 'Jl. Percobaan No. ' . ($index + 1),
                    'pickup_date' => $waktuPesan->toDateString(),
                ]);

                \Illuminate\Support\Facades\DB::table('transaksi')
                    ->where('id', $transaksi->id)
                    ->update([
                        'created_at' => $waktuPesan,
                        'updated_at' => $updatedAt,
                    ]);

                if ($cond['payment_status'] === 'paid') {
                    $payment = $transaksi->payments()->create([
                        'amount' => $transaksi->total_bayar_akhir,
                        'method' => 'qris',
                        'status' => 'paid',
                    ]);
                    
                    \Illuminate\Support\Facades\DB::table('payments')
                        ->where('id', $payment->id)
                        ->update([
                            'created_at' => $waktuPesan,
                            'updated_at' => $waktuPesan,
                        ]);
                }
            }
        }

        // Add 1 specific transaction guaranteed to be upgradeable
        $tanggalUpgrade = Carbon::now()->subHours(5);
        $jamNotaUpgrade = $tanggalUpgrade->format('His');
        $tanggalNotaUpgrade = $tanggalUpgrade->format('dmY');
        $transaksiUpgrade = $this->createTransaksiReguler(1, 1, 'Proses', $jamNotaUpgrade, $tanggalNotaUpgrade, $tanggalUpgrade, 0);
        
        $transaksiUpgrade->update([
            'is_roundtrip' => true,
            'payment_status' => 'pending',
            'pickup_address' => 'Jl. Khusus Upgrade Layanan No. 99',
            'pickup_date' => $tanggalUpgrade->toDateString(),
        ]);

        \Illuminate\Support\Facades\DB::table('transaksi')
            ->where('id', $transaksiUpgrade->id)
            ->update([
                'created_at' => $tanggalUpgrade,
                'updated_at' => $tanggalUpgrade->copy()->addHours(2),
            ]);
    }

    public function createTransaksiReguler($pelanggan, $gamis, $status, $jamNota, $tanggalNota, $tanggal, $konfirmasi = 0)
    {
        $nota1 = $jamNota . '-' . $tanggalNota . '-' . 1 . $pelanggan;
        $transaksi = Transaksi::create([
            'nota_layanan' => 'layanan-' . $nota1,
            'nota_pelanggan' => 'pelanggan-' . $nota1,
            'waktu' => $tanggal,
            'total_biaya_layanan' => 84000,
            'total_biaya_prioritas' => 0,
            'total_bayar_akhir' => 84000,
            'total_biaya_layanan_tambahan' => 0,
            'jenis_pembayaran' => 'Tunai',
            'bayar' => 100000,
            'kembalian' => 16000,
            'status' => $status,
            'konfirmasi_upah_gamis' => $konfirmasi,
            'layanan_prioritas_id' => 1,
            'pelanggan_id' => $pelanggan,
            'pegawai_id' => 9,
            'gamis_id' => $gamis,
            'cabang_id' => 1,
        ]);

        $detail1 = DetailTransaksi::create([
            'total_pakaian' => 24,
            'harga_layanan_akhir' => 3500,
            'total_biaya_layanan' => 84000,
            'total_biaya_prioritas' => 0,
            'transaksi_id' => $transaksi->id,
        ]);
        DetailLayananTransaksi::create([
            'harga_jenis_layanan_id' => 3,
            'detail_transaksi_id' => $detail1->id,
        ]);
        DetailLayananTransaksi::create([
            'harga_jenis_layanan_id' => 4,
            'detail_transaksi_id' => $detail1->id,
        ]);

        return $transaksi;
    }

    public function createTransaksiKilat($pelanggan, $gamis, $status, $jamNota, $tanggalNota, $tanggal, $konfirmasi = 0)
    {
        $nota1 = $jamNota . '-' . $tanggalNota . '-' . 1 . $pelanggan;
        $transaksi = Transaksi::create([
            'nota_layanan' => 'layanan-' . $nota1,
            'nota_pelanggan' => 'pelanggan-' . $nota1,
            'waktu' => $tanggal,
            'total_biaya_layanan' => 84000,
            'total_biaya_prioritas' => 36000,
            'total_bayar_akhir' => 120000,
            'total_biaya_layanan_tambahan' => 0,
            'jenis_pembayaran' => 'Tunai',
            'bayar' => 150000,
            'kembalian' => 30000,
            'status' => $status,
            'konfirmasi_upah_gamis' => $konfirmasi,
            'layanan_prioritas_id' => 2,
            'pelanggan_id' => $pelanggan,
            'pegawai_id' => 8,
            'gamis_id' => $gamis,
            'cabang_id' => 1,
        ]);

        $detail1 = DetailTransaksi::create([
            'total_pakaian' => 24,
            'harga_layanan_akhir' => 3500,
            'total_biaya_layanan' => 84000,
            'total_biaya_prioritas' => 36000,
            'transaksi_id' => $transaksi->id,
        ]);
        DetailLayananTransaksi::create([
            'harga_jenis_layanan_id' => 3,
            'detail_transaksi_id' => $detail1->id,
        ]);
        DetailLayananTransaksi::create([
            'harga_jenis_layanan_id' => 4,
            'detail_transaksi_id' => $detail1->id,
        ]);

        return $transaksi;
    }

    public function createTransaksiCahaya($pelanggan, $gamis, $status, $jamNota, $tanggalNota, $tanggal, $konfirmasi = 0)
    {
        $nota1 = $jamNota . '-' . $tanggalNota . '-' . 1 . $pelanggan;
        $transaksi = Transaksi::create([
            'nota_layanan' => 'layanan-' . $nota1,
            'nota_pelanggan' => 'pelanggan-' . $nota1,
            'waktu' => $tanggal,
            'total_biaya_layanan' => 84000,
            'total_biaya_prioritas' => 48000,
            'total_bayar_akhir' => 132000,
            'total_biaya_layanan_tambahan' => 0,
            'jenis_pembayaran' => 'Tunai',
            'bayar' => 150000,
            'kembalian' => 18000,
            'status' => $status,
            'konfirmasi_upah_gamis' => $konfirmasi,
            'layanan_prioritas_id' => 3,
            'pelanggan_id' => $pelanggan,
            'pegawai_id' => 8,
            'gamis_id' => $gamis,
            'cabang_id' => 1,
        ]);

        $detail1 = DetailTransaksi::create([
            'total_pakaian' => 24,
            'harga_layanan_akhir' => 3500,
            'total_biaya_layanan' => 84000,
            'total_biaya_prioritas' => 48000,
            'transaksi_id' => $transaksi->id,
        ]);
        DetailLayananTransaksi::create([
            'harga_jenis_layanan_id' => 3,
            'detail_transaksi_id' => $detail1->id,
        ]);
        DetailLayananTransaksi::create([
            'harga_jenis_layanan_id' => 4,
            'detail_transaksi_id' => $detail1->id,
        ]);

        return $transaksi;
    }

    public function createTransaksiRegulerTambahan($pelanggan, $gamis, $status, $jamNota, $tanggalNota, $tanggal, $konfirmasi = 0)
    {
        $nota1 = $jamNota . '-' . $tanggalNota . '-' . 1 . $pelanggan;
        $transaksi = Transaksi::create([
            'nota_layanan' => 'layanan-' . $nota1,
            'nota_pelanggan' => 'pelanggan-' . $nota1,
            'waktu' => $tanggal,
            'total_biaya_layanan' => 84000,
            'total_biaya_prioritas' => 0,
            'total_bayar_akhir' => 104000,
            'total_biaya_layanan_tambahan' => 20000,
            'jenis_pembayaran' => 'Tunai',
            'bayar' => 110000,
            'kembalian' => 6000,
            'status' => $status,
            'konfirmasi_upah_gamis' => $konfirmasi,
            'layanan_prioritas_id' => 1,
            'pelanggan_id' => $pelanggan,
            'pegawai_id' => 8,
            'gamis_id' => $gamis,
            'cabang_id' => 1,
        ]);

        $detail1 = DetailTransaksi::create([
            'total_pakaian' => 24,
            'harga_layanan_akhir' => 3500,
            'total_biaya_layanan' => 84000,
            'total_biaya_prioritas' => 0,
            'transaksi_id' => $transaksi->id,
        ]);
        DetailLayananTransaksi::create([
            'harga_jenis_layanan_id' => 3,
            'detail_transaksi_id' => $detail1->id,
        ]);
        DetailLayananTransaksi::create([
            'harga_jenis_layanan_id' => 4,
            'detail_transaksi_id' => $detail1->id,
        ]);

        LayananTambahanTransaksi::create([
            'layanan_tambahan_id' => 1,
            'transaksi_id' => $transaksi->id,
        ]);
        LayananTambahanTransaksi::create([
            'layanan_tambahan_id' => 2,
            'transaksi_id' => $transaksi->id,
        ]);

        return $transaksi;
    }
}
