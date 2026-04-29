<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('Fee Structures') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="bg-green-100 text-green-800 p-3 rounded">{{ session('status') }}</div>
            @endif

            <form wire:submit="save" class="bg-white shadow rounded-lg p-5 space-y-3">
                <h3 class="font-semibold">{{ $editingId ? __('Edit Fee Structure') : __('Add Fee Structure') }}</h3>
                <div class="grid sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm">{{ __('Name') }}</label>
                        <input wire:model="name" type="text" class="border-gray-300 rounded w-full" />
                        @error('name') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm">{{ __('Monthly Amount (RM)') }}</label>
                        <input wire:model="amount" type="number" step="0.01" min="0" class="border-gray-300 rounded w-full" />
                        @error('amount') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm">{{ __('Due Day (1–28)') }}</label>
                        <input wire:model="due_day" type="number" min="1" max="28" class="border-gray-300 rounded w-full" />
                    </div>
                    <div>
                        <label class="block text-sm">{{ __('Late Fee (RM)') }}</label>
                        <input wire:model="late_fee_amount" type="number" step="0.01" min="0" class="border-gray-300 rounded w-full" />
                    </div>
                    <div>
                        <label class="block text-sm">{{ __('Grace Days') }}</label>
                        <input wire:model="late_fee_grace_days" type="number" min="0" max="30" class="border-gray-300 rounded w-full" />
                    </div>
                    <label class="inline-flex items-center gap-2 mt-7">
                        <input wire:model="is_default" type="checkbox" />
                        <span>{{ __('Set as default') }}</span>
                    </label>
                </div>
                <div class="flex gap-2 pt-2">
                    <button class="px-4 py-2 bg-indigo-600 text-white rounded">{{ __('Save') }}</button>
                    @if ($editingId)
                        <button type="button" wire:click="newRow" class="px-4 py-2 bg-gray-200 rounded">{{ __('Cancel') }}</button>
                    @endif
                </div>
            </form>

            <div class="bg-white shadow rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs uppercase">{{ __('Name') }}</th>
                            <th class="px-3 py-2 text-right text-xs uppercase">{{ __('Amount') }}</th>
                            <th class="px-3 py-2 text-center text-xs uppercase">{{ __('Due Day') }}</th>
                            <th class="px-3 py-2 text-right text-xs uppercase">{{ __('Late Fee') }}</th>
                            <th class="px-3 py-2 text-center text-xs uppercase">{{ __('Default') }}</th>
                            <th class="px-3 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($rows as $r)
                            <tr>
                                <td class="px-3 py-2">{{ $r->name }}</td>
                                <td class="px-3 py-2 text-right">RM {{ number_format($r->amount, 2) }}</td>
                                <td class="px-3 py-2 text-center">{{ $r->due_day }}</td>
                                <td class="px-3 py-2 text-right">RM {{ number_format($r->late_fee_amount, 2) }} (+{{ $r->late_fee_grace_days }}d)</td>
                                <td class="px-3 py-2 text-center">{{ $r->is_default ? '✓' : '' }}</td>
                                <td class="px-3 py-2 text-right">
                                    <button wire:click="edit({{ $r->id }})" class="text-indigo-600">{{ __('Edit') }}</button>
                                    <button wire:click="delete({{ $r->id }})" wire:confirm="{{ __('Delete this fee structure?') }}" class="text-red-600 ml-2">{{ __('Delete') }}</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-3 py-6 text-center text-gray-400">{{ __('No fee structures yet — add one above.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
