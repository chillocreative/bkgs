<?php

namespace App\Notifications\Concerns;

use App\Models\Setting;
use App\Models\WhatsappTemplate;
use App\Support\TemplateRenderer;

trait BuildsSendoraMessage
{
    protected function buildSendora(string $templateKey, array $vars, $notifiable): array
    {
        $tpl = WhatsappTemplate::findByKey($templateKey);
        $body = $tpl?->body_template ?? 'Hello {{ teacher_name }}';

        $vars = array_merge([
            'school_name' => Setting::get('school_name', config('app.name')),
            'teacher_name' => $notifiable->name ?? '',
        ], $vars);

        return [
            'phone' => $notifiable->phone ?? $notifiable->routeNotificationFor('sendora'),
            'message' => TemplateRenderer::render($body, $vars),
            'template_key' => $templateKey,
            'user_id' => $notifiable->id ?? null,
        ];
    }
}
