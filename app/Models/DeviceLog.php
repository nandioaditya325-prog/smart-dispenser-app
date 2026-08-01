<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Maps to the `logs` table. Named DeviceLog (rather than Log) to avoid
 * clashing with Laravel's own Log facade/class.
 *
 * @property int $id
 * @property int|null $device_id
 * @property string $level
 * @property string|null $source
 * @property string $message
 * @property array|null $context
 */
class DeviceLog extends Model
{
    use HasFactory;

    protected $table = 'logs';

    protected $fillable = [
        'device_id',
        'level',
        'source',
        'message',
        'context',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
