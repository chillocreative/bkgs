<?php

namespace App\Livewire\Admin\FeeStructures;

use App\Models\FeeStructure;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Fee Structures')]
#[Layout('layouts.app')]
class Index extends Component
{
    public ?int $editingId = null;

    public string $name = '';

    public ?string $amount = '';

    public int $due_day = 5;

    public ?string $late_fee_amount = '0';

    public int $late_fee_grace_days = 0;

    public bool $is_default = false;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:120',
            'amount' => 'required|numeric|min:0',
            'due_day' => 'required|integer|min:1|max:28',
            'late_fee_amount' => 'required|numeric|min:0',
            'late_fee_grace_days' => 'required|integer|min:0|max:30',
            'is_default' => 'boolean',
        ];
    }

    public function edit(int $id): void
    {
        $row = FeeStructure::findOrFail($id);
        $this->editingId = $row->id;
        $this->name = $row->name;
        $this->amount = (string) $row->amount;
        $this->due_day = $row->due_day;
        $this->late_fee_amount = (string) $row->late_fee_amount;
        $this->late_fee_grace_days = $row->late_fee_grace_days;
        $this->is_default = (bool) $row->is_default;
    }

    public function newRow(): void
    {
        $this->resetExcept([]);
        $this->editingId = null;
        $this->name = '';
        $this->amount = '';
        $this->due_day = 5;
        $this->late_fee_amount = '0';
        $this->late_fee_grace_days = 0;
        $this->is_default = false;
    }

    public function save(): void
    {
        $data = $this->validate();

        if (! empty($data['is_default'])) {
            FeeStructure::query()->update(['is_default' => false]);
        }

        if ($this->editingId) {
            FeeStructure::findOrFail($this->editingId)->update($data);
        } else {
            FeeStructure::create($data);
        }

        $this->newRow();
        session()->flash('status', __('Fee structure saved.'));
    }

    public function delete(int $id): void
    {
        FeeStructure::findOrFail($id)->delete();
        session()->flash('status', __('Fee structure deleted.'));
    }

    public function render()
    {
        return view('livewire.admin.fee-structures.index', [
            'rows' => FeeStructure::orderByDesc('is_default')->orderBy('name')->get(),
        ]);
    }
}
