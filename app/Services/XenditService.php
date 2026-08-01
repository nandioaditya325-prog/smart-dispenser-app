<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Thin wrapper around the Xendit QR Codes (Dynamic QRIS) API.
 *
 * Uses the modern "QR Codes" endpoint (POST /qr_codes) which creates a
 * dynamic, single-use QRIS code with a fixed amount, as opposed to the
 * legacy static QRIS product. Secret key is read from config/xendit.php,
 * which in turn reads from ENV — never hardcoded.
 */
class XenditService
{
    private string $secretKey;

    private string $baseUrl;

    private string $callbackToken;

    public function __construct()
    {
        $this->secretKey = (string) config('xendit.secret_key');
        $this->baseUrl = (string) config('xendit.base_url', 'https://api.xendit.co');
        $this->callbackToken = (string) config('xendit.callback_token');

        if ($this->secretKey === '') {
            throw new RuntimeException('XENDIT_SECRET_KEY is not configured.');
        }
    }

    /**
     * Creates a dynamic QRIS code for the given external id and amount.
     *
     * @return array{
     *     qr_id: string,
     *     qr_string: string,
     *     reference_id: string,
     *     raw: array<string, mixed>
     * }
     */
    public function createDynamicQris(string $externalId, int amount, int $expirySeconds): array
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->baseUrl($this->baseUrl)
            ->post('/qr_codes', [
                'reference_id' => $externalId,
                'type' => 'DYNAMIC',
                'currency' => 'IDR',
                'amount' => $amount,
                'expires_at' => now()->addSeconds($expirySeconds)->toIso8601String(),
                'channel_code' => 'ID_DANA', // ignored for multi-wallet dynamic QRIS; QR works across QRIS-compliant apps
            ]);

        if ($response->failed()) {
            Log::channel('stack')->error('Xendit createDynamicQris failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new RuntimeException('Failed to create Xendit QRIS: '.$response->body());
        }

        $body = $response->json();

        return [
            'qr_id' => $body['id'] ?? '',
            'qr_string' => $body['qr_string'] ?? '',
            'reference_id' => $body['reference_id'] ?? $externalId,
            'raw' => $body,
        ];
    }

    /**
     * Requests cancellation/deactivation of a dynamic QRIS code so it
     * can no longer be paid once the order is cancelled locally.
     */
    public function deactivateQris(string $qrId): bool
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->baseUrl($this->baseUrl)
            ->post("/qr_codes/{$qrId}", ['status' => 'INACTIVE']);

        return $response->successful();
    }

    /**
     * Verifies the `x-callback-token` header sent by Xendit on every
     * webhook call against the configured callback verification token.
     * Uses constant-time comparison to avoid timing attacks.
     */
    public function verifyWebhookSignature(?string $callbackTokenHeader): bool
    {
        if ($this->callbackToken === '' || $callbackTokenHeader === null) {
            return false;
        }

        return hash_equals($this->callbackToken, $callbackTokenHeader);
    }
}
