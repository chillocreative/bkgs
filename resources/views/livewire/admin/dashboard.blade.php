<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Admin Dashboard') }} — {{ $monthLabel }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow p-5">
                    <div class="text-sm text-gray-500">{{ __('Collected This Month') }}</div>
                    <div class="mt-2 text-2xl font-bold text-green-600">RM {{ number_format($collectedThisMonth, 2) }}</div>
                </div>
                <div class="bg-white rounded-lg shadow p-5">
                    <div class="text-sm text-gray-500">{{ __('Outstanding') }}</div>
                    <div class="mt-2 text-2xl font-bold text-red-600">RM {{ number_format($outstanding, 2) }}</div>
                </div>
                <div class="bg-white rounded-lg shadow p-5">
                    <div class="text-sm text-gray-500">{{ __('% Paid (this month)') }}</div>
                    <div class="mt-2 text-2xl font-bold text-indigo-600">{{ $percentPaid }}%</div>
                    <div class="mt-2 h-2 w-full bg-gray-200 rounded">
                        <div class="h-2 bg-indigo-600 rounded" style="width: {{ min(100, $percentPaid) }}%"></div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-5">
                    <div class="text-sm text-gray-500">{{ __('Overdue Invoices') }}</div>
                    <div class="mt-2 text-2xl font-bold text-orange-600">{{ $overdueCount }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-center">
                <div class="bg-white rounded shadow p-3"><div class="text-xs text-gray-500">{{ __('Total Teachers') }}</div><div class="text-lg font-semibold">{{ $totals['teachers'] }}</div></div>
                <div class="bg-white rounded shadow p-3"><div class="text-xs text-gray-500">{{ __('Active Teachers') }}</div><div class="text-lg font-semibold">{{ $totals['active_teachers'] }}</div></div>
                <div class="bg-white rounded shadow p-3"><div class="text-xs text-gray-500">{{ __('Pending Notifications') }}</div><div class="text-lg font-semibold">{{ $totals['pending_notifications'] }}</div></div>
                <div class="bg-white rounded shadow p-3"><div class="text-xs text-gray-500">{{ __('Failed Notifications') }}</div><div class="text-lg font-semibold text-red-600">{{ $totals['failed_notifications'] }}</div></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-5 py-4 border-b font-semibold text-gray-700">{{ __('Top Overdue Teachers') }}</div>
                    <div class="divide-y">
                        @forelse ($topOverdue as $row)
                            <div class="px-5 py-3 flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-gray-800">{{ $row->user->name ?? '—' }}</div>
                                    <div class="text-xs text-gray-500">{{ $row->cnt }} {{ __('overdue invoice(s)') }}</div>
                                </div>
                                <div class="font-semibold text-red-600">RM {{ number_format((float) $row->owed, 2) }}</div>
                            </div>
                        @empty
                            <div class="px-5 py-6 text-center text-gray-400">{{ __('No overdue invoices.') }}</div>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow">
                    <div class="px-5 py-4 border-b font-semibold text-gray-700 flex justify-between">
                        <span>{{ __('Recent Payments') }}</span>
                        <a href="{{ route('admin.payments.index') }}" class="text-indigo-600 text-sm" wire:navigate>{{ __('View all') }}</a>
                    </div>
                    <div class="divide-y">
                        @forelse ($recentPayments as $p)
                            <div class="px-5 py-3 flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-gray-800">{{ $p->user->name ?? '—' }}</div>
                                    <div class="text-xs text-gray-500">{{ $p->invoice->invoice_number ?? '—' }} · {{ $p->method?->label() }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold text-green-600">RM {{ number_format((float) $p->amount, 2) }}</div>
                                    <div class="text-xs text-gray-400">{{ optional($p->paid_at)->format('d M Y') }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="px-5 py-6 text-center text-gray-400">{{ __('No payments yet.') }}</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-5">
                <div class="font-semibold text-gray-700 mb-3">{{ __('Quick Actions') }}</div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.invoices.generate') }}" wire:navigate class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">{{ __('Generate Monthly Invoices') }}</a>
                    <a href="{{ route('admin.teachers.create') }}" wire:navigate class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800">{{ __('Add Teacher') }}</a>
                    <a href="{{ route('admin.teachers.import') }}" wire:navigate class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800">{{ __('Bulk Import') }}</a>
                    <a href="{{ route('admin.reports.monthly') }}" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">{{ __('Export Monthly Report') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
