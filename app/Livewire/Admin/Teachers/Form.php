<?php

namespace App\Livewire\Admin\Teachers;

use App\Models\User;
use App\Support\PhoneFormatter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Teacher')]
#[Layout('layouts.app')]
class Form extends Component
{
    public ?int $userId = null;

    public string $name = '';

    public string $email = '';

    public ?string $phone = '';

    public ?string $ic_number = '';

    public ?string $monthly_fee_amount = '';

    public bool $is_active = true;

    public ?string $password = '';

    public function mount(?User $user = null): void
    {
        if ($user && $user->exists) {
            $this->userId = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->phone = $user->phone;
            $this->ic_number = $user->ic_number;
            $this->monthly_fee_amount = $user->monthly_fee_amount !== null ? (string) $user->monthly_fee_amount : '';
            $this->is_active = (bool) $user->is_active;
        }
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:120',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->userId)],
            'phone' => 'required|string|min:9|max:20',
            'ic_number' => ['nullable', 'string', 'max:20', Rule::unique('users', 'ic_number')->ignore($this->userId)],
            'monthly_fee_amount' => 'nullable|numeric|min:0|max:9999999.99',
            'is_active' => 'boolean',
            'password' => 'nullable|string|min:6',
        ];
    }

    public function save(): void
    {
        $data = $this->validate();

        try {
            $data['phone'] = PhoneFormatter::toSendora($data['phone']);
        } catch (\InvalidArgumentException $e) {
            $this->addError('phone', $e->getMessage());
            return;
        }

        if ($data['monthly_fee_amount'] === '' || $data['monthly_fee_amount'] === null) {
            $data['monthly_fee_amount'] = null;
        }

        if ($this->userId) {
            $user = User::findOrFail($this->userId);
            $payload = collect($data)->except('password')->toArray();
            if (! empty($data['password'])) {
                $payload['password'] = Hash::make($data['password']);
            }
            $user->update($payload);
        } else {
            $payload = collect($data)->except('password')->toArray();
            $payload['password'] = Hash::make($data['password'] ?: Str::random(12));
            $payload['email_verified_at'] = now();
            $user = User::create($payload);
            $user->assignRole('teacher');
        }

        session()->flash('status', __('Teacher saved.'));
        $this->redirectRoute('admin.teachers.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.teachers.form');
    }
}
