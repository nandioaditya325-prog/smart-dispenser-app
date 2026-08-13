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
        // Safe check: Hanya tambahkan kolom 'status' jika belum ada di tabel
        if (Schema::hasTable('transactions') && !Schema::hasColumn('transactions', 'status')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->string('status')->default('success')->after('final_volume_ml');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('transactions') && Schema::hasColumn('transactions', 'status')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};