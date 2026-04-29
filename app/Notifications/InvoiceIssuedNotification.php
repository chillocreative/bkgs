<?php

namespace App\Notifications;

use App\Models\Invoice;
use App\Notifications\Channels\SendoraChannel;
use App\Notifications\Concerns\BuildsSendoraMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceIssuedNotification extends Notification implements ShouldQueue
{
    use Queueable, BuildsSendoraMessage;

    public int $tries = 3;

    public array $backoff = [30, 120, 600];

    public int $timeout = 20;

    public function __construct(public Invoice $invoice) {}

    public function via($notifiable): array
    {
        $channels = [];
        if (! empty($notifiable->phone ?? null)) {
            $channels[] = SendoraChannel::class;
        }
        if (! empty($notifiable->email ?? null)) {
            $channels[] = 'mail';
        }
        return $channels;
    }

    public function toSendora($notifiable): array
    {
        return $this->buildSendora('invoice_issued', $this->variables(), $notifiable);
    }

    public function toMail($notifiable): MailMessage
    {
        $v = $this->variables();
        return (new MailMessage)
            ->subject('Invoice '.$v['invoice_number'].' — '.$v['month'])
            ->line('Invoice for '.$v['month'].': RM '.$v['amount'])
            ->line('Due date: '.$v['due_date'])
            ->action('Pay Now', $v['pay_url']);
    }

    protected function variables(): array
    {
        return [
            'invoice_number' => $this->invoice->invoice_number,
            'month' => $this->invoice->period_month->translatedFormat('F Y'),
            'amount' => number_format((float) $this->invoice->total, 2),
            'due_date' => $this->invoice->due_date->format('d M Y'),
            'pay_url' => route('payment.pay', $this->invoice),
        ];
    }
}
