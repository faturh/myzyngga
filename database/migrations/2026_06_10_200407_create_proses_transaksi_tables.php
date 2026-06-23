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
        Schema::create('timbangan', function (Blueprint $table) {
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

        Schema::create('list_pakaian_timbangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('timbangan_id')->constrained('timbangan')->onDelete('cascade');
            $table->foreignId('jenis_pakaian_id')->constrained('jenis_pakaian')->onDelete('cascade');
            $table->integer('qty');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('list_pakaian_timbangan');
        Schema::dropIfExists('timbangan');
    }
};
