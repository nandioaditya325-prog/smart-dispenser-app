<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $order_id
 * @property string|null $xendit_qr_id
 * @property string|null $xendit_reference_id
 * @property string|null $qr_string
 * @property string|null $payment_method
 * @property PaymentStatus $status
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property array|null $raw_response
 * @property array|null $webhook_payload
 */
class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'xendit_qr_id',
        'xendit_reference_id',
        'qr_string',
        'payment_method',
        'status',
        'paid_at',
        'raw_response',
        'webhook_payload',
    ];

    protected $casts = [
        'status' => PaymentStatus::class,
        'paid_at' => 'datetime',
        'raw_response' => 'array',
        'webhook_payload' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
