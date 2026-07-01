<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->unsignedBigInteger('list_status_pengerjaan_id')->nullable()->after('status');
        });

        // Data Backfill
        $transaksis = DB::table('transaksi')->get();
        foreach ($transaksis as $t) {
            $status = strtolower(trim($t->status ?? ''));
            $paymentStatus = strtolower(trim($t->payment_status ?? ''));

            $id = 1; // Default: Perlu Diproses
            if ($status === 'baru' || $status === 'created') {
                $id = 1;
            } elseif ($status === 'proses') {
                if ($paymentStatus === 'paid') {
                    $id = 3; // Perlu Dikerjakan
                } else {
                    $id = 2; // Menunggu Pembayaran
                }
            } elseif (in_array($status, ['siap ambil', 'antar', 'siap_ambil', 'pengantaran'])) {
                $id = 4; // Proses Pengerjaan
            } elseif ($status === 'selesai' || $status === 'completed') {
                $id = 5; // Pesanan Selesai
            } elseif ($status === 'kendala') {
                $id = 6; // Kendala Pesanan
            } elseif ($status === 'batal' || $status === 'dibatalkan' || $status === 'cancelled') {
                $id = 7; // Sedang Dibatalkan
            } elseif ($status === 'jemput' || $status === 'penjemputan' || $status === 'picked_up') {
                $id = 8; // Sedang Dijemput
            }

            DB::table('transaksi')->where('id', $t->id)->update([
                'list_status_pengerjaan_id' => $id
            ]);
        }

        // Set list_status_pengerjaan_id to not nullable and add foreign key
        Schema::table('transaksi', function (Blueprint $table) {
            $table->unsignedBigInteger('list_status_pengerjaan_id')->nullable(false)->change();
            $table->foreign('list_status_pengerjaan_id')->references('id')->on('list_status_pengerjaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            try {
                $table->dropForeign(['list_status_pengerjaan_id']);
            } catch (\Exception $e) {
                // Ignore
            }
            $table->dropColumn('list_status_pengerjaan_id');
        });
    }
};
