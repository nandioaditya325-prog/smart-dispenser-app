<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\DeviceLog;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Services\OrderService;
use App\Services\XenditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Handles POST /api/xendit/webhook.
 *
 * Every request is verified against the `x-callback-token` header
 * before any data is trusted, per Xendit's webhook security guidance.
 * On a verified PAID event, the matching order is marked paid and an
 * MQTT message is published so the ESP32 can start dispensing.
 */
class XenditWebhookController extends Controller
{
    public function __construct(
        private readonly XenditService $xendit,
        private readonly OrderService $orderService,
        private readonly OrderRepositoryInterface $orders,
    ) {
    }

    public function handle(Request $request): JsonResponse
    {
        $callbackToken = $request->header('x-callback-token');

        if (! $this->xendit->verifyWebhookSignature($callbackToken)) {
            $this->logWebhook('warning', 'Rejected webhook: invalid callback token', $request->all());

            return response()->json(['success' => false, 'message' => 'Invalid signature.'], 401);
        }

        $payload = $request->all();
        $event = $payload['event'] ?? $payload['status'] ?? null;
        $referenceId = $payload['reference_id'] ?? $payload['data']['reference_id'] ?? null;
        $externalId = $payload['external_id'] ?? $payload['data']['reference_id'] ?? $referenceId;

        $this->logWebhook('info', 'Xendit webhook received', $payload);

        if (! $externalId) {
            return response()->json(['success' => false, 'message' => 'Missing reference/external id.'], 422);
        }

        $order = $this->orders->findByOrderIdWithPayment((string) $externalId);

        if (! $order) {
            $this->logWebhook('warning', "Webhook for unknown order: {$externalId}", $payload);

            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        $status = strtoupper((string) ($payload['status'] ?? $payload['data']['status'] ?? $event ?? ''));

        match (true) {
            str_contains($status, 'PAID'), str_contains($status, 'SUCCEEDED') => $this->orderService->markOrderAsPaid(
                $order,
                (string) ($payload['payment_method'] ?? $payload['data']['payment_method']['channel'] ?? 'QRIS')
            ),
            str_contains($status, 'EXPIRED') => $this->orderService->markOrderAsFailed($order, OrderStatus::Expired),
            str_contains($status, 'FAILED') => $this->orderService->markOrderAsFailed($order, OrderStatus::Failed),
            default => null,
        };

        return response()->json(['success' => true]);
    }

    /**
     * Persists a webhook event to the logs table for audit/debugging.
     *
     * @param array<string, mixed> $context
     */
    private function logWebhook(string $level, string $message, array $context): void
    {
        try {
            DeviceLog::query()->create([
                'level' => $level,
                'source' => 'xendit_webhook',
                'message' => $message,
                'context' => $context,
            ]);
        } catch (\Throwable $exception) {
            Log::channel('stack')->error('Failed to persist webhook log', [
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
