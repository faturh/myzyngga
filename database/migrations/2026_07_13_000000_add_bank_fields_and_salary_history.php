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
        // 1. Add bank account columns to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('nomor_rekening')->nullable()->after('gaji');
            $table->string('bank')->nullable()->after('nomor_rekening');
        });

        // 2. Add salary paid status column to transaksi table
        Schema::table('transaksi', function (Blueprint $table) {
            $table->boolean('gaji_dibayar')->default(0)->after('pegawai_id');
        });

        // 3. Create history_gaji table
        Schema::create('history_gaji', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('users')->onDelete('cascade');
            $table->double('nominal');
            $table->date('tanggal');
            $table->string('bank')->nullable();
            $table->string('nomor_rekening')->nullable();
            $table->text('keterangan')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_gaji');

        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn('gaji_dibayar');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nomor_rekening', 'bank']);
        });
    }
};
