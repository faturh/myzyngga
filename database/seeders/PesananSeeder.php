<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\DetailLayananTransaksi;
use App\Models\Pelanggan;
use App\Models\User;
use App\Models\DetailGamis;
use App\Models\Gamis;
use App\Models\Cabang;
use App\Models\LayananPrioritas;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PesananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada Pelanggan, Cabang, Pegawai, Gamis, dan LayananPrioritas
        $pelanggan = Pelanggan::first() ?? Pelanggan::factory()->create();
        $cabang = Cabang::first() ?? Cabang::create(['nama' => 'Cabang Utama', 'alamat' => 'Surabaya']);
        
        $pegawai = User::whereHas('roles', function($q) {
            $q->where('name', 'pegawai');
        })->first() ?? User::factory()->create(['cabang_id' => $cabang->id]);
        
        $gamisModel = Gamis::first() ?? Gamis::create([
            'kartu_keluarga' => '1234567890123456',
            'alamat' => 'Surabaya',
            'rt' => 1,
            'rw' => 1,
        ]);

        $gamis = DetailGamis::first() ?? DetailGamis::create([
            'nama' => 'Gamis Test',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Surabaya',
            'tanggal_lahir' => '1999-01-01',
            'telepon' => '081234567890',
            'alamat' => 'Surabaya',
            'gamis_id' => $gamisModel->id,
            'user_id' => $pegawai->id,
        ]);

        $prioritasReguler = LayananPrioritas::where('id', 1)->first() ?? LayananPrioritas::create(['id' => 1, 'nama' => 'Reguler', 'harga' => 0]);
        $prioritasKilat = LayananPrioritas::where('id', 2)->first() ?? LayananPrioritas::create(['id' => 2, 'nama' => 'Kilat', 'harga' => 15000]);
        $prioritasCahaya = LayananPrioritas::where('id', 3)->first() ?? LayananPrioritas::create(['id' => 3, 'nama' => 'Express', 'harga' => 25000]);

        $ordersData = [
            [
                'status' => 'Baru',
                'payment_status' => 'pending',
                'prioritas_id' => 1,
                'total_biaya_layanan' => 35000,
                'total_biaya_prioritas' => 0,
                'total_bayar_akhir' => 35000,
                'is_roundtrip' => false,
                'pickup_address' => 'Jl. Mawar No. 12, Surabaya',
                'waktu' => Carbon::now()->subMinutes(30),
            ],
            [
                'status' => 'Baru',
                'payment_status' => 'paid',
                'prioritas_id' => 2,
                'total_biaya_layanan' => 50000,
                'total_biaya_prioritas' => 15000,
                'total_bayar_akhir' => 65000,
                'is_roundtrip' => true,
                'pickup_address' => 'Jl. Melati No. 4, Surabaya',
                'waktu' => Carbon::now()->subHours(2),
            ],
            [
                'status' => 'Proses',
                'payment_status' => 'pending',
                'prioritas_id' => 1,
                'total_biaya_layanan' => 42000,
                'total_biaya_prioritas' => 0,
                'total_bayar_akhir' => 42000,
                'is_roundtrip' => false,
                'pickup_address' => 'Jl. Anggrek No. 8, Surabaya',
                'waktu' => Carbon::now()->subDays(1),
            ],
            [
                'status' => 'Proses',
                'payment_status' => 'paid',
                'prioritas_id' => 3,
                'total_biaya_layanan' => 60000,
                'total_biaya_prioritas' => 25000,
                'total_bayar_akhir' => 85000,
                'is_roundtrip' => true,
                'pickup_address' => 'Jl. Dahlia No. 19, Surabaya',
                'waktu' => Carbon::now()->subDays(2),
            ],
            [
                'status' => 'Selesai',
                'payment_status' => 'paid',
                'prioritas_id' => 1,
                'total_biaya_layanan' => 70000,
                'total_biaya_prioritas' => 0,
                'total_bayar_akhir' => 70000,
                'is_roundtrip' => false,
                'pickup_address' => 'Jl. Kenanga No. 25, Surabaya',
                'waktu' => Carbon::now()->subDays(5),
            ],
        ];

        foreach ($ordersData as $i => $data) {
            $nota1 = Carbon::parse($data['waktu'])->format('His-dmY') . '-' . ($i + 1);
            
            $transaksi = Transaksi::create([
                'nota' => 'pelanggan-' . $nota1,
                'waktu' => $data['waktu'],
                'pickup_address' => $data['pickup_address'],
                'pickup_date' => Carbon::parse($data['waktu'])->toDateString(),
                'pickup_time' => Carbon::parse($data['waktu'])->toTimeString(),
                'is_roundtrip' => $data['is_roundtrip'],
                'total_biaya_layanan' => $data['total_biaya_layanan'],
                'total_biaya_prioritas' => $data['total_biaya_prioritas'],
                'total_biaya_layanan_tambahan' => 0,
                'total_bayar_akhir' => $data['total_bayar_akhir'],
                'jenis_pembayaran' => 'QRIS',
                'payment_status' => $data['payment_status'],
                'paid_at' => $data['payment_status'] === 'paid' ? Carbon::parse($data['waktu'])->addMinutes(10) : null,
                'bayar' => $data['payment_status'] === 'paid' ? $data['total_bayar_akhir'] : 0,
                'kembalian' => 0,
                'status' => $data['status'],
                'layanan_prioritas_id' => $data['prioritas_id'],
                'pelanggan_id' => $pelanggan->id,
                'pegawai_id' => $pegawai->id,
                'cabang_id' => $cabang->id,
            ]);

            $detail = DetailTransaksi::create([
                'total_pakaian' => 10,
                'harga_layanan_akhir' => 3500,
                'total_biaya_layanan' => $data['total_biaya_layanan'],
                'total_biaya_prioritas' => $data['total_biaya_prioritas'],
                'transaksi_id' => $transaksi->id,
            ]);

            DetailLayananTransaksi::create([
                'harga_jenis_layanan_id' => 1,
                'detail_transaksi_id' => $detail->id,
            ]);

            // Set timestamps correctly
            DB::table('transaksi')
                ->where('id', $transaksi->id)
                ->update([
                    'created_at' => $data['waktu'],
                    'updated_at' => $data['status'] === 'Selesai' ? Carbon::parse($data['waktu'])->addDays(2) : Carbon::parse($data['waktu'])->addHours(4),
                ]);

            if ($data['payment_status'] === 'paid') {
                $payment = $transaksi->payments()->create([
                    'amount' => $data['total_bayar_akhir'],
                    'method' => 'qris',
                    'status' => 'paid',
                ]);

                DB::table('payments')
                    ->where('id', $payment->id)
                    ->update([
                        'created_at' => Carbon::parse($data['waktu'])->addMinutes(10),
                        'updated_at' => Carbon::parse($data['waktu'])->addMinutes(10),
                    ]);
            }
        }
    }
}
