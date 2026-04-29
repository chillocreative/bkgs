<?php

namespace App\Livewire;

use App\Services\BayarcashService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Payment Status')]
#[Layout('layouts.app')]
class PaymentReturn extends Component
{
    public array $info = [];

    public function mount(BayarcashService $svc): void
    {
        $this->info = $svc->handleReturn(request()->all());
    }

    public function render()
    {
        return view('livewire.payment-return');
    }
}
