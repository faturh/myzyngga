<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('customer_preferences')) {
            return;
        }

        Schema::create('customer_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelanggan_id')->unique()->constrained('pelanggan', 'id')->cascadeOnDelete();
            $table->string('default_parfum')->nullable();
            $table->text('default_note')->nullable();
            $table->string('default_payment_method')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('customer_preferences')) {
            return;
        }

        Schema::dropIfExists('customer_preferences');
    }
};
