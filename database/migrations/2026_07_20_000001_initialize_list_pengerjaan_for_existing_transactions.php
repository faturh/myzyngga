<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $transactions = DB::table('transaksi')->whereNull('list_pengerjaan_id')->get();

        foreach ($transactions as $t) {
            $statusVal = strtolower(trim($t->status ?? ''));
            $isRoundtrip = (bool) $t->is_roundtrip;

            $statusId = 1; // Default
            if (in_array($statusVal, ['baru', 'created', 'perlu diproses', 'perlu_diproses'])) {
                $statusId = $isRoundtrip ? 8 : 1;
            } elseif (in_array($statusVal, ['menunggu pembayaran', 'menunggu_pembayaran'])) {
                $statusId = 2;
            } elseif (in_array($statusVal, ['proses', 'perlu dikerjakan', 'perlu_dikerjakan'])) {
                $statusId = 3;
            } elseif (in_array($statusVal, ['proses pengerjaan', 'proses_pengerjaan', 'siap ambil', 'siap_ambil', 'in_progress'])) {
                $statusId = 4;
            } elseif (in_array($statusVal, ['selesai', 'completed', 'pesanan selesai', 'pesanan_selesai'])) {
                $statusId = 5;
            } elseif (in_array($statusVal, ['kendala', 'kendala pesanan', 'kendala_pesanan'])) {
                $statusId = 6;
            } elseif (in_array($statusVal, ['batal', 'dibatalkan', 'cancelled', 'sedang dibatalkan', 'sedang_dibatalkan'])) {
                $statusId = 7;
            } elseif (in_array($statusVal, ['jemput', 'penjemputan', 'picked_up', 'sedang dijemput', 'sedang_dijemput', 'menunggu di jemput', 'menunggu_di_jemput'])) {
                $statusId = 8;
            } elseif (in_array($statusVal, ['antar', 'pengantaran', 'ready_for_delivery', 'perlu di antar', 'perlu_di_antar'])) {
                $statusId = 9;
            }

            // Create list_pengerjaan record
            $lpId = DB::table('list_pengerjaan')->insertGetId([
                'list_status_pengerjaan_id' => $statusId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update transaksi
            DB::table('transaksi')->where('id', $t->id)->update([
                'list_pengerjaan_id' => $lpId,
                'status' => $this->getStatusName($statusId)
            ]);
        }
    }

    private function getStatusName($id)
    {
        $statusNames = [
            1 => 'Perlu Diproses',
            2 => 'Menunggu Pembayaran',
            3 => 'Perlu Dikerjakan',
            4 => 'Proses Pengerjaan',
            5 => 'Pesanan Selesai',
            6 => 'Kendala Pesanan',
            7 => 'Sedang Dibatalkan',
            8 => 'Menunggu di Jemput',
            9 => 'Perlu di Antar',
        ];
        return $statusNames[$id] ?? 'Perlu Diproses';
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed
    }
};
