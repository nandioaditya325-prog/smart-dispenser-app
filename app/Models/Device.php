<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $device_id
 * @property string|null $name
 * @property string|null $location
 * @property string|null $firmware_version
 * @property string|null $ip_address
 * @property int|null $wifi_rssi
 * @property bool $is_online
 * @property bool $mqtt_connected
 * @property \Illuminate\Support\Carbon|null $last_seen_at
 */
class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'name',
        'location',
        'firmware_version',
        'ip_address',
        'wifi_rssi',
        'is_online',
        'mqtt_connected',
        'last_seen_at',
    ];

    protected $casts = [
        'is_online' => 'boolean',
        'mqtt_connected' => 'boolean',
        'last_seen_at' => 'datetime',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(DeviceLog::class);
    }
}
