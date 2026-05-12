<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pelanggan')) {
            return;
        }

        Schema::table('pelanggan', function (Blueprint $table) {
            if (! Schema::hasColumn('pelanggan', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users', 'id')->nullOnDelete();
                $table->unique('user_id');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('pelanggan')) {
            return;
        }

        Schema::table('pelanggan', function (Blueprint $table) {
            if (Schema::hasColumn('pelanggan', 'user_id')) {
                $table->dropUnique(['user_id']);
                $table->dropConstrainedForeignId('user_id');
            }
        });
    }
};
