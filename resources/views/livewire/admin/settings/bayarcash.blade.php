<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('BayarCash Settings') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-3 rounded text-sm mb-4">
                {{ __('These credentials are encrypted at rest. Sandbox mode swaps to BayarCash sandbox URLs.') }}
            </div>

            <form wire:submit="save" class="bg-white shadow rounded-lg p-6 space-y-3">
                @if (session('status')) <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div> @endif

                <div>
                    <label class="block text-sm">{{ __('API Token') }}</label>
                    <input wire:model="api_token" type="text" class="mt-1 border-gray-300 rounded w-full font-mono text-xs" />
                </div>
                <div>
                    <label class="block text-sm">{{ __('API Secret Key (for callback verification)') }}</label>
                    <input wire:model="api_secret_key" type="text" class="mt-1 border-gray-300 rounded w-full font-mono text-xs" />
                </div>
                <div>
                    <label class="block text-sm">{{ __('Portal Key') }}</label>
                    <input wire:model="portal_key" type="text" class="mt-1 border-gray-300 rounded w-full font-mono text-xs" />
                </div>
                <div>
                    <label class="block text-sm">{{ __('Mode') }}</label>
                    <select wire:model="sandbox" class="mt-1 border-gray-300 rounded">
                        <option value="1">{{ __('Sandbox') }}</option>
                        <option value="0">{{ __('Live') }}</option>
                    </select>
                </div>

                <div class="pt-2">
                    <button class="px-4 py-2 bg-indigo-600 text-white rounded">{{ __('Save') }}</button>
                </div>

                <div class="text-xs text-gray-500 mt-4">
                    {{ __('Callback URL to register with BayarCash:') }}
                    <code class="bg-gray-100 px-2 py-1 rounded">{{ route('webhooks.bayarcash') }}</code><br />
                    {{ __('Return URL:') }} <code class="bg-gray-100 px-2 py-1 rounded">{{ route('payment.return') }}</code>
                </div>
            </form>
        </div>
    </div>
</div>
