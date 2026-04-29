<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">{{ $user->name }}</h2>
            <a href="{{ route('admin.teachers.edit', $user) }}" wire:navigate class="px-3 py-2 bg-indigo-600 text-white rounded">{{ __('Edit') }}</a>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid sm:grid-cols-2 gap-4 bg-white shadow rounded p-5">
                <div><strong>Email:</strong> {{ $user->email }}</div>
                <div><strong>Phone:</strong> {{ $user->phone }}</div>
                <div><strong>IC:</strong> {{ $user->ic_number ?? '—' }}</div>
                <div><strong>Fee:</strong> {{ $user->monthly_fee_amount ? 'RM '.number_format($user->monthly_fee_amount, 2) : __('Default') }}</div>
                <div><strong>Status:</strong> {{ $user->is_active ? __('Active') : __('Inactive') }}</div>
            </div>

            <h3 class="mt-6 mb-3 font-semibold">{{ __('Invoices') }}</h3>
            <div class="bg-white shadow rounded overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs uppercase">{{ __('Invoice #') }}</th>
                            <th class="px-4 py-2 text-left text-xs uppercase">{{ __('Period') }}</th>
                            <th class="px-4 py-2 text-right text-xs uppercase">{{ __('Total') }}</th>
                            <th class="px-4 py-2 text-left text-xs uppercase">{{ __('Status') }}</th>
                            <th class="px-4 py-2 text-left text-xs uppercase">{{ __('Due') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($invoices as $inv)
                            <tr>
                                <td class="px-4 py-2"><a class="text-indigo-600" href="{{ route('admin.invoices.show', $inv) }}" wire:navigate>{{ $inv->invoice_number }}</a></td>
                                <td class="px-4 py-2">{{ $inv->period_month->format('M Y') }}</td>
                                <td class="px-4 py-2 text-right">RM {{ number_format($inv->total, 2) }}</td>
                                <td class="px-4 py-2"><span class="px-2 py-1 rounded text-xs {{ $inv->status->badgeClass() }}">{{ $inv->status->label() }}</span></td>
                                <td class="px-4 py-2">{{ $inv->due_date->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">{{ __('No invoices yet.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $invoices->links() }}</div>
        </div>
    </div>
</div>
