<?php

namespace App\Repositories\Eloquent;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    public function create(array $attributes): Order
    {
        return Order::query()->create($attributes);
    }

    public function findByOrderId(string $orderId): ?Order
    {
        return Order::query()->where('order_id', $orderId)->first();
    }

    public function findByOrderIdWithPayment(string $orderId): ?Order
    {
        return Order::query()
            ->with('payment')
            ->where('order_id', $orderId)
            ->first();
    }

    public function updateStatus(Order $order, OrderStatus $status): Order
    {
        $order->update(['status' => $status]);

        return $order->fresh();
    }
}
