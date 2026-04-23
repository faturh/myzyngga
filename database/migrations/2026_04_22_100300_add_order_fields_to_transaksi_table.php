<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('transaksi')) {
            return;
        }

        Schema::table('transaksi', function (Blueprint $table) {
            if (! Schema::hasColumn('transaksi', 'pickup_address')) {
                $table->text('pickup_address')->nullable()->after('waktu');
            }
            if (! Schema::hasColumn('transaksi', 'pickup_detail_address')) {
                $table->string('pickup_detail_address')->nullable()->after('pickup_address');
            }
            if (! Schema::hasColumn('transaksi', 'pickup_date')) {
                $table->date('pickup_date')->nullable()->after('pickup_detail_address');
            }
            if (! Schema::hasColumn('transaksi', 'pickup_time')) {
                $table->string('pickup_time')->nullable()->after('pickup_date');
            }
            if (! Schema::hasColumn('transaksi', 'parfum')) {
                $table->string('parfum')->nullable()->after('pickup_time');
            }
            if (! Schema::hasColumn('transaksi', 'catatan')) {
                $table->text('catatan')->nullable()->after('parfum');
            }
            if (! Schema::hasColumn('transaksi', 'payment_status')) {
                $table->string('payment_status')->default('pending')->after('jenis_pembayaran');
            }
            if (! Schema::hasColumn('transaksi', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_status');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('transaksi')) {
            return;
        }

        Schema::table('transaksi', function (Blueprint $table) {
            $dropColumns = [
                'pickup_address',
                'pickup_detail_address',
                'pickup_date',
                'pickup_time',
                'parfum',
                'catatan',
                'payment_status',
                'paid_at',
            ];

            foreach ($dropColumns as $column) {
                if (Schema::hasColumn('transaksi', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
