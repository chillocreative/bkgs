<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Setting;
use App\Services\SendoraService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Sendora Settings')]
#[Layout('layouts.app')]
class Sendora extends Component
{
    public string $api_key = '';

    public string $base_url = 'https://sendora.cc/api/v1';

    public string $device_id = '';

    public ?array $testResult = null;

    public string $testPhone = '';

    public function mount(): void
    {
        if (! auth()->user()?->isSuperAdmin()) abort(403);
        $this->api_key = (string) (Setting::get('sendora_api_key') ?? '');
        $this->base_url = (string) (Setting::get('sendora_base_url') ?? 'https://sendora.cc/api/v1');
        $this->device_id = (string) (Setting::get('sendora_device_id') ?? '');
        $this->testPhone = (string) (auth()->user()->phone ?? '');
    }

    public function save(): void
    {
        Setting::set('sendora_api_key', $this->api_key, true);
        Setting::set('sendora_base_url', $this->base_url);
        Setting::set('sendora_device_id', $this->device_id);
        session()->flash('status', __('Sendora settings saved.'));
    }

    public function sendTest(SendoraService $svc): void
    {
        $this->save();
        try {
            $this->testResult = $svc->send($this->testPhone, '✅ BKGS Sendora test message at '.now()->format('d M Y H:i'));
        } catch (\Throwable $e) {
            $this->testResult = ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function render()
    {
        return view('livewire.admin.settings.sendora');
    }
}
