<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Setting;
use App\Notifications\PaymentReceivedNotification;
use Illuminate\Support\Facades\Log;
use Webimpian\BayarcashSdk\Bayarcash;

class BayarcashService
{
    protected Bayarcash $sdk;

    public function __construct(?Bayarcash $sdk = null)
    {
        $this->sdk = $sdk ?? $this->build();
    }

    protected function build(): Bayarcash
    {
        $token = Setting::get('bayarcash_api_token') ?: config('services.bayarcash.api_token', env('BAYARCASH_API_TOKEN', ''));
        $sdk = new Bayarcash((string) $token);
        $sdk->setApiVersion(config('services.bayarcash.api_version', env('BAYARCASH_API_VERSION', 'v3')));
        if ((string) (Setting::get('bayarcash_sandbox') ?? env('BAYARCASH_SANDBOX', '1')) === '1') {
            $sdk->useSandbox();
        }
        $sdk->setTimeout(20);
        return $sdk;
    }

    public function createPaymentIntent(Invoice $invoice): string
    {
        $portalKey = (string) (Setting::get('bayarcash_portal_key') ?: env('BAYARCASH_PORTAL_KEY', ''));
        if ($portalKey === '') {
            throw new \RuntimeException('BayarCash portal key is not configured.');
        }

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'user_id' => $invoice->user_id,
            'amount' => $invoice->total,
            'method' => PaymentMethod::Bayarcash->value,
            'status' => PaymentStatus::Pending->value,
        ]);

        $orderNumber = $invoice->invoice_number.'-'.$payment->id;
        $payment->bayarcash_exchange_reference = $orderNumber;
        $payment->save();

        $resource = $this->sdk->createPaymentIntent([
            'portal_key' => $portalKey,
            'payment_channel' => Bayarcash::FPX,
            'order_number' => $orderNumber,
            'amount' => number_format((float) $invoice->total, 2, '.', ''),
            'payer_name' => $invoice->user->name ?? 'Teacher',
            'payer_email' => $invoice->user->email ?? 'teacher@school.test',
            'payer_telephone_number' => $invoice->user->phone ?? null,
            'return_url' => route('payment.return'),
            'callback_url' => route('webhooks.bayarcash'),
        ]);

        if (empty($resource->url)) {
            Log::warning('BayarCash payment intent returned no URL', ['raw' => (array) $resource]);
            throw new \RuntimeException('BayarCash did not return a checkout URL.');
        }

        return $resource->url;
    }

    public function verifyCallback(array $payload): bool
    {
        $secret = (string) (Setting::get('bayarcash_api_secret_key') ?: env('BAYARCASH_API_SECRET_KEY', ''));
        if ($secret === '' || empty($payload['checksum'])) {
            return false;
        }
        try {
            return $this->sdk->verifyTransactionCallbackData($payload, $secret);
        } catch (\Throwable $e) {
            Log::warning('BayarCash callback verification failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Idempotent: keys on bayarcash_transaction_id.
     */
    public function handleCallback(array $payload): void
    {
        $orderNumber = (string) ($payload['order_number'] ?? '');
        $txId = (string) ($payload['transaction_id'] ?? '');
        $exchangeRef = (string) ($payload['exchange_reference_number'] ?? '');
        $status = (string) ($payload['status'] ?? '');
        $amount = (float) ($payload['amount'] ?? 0);

        $payment = null;

        if ($txId !== '') {
            $payment = Payment::where('bayarcash_transaction_id', $txId)->first();
        }
        if (! $payment && $orderNumber !== '') {
            $payment = Payment::where('bayarcash_exchange_reference', $orderNumber)->first();
        }
        if (! $payment) {
            // Try parsing invoice from order number tail "-{paymentId}"
            $parts = explode('-', $orderNumber);
            $maybePaymentId = (int) end($parts);
            if ($maybePaymentId > 0) {
                $payment = Payment::find($maybePaymentId);
            }
        }
        if (! $payment) {
            Log::warning('BayarCash callback: payment row not found', ['order' => $orderNumber, 'tx' => $txId]);
            return;
        }

        $alreadySuccessful = $payment->status === PaymentStatus::Successful;

        $payment->bayarcash_transaction_id = $txId ?: $payment->bayarcash_transaction_id;
        $payment->bayarcash_exchange_reference = $exchangeRef ?: $payment->bayarcash_exchange_reference;
        $payment->bayarcash_payment_channel = (string) ($payload['payment_channel_id'] ?? $payload['payment_channel'] ?? null);
        $payment->raw_callback_payload = $payload;

        // BayarCash status: '3' or 'Successful' for paid (varies by API version)
        $isSuccess = in_array($status, ['3', 'Successful', 'successful', 'Paid', 'paid'], true);
        if ($isSuccess) {
            $payment->status = PaymentStatus::Successful;
            $payment->paid_at = $payment->paid_at ?: now();
            if ($amount > 0) {
                $payment->amount = $amount;
            }
        } elseif (in_array($status, ['4', 'Failed', 'failed', 'Cancelled', 'cancelled'], true)) {
            $payment->status = PaymentStatus::Failed;
        }
        $payment->save();

        if ($payment->status === PaymentStatus::Successful) {
            $invoice = $payment->invoice;
            if ($invoice && ! $invoice->isPaid()) {
                $invoice->status = InvoiceStatus::Paid;
                $invoice->paid_at = $payment->paid_at;
                $invoice->save();
            }
            if (! $alreadySuccessful && $invoice && $invoice->user) {
                $invoice->user->notify(new PaymentReceivedNotification($payment, $invoice));
            }
        }
    }

    public function handleReturn(array $payload): array
    {
        $verified = $this->verifyReturn($payload);
        return [
            'verified' => $verified,
            'status' => (string) ($payload['status'] ?? ''),
            'amount' => (float) ($payload['amount'] ?? 0),
            'order_number' => (string) ($payload['order_number'] ?? ''),
            'transaction_id' => (string) ($payload['transaction_id'] ?? ''),
        ];
    }

    public function verifyReturn(array $payload): bool
    {
        $secret = (string) (Setting::get('bayarcash_api_secret_key') ?: env('BAYARCASH_API_SECRET_KEY', ''));
        if ($secret === '' || empty($payload['checksum'])) {
            return false;
        }
        try {
            return $this->sdk->verifyReturnUrlCallbackData($payload, $secret);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
