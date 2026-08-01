<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Orders table: one row per dispense request created by the ESP32.
     * order_id is the public-facing invoice identifier returned to the
     * device and used as the Xendit external_id (e.g. INV-XXXXXXXX).
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->foreignId('device_id')->constrained('devices')->cascadeOnDelete();
            $table->enum('water_type', ['normal', 'dingin', 'panas']);
            $table->unsignedInteger('volume_ml');
            $table->unsignedInteger('nominal');
            $table->enum('status', [
                'pending',
                'waiting_payment',
                'paid',
                'expired',
                'cancelled',
                'failed',
                'completed',
            ])->default('pending');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['device_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
