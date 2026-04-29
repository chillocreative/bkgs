<?php

namespace App\Notifications;

use App\Models\Invoice;
use App\Notifications\Channels\SendoraChannel;
use App\Notifications\Concerns\BuildsSendoraMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PaymentOverdueNotification extends Notification implements ShouldQueue
{
    use Queueable, BuildsSendoraMessage;

    public int $tries = 3;

    public array $backoff = [30, 120, 600];

    public function __construct(public Invoice $invoice) {}

    public function via($notifiable): array
    {
        return ! empty($notifiable->phone ?? null) ? [SendoraChannel::class] : [];
    }

    public function toSendora($notifiable): array
    {
        return $this->buildSendora('payment_overdue', [
            'invoice_number' => $this->invoice->invoice_number,
            'amount' => number_format((float) $this->invoice->total, 2),
            'due_date' => $this->invoice->due_date->format('d M Y'),
            'pay_url' => route('payment.pay', $this->invoice),
        ], $notifiable);
    }
}
