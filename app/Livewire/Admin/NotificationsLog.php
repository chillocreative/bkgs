<?php

namespace App\Livewire\Admin;

use App\Models\NotificationLog as Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Notifications Log')]
#[Layout('layouts.app')]
class NotificationsLog extends Component
{
    use WithPagination;

    #[Url]
    public string $status = '';

    #[Url]
    public string $template_key = '';

    public function updating($name): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $q = Log::with('user:id,name')->latest('id');
        if ($this->status) $q->where('status', $this->status);
        if ($this->template_key) $q->where('template_key', $this->template_key);

        return view('livewire.admin.notifications-log', [
            'logs' => $q->paginate(25),
            'templates' => \App\Models\WhatsappTemplate::orderBy('name')->get(),
        ]);
    }
}
