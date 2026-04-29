<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">{{ __('Invoice') }} {{ $invoice->invoice_number }}</h2>
            <div class="flex gap-2">
                @if ($invoice->isPaid())
                    <a href="{{ route('teacher.invoices.receipt', $invoice) }}" class="px-3 py-2 bg-emerald-600 text-white rounded">{{ __('Download Receipt') }}</a>
                @else
                    <a href="{{ route('payment.pay', $invoice) }}" class="px-3 py-2 bg-indigo-600 text-white rounded">{{ __('Pay Now') }}</a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-5 grid sm:grid-cols-2 gap-3">
                <div><strong>{{ __('Period') }}:</strong> {{ $invoice->period_month->format('M Y') }}</div>
                <div><strong>{{ __('Due Date') }}:</strong> {{ $invoice->due_date->format('d M Y') }}</div>
                <div><strong>{{ __('Amount') }}:</strong> RM {{ number_format($invoice->amount, 2) }}</div>
                <div><strong>{{ __('Late Fee') }}:</strong> RM {{ number_format($invoice->late_fee, 2) }}</div>
                <div class="text-lg col-span-2 mt-2 border-t pt-2"><strong>{{ __('Total') }}:</strong> RM {{ number_format($invoice->total, 2) }}</div>
                <div><strong>{{ __('Status') }}:</strong> <span class="px-2 py-1 rounded text-xs {{ $invoice->status->badgeClass() }}">{{ $invoice->status->label() }}</span></div>
            </div>
        </div>
    </div>
</div>
