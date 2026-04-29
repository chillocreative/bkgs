<?php

namespace App\Console\Commands;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Notifications\PaymentReminderNotification;
use Illuminate\Console\Command;

class SendPaymentReminders extends Command
{
    protected $signature = 'invoices:send-reminders {--days=3 : Days before due date}';

    protected $description = 'Send WhatsApp reminders for invoices due in N days.';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $target = now()->copy()->startOfDay()->addDays($days)->toDateString();

        $count = 0;
        Invoice::with('user')
            ->where('status', InvoiceStatus::Pending->value)
            ->whereDate('due_date', $target)
            ->chunk(50, function ($invoices) use (&$count) {
                foreach ($invoices as $inv) {
                    if (! $inv->user) continue;
                    $inv->user->notify(new PaymentReminderNotification($inv));
                    $count++;
                }
            });

        $this->info("Queued {$count} reminders for invoices due {$target}");
        return self::SUCCESS;
    }
}
