<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">{{ __('Invoices') }}</h2>
            <a href="{{ route('admin.invoices.generate') }}" wire:navigate class="px-3 py-2 bg-indigo-600 text-white rounded">{{ __('Generate Monthly') }}</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-4">
                <div class="grid sm:grid-cols-3 gap-3 mb-4">
                    <input wire:model.live="month" type="month" class="border-gray-300 rounded" />
                    <select wire:model.live="status" class="border-gray-300 rounded">
                        <option value="">{{ __('All statuses') }}</option>
                        <option value="pending">{{ __('Pending') }}</option>
                        <option value="paid">{{ __('Paid') }}</option>
                        <option value="overdue">{{ __('Overdue') }}</option>
                        <option value="cancelled">{{ __('Cancelled') }}</option>
                    </select>
                    <select wire:model.live="teacher_id" class="border-gray-300 rounded">
                        <option value="">{{ __('All teachers') }}</option>
                        @foreach ($teachers as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs uppercase">{{ __('Invoice #') }}</th>
                                <th class="px-3 py-2 text-left text-xs uppercase">{{ __('Teacher') }}</th>
                                <th class="px-3 py-2 text-left text-xs uppercase">{{ __('Period') }}</th>
                                <th class="px-3 py-2 text-right text-xs uppercase">{{ __('Total') }}</th>
                                <th class="px-3 py-2 text-left text-xs uppercase">{{ __('Status') }}</th>
                                <th class="px-3 py-2 text-left text-xs uppercase">{{ __('Due') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($invoices as $inv)
                                <tr>
                                    <td class="px-3 py-2"><a class="text-indigo-600" href="{{ route('admin.invoices.show', $inv) }}" wire:navigate>{{ $inv->invoice_number }}</a></td>
                                    <td class="px-3 py-2">{{ $inv->user->name ?? '—' }}</td>
                                    <td class="px-3 py-2">{{ $inv->period_month->format('M Y') }}</td>
                                    <td class="px-3 py-2 text-right">RM {{ number_format($inv->total, 2) }}</td>
                                    <td class="px-3 py-2"><span class="px-2 py-1 rounded text-xs {{ $inv->status->badgeClass() }}">{{ $inv->status->label() }}</span></td>
                                    <td class="px-3 py-2">{{ $inv->due_date->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-3 py-8 text-center text-gray-400">{{ __('No invoices match the filters.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $invoices->links() }}</div>
            </div>
        </div>
    </div>
</div>
