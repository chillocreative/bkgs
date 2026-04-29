<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Models\FeeStructure;
use App\Models\Invoice;
use App\Models\User;
use App\Notifications\InvoiceIssuedNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class InvoiceGenerator
{
    /**
     * Generate (or skip if exists) one invoice per active teacher for the given period (any date in target month).
     * Returns ['created' => int, 'skipped' => int].
     */
    public function generateForMonth(?Carbon $period = null): array
    {
        $period = ($period ?? now())->copy()->startOfMonth();

        $defaultFee = FeeStructure::default();

        return DB::transaction(function () use ($period, $defaultFee) {
            $created = 0;
            $skipped = 0;

            $teachers = User::role('teacher')
                ->where('is_active', true)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            foreach ($teachers as $teacher) {
                $exists = Invoice::where('user_id', $teacher->id)
                    ->whereDate('period_month', $period->toDateString())
                    ->exists();
                if ($exists) {
                    $skipped++;
                    continue;
                }

                $amount = $teacher->monthly_fee_amount !== null
                    ? (float) $teacher->monthly_fee_amount
                    : ($defaultFee?->amount ? (float) $defaultFee->amount : 0);

                if ($amount <= 0) {
                    $skipped++;
                    continue; // skip zero-amount teachers
                }

                $dueDay = $defaultFee?->due_day ?? 5;
                $dueDate = $period->copy()->day(min($dueDay, $period->daysInMonth));

                $invoice = Invoice::create([
                    'user_id' => $teacher->id,
                    'fee_structure_id' => $defaultFee?->id,
                    'invoice_number' => $this->nextInvoiceNumber($period),
                    'period_month' => $period->toDateString(),
                    'amount' => $amount,
                    'late_fee' => 0,
                    'total' => $amount,
                    'due_date' => $dueDate->toDateString(),
                    'status' => InvoiceStatus::Pending->value,
                ]);

                Notification::send($teacher, new InvoiceIssuedNotification($invoice));
                $created++;
            }

            return ['created' => $created, 'skipped' => $skipped];
        });
    }

    public function nextInvoiceNumber(Carbon $period): string
    {
        $prefix = 'INV-'.$period->format('Ym').'-';

        $last = Invoice::where('invoice_number', 'like', $prefix.'%')
            ->orderByDesc('invoice_number')
            ->lockForUpdate()
            ->value('invoice_number');

        $next = 1;
        if ($last) {
            $tail = (int) substr($last, strrpos($last, '-') + 1);
            $next = $tail + 1;
        }
        return $prefix.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
