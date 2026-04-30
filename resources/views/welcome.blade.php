<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $schoolName = \App\Models\Setting::get('school_name', config('app.name'));
        $schoolAddress = \App\Models\Setting::get('school_address');
        $schoolEmail = \App\Models\Setting::get('school_email');
        $schoolPhone = \App\Models\Setting::get('school_phone');
        $schoolReg = \App\Models\Setting::get('school_registration_number');
    @endphp

    <title>{{ $schoolName }} — {{ __('Teacher Fee Portal') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-gradient-to-br from-indigo-50 via-white to-emerald-50 min-h-screen flex flex-col">

    <header class="w-full">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3">
                <x-app-logo size="small" />
                <div>
                    <div class="text-base font-semibold text-gray-800 leading-tight">{{ $schoolName }}</div>
                    <div class="text-xs text-gray-500">{{ __('Teacher Fee Portal') }}</div>
                </div>
            </a>

            <nav class="flex items-center gap-2 text-sm">
                @auth
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('teacher.invoices.index') }}"
                       class="inline-flex items-center px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700 transition">
                        {{ __('Go to Dashboard') }}
                    </a>
                @else
                    <a href="{{ route('login') }}" wire:navigate
                       class="inline-flex items-center px-4 py-2 rounded-md text-gray-700 hover:text-indigo-700">
                        {{ __('Login') }}
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" wire:navigate
                           class="inline-flex items-center px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700 transition">
                            {{ __('Register') }}
                        </a>
                    @endif
                @endauth
            </nav>
        </div>
    </header>

    <main class="flex-1 flex items-center">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-20 grid lg:grid-cols-2 gap-12 items-center w-full">
            <div>
                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-700 mb-4">
                    {{ __('For teaching staff') }}
                </span>
                <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 leading-tight">
                    {{ __('Pay your monthly fees') }}
                    <span class="text-indigo-600">{{ __('in seconds.') }}</span>
                </h1>
                <p class="mt-4 text-lg text-gray-600 max-w-xl">
                    {{ __(':school provides a secure online portal for teachers to view invoices, pay via FPX or DuitNow, and download official receipts — with WhatsApp confirmations on every payment.', ['school' => $schoolName]) }}
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    @auth
                        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('teacher.invoices.index') }}"
                           class="px-6 py-3 rounded-lg bg-indigo-600 text-white font-semibold shadow hover:bg-indigo-700 transition">
                            {{ __('Open dashboard') }} →
                        </a>
                    @else
                        <a href="{{ route('login') }}" wire:navigate
                           class="px-6 py-3 rounded-lg bg-indigo-600 text-white font-semibold shadow hover:bg-indigo-700 transition">
                            {{ __('Login to your account') }}
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" wire:navigate
                               class="px-6 py-3 rounded-lg bg-white border border-gray-300 text-gray-700 font-semibold hover:border-indigo-500 hover:text-indigo-700 transition">
                                {{ __('Create teacher account') }}
                            </a>
                        @endif
                    @endauth
                </div>

                <p class="mt-4 text-xs text-gray-500">
                    {{ __('Need help? Contact your school administrator.') }}
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-feature-card
                    icon="🔒"
                    title="{{ __('Secure online payments') }}"
                    body="{{ __('FPX, DuitNow and supported e-wallets through BayarCash — fully encrypted at every step.') }}" />
                <x-feature-card
                    icon="💬"
                    title="{{ __('WhatsApp updates') }}"
                    body="{{ __('Get instant invoice and receipt notifications on WhatsApp via Sendora.') }}" />
                <x-feature-card
                    icon="🧾"
                    title="{{ __('Instant PDF receipts') }}"
                    body="{{ __('Download official school-stamped receipts the moment your payment is confirmed.') }}" />
                <x-feature-card
                    icon="📅"
                    title="{{ __('Track every month') }}"
                    body="{{ __('See pending, paid and overdue invoices for the current and past years at a glance.') }}" />
            </div>
        </div>
    </main>

    <footer class="border-t border-gray-200 bg-white/60">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500 gap-2">
            <div>
                © {{ now()->year }} {{ $schoolName }}.
                @if ($schoolReg)
                    <span class="ml-2">{{ __('Reg.') }} {{ $schoolReg }}</span>
                @endif
            </div>
            <div class="flex flex-wrap items-center gap-3">
                @if ($schoolPhone) <span>📞 {{ $schoolPhone }}</span> @endif
                @if ($schoolEmail) <span>✉️ {{ $schoolEmail }}</span> @endif
                @if ($schoolAddress) <span class="max-w-xs truncate">📍 {{ $schoolAddress }}</span> @endif
            </div>
        </div>
    </footer>
</body>
</html>
