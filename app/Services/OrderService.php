<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\WaterType;
use App\Models\Order;
use App\Repositories\Contracts\DeviceRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use Illuminate\Support\Str;

/**
 * Orchestrates the full order lifecycle: pricing, order + payment
 * persistence, dynamic QRIS creation via Xendit, cancellation, and
 * webhook-driven payment confirmation (including the MQTT publish
 * that tells the physical dispenser to start pumping).
 */
class OrderService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
        private readonly PaymentRepositoryInterface $payments,
        private readonly DeviceRepositoryInterface $devices,
        private readonly XenditService $xendit,
        private readonly MqttService $mqtt,
        private readonly PricingService $pricing,
    ) {
    }

    /**
     * Creates a new order for the given device, generates a dynamic
     * QRIS via Xendit, and persists both the order and payment rows.
     *
     * @return array{order: Order, nominal: int, qr_string: string}
     */
    public function createOrder(string $deviceId, WaterType $waterType, int $volumeMl): array
    {
        $device = $this->devices->findOrCreateByDeviceId($deviceId);
        $nominal = $this->pricing->calculate($waterType, $volumeMl);
        $orderId = $this->generateOrderId();
        $expirySeconds = (int) config('dispenser.qris_expiry_seconds', 120);

        $order = $this->orders->create([
            'order_id' => $orderId,
            'device_id' => $device->id,
            'water_type' => $waterType,
            'volume_ml' => $volumeMl,
            'nominal' => $nominal,
            'status' => OrderStatus::Pending,
            'expires_at' => now()->addSeconds($expirySeconds),
        ]);

        $qris = $this->xendit->createDynamicQris($orderId, $nominal, $expirySeconds);

        $this->payments->createForOrder($order, [
            'xendit_qr_id' => $qris['qr_id'],
            'xendit_reference_id' => $qris['reference_id'],
            'qr_string' => $qris['qr_string'],
            'status' => PaymentStatus::Active,
            'raw_response' => $qris['raw'],
        ]);

        $this->orders->updateStatus($order, OrderStatus::WaitingPayment);

        return [
            'order' => $order->fresh('payment'),
            'nominal' => $nominal,
            'qr_string' => $qris['qr_string'],
        ];
    }

    /**
     * Cancels a pending/waiting order: deactivates the QRIS on Xendit
     * (best-effort) and marks the order as cancelled.
     */
    public function cancelOrder(string $orderId): ?Order
    {
        $order = $this->orders->findByOrderIdWithPayment($orderId);

        if (! $order) {
            return null;
        }

        if ($order->payment?->xendit_qr_id) {
            $this->xendit->deactivateQris($order->payment->xendit_qr_id);
        }

        return $this->orders->updateStatus($order, OrderStatus::Cancelled);
    }

    /**
     * Returns the current status of an order for the ESP32 polling
     * fallback (GET /api/order/status).
     */
    public function getOrderStatus(string $orderId): ?Order
    {
        $order = $this->orders->findByOrderIdWithPayment($orderId);

        if ($order && $order->status === OrderStatus::WaitingPayment && $order->isExpired()) {
            $order = $this->orders->updateStatus($order, OrderStatus::Expired);
        }

        return $order;
    }

    /**
     * Marks an order as paid (called from the webhook handler after
     * signature verification) and publishes the MQTT payment event
     * that triggers the physical dispensing sequence on the ESP32.
     */
    public function markOrderAsPaid(Order $order, string $paymentMethod): Order
    {
        $order = $this->orders->updateStatus($order, OrderStatus::Paid);

        $this->payments->update($order->payment, [
            'status' => PaymentStatus::Paid,
            'payment_method' => $paymentMethod,
            'paid_at' => now(),
        ]);

        $this->mqtt->publishPaymentStatus($order->order_id, 'PAID');

        return $order;
    }

    /**
     * Marks an order as expired/failed and notifies the device via MQTT
     * so it can leave the QRIS screen instead of waiting indefinitely.
     */
    public function markOrderAsFailed(Order $order, OrderStatus $status): Order
    {
        $order = $this->orders->updateStatus($order, $status);

        $mqttStatus = $status === OrderStatus::Expired ? 'EXPIRED' : 'FAILED';
        $this->mqtt->publishPaymentStatus($order->order_id, $mqttStatus);

        return $order;
    }

    /**
     * Generates a unique, human-readable invoice/order id, e.g.
     * INV-20260704-AB12CD.
     */
    private function generateOrderId(): string
    {
        return sprintf(
            'INV-%s-%s',
            now()->format('Ymd'),
            Str::upper(Str::random(6))
        );
    }
}
