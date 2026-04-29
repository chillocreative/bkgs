<?php

namespace App\Console\Commands;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Notifications\PaymentOverdueNotification;
use Illuminate\Console\Command;

class SendOverdueNotices extends Command
{
    protected $signature = 'invoices:send-overdue';

    protected $description = 'Mark overdue + notify teachers whose due date passed yesterday.';

    public function handle(): int
    {
        $yesterday = now()->copy()->subDay()->toDateString();
        $count = 0;

        Invoice::with('user')
            ->where('status', InvoiceStatus::Pending->value)
            ->whereDate('due_date', $yesterday)
            ->chunk(50, function ($invoices) use (&$count) {
                foreach ($invoices as $inv) {
                    $inv->status = InvoiceStatus::Overdue;
                    $inv->save();
                    if ($inv->user) {
                        $inv->user->notify(new PaymentOverdueNotification($inv));
                        $count++;
                    }
                }
            });

        $this->info("Notified {$count} teachers about overdue invoices.");
        return self::SUCCESS;
    }
}
