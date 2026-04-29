<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('Payment Status') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6 space-y-3 text-center">
                @php $isSuccess = in_array(($info['status'] ?? ''), ['3', 'Successful', 'successful', 'Paid', 'paid'], true); @endphp

                @if ($isSuccess)
                    <div class="text-3xl">✅</div>
                    <h3 class="text-xl font-semibold text-green-600">{{ __('Payment received') }}</h3>
                    <p class="text-sm">{{ __('Order:') }} {{ $info['order_number'] ?? '—' }}</p>
                    <p class="text-sm">{{ __('Amount:') }} RM {{ number_format((float) ($info['amount'] ?? 0), 2) }}</p>
                @else
                    <div class="text-3xl">⚠️</div>
                    <h3 class="text-xl font-semibold text-orange-600">{{ __('Payment status:') }} {{ $info['status'] ?: __('Unknown') }}</h3>
                    <p class="text-sm text-gray-500">{{ __('If your bank has charged you, the receipt will appear shortly once we receive confirmation from BayarCash.') }}</p>
                @endif

                <div class="pt-3 flex justify-center gap-2">
                    <a href="{{ route('teacher.invoices.index') }}" wire:navigate class="px-4 py-2 bg-indigo-600 text-white rounded">{{ __('Back to My Invoices') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
