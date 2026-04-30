<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php $schoolName = \App\Models\Setting::get('school_name', config('app.name')); @endphp

    <title>{{ $schoolName }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased bg-gradient-to-br from-indigo-50 via-white to-emerald-50 min-h-screen">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div class="mb-2">
            <a href="/" wire:navigate class="flex flex-col items-center gap-3">
                <x-app-logo size="large" />
                <span class="text-lg font-semibold text-gray-700">{{ $schoolName }}</span>
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-4 px-6 py-6 bg-white shadow-xl ring-1 ring-gray-200/60 overflow-hidden sm:rounded-2xl">
            {{ $slot }}
        </div>

        <a href="/" wire:navigate class="mt-6 text-xs text-gray-500 hover:text-indigo-700">
            ← {{ __('Back to home') }}
        </a>
    </div>
</body>
</html>
