<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('Payments') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-4">
                <div class="grid sm:grid-cols-3 gap-3 mb-4">
                    <input wire:model.live="month" type="month" class="border-gray-300 rounded" />
                    <select wire:model.live="method" class="border-gray-300 rounded">
                        <option value="">{{ __('All methods') }}</option>
                        <option value="bayarcash">BayarCash</option>
                        <option value="manual_cash">{{ __('Cash') }}</option>
                        <option value="manual_transfer">{{ __('Bank Transfer') }}</option>
                        <option value="manual_cheque">{{ __('Cheque') }}</option>
                    </select>
                    <a href="{{ route('admin.reports.monthly') }}{{ $month ? '?month='.$month : '' }}" class="px-3 py-2 bg-emerald-600 text-white rounded text-center">{{ __('Export Excel') }}</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs uppercase">{{ __('Paid At') }}</th>
                                <th class="px-3 py-2 text-left text-xs uppercase">{{ __('Teacher') }}</th>
                                <th class="px-3 py-2 text-left text-xs uppercase">{{ __('Invoice') }}</th>
                                <th class="px-3 py-2 text-left text-xs uppercase">{{ __('Method') }}</th>
                                <th class="px-3 py-2 text-right text-xs uppercase">{{ __('Amount') }}</th>
                                <th class="px-3 py-2 text-left text-xs uppercase">{{ __('Reference') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($payments as $p)
                                <tr>
                                    <td class="px-3 py-2">{{ optional($p->paid_at)->format('d M Y H:i') }}</td>
                                    <td class="px-3 py-2">{{ $p->user->name ?? '—' }}</td>
                                    <td class="px-3 py-2">{{ $p->invoice->invoice_number ?? '—' }}</td>
                                    <td class="px-3 py-2">{{ $p->method?->label() }}</td>
                                    <td class="px-3 py-2 text-right">RM {{ number_format($p->amount, 2) }}</td>
                                    <td class="px-3 py-2 text-xs text-gray-500">{{ $p->bayarcash_transaction_id ?? $p->bayarcash_exchange_reference ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-3 py-8 text-center text-gray-400">{{ __('No payments match.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $payments->links() }}</div>
            </div>
        </div>
    </div>
</div>
