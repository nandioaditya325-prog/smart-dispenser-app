<?php

namespace App\Jobs;

use App\Services\MqttService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Publishes the EXPIRED payment event to MQTT outside of the HTTP
 * request/scheduler run, so a slow/unreachable broker never blocks
 * the console command or webhook response.
 */
class NotifyDeviceOrderExpiredJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 5;

    public function __construct(private readonly string $orderId)
    {
    }

    public function handle(MqttService $mqtt): void
    {
        $mqtt->publishPaymentStatus($this->orderId, 'EXPIRED');
    }
}
