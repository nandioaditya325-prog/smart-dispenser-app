<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Shapes the response for GET /api/order/status, matching the status
 * field expected by the ESP32 Payment::pollOrderStatus() parser
 * ({ status: "PAID"|"EXPIRED"|"FAILED"|... }) while also exposing
 * richer order detail for dashboards/other API consumers.
 */
class OrderResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'order_id' => $this->order_id,
            'water_type' => $this->water_type?->value,
            'volume_ml' => $this->volume_ml,
            'nominal' => $this->nominal,
            'status' => $this->mapStatusForDevice(),
            'created_at' => $this->created_at?->toIso8601String(),
            'expires_at' => $this->expires_at?->toIso8601String(),
        ];
    }

    /**
     * Maps the internal OrderStatus enum to the uppercase status token
     * the ESP32 firmware understands (PAID/EXPIRED/FAILED/WAITING).
     */
    private function mapStatusForDevice(): string
    {
        return match ($this->status?->value) {
            'paid', 'completed' => 'PAID',
            'expired' => 'EXPIRED',
            'failed' => 'FAILED',
            'cancelled' => 'CANCELLED',
            default => 'WAITING',
        };
    }
}
