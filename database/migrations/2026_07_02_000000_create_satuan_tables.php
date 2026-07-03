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
        Schema::create('kategori_pakaian_satuan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pakaian');
            $table->double('harga');
            $table->timestamps();
        });

        Schema::create('tambahan', function (Blueprint $table) {
            $table->id();
            $table->integer('tambahan_id')->index();
            $table->foreignId('kategori_pakaian_satuan_id')->constrained('kategori_pakaian_satuan')->onDelete('cascade');
            $table->integer('jumlah');
            $table->double('harga_akhir');
            $table->timestamps();
        });

        Schema::table('transaksi', function (Blueprint $table) {
            $table->integer('fk_tambahan')->nullable()->after('list_pengerjaan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn('fk_tambahan');
        });

        Schema::dropIfExists('tambahan');
        Schema::dropIfExists('kategori_pakaian_satuan');
    }
};
