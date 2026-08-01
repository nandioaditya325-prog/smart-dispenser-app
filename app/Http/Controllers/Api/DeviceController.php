<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeviceStatusRequest;
use App\Http\Resources\DeviceResource;
use App\Repositories\Contracts\DeviceRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Handles device connectivity endpoints:
 *   POST /api/device/status  -> heartbeat ingestion from the ESP32
 *   GET  /api/device/status  -> status query (dashboard/monitoring)
 */
class DeviceController extends Controller
{
    public function __construct(private readonly DeviceRepositoryInterface $devices)
    {
    }

    /**
     * Ingests a periodic heartbeat sent by Payment::sendDeviceStatus()
     * on the ESP32 (device_id, firmware, rssi, mqtt connectivity).
     */
    public function heartbeat(DeviceStatusRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $device = $this->devices->findOrCreateByDeviceId($validated['device_id']);

        $device = $this->devices->updateHeartbeat($device, [
            'firmware_version' => $validated['firmware'] ?? $device->firmware_version,
            'wifi_rssi' => $validated['rssi'] ?? $device->wifi_rssi,
            'mqtt_connected' => $validated['mqtt'] ?? $device->mqtt_connected,
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'device_id' => $device->device_id,
        ]);
    }

    /**
     * Returns the last known status for a device, for dashboards or
     * monitoring tools. Expects ?device_id=DISPENSER001.
     */
    public function show(Request $request): JsonResponse
    {
        $deviceId = (string) $request->query('device_id', '');
        $device = $deviceId !== '' ? $this->devices->findByDeviceId($deviceId) : null;

        if (! $device) {
            return response()->json([
                'success' => false,
                'message' => 'Device not found.',
            ], 404);
        }

        return response()->json((new DeviceResource($device))->resolve());
    }
}
