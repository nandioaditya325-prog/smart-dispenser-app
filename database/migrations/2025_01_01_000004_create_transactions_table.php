<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->cascadeOnDelete();
            $table->string('qr_code_id')->unique();
            $table->integer('amount');
            $table->integer('target_volume_ml');
            $table->integer('final_volume_ml')->default(0);
            $table->string('status')->default('success'); // <-- Tambahkan baris ini langsung di sini!
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};