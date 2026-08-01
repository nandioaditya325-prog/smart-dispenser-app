<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Devices table: represents each physical Smart Dispenser unit.
     * device_id is the human/device-friendly identifier (e.g. DISPENSER001)
     * used by the ESP32 firmware in every API request, distinct from the
     * numeric primary key used for internal relations.
     */
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->unique();
            $table->string('name')->nullable();
            $table->string('location')->nullable();
            $table->string('firmware_version')->nullable();
            $table->string('ip_address')->nullable();
            $table->integer('wifi_rssi')->nullable();
            $table->boolean('is_online')->default(false);
            $table->boolean('mqtt_connected')->default(false);
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
