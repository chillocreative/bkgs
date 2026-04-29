<?php

namespace App\Livewire\Teacher;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('My Invoices')]
#[Layout('layouts.app')]
class InvoicesIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $year = '';

    public function mount(): void
    {
        $this->year = $this->year ?: (string) now()->year;
    }

    public function render()
    {
        $q = auth()->user()->invoices()->orderByDesc('period_month');
        if ($this->year) {
            $q->whereYear('period_month', $this->year);
        }

        return view('livewire.teacher.invoices-index', [
            'invoices' => $q->paginate(12),
        ]);
    }
}
