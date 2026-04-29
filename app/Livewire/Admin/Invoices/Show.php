<?php

namespace App\Livewire\Admin\Invoices;

use App\Models\Invoice;
use App\Notifications\ManualReminderNotification;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Invoice')]
#[Layout('layouts.app')]
class Show extends Component
{
    public Invoice $invoice;

    public function mount(Invoice $invoice): void
    {
        $this->invoice = $invoice->load('user', 'payments.recordedBy');
    }

    public function sendReminder(): void
    {
        if (! $this->invoice->user) {
            $this->dispatch('toast', message: __('No user attached.'));
            return;
        }
        $this->invoice->user->notify(new ManualReminderNotification($this->invoice));
        $this->dispatch('toast', message: __('Reminder queued.'));
    }

    public function render()
    {
        return view('livewire.admin.invoices.show');
    }
}
