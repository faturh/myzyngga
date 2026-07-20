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
        Schema::create('list_status_pengerjaan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50);
            $table->timestamps();
        });

        DB::table('list_status_pengerjaan')->insert([
            ['id' => 1, 'nama' => 'Perlu Diproses', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nama' => 'Menunggu Pembayaran', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nama' => 'Perlu Dikerjakan', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'nama' => 'Proses Pengerjaan', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'nama' => 'Pesanan Selesai', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'nama' => 'Kendala Pesanan', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'nama' => 'Sedang Dibatalkan', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'nama' => 'Menunggu di Jemput', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'nama' => 'Perlu di Antar', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('list_status_pengerjaan');
    }
};
