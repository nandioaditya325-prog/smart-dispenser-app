<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Jobs\NotifyDeviceOrderExpiredJob;
use App\Models\Order;
use Illuminate\Console\Command;

/**
 * Sweeps orders stuck in `waiting_payment` whose expires_at has passed
 * (e.g. the user never scanned the QRIS) and marks them expired,
 * dispatching an MQTT notification job for each so the device leaves
 * the QRIS screen instead of waiting forever.
 */
class ExpireStaleOrdersCommand extends Command
{
    protected $signature = 'dispenser:expire-stale-orders';

    protected $description = 'Marks waiting_payment orders past their expiry as expired and notifies the device.';

    public function handle(): int
    {
        $expiredOrders = Order::query()
            ->where('status', OrderStatus::WaitingPayment)
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expiredOrders as $order) {
            $order->update(['status' => OrderStatus::Expired]);
            NotifyDeviceOrderExpiredJob::dispatch($order->order_id);
        }

        $this->info("Expired {$expiredOrders->count()} stale order(s).");

        return self::SUCCESS;
    }
}
