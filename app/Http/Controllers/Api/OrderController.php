<?php

namespace App\Http\Controllers\Api;

use App\Enums\WaterType;
use App\Http\Controllers\Controller;
use App\Http\Requests\CancelOrderRequest;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\OrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Handles the three order-related endpoints consumed directly by the
 * ESP32 firmware's payment.cpp module:
 *   POST /api/order/create
 *   POST /api/order/cancel
 *   GET  /api/order/status
 */
class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService)
    {
    }

    /**
     * Creates a new order + dynamic QRIS and returns the exact payload
     * shape the firmware expects:
     * { success, order_id, nominal, qr_string }.
     */
    public function create(CreateOrderRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $result = $this->orderService->createOrder(
                deviceId: $validated['device_id'],
                waterType: WaterType::from($validated['water_type']),
                volumeMl: (int) $validated['volume'],
            );
        } catch (Throwable $exception) {
            Log::channel('stack')->error('Order creation failed', [
                'device_id' => $validated['device_id'],
                'error' => $exception->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create order.',
            ], 502);
        }

        return response()->json([
            'success' => true,
            'order_id' => $result['order']->order_id,
            'nominal' => $result['nominal'],
            'qr_string' => $result['qr_string'],
        ], 201);
    }

    /**
     * Cancels an in-progress order (e.g. user pressed "Batalkan").
     */
    public function cancel(CancelOrderRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $order = $this->orderService->cancelOrder($validated['order_id']);

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'order_id' => $order->order_id,
            'status' => 'CANCELLED',
        ]);
    }

    /**
     * Returns the current status of an order, used as a fallback
     * reconciliation path if the device misses the MQTT payment event.
     */
    public function status(OrderStatusRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $order = $this->orderService->getOrderStatus($validated['order_id']);

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        return response()->json((new OrderResource($order))->resolve());
    }
}
