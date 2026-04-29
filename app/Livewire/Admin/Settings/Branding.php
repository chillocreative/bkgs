<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Setting;
use App\Services\LogoProcessor;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Branding')]
#[Layout('layouts.app')]
class Branding extends Component
{
    use WithFileUploads;

    public $logo;

    public function rules(): array
    {
        return [
            'logo' => 'required|image|mimes:png,jpg,jpeg,webp,svg|max:2048|dimensions:min_width=200,min_height=200,max_width=2000,max_height=2000',
        ];
    }

    public function upload(LogoProcessor $proc): void
    {
        $this->validate();

        $paths = $proc->process($this->logo);

        // Remove old files (best-effort)
        foreach (['logo_original', 'logo_small', 'logo_large'] as $k) {
            $old = Setting::get($k);
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }
        }

        Setting::set('logo_original', $paths['original']);
        Setting::set('logo_small', $paths['small']);
        Setting::set('logo_large', $paths['large']);

        $this->logo = null;
        session()->flash('status', __('Logo updated.'));
    }

    public function removeLogo(): void
    {
        foreach (['logo_original', 'logo_small', 'logo_large'] as $k) {
            $old = Setting::get($k);
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }
            Setting::set($k, null);
        }
        session()->flash('status', __('Logo removed.'));
    }

    public function render()
    {
        $smallPath = Setting::get('logo_small');
        $smallUrl = $smallPath && Storage::disk('public')->exists($smallPath)
            ? Storage::disk('public')->url($smallPath).'?v='.@filemtime(Storage::disk('public')->path($smallPath))
            : null;

        return view('livewire.admin.settings.branding', [
            'currentLogoUrl' => $smallUrl,
        ]);
    }
}
