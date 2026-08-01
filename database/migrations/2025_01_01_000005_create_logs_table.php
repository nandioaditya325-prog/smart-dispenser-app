<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Logs table: centralized application/device event log (errors,
     * MQTT events, webhook events) for observability and debugging.
     */
    public function up(): void
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->nullable()->constrained('devices')->nullOnDelete();
            $table->string('level')->default('info'); // debug|info|warning|error
            $table->string('source')->nullable();     // e.g. webhook, mqtt, api
            $table->text('message');
            $table->json('context')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
