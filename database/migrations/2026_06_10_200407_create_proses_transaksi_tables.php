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
        Schema::create('proses_transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('transaksi_id')->constrained('transaksi')->onDelete('cascade');
            $table->string('nota')->index();
            $table->double('actual_weight');
            $table->double('minimum_weight');
            $table->double('price_per_kg');
            $table->double('charged_weight');
            $table->double('total_price');
            $table->timestamps();
        });

        Schema::create('proses_transaksi_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proses_transaksi_id')->constrained('proses_transaksi')->onDelete('cascade');
            $table->string('nama_item');
            $table->integer('qty');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proses_transaksi_items');
        Schema::dropIfExists('proses_transaksi');
    }
};
