<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('Record Manual Payment') }} — {{ $invoice->invoice_number }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <form wire:submit="save" class="bg-white shadow rounded-lg p-6 space-y-4">
                <div class="grid sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm">{{ __('Method') }}</label>
                        <select wire:model="method" class="border-gray-300 rounded w-full">
                            <option value="manual_cash">{{ __('Cash') }}</option>
                            <option value="manual_transfer">{{ __('Bank Transfer') }}</option>
                            <option value="manual_cheque">{{ __('Cheque') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm">{{ __('Amount (RM)') }}</label>
                        <input wire:model="amount" type="number" step="0.01" class="border-gray-300 rounded w-full" />
                        @error('amount') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm">{{ __('Reference / Slip No.') }}</label>
                        <input wire:model="reference" type="text" class="border-gray-300 rounded w-full" />
                    </div>
                    <div>
                        <label class="block text-sm">{{ __('Paid At') }}</label>
                        <input wire:model="paid_at" type="date" class="border-gray-300 rounded w-full" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm">{{ __('Receipt (optional, PDF/JPG/PNG ≤ 2MB)') }}</label>
                    <input type="file" wire:model="receipt" accept=".pdf,image/jpeg,image/png" class="border border-gray-300 rounded px-2 py-1" />
                    @error('receipt') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm">{{ __('Notes (optional)') }}</label>
                    <textarea wire:model="notes" rows="3" class="border-gray-300 rounded w-full"></textarea>
                </div>

                <div class="pt-2 flex gap-2">
                    <button class="px-4 py-2 bg-emerald-600 text-white rounded">{{ __('Record Payment') }}</button>
                    <a href="{{ route('admin.invoices.show', $invoice) }}" wire:navigate class="px-4 py-2 bg-gray-200 rounded">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
