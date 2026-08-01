<?php

namespace App\Repositories\Contracts;

use App\Models\Order;
use App\Models\Payment;

interface PaymentRepositoryInterface
{
    /**
     * Creates a payment record attached to the given order.
     *
     * @param array<string, mixed> $attributes
     */
    public function createForOrder(Order $order, array $attributes): Payment;

    /**
     * Finds a payment by the Xendit QR/invoice id (used to correlate
     * incoming webhooks back to the local order).
     */
    public function findByXenditReferenceId(string $referenceId): ?Payment;

    /**
     * Updates a payment record (status, paid_at, webhook_payload, etc).
     *
     * @param array<string, mixed> $attributes
     */
    public function update(Payment $payment, array $attributes): Payment;
}
