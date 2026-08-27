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
            $table->string('order_id')->nullable();
            $table->unsignedBigInteger('device_id')->nullable();
            $table->string('rfid_uid')->nullable();
            $table->string('qr_code_id')->nullable();
            $table->string('water_type')->default('normal');
            $table->integer('volume_ml')->default(0);
            $table->integer('target_volume_ml')->default(0);
            $table->integer('final_volume_ml')->default(0);
            $table->integer('amount')->default(0);
            $table->integer('price')->default(0);
            $table->string('status')->default('success');
            $table->string('payment_status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};