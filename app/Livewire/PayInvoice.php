<?php

namespace App\Livewire;

use App\Models\Invoice;
use App\Services\BayarcashService;
use Illuminate\Auth\Access\AuthorizationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Pay Invoice')]
#[Layout('layouts.app')]
class PayInvoice extends Component
{
    public Invoice $invoice;

    public ?string $error = null;

    public function mount(Invoice $invoice): void
    {
        if ($invoice->user_id !== auth()->id() && ! auth()->user()->isAdmin()) {
            throw new AuthorizationException();
        }
        $this->invoice = $invoice;
    }

    public function pay(BayarcashService $svc)
    {
        if ($this->invoice->isPaid()) {
            $this->error = __('This invoice is already paid.');
            return null;
        }

        try {
            $url = $svc->createPaymentIntent($this->invoice);
            return redirect()->away($url);
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
            return null;
        }
    }

    public function render()
    {
        return view('livewire.pay-invoice');
    }
}
