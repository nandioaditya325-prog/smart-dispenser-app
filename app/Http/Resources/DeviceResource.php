<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Shapes the response for GET /api/device/status.
 */
class DeviceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'device_id' => $this->device_id,
            'name' => $this->name,
            'firmware_version' => $this->firmware_version,
            'is_online' => $this->is_online,
            'mqtt_connected' => $this->mqtt_connected,
            'wifi_rssi' => $this->wifi_rssi,
            'last_seen_at' => $this->last_seen_at?->toIso8601String(),
        ];
    }
}
