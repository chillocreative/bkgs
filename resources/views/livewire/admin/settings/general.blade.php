<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('General Settings') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap gap-2 mb-4 text-sm">
                <a href="{{ route('admin.settings.general') }}" wire:navigate class="px-3 py-1 rounded bg-indigo-600 text-white">{{ __('General') }}</a>
                <a href="{{ route('admin.settings.branding') }}" wire:navigate class="px-3 py-1 rounded bg-gray-200">{{ __('Branding') }}</a>
                <a href="{{ route('admin.fee-structures.index') }}" wire:navigate class="px-3 py-1 rounded bg-gray-200">{{ __('Fee Structures') }}</a>
                <a href="{{ route('admin.templates.index') }}" wire:navigate class="px-3 py-1 rounded bg-gray-200">{{ __('WhatsApp Templates') }}</a>
                @if (auth()->user()->isSuperAdmin())
                    <a href="{{ route('admin.settings.bayarcash') }}" wire:navigate class="px-3 py-1 rounded bg-gray-200">{{ __('BayarCash') }}</a>
                    <a href="{{ route('admin.settings.sendora') }}" wire:navigate class="px-3 py-1 rounded bg-gray-200">{{ __('Sendora') }}</a>
                @endif
            </div>

            <form wire:submit="save" class="bg-white shadow rounded-lg p-6 space-y-3">
                @if (session('status')) <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div> @endif

                <div>
                    <label class="block text-sm font-medium">{{ __('School Name') }}</label>
                    <input wire:model="school_name" type="text" class="mt-1 border-gray-300 rounded w-full" />
                    @error('school_name') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">{{ __('Address') }}</label>
                    <textarea wire:model="school_address" rows="3" class="mt-1 border-gray-300 rounded w-full"></textarea>
                </div>
                <div class="grid sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium">{{ __('Contact Email') }}</label>
                        <input wire:model="school_email" type="email" class="mt-1 border-gray-300 rounded w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium">{{ __('Contact Phone') }}</label>
                        <input wire:model="school_phone" type="text" class="mt-1 border-gray-300 rounded w-full" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium">{{ __('Registration Number') }}</label>
                    <input wire:model="school_registration_number" type="text" class="mt-1 border-gray-300 rounded w-full" />
                </div>
                <div>
                    <label class="block text-sm font-medium">{{ __('Receipt Footer Text') }}</label>
                    <textarea wire:model="receipt_footer" rows="2" class="mt-1 border-gray-300 rounded w-full"></textarea>
                </div>

                <div class="pt-2">
                    <button class="px-4 py-2 bg-indigo-600 text-white rounded">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
