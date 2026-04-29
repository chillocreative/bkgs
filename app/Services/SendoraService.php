<?php

namespace App\Services;

use App\Models\Setting;
use App\Support\PhoneFormatter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendoraService
{
    public function send(string $rawPhone, string $message): array
    {
        $apiKey = (string) (Setting::get('sendora_api_key') ?: env('SENDORA_API_KEY', ''));
        $baseUrl = rtrim((string) (Setting::get('sendora_base_url') ?: env('SENDORA_BASE_URL', 'https://sendora.cc/api/v1')), '/');
        $deviceId = Setting::get('sendora_device_id') ?: env('SENDORA_DEVICE_ID');
        $timeout = (int) env('SENDORA_TIMEOUT', 10);

        if ($apiKey === '') {
            return [
                'success' => false,
                'status' => 'failed',
                'error' => 'Sendora API key not configured.',
                'response' => null,
                'provider_message_id' => null,
            ];
        }

        $phone = PhoneFormatter::toSendora($rawPhone);

        $payload = [
            'phone' => $phone,
            'message' => $message,
        ];
        if ($deviceId) {
            $payload['device_id'] = is_numeric($deviceId) ? (int) $deviceId : $deviceId;
        }

        try {
            $resp = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout($timeout)
                ->retry(2, 500, throw: false)
                ->post($baseUrl.'/messages/send', $payload);
        } catch (\Throwable $e) {
            Log::warning('Sendora HTTP error', ['error' => $e->getMessage(), 'phone' => $phone]);
            return [
                'success' => false,
                'status' => 'failed',
                'error' => $e->getMessage(),
                'response' => null,
                'provider_message_id' => null,
            ];
        }

        $body = $resp->json() ?? [];
        $ok = $resp->successful() && (($body['success'] ?? false) === true);

        return [
            'success' => $ok,
            'status' => $ok ? 'sent' : 'failed',
            'error' => $ok ? null : ($body['message'] ?? ('HTTP '.$resp->status())),
            'response' => $body,
            'provider_message_id' => $body['data']['message_id'] ?? $body['data']['id'] ?? null,
            'http_status' => $resp->status(),
            'phone' => $phone,
        ];
    }
}
