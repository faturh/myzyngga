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
        Schema::create('keuangan_toko', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('tipe'); // 'pemasukan' or 'pengeluaran'
            $table->string('kategori');
            $table->double('nominal');
            $table->text('keterangan')->nullable();
            $table->foreignId('cabang_id')->nullable()->constrained('cabang')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keuangan_toko');
    }
};
