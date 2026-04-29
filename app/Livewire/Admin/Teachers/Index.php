<?php

namespace App\Livewire\Admin\Teachers;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Teachers')]
#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = 'all';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function toggleActive(int $id): void
    {
        $u = User::role('teacher')->findOrFail($id);
        $u->is_active = ! $u->is_active;
        $u->save();
        $this->dispatch('toast', message: $u->name.' '.($u->is_active ? __('activated') : __('deactivated')));
    }

    public function render()
    {
        $query = User::role('teacher')->orderBy('name');

        if ($this->search !== '') {
            $like = '%'.$this->search.'%';
            $query->where(function ($q) use ($like) {
                $q->where('name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('ic_number', 'like', $like)
                    ->orWhere('phone', 'like', $like);
            });
        }

        if ($this->status === 'active') {
            $query->where('is_active', true);
        } elseif ($this->status === 'inactive') {
            $query->where('is_active', false);
        }

        return view('livewire.admin.teachers.index', [
            'teachers' => $query->paginate(20),
        ]);
    }
}
