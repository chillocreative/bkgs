<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SendoraController extends Controller
{
    public function webhook(Request $request)
    {
        $payload = $request->all();
        Log::info('Sendora webhook received', $payload);

        // No documented signature in Sendora API docs at the time of build —
        // accept and best-effort update by provider_message_id.
        $providerId = (string) ($payload['message_id'] ?? $payload['id'] ?? '');
        $status = (string) ($payload['status'] ?? '');

        if ($providerId !== '' && $status !== '') {
            NotificationLog::where('provider_message_id', $providerId)
                ->update(['status' => match (strtolower($status)) {
                    'delivered', 'read' => \App\Enums\NotificationStatus::Delivered->value,
                    'sent' => \App\Enums\NotificationStatus::Sent->value,
                    'failed', 'error' => \App\Enums\NotificationStatus::Failed->value,
                    default => \App\Enums\NotificationStatus::Sent->value,
                }]);
        }

        return response()->json(['ok' => true]);
    }
}
