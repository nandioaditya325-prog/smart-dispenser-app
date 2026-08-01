<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Payments table: stores the Xendit QRIS invoice/QR metadata and
     * the eventual webhook result for each order (1:1 with orders).
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('xendit_qr_id')->nullable();
            $table->string('xendit_reference_id')->nullable();
            $table->text('qr_string')->nullable();
            $table->string('payment_method')->nullable();
            $table->enum('status', [
                'PENDING',
                'ACTIVE',
                'PAID',
                'EXPIRED',
                'FAILED',
            ])->default('PENDING');
            $table->timestamp('paid_at')->nullable();
            $table->json('raw_response')->nullable();
            $table->json('webhook_payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
