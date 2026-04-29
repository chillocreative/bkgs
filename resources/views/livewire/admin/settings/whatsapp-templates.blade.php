<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('WhatsApp Templates') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-6">

            <form wire:submit="save" class="bg-white shadow rounded-lg p-5 space-y-3">
                <h3 class="font-semibold">{{ $editingId ? __('Edit Template') : __('Add Template') }}</h3>
                @if (session('status')) <div class="p-2 bg-green-100 text-green-800 rounded text-sm">{{ session('status') }}</div> @endif

                <div>
                    <label class="block text-sm">{{ __('Key (used in code)') }}</label>
                    <input wire:model="key" type="text" class="border-gray-300 rounded w-full" />
                    @error('key') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm">{{ __('Display Name') }}</label>
                    <input wire:model="name" type="text" class="border-gray-300 rounded w-full" />
                </div>
                <div>
                    <label class="block text-sm">{{ __('Body') }}</label>
                    <textarea wire:model="body_template" rows="9" class="border-gray-300 rounded w-full font-mono text-sm"></textarea>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ __('Available variables:') }}
                        <code>@{{ teacher_name }}</code>, <code>@{{ school_name }}</code>, <code>@{{ amount }}</code>,
                        <code>@{{ invoice_number }}</code>, <code>@{{ month }}</code>, <code>@{{ due_date }}</code>,
                        <code>@{{ paid_at }}</code>, <code>@{{ pay_url }}</code>
                    </p>
                </div>
                <label class="inline-flex items-center gap-2">
                    <input wire:model="is_active" type="checkbox" />
                    <span>{{ __('Active') }}</span>
                </label>
                <div class="flex gap-2">
                    <button class="px-4 py-2 bg-indigo-600 text-white rounded">{{ __('Save') }}</button>
                    @if ($editingId)
                        <button type="button" wire:click="newRow" class="px-4 py-2 bg-gray-200 rounded">{{ __('Cancel') }}</button>
                    @endif
                </div>
            </form>

            <div class="bg-white shadow rounded-lg p-5 space-y-3">
                <h3 class="font-semibold">{{ __('Existing Templates') }}</h3>
                <div class="divide-y">
                    @foreach ($templates as $t)
                        <div class="py-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium">{{ $t->name }} <span class="text-xs text-gray-400 ml-1">({{ $t->key }})</span></div>
                                    <div class="text-xs">{{ $t->is_active ? __('Active') : __('Inactive') }}</div>
                                </div>
                                <button wire:click="edit({{ $t->id }})" class="text-indigo-600 text-sm">{{ __('Edit') }}</button>
                            </div>
                            <pre class="mt-2 text-xs text-gray-600 whitespace-pre-wrap">{{ \Illuminate\Support\Str::limit($t->body_template, 200) }}</pre>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
