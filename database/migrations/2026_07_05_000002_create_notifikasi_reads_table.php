<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi_reads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notifikasi_id');
            $table->unsignedBigInteger('pelanggan_id');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->unique(['notifikasi_id', 'pelanggan_id']);

            $table->foreign('notifikasi_id')->references('id')->on('notifikasi')->onDelete('cascade');
            $table->foreign('pelanggan_id')->references('id')->on('pelanggan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi_reads');
    }
};
