<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\WaterType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property string $order_id
 * @property int $device_id
 * @property WaterType $water_type
 * @property int $volume_ml
 * @property int $nominal
 * @property OrderStatus $status
 * @property \Illuminate\Support\Carbon|null $expires_at
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'device_id',
        'water_type',
        'volume_ml',
        'nominal',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'water_type' => WaterType::class,
        'status' => OrderStatus::class,
        'expires_at' => 'datetime',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }
}
