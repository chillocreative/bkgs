<?php

namespace App\Notifications;

use App\Models\Invoice;
use App\Models\Payment;
use App\Notifications\Channels\SendoraChannel;
use App\Notifications\Concerns\BuildsSendoraMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable, BuildsSendoraMessage;

    public int $tries = 3;

    public array $backoff = [30, 120, 600];

    public function __construct(public Payment $payment, public Invoice $invoice) {}

    public function via($notifiable): array
    {
        $c = [];
        if (! empty($notifiable->phone ?? null)) $c[] = SendoraChannel::class;
        if (! empty($notifiable->email ?? null)) $c[] = 'mail';
        return $c;
    }

    public function toSendora($notifiable): array
    {
        return $this->buildSendora('payment_received', [
            'invoice_number' => $this->invoice->invoice_number,
            'amount' => number_format((float) $this->payment->amount, 2),
            'paid_at' => optional($this->payment->paid_at)->format('d M Y H:i'),
        ], $notifiable);
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payment received — '.$this->invoice->invoice_number)
            ->line('We received RM '.number_format((float) $this->payment->amount, 2).' for '.$this->invoice->invoice_number.'.')
            ->action('View Invoice', route('teacher.invoices.show', $this->invoice));
    }
}
