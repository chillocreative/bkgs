<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('Sendora Settings') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            <form wire:submit="save" class="bg-white shadow rounded-lg p-6 space-y-3">
                @if (session('status')) <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div> @endif

                <div>
                    <label class="block text-sm">{{ __('API Key') }} <span class="text-xs text-gray-500">({{ __('encrypted at rest') }})</span></label>
                    <input wire:model="api_key" type="text" class="mt-1 border-gray-300 rounded w-full font-mono text-xs" />
                </div>
                <div>
                    <label class="block text-sm">{{ __('Base URL') }}</label>
                    <input wire:model="base_url" type="url" class="mt-1 border-gray-300 rounded w-full" />
                </div>
                <div>
                    <label class="block text-sm">{{ __('Device ID') }} <span class="text-xs text-gray-500">({{ __('optional — uses first connected if blank') }})</span></label>
                    <input wire:model="device_id" type="text" class="mt-1 border-gray-300 rounded w-full" />
                </div>

                <div class="pt-2 flex gap-2">
                    <button class="px-4 py-2 bg-indigo-600 text-white rounded">{{ __('Save') }}</button>
                </div>
            </form>

            <div class="bg-white shadow rounded-lg p-6 space-y-3">
                <h3 class="font-semibold">{{ __('Send a test message') }}</h3>
                <div>
                    <label class="block text-sm">{{ __('Phone (any local format)') }}</label>
                    <input wire:model="testPhone" type="text" class="mt-1 border-gray-300 rounded w-full" />
                </div>
                <button wire:click="sendTest" wire:loading.attr="disabled" class="px-4 py-2 bg-emerald-600 text-white rounded">
                    <span wire:loading.remove>{{ __('Send Test') }}</span>
                    <span wire:loading>{{ __('Sending…') }}</span>
                </button>

                @if ($testResult)
                    <div class="text-sm p-3 rounded {{ ($testResult['success'] ?? false) ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800' }}">
                        @if ($testResult['success'] ?? false)
                            {{ __('Sent ✔') }} @if (!empty($testResult['provider_message_id'])) ({{ $testResult['provider_message_id'] }}) @endif
                        @else
                            {{ __('Failed:') }} {{ $testResult['error'] ?? __('Unknown error') }}
                        @endif
                        <pre class="text-xs mt-2 overflow-x-auto">{{ json_encode($testResult, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}</pre>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
