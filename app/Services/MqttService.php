<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\Facades\MQTT;
use Throwable;

/**
 * Publishes JSON payloads to the MQTT broker from the Laravel backend,
 * primarily used by the Xendit webhook to notify the ESP32 that a
 * payment has been confirmed.
 *
 * Backed by php-mqtt/laravel-client (config/mqtt.php), connecting over
 * TLS to the same broker the ESP32 firmware uses.
 */
class MqttService
{
    /**
     * Publishes the payment confirmation event to smartdispenser/payment.
     * Payload shape matches exactly what mqtt.cpp::onMessage() expects.
     */
    public function publishPaymentStatus(string $orderId, string $status): void
    {
        $this->publish(config('mqtt.topics.payment'), [
            'order_id' => $orderId,
            'status' => $status,
        ]);
    }

    /**
     * Publishes a generic status update to smartdispenser/status.
     */
    public function publishDeviceStatus(string $deviceId, array $payload): void
    {
        $this->publish(config('mqtt.topics.status'), array_merge(
            ['device_id' => $deviceId],
            $payload
        ));
    }

    /**
     * Publishes an error notification to smartdispenser/error.
     */
    public function publishError(string $deviceId, string $message): void
    {
        $this->publish(config('mqtt.topics.error'), [
            'device_id' => $deviceId,
            'message' => $message,
        ]);
    }

    /**
     * Low-level publish helper: connects, publishes with QoS 1, and
     * disconnects. Connection errors are logged but never thrown, so a
     * broker outage does not fail the HTTP webhook response to Xendit.
     *
     * @param array<string, mixed> $payload
     */
    private function publish(string $topic, array $payload): void
    {
        try {
            MQTT::connection()->publish($topic, json_encode($payload), 1);
        } catch (Throwable $exception) {
            Log::channel('stack')->error('MQTT publish failed', [
                'topic' => $topic,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
