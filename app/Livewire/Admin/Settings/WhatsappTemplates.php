<?php

namespace App\Livewire\Admin\Settings;

use App\Models\WhatsappTemplate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('WhatsApp Templates')]
#[Layout('layouts.app')]
class WhatsappTemplates extends Component
{
    public ?int $editingId = null;

    public string $key = '';

    public string $name = '';

    public string $body_template = '';

    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'key' => 'required|string|max:64',
            'name' => 'required|string|max:120',
            'body_template' => 'required|string|max:4000',
            'is_active' => 'boolean',
        ];
    }

    public function edit(int $id): void
    {
        $t = WhatsappTemplate::findOrFail($id);
        $this->editingId = $t->id;
        $this->key = $t->key;
        $this->name = $t->name;
        $this->body_template = $t->body_template;
        $this->is_active = (bool) $t->is_active;
    }

    public function newRow(): void
    {
        $this->editingId = null;
        $this->key = '';
        $this->name = '';
        $this->body_template = '';
        $this->is_active = true;
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->editingId) {
            WhatsappTemplate::findOrFail($this->editingId)->update($data);
        } else {
            WhatsappTemplate::create($data);
        }
        $this->newRow();
        session()->flash('status', __('Template saved.'));
    }

    public function render()
    {
        return view('livewire.admin.settings.whatsapp-templates', [
            'templates' => WhatsappTemplate::orderBy('name')->get(),
        ]);
    }
}
