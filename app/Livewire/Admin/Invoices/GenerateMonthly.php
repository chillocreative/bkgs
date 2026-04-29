<?php

namespace App\Livewire\Admin\Invoices;

use App\Services\InvoiceGenerator;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Generate Monthly Invoices')]
#[Layout('layouts.app')]
class GenerateMonthly extends Component
{
    public string $month;

    public ?array $result = null;

    public function mount(): void
    {
        $this->month = now()->format('Y-m');
    }

    public function run(InvoiceGenerator $gen): void
    {
        $this->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        $period = Carbon::createFromFormat('Y-m', $this->month)->startOfMonth();
        $this->result = $gen->generateForMonth($period);
        session()->flash('status', __('Generation completed.'));
    }

    public function render()
    {
        return view('livewire.admin.invoices.generate-monthly');
    }
}
