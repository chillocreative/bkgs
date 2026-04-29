<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('My Invoices') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-4">
                <div class="mb-3 flex items-center gap-3">
                    <label class="text-sm">{{ __('Year') }}</label>
                    <select wire:model.live="year" class="border-gray-300 rounded">
                        @for ($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs uppercase">{{ __('Invoice #') }}</th>
                                <th class="px-3 py-2 text-left text-xs uppercase">{{ __('Period') }}</th>
                                <th class="px-3 py-2 text-right text-xs uppercase">{{ __('Total') }}</th>
                                <th class="px-3 py-2 text-left text-xs uppercase">{{ __('Status') }}</th>
                                <th class="px-3 py-2 text-left text-xs uppercase">{{ __('Due') }}</th>
                                <th class="px-3 py-2 text-right text-xs uppercase">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($invoices as $inv)
                                <tr>
                                    <td class="px-3 py-2"><a class="text-indigo-600" href="{{ route('teacher.invoices.show', $inv) }}" wire:navigate>{{ $inv->invoice_number }}</a></td>
                                    <td class="px-3 py-2">{{ $inv->period_month->format('M Y') }}</td>
                                    <td class="px-3 py-2 text-right">RM {{ number_format($inv->total, 2) }}</td>
                                    <td class="px-3 py-2"><span class="px-2 py-1 rounded text-xs {{ $inv->status->badgeClass() }}">{{ $inv->status->label() }}</span></td>
                                    <td class="px-3 py-2">{{ $inv->due_date->format('d M Y') }}</td>
                                    <td class="px-3 py-2 text-right">
                                        @if ($inv->isPaid())
                                            <a class="text-emerald-700" href="{{ route('teacher.invoices.receipt', $inv) }}">{{ __('Receipt') }}</a>
                                        @else
                                            <a class="px-2 py-1 bg-indigo-600 text-white rounded" href="{{ route('payment.pay', $inv) }}">{{ __('Pay Now') }}</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-3 py-8 text-center text-gray-400">{{ __('No invoices for this year.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $invoices->links() }}</div>
            </div>
        </div>
    </div>
</div>
