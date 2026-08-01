<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates POST /api/order/cancel payloads sent by the ESP32 firmware.
 */
class CancelOrderRequest extends FormRequest
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
            'order_id' => ['required', 'string', 'max:64'],
        ];
    }
}
