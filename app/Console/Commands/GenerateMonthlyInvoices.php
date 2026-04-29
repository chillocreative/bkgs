<?php

namespace App\Console\Commands;

use App\Services\InvoiceGenerator;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateMonthlyInvoices extends Command
{
    protected $signature = 'invoices:generate-monthly {--month= : YYYY-MM, defaults to current month}';

    protected $description = 'Generate one invoice per active teacher for the given month (idempotent).';

    public function handle(InvoiceGenerator $gen): int
    {
        $month = $this->option('month');
        $period = $month ? Carbon::createFromFormat('Y-m', $month)->startOfMonth() : Carbon::now()->startOfMonth();

        $r = $gen->generateForMonth($period);
        $this->info("Created: {$r['created']} · Skipped: {$r['skipped']} for {$period->format('M Y')}");
        return self::SUCCESS;
    }
}
