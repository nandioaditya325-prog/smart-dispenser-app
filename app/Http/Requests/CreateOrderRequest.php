<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates POST /api/order/create payloads sent by the ESP32 firmware.
 */
class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Device identity is validated at the service layer.
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'device_id' => ['required', 'string', 'max:64'],
            'water_type' => ['required', 'string', 'in:normal,dingin,panas'],
            'volume' => ['required', 'integer', 'in:250,500,1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'water_type.in' => 'water_type must be one of: normal, dingin, panas.',
            'volume.in' => 'volume must be one of: 250, 500, 1000 (ml).',
        ];
    }
}
