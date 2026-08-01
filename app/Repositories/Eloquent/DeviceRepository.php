<?php

namespace App\Repositories\Eloquent;

use App\Models\Device;
use App\Repositories\Contracts\DeviceRepositoryInterface;

class DeviceRepository implements DeviceRepositoryInterface
{
    public function findOrCreateByDeviceId(string $deviceId): Device
    {
        return Device::query()->firstOrCreate(
            ['device_id' => $deviceId],
            ['name' => $deviceId, 'is_online' => true, 'last_seen_at' => now()]
        );
    }

    public function findByDeviceId(string $deviceId): ?Device
    {
        return Device::query()->where('device_id', $deviceId)->first();
    }

    public function updateHeartbeat(Device $device, array $attributes): Device
    {
        $device->update(array_merge($attributes, [
            'is_online' => true,
            'last_seen_at' => now(),
        ]));

        return $device->fresh();
    }
}
