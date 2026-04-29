<?php

namespace App\Notifications\Channels;

use App\Enums\NotificationStatus;
use App\Models\NotificationLog;
use App\Services\SendoraService;
use Illuminate\Notifications\Notification;

class SendoraChannel
{
    public function __construct(protected SendoraService $service) {}

    public function send($notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toSendora')) {
            return;
        }

        /** @var array{phone:string,message:string,template_key?:?string,user_id?:?int} $payload */
        $payload = $notification->toSendora($notifiable);

        $log = NotificationLog::create([
            'user_id' => $payload['user_id'] ?? ($notifiable->id ?? null),
            'channel' => 'whatsapp',
            'template_key' => $payload['template_key'] ?? null,
            'recipient' => $payload['phone'],
            'payload' => ['message' => $payload['message']],
            'status' => NotificationStatus::Queued->value,
        ]);

        $result = $this->service->send($payload['phone'], $payload['message']);

        $log->update([
            'status' => $result['success'] ? NotificationStatus::Sent->value : NotificationStatus::Failed->value,
            'error' => $result['error'],
            'provider_message_id' => $result['provider_message_id'] ?? null,
            'sent_at' => $result['success'] ? now() : null,
            'payload' => array_merge((array) $log->payload, [
                'response' => $result['response'] ?? null,
                'http_status' => $result['http_status'] ?? null,
            ]),
        ]);

        if (! $result['success']) {
            // Bubble for queue retry
            throw new \RuntimeException('Sendora send failed: '.$result['error']);
        }
    }
}
