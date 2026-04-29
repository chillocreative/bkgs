<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">{{ __('Invoice') }} {{ $invoice->invoice_number }}</h2>
            <div class="flex gap-2">
                @unless ($invoice->isPaid())
                    <a href="{{ route('admin.payments.record', $invoice) }}" wire:navigate class="px-3 py-2 bg-emerald-600 text-white rounded">{{ __('Record Manual Payment') }}</a>
                    <button wire:click="sendReminder" class="px-3 py-2 bg-indigo-600 text-white rounded" wire:loading.attr="disabled">{{ __('Send WhatsApp Reminder') }}</button>
                @endunless
                @if ($invoice->isPaid())
                    <a href="{{ route('teacher.invoices.receipt', $invoice) }}" class="px-3 py-2 bg-gray-700 text-white rounded">{{ __('Download Receipt') }}</a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-5 grid sm:grid-cols-2 gap-3">
                <div><strong>{{ __('Teacher') }}:</strong> {{ $invoice->user->name ?? '—' }}</div>
                <div><strong>{{ __('Phone') }}:</strong> {{ $invoice->user->phone ?? '—' }}</div>
                <div><strong>{{ __('Period') }}:</strong> {{ $invoice->period_month->format('M Y') }}</div>
                <div><strong>{{ __('Due Date') }}:</strong> {{ $invoice->due_date->format('d M Y') }}</div>
                <div><strong>{{ __('Amount') }}:</strong> RM {{ number_format($invoice->amount, 2) }}</div>
                <div><strong>{{ __('Late Fee') }}:</strong> RM {{ number_format($invoice->late_fee, 2) }}</div>
                <div class="text-lg"><strong>{{ __('Total') }}:</strong> RM {{ number_format($invoice->total, 2) }}</div>
                <div><strong>{{ __('Status') }}:</strong> <span class="px-2 py-1 rounded text-xs {{ $invoice->status->badgeClass() }}">{{ $invoice->status->label() }}</span></div>
            </div>

            <h3 class="mt-6 mb-3 font-semibold">{{ __('Payments') }}</h3>
            <div class="bg-white shadow rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs uppercase">{{ __('Method') }}</th>
                            <th class="px-3 py-2 text-right text-xs uppercase">{{ __('Amount') }}</th>
                            <th class="px-3 py-2 text-left text-xs uppercase">{{ __('Status') }}</th>
                            <th class="px-3 py-2 text-left text-xs uppercase">{{ __('Paid At') }}</th>
                            <th class="px-3 py-2 text-left text-xs uppercase">{{ __('Reference') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($invoice->payments as $p)
                            <tr>
                                <td class="px-3 py-2">{{ $p->method?->label() }}</td>
                                <td class="px-3 py-2 text-right">RM {{ number_format($p->amount, 2) }}</td>
                                <td class="px-3 py-2">{{ $p->status->value }}</td>
                                <td class="px-3 py-2">{{ optional($p->paid_at)->format('d M Y H:i') }}</td>
                                <td class="px-3 py-2 text-xs text-gray-500">{{ $p->bayarcash_transaction_id ?? $p->bayarcash_exchange_reference ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-3 py-6 text-center text-gray-400">{{ __('No payment recorded.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
