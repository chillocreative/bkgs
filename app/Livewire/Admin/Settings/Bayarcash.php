<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('BayarCash Settings')]
#[Layout('layouts.app')]
class Bayarcash extends Component
{
    public string $api_token = '';

    public string $api_secret_key = '';

    public string $portal_key = '';

    public string $sandbox = '1';

    public function mount(): void
    {
        if (! auth()->user()?->isSuperAdmin()) {
            abort(403);
        }
        $this->api_token = (string) (Setting::get('bayarcash_api_token') ?? '');
        $this->api_secret_key = (string) (Setting::get('bayarcash_api_secret_key') ?? '');
        $this->portal_key = (string) (Setting::get('bayarcash_portal_key') ?? '');
        $this->sandbox = (string) (Setting::get('bayarcash_sandbox') ?? '1');
    }

    public function save(): void
    {
        Setting::set('bayarcash_api_token', $this->api_token, true);
        Setting::set('bayarcash_api_secret_key', $this->api_secret_key, true);
        Setting::set('bayarcash_portal_key', $this->portal_key, true);
        Setting::set('bayarcash_sandbox', $this->sandbox === '1' ? '1' : '0');
        session()->flash('status', __('BayarCash settings saved.'));
    }

    public function render()
    {
        return view('livewire.admin.settings.bayarcash');
    }
}
