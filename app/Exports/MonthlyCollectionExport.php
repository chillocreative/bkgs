<?php

namespace App\Exports;

use App\Models\Payment;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class MonthlyCollectionExport implements FromQuery, WithHeadings, WithMapping, WithTitle
{
    public function __construct(public Carbon $month) {}

    public function query()
    {
        return Payment::query()
            ->with(['user:id,name,email,phone', 'invoice:id,invoice_number,period_month,total'])
            ->where('status', 'successful')
            ->whereBetween('paid_at', [$this->month->copy()->startOfMonth(), $this->month->copy()->endOfMonth()])
            ->orderBy('paid_at');
    }

    public function headings(): array
    {
        return ['Paid At', 'Teacher', 'Email', 'Phone', 'Invoice #', 'Period', 'Method', 'Amount (RM)', 'Reference'];
    }

    public function map($p): array
    {
        return [
            optional($p->paid_at)->format('Y-m-d H:i'),
            $p->user->name ?? '—',
            $p->user->email ?? '—',
            $p->user->phone ?? '—',
            $p->invoice->invoice_number ?? '—',
            optional($p->invoice?->period_month)->format('Y-m'),
            $p->method?->label(),
            number_format((float) $p->amount, 2, '.', ''),
            $p->bayarcash_transaction_id ?? $p->bayarcash_exchange_reference ?? '',
        ];
    }

    public function title(): string
    {
        return $this->month->format('M Y');
    }
}
