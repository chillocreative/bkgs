<?php

namespace App\Livewire\Teacher;

use App\Models\Invoice;
use Illuminate\Auth\Access\AuthorizationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Invoice')]
#[Layout('layouts.app')]
class InvoiceShow extends Component
{
    public Invoice $invoice;

    public function mount(Invoice $invoice): void
    {
        if ($invoice->user_id !== auth()->id() && ! auth()->user()->isAdmin()) {
            throw new AuthorizationException();
        }
        $this->invoice = $invoice->load('payments', 'user');
    }

    public function render()
    {
        return view('livewire.teacher.invoice-show');
    }
}
