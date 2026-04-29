<?php

namespace App\Livewire\Admin\Teachers;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Teacher')]
#[Layout('layouts.app')]
class Show extends Component
{
    public User $user;

    public function mount(User $user): void
    {
        $this->user = $user;
    }

    public function render()
    {
        return view('livewire.admin.teachers.show', [
            'invoices' => $this->user->invoices()->latest('period_month')->paginate(10),
        ]);
    }
}
