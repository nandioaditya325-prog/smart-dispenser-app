<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates POST /api/device/status heartbeat payloads sent periodically
 * by the ESP32 firmware's TaskNetwork.
 */
class DeviceStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'device_id' => ['required', 'string', 'max:64'],
            'firmware' => ['sometimes', 'string', 'max:32'],
            'rssi' => ['sometimes', 'integer'],
            'mqtt' => ['sometimes', 'boolean'],
        ];
    }
}
