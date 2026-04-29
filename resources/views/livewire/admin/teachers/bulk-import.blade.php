<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('Bulk Import Teachers') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow rounded-lg p-5">
                <p class="text-sm text-gray-600 mb-3">
                    {{ __('Upload a CSV with headers:') }}
                    <code>name,email,phone,ic_number,monthly_fee_amount</code>
                </p>

                @if (! $previewed)
                    <form wire:submit="preview" class="space-y-3">
                        <input type="file" wire:model="file" accept=".csv,text/csv" class="border border-gray-300 rounded px-2 py-1" />
                        @error('file') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
                        <div>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">{{ __('Preview') }}</button>
                        </div>
                    </form>
                @else
                    <div class="flex justify-between items-center mb-3">
                        <div class="text-sm text-gray-700">
                            <span class="text-green-600 font-medium">{{ $valid }}</span> {{ __('valid,') }}
                            <span class="text-red-600 font-medium">{{ $invalid }}</span> {{ __('invalid') }}
                        </div>
                        <div class="flex gap-2">
                            <button wire:click="reset_preview" class="px-3 py-2 bg-gray-200 rounded">{{ __('Cancel') }}</button>
                            <button wire:click="commit" wire:loading.attr="disabled" class="px-3 py-2 bg-green-600 text-white rounded" @if($valid === 0) disabled @endif>{{ __('Commit :n valid row(s)', ['n' => $valid]) }}</button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2">#</th>
                                    <th class="px-3 py-2">{{ __('Name') }}</th>
                                    <th class="px-3 py-2">{{ __('Email') }}</th>
                                    <th class="px-3 py-2">{{ __('Phone') }}</th>
                                    <th class="px-3 py-2">{{ __('IC') }}</th>
                                    <th class="px-3 py-2">{{ __('Fee') }}</th>
                                    <th class="px-3 py-2">{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($rows as $r)
                                    <tr class="{{ $r['valid'] ? '' : 'bg-red-50' }}">
                                        <td class="px-3 py-2">{{ $r['row'] }}</td>
                                        <td class="px-3 py-2">{{ $r['data']['name'] ?? '' }}</td>
                                        <td class="px-3 py-2">{{ $r['data']['email'] ?? '' }}</td>
                                        <td class="px-3 py-2">{{ $r['data']['phone_normalised'] ?? $r['data']['phone'] ?? '' }}</td>
                                        <td class="px-3 py-2">{{ $r['data']['ic_number'] ?? '' }}</td>
                                        <td class="px-3 py-2">{{ $r['data']['monthly_fee_amount'] ?? '' }}</td>
                                        <td class="px-3 py-2">
                                            @if ($r['valid'])
                                                <span class="text-green-700">{{ __('OK') }}</span>
                                            @else
                                                <ul class="text-red-700 list-disc pl-4">
                                                    @foreach ($r['errors'] as $e) <li>{{ $e }}</li> @endforeach
                                                </ul>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
