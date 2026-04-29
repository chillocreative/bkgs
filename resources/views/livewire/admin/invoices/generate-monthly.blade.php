<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('Generate Monthly Invoices') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <form wire:submit="run" class="bg-white shadow rounded-lg p-6 space-y-4">
                <p class="text-sm text-gray-600">
                    {{ __('Generates one invoice per active teacher for the chosen month. Running again is safe — duplicates are skipped.') }}
                </p>

                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('Month') }}</label>
                    <input wire:model="month" type="month" class="mt-1 border-gray-300 rounded" />
                    @error('month') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <button class="px-4 py-2 bg-indigo-600 text-white rounded" wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ __('Generate Invoices') }}</span>
                        <span wire:loading>{{ __('Generating…') }}</span>
                    </button>
                </div>

                @if ($result)
                    <div class="p-3 bg-green-50 border border-green-200 rounded text-sm">
                        <strong>{{ __('Done.') }}</strong>
                        {{ __('Created:') }} {{ $result['created'] }} ·
                        {{ __('Skipped:') }} {{ $result['skipped'] }}
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
