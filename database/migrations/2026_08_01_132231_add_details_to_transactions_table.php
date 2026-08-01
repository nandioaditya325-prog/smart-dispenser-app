<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
        $table->string('rfid_uid')->nullable();
        $table->string('water_type')->default('normal');
        $table->integer('volume_ml')->default(500);
        $table->integer('price')->default(1000);
        $table->string('payment_status')->default('pending');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['water_type', 'price', 'payment_status']);
        });
    }
};