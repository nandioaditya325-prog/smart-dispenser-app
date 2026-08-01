<?php

namespace App\Repositories\Contracts;

use App\Enums\OrderStatus;
use App\Models\Order;

interface OrderRepositoryInterface
{
    /**
     * Creates a new order record.
     *
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): Order;

    /**
     * Finds an order by its public order_id (e.g. INV-XXXXXXXX).
     */
    public function findByOrderId(string $orderId): ?Order;

    /**
     * Finds an order by its public order_id, eager-loading the payment
     * relation, for the status polling and webhook flows.
     */
    public function findByOrderIdWithPayment(string $orderId): ?Order;

    /**
     * Updates the status of an order.
     */
    public function updateStatus(Order $order, OrderStatus $status): Order;
}
