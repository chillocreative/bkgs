<?php

namespace App\Livewire\Admin\Invoices;

use App\Models\Invoice;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Invoices')]
#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $month = '';

    #[Url]
    public string $status = '';

    #[Url]
    public ?int $teacher_id = null;

    public function updating($name): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $q = Invoice::with('user')->orderByDesc('period_month')->orderBy('id');

        if ($this->month) {
            try {
                $start = \Carbon\Carbon::createFromFormat('Y-m', $this->month)->startOfMonth();
                $q->whereDate('period_month', $start->toDateString());
            } catch (\Throwable $e) {}
        }
        if ($this->status) $q->where('status', $this->status);
        if ($this->teacher_id) $q->where('user_id', $this->teacher_id);

        return view('livewire.admin.invoices.index', [
            'invoices' => $q->paginate(25),
            'teachers' => User::role('teacher')->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
