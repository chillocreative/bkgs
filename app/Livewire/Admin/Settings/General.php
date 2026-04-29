<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('General Settings')]
#[Layout('layouts.app')]
class General extends Component
{
    public string $school_name = '';

    public ?string $school_address = '';

    public ?string $school_email = '';

    public ?string $school_phone = '';

    public ?string $school_registration_number = '';

    public ?string $receipt_footer = '';

    public function mount(): void
    {
        $this->school_name = (string) Setting::get('school_name', 'My School');
        $this->school_address = (string) Setting::get('school_address');
        $this->school_email = (string) Setting::get('school_email');
        $this->school_phone = (string) Setting::get('school_phone');
        $this->school_registration_number = (string) Setting::get('school_registration_number');
        $this->receipt_footer = (string) Setting::get('receipt_footer');
    }

    protected function rules(): array
    {
        return [
            'school_name' => 'required|string|max:160',
            'school_address' => 'nullable|string|max:500',
            'school_email' => 'nullable|email|max:160',
            'school_phone' => 'nullable|string|max:40',
            'school_registration_number' => 'nullable|string|max:80',
            'receipt_footer' => 'nullable|string|max:500',
        ];
    }

    public function save(): void
    {
        $data = $this->validate();
        foreach ($data as $k => $v) {
            Setting::set($k, $v);
        }
        session()->flash('status', __('Settings saved.'));
    }

    public function render()
    {
        return view('livewire.admin.settings.general');
    }
}
