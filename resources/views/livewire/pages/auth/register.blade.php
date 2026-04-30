<?php

use App\Models\User;
use App\Support\PhoneFormatter;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $ic_number = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class, 'email')],
            'phone' => ['required', 'string', 'min:9', 'max:20'],
            'ic_number' => ['required', 'string', 'regex:/^\d{12}$/', Rule::unique(User::class, 'ic_number')],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ], [
            'ic_number.regex' => __('IC number must be exactly 12 digits.'),
        ]);

        try {
            $validated['phone'] = PhoneFormatter::toSendora($validated['phone']);
        } catch (\InvalidArgumentException $e) {
            $this->addError('phone', __('Phone must be a valid Malaysian number (e.g. 0123456789).'));
            return;
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = true;
        $validated['email_verified_at'] = null;

        $user = User::create($validated);
        $user->assignRole('teacher');

        event(new Registered($user));

        Auth::login($user);

        $this->redirect(route('teacher.invoices.index', absolute: false), navigate: true);
    }
}; ?>

<div>
    <div class="mb-5 text-center">
        <h2 class="text-xl font-semibold text-gray-800">{{ __('Create teacher account') }}</h2>
        <p class="text-xs text-gray-500 mt-1">{{ __('Self-registration is for teaching staff only. Admins are created internally.') }}</p>
    </div>

    <form wire:submit="register">
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Full Name')" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Phone + IC side-by-side on sm+ -->
        <div class="mt-4 grid sm:grid-cols-2 gap-3">
            <div>
                <x-input-label for="phone" :value="__('Mobile Phone')" />
                <x-text-input wire:model="phone" id="phone" class="block mt-1 w-full" type="text" name="phone" placeholder="0123456789" required autocomplete="tel" />
                <p class="text-xs text-gray-400 mt-1">{{ __('We will send WhatsApp invoices here.') }}</p>
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="ic_number" :value="__('IC Number (MyKad)')" />
                <x-text-input wire:model="ic_number" id="ic_number" class="block mt-1 w-full" type="text" name="ic_number" placeholder="900101012345" required maxlength="12" />
                <p class="text-xs text-gray-400 mt-1">{{ __('12 digits, no dashes.') }}</p>
                <x-input-error :messages="$errors->get('ic_number')" class="mt-2" />
            </div>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input wire:model="password" id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <a class="underline text-sm text-gray-600 hover:text-indigo-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}" wire:navigate>
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</div>
