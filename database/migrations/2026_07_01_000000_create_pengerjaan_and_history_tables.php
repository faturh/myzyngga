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
        Schema::create('list_history_pengerjaan', function (Blueprint $table) {
            $table->id();
            $table->uuid('transaksi_id');
            $table->unsignedBigInteger('status_sebelumnya')->nullable();
            $table->unsignedBigInteger('status_sesudahnya')->nullable();
            $table->unsignedBigInteger('operator_id')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('transaksi_id')->references('id')->on('transaksi')->onDelete('cascade');
            $table->foreign('status_sebelumnya')->references('id')->on('list_status_pengerjaan');
            $table->foreign('status_sesudahnya')->references('id')->on('list_status_pengerjaan');
            $table->foreign('operator_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('list_pengerjaan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('list_status_pengerjaan_id');
            $table->unsignedBigInteger('list_history_pengerjaan_id')->nullable();
            $table->timestamps();

            $table->foreign('list_status_pengerjaan_id')->references('id')->on('list_status_pengerjaan');
            $table->foreign('list_history_pengerjaan_id')->references('id')->on('list_history_pengerjaan')->onDelete('set null');
        });

        Schema::table('transaksi', function (Blueprint $table) {
            $table->unsignedBigInteger('list_pengerjaan_id')->nullable()->after('status');
            $table->foreign('list_pengerjaan_id')->references('id')->on('list_pengerjaan')->onDelete('set null');
        });

        // Data Backfill
        $transaksis = DB::table('transaksi')->get();
        foreach ($transaksis as $t) {
            $statusId = $t->list_status_pengerjaan_id ?? null;
            if (!$statusId) {
                $statusId = 1;
            }

            // Create list_pengerjaan record
            $lpId = DB::table('list_pengerjaan')->insertGetId([
                'list_status_pengerjaan_id' => $statusId,
                'list_history_pengerjaan_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update transaksi
            DB::table('transaksi')->where('id', $t->id)->update([
                'list_pengerjaan_id' => $lpId
            ]);
        }

        // Drop foreign key and column list_status_pengerjaan_id from transaksi table
        Schema::table('transaksi', function (Blueprint $table) {
            try {
                $table->dropForeign(['list_status_pengerjaan_id']);
            } catch (\Exception $e) {
                // Ignore if not present
            }
            $table->dropColumn('list_status_pengerjaan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->unsignedBigInteger('list_status_pengerjaan_id')->nullable()->after('status');
            $table->foreign('list_status_pengerjaan_id')->references('id')->on('list_status_pengerjaan');
        });

        // Backfill
        $transaksis = DB::table('transaksi')
            ->join('list_pengerjaan as lp', 'lp.id', '=', 'transaksi.list_pengerjaan_id')
            ->select('transaksi.id', 'lp.list_status_pengerjaan_id')
            ->get();

        foreach ($transaksis as $t) {
            DB::table('transaksi')->where('id', $t->id)->update([
                'list_status_pengerjaan_id' => $t->list_status_pengerjaan_id
            ]);
        }

        Schema::table('transaksi', function (Blueprint $table) {
            try {
                $table->dropForeign(['list_pengerjaan_id']);
            } catch (\Exception $e) {
                // Ignore
            }
            $table->dropColumn('list_pengerjaan_id');
        });

        Schema::dropIfExists('list_pengerjaan');
        Schema::dropIfExists('list_history_pengerjaan');
    }
};
