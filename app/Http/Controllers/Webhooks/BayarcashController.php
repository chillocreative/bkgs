<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Services\BayarcashService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BayarcashController extends Controller
{
    public function callback(Request $request, BayarcashService $svc)
    {
        $payload = $request->all();

        if (! $svc->verifyCallback($payload)) {
            Log::warning('BayarCash callback rejected (bad checksum)', ['payload' => $payload]);
            return response()->json(['ok' => false, 'reason' => 'invalid_checksum'], 400);
        }

        try {
            $svc->handleCallback($payload);
        } catch (\Throwable $e) {
            Log::error('BayarCash callback handler error', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);
            return response()->json(['ok' => false], 500);
        }

        return response()->json(['ok' => true]);
    }
}
