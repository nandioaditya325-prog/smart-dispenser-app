<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Models\Payment;
use App\Repositories\Contracts\PaymentRepositoryInterface;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function createForOrder(Order $order, array $attributes): Payment
    {
        return $order->payment()->create($attributes);
    }

    public function findByXenditReferenceId(string $referenceId): ?Payment
    {
        return Payment::query()
            ->where('xendit_qr_id', $referenceId)
            ->orWhere('xendit_reference_id', $referenceId)
            ->first();
    }

    public function update(Payment $payment, array $attributes): Payment
    {
        $payment->update($attributes);

        return $payment->fresh();
    }
}
