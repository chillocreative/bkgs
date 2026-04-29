<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ $userId ? __('Edit Teacher') : __('Add Teacher') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <form wire:submit="save" class="bg-white shadow rounded-lg p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('Name') }}</label>
                    <input wire:model="name" type="text" class="mt-1 block w-full border-gray-300 rounded" />
                    @error('name') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
                    <input wire:model="email" type="email" class="mt-1 block w-full border-gray-300 rounded" />
                    @error('email') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Phone (Malaysian)') }}</label>
                        <input wire:model="phone" type="text" placeholder="0123456789" class="mt-1 block w-full border-gray-300 rounded" />
                        <p class="text-xs text-gray-500 mt-1">{{ __('Auto-normalised to 60xxxxxxxxx for Sendora.') }}</p>
                        @error('phone') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('IC Number') }}</label>
                        <input wire:model="ic_number" type="text" class="mt-1 block w-full border-gray-300 rounded" />
                        @error('ic_number') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('Monthly Fee Amount (RM, optional)') }}</label>
                    <input wire:model="monthly_fee_amount" type="number" step="0.01" min="0" class="mt-1 block w-full border-gray-300 rounded" />
                    <p class="text-xs text-gray-500 mt-1">{{ __('Leave empty to use the default fee structure.') }}</p>
                    @error('monthly_fee_amount') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('Password') }}</label>
                    <input wire:model="password" type="password" class="mt-1 block w-full border-gray-300 rounded" />
                    <p class="text-xs text-gray-500 mt-1">{{ $userId ? __('Leave blank to keep existing.') : __('Leave blank to auto-generate.') }}</p>
                    @error('password') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>

                <label class="inline-flex items-center gap-2">
                    <input wire:model="is_active" type="checkbox" class="rounded" />
                    <span class="text-sm">{{ __('Active') }}</span>
                </label>

                <div class="pt-3 flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700" wire:loading.attr="disabled">{{ __('Save') }}</button>
                    <a href="{{ route('admin.teachers.index') }}" wire:navigate class="px-4 py-2 bg-gray-200 text-gray-700 rounded">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
