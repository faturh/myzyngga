<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('upgrade_layanans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('transaksi_id');
            $table->unsignedBigInteger('layanan_asal_id');
            $table->unsignedBigInteger('layanan_tujuan_id');
            $table->decimal('biaya_upgrade', 15, 2);
            $table->timestamps();

            // Setup foreign keys explicitly because we know the tables
            $table->foreign('transaksi_id')->references('id')->on('transaksi')->onDelete('cascade');
            $table->foreign('layanan_asal_id')->references('id')->on('layanan_prioritas')->onDelete('cascade');
            $table->foreign('layanan_tujuan_id')->references('id')->on('layanan_prioritas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upgrade_layanans');
    }
};
