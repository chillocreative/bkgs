<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('Pay Invoice') }} {{ $invoice->invoice_number }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6 space-y-3 text-center">
                <div class="text-sm text-gray-500">{{ __('Period') }}: {{ $invoice->period_month->format('M Y') }}</div>
                <div class="text-3xl font-bold text-indigo-600">RM {{ number_format($invoice->total, 2) }}</div>
                <div class="text-sm">{{ __('Due') }}: {{ $invoice->due_date->format('d M Y') }}</div>

                @if ($error)
                    <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded text-sm">{{ $error }}</div>
                @endif

                <div class="pt-3 flex justify-center gap-2">
                    <button wire:click="pay" wire:loading.attr="disabled" class="px-6 py-3 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700">
                        <span wire:loading.remove>{{ __('Pay with BayarCash (FPX/DuitNow)') }}</span>
                        <span wire:loading>{{ __('Redirecting…') }}</span>
                    </button>
                    <a href="{{ route('teacher.invoices.index') }}" wire:navigate class="px-4 py-3 bg-gray-200 rounded-lg">{{ __('Back') }}</a>
                </div>

                <p class="text-xs text-gray-400 mt-2">
                    {{ __('You will be redirected to BayarCash. After payment you will return here automatically.') }}
                </p>
            </div>
        </div>
    </div>
</div>
