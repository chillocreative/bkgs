<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Dashboard') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6 space-y-3">
                <h3 class="font-semibold">{{ __('Welcome,') }} {{ auth()->user()->name }}</h3>

                @if (auth()->user()->isAdmin())
                    <p>{{ __('You have administrative access.') }}</p>
                    <div class="flex flex-wrap gap-2 pt-2">
                        <a href="{{ route('admin.dashboard') }}" wire:navigate class="px-4 py-2 bg-indigo-600 text-white rounded">{{ __('Open Admin Dashboard') }}</a>
                        <a href="{{ route('teacher.invoices.index') }}" wire:navigate class="px-4 py-2 bg-gray-700 text-white rounded">{{ __('My Invoices') }}</a>
                    </div>
                @else
                    <p>{{ __('View your invoices and pay outstanding fees.') }}</p>
                    <a href="{{ route('teacher.invoices.index') }}" wire:navigate class="px-4 py-2 bg-indigo-600 text-white rounded inline-block">{{ __('My Invoices') }}</a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
