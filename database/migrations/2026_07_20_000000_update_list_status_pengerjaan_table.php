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
        // Update name of ID 8 to 'Menunggu di Jemput'
        DB::table('list_status_pengerjaan')
            ->where('id', 8)
            ->update([
                'nama' => 'Menunggu di Jemput',
                'updated_at' => now(),
            ]);

        // Insert new status ID 9 'Perlu di Antar' if it doesn't exist
        if (DB::table('list_status_pengerjaan')->where('id', 9)->count() === 0) {
            DB::table('list_status_pengerjaan')->insert([
                'id' => 9,
                'nama' => 'Perlu di Antar',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert name of ID 8 to 'Sedang Dijemput'
        DB::table('list_status_pengerjaan')
            ->where('id', 8)
            ->update([
                'nama' => 'Sedang Dijemput',
                'updated_at' => now(),
            ]);

        // Delete status ID 9
        DB::table('list_status_pengerjaan')->where('id', 9)->delete();
    }
};
