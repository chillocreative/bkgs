<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('Branding — Logo') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6 space-y-4">
                @if (session('status'))
                    <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
                @endif

                <div class="flex items-center gap-6">
                    <div>
                        <p class="text-sm text-gray-500 mb-2">{{ __('Current logo') }}</p>
                        @if ($currentLogoUrl)
                            <img src="{{ $currentLogoUrl }}" class="h-32 w-32 object-contain rounded bg-white border" alt="logo" />
                        @else
                            <div class="h-32 w-32 rounded bg-gray-100 border flex items-center justify-center text-gray-400">{{ __('No logo set') }}</div>
                        @endif
                    </div>
                    <div class="flex-1 text-sm text-gray-600">
                        <p>{{ __('PNG, JPG, WEBP or SVG. 200×200 to 2000×2000. Max 2 MB. We will generate 256×256 (PDFs/email) and 512×512 (header) automatically.') }}</p>
                    </div>
                </div>

                <form wire:submit="upload" class="space-y-3">
                    <input type="file" wire:model="logo" accept="image/*" class="border border-gray-300 rounded px-2 py-1" />
                    @error('logo') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror

                    @if ($logo)
                        <div>
                            <p class="text-sm text-gray-500 mb-1">{{ __('Preview') }}</p>
                            <img src="{{ $logo->temporaryUrl() }}" class="h-32 w-32 object-contain rounded border" />
                        </div>
                    @endif

                    <div class="flex gap-2">
                        <button class="px-4 py-2 bg-indigo-600 text-white rounded" wire:loading.attr="disabled">{{ __('Upload & Apply') }}</button>
                        @if ($currentLogoUrl)
                            <button type="button" wire:click="removeLogo" wire:confirm="{{ __('Remove the current logo?') }}" class="px-4 py-2 bg-red-100 text-red-700 rounded">{{ __('Remove Logo') }}</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
