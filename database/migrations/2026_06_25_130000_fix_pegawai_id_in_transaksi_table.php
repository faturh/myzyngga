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
        Schema::table('transaksi', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign('transaksi_pegawai_id_foreign');
            
            // Change pegawai_id column to string
            $table->string('pegawai_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->unsignedBigInteger('pegawai_id')->change();
            $table->foreign('pegawai_id')->references('id')->on('users');
        });
    }
};
