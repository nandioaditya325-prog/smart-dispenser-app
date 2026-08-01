<?php

namespace App\Repositories\Contracts;

use App\Models\Device;

interface DeviceRepositoryInterface
{
    /**
     * Finds a device by its firmware-facing device_id, or creates it
     * on first contact (so a new physical unit can start reporting
     * without a manual provisioning step).
     */
    public function findOrCreateByDeviceId(string $deviceId): Device;

    /**
     * Finds a device by device_id, or null if it has never registered.
     */
    public function findByDeviceId(string $deviceId): ?Device;

    /**
     * Updates connectivity/heartbeat fields for a device.
     *
     * @param array<string, mixed> $attributes
     */
    public function updateHeartbeat(Device $device, array $attributes): Device;
}
