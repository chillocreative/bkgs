<?php

namespace App\Livewire\Admin\Payments;

use App\Models\Payment;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Payments')]
#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $month = '';

    #[Url]
    public string $method = '';

    public function updating($name): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $q = Payment::with('user:id,name', 'invoice:id,invoice_number')
            ->where('status', 'successful')
            ->orderByDesc('paid_at');

        if ($this->month) {
            try {
                $start = \Carbon\Carbon::createFromFormat('Y-m', $this->month)->startOfMonth();
                $q->whereBetween('paid_at', [$start, $start->copy()->endOfMonth()]);
            } catch (\Throwable $e) {}
        }
        if ($this->method) $q->where('method', $this->method);

        return view('livewire.admin.payments.index', [
            'payments' => $q->paginate(25),
        ]);
    }
}
