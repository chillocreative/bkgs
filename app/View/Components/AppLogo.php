<?php

namespace App\View\Components;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Component;
use Illuminate\View\View;

class AppLogo extends Component
{
    public ?string $url = null;

    public string $alt;

    public string $sizeClasses;

    public function __construct(public string $size = 'small')
    {
        $key = $size === 'large' ? 'logo_large' : 'logo_small';
        $path = Setting::get($key);
        if ($path && Storage::disk('public')->exists($path)) {
            $this->url = Storage::disk('public')->url($path).'?v='.@filemtime(Storage::disk('public')->path($path));
        }
        $this->alt = (string) Setting::get('school_name', config('app.name', 'School'));
        $this->sizeClasses = match ($size) {
            'large' => 'h-20 w-20',
            'small' => 'h-12 w-12',
            default => 'h-12 w-12',
        };
    }

    public function render(): View
    {
        return view('components.app-logo');
    }
}
