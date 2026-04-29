<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">{{ __('Teachers') }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.teachers.import') }}" wire:navigate class="px-3 py-2 bg-gray-700 text-white rounded">{{ __('Bulk Import') }}</a>
                <a href="{{ route('admin.teachers.create') }}" wire:navigate class="px-3 py-2 bg-indigo-600 text-white rounded">{{ __('Add Teacher') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-4">
                <div class="flex flex-col sm:flex-row gap-3 mb-4">
                    <input wire:model.live.debounce.300ms="search" type="search" placeholder="{{ __('Search by name, email, IC or phone…') }}" class="border-gray-300 rounded w-full sm:w-2/3" />
                    <select wire:model.live="status" class="border-gray-300 rounded w-full sm:w-1/3">
                        <option value="all">{{ __('All') }}</option>
                        <option value="active">{{ __('Active only') }}</option>
                        <option value="inactive">{{ __('Inactive only') }}</option>
                    </select>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Name') }}</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Email') }}</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Phone') }}</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('IC') }}</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('Fee (RM)') }}</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">{{ __('Active') }}</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($teachers as $t)
                                <tr>
                                    <td class="px-4 py-2"><a href="{{ route('admin.teachers.show', $t) }}" wire:navigate class="text-indigo-600 hover:underline">{{ $t->name }}</a></td>
                                    <td class="px-4 py-2">{{ $t->email }}</td>
                                    <td class="px-4 py-2">{{ $t->phone }}</td>
                                    <td class="px-4 py-2">{{ $t->ic_number }}</td>
                                    <td class="px-4 py-2 text-right">{{ $t->monthly_fee_amount ? number_format($t->monthly_fee_amount, 2) : '—' }}</td>
                                    <td class="px-4 py-2 text-center">
                                        <button wire:click="toggleActive({{ $t->id }})" wire:loading.attr="disabled" class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $t->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                                            {{ $t->is_active ? __('Active') : __('Inactive') }}
                                        </button>
                                    </td>
                                    <td class="px-4 py-2 text-right">
                                        <a href="{{ route('admin.teachers.edit', $t) }}" wire:navigate class="text-indigo-600">{{ __('Edit') }}</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">{{ __('No teachers yet.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $teachers->links() }}</div>
            </div>
        </div>
    </div>
</div>
