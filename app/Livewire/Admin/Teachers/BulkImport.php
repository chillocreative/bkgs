<?php

namespace App\Livewire\Admin\Teachers;

use App\Models\User;
use App\Support\PhoneFormatter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Bulk Import Teachers')]
#[Layout('layouts.app')]
class BulkImport extends Component
{
    use WithFileUploads;

    public $file;

    public array $rows = [];

    public bool $previewed = false;

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ];
    }

    public function preview(): void
    {
        $this->validate();

        $path = $this->file->getRealPath();
        $handle = fopen($path, 'r');
        if (! $handle) {
            $this->addError('file', __('Could not open file.'));
            return;
        }

        $headers = fgetcsv($handle);
        if (! $headers) {
            $this->addError('file', __('Empty CSV.'));
            fclose($handle);
            return;
        }
        $headers = array_map(fn ($h) => strtolower(trim((string) $h)), $headers);

        $required = ['name', 'email', 'phone'];
        foreach ($required as $r) {
            if (! in_array($r, $headers, true)) {
                $this->addError('file', __('Missing required column: ').$r);
                fclose($handle);
                return;
            }
        }

        $rows = [];
        $rowNum = 1;
        while (($data = fgetcsv($handle)) !== false) {
            $rowNum++;
            $assoc = [];
            foreach ($headers as $i => $h) {
                $assoc[$h] = isset($data[$i]) ? trim((string) $data[$i]) : null;
            }

            $errors = [];
            $v = Validator::make($assoc, [
                'name' => 'required|string|max:120',
                'email' => 'required|email',
                'phone' => 'required|string',
                'ic_number' => 'nullable|string|max:20',
                'monthly_fee_amount' => 'nullable|numeric|min:0',
            ]);
            if ($v->fails()) {
                $errors = $v->errors()->all();
            }
            try {
                $assoc['phone_normalised'] = PhoneFormatter::toSendora($assoc['phone'] ?? '');
            } catch (\InvalidArgumentException $e) {
                $errors[] = $e->getMessage();
            }

            if (User::where('email', $assoc['email'])->exists()) {
                $errors[] = __('Email already exists.');
            }
            if (! empty($assoc['ic_number']) && User::where('ic_number', $assoc['ic_number'])->exists()) {
                $errors[] = __('IC already exists.');
            }

            $rows[] = [
                'row' => $rowNum,
                'data' => $assoc,
                'errors' => $errors,
                'valid' => empty($errors),
            ];
        }
        fclose($handle);

        $this->rows = $rows;
        $this->previewed = true;
    }

    public function commit(): void
    {
        if (! $this->previewed || empty($this->rows)) {
            return;
        }

        $created = 0;
        foreach ($this->rows as $r) {
            if (! $r['valid']) continue;
            $d = $r['data'];
            $user = User::create([
                'name' => $d['name'],
                'email' => $d['email'],
                'phone' => $d['phone_normalised'] ?? $d['phone'],
                'ic_number' => $d['ic_number'] ?? null,
                'monthly_fee_amount' => isset($d['monthly_fee_amount']) && $d['monthly_fee_amount'] !== '' ? (float) $d['monthly_fee_amount'] : null,
                'is_active' => true,
                'password' => Hash::make(Str::random(12)),
                'email_verified_at' => now(),
            ]);
            $user->assignRole('teacher');
            $created++;
        }

        session()->flash('status', __(':n teacher(s) imported.', ['n' => $created]));
        $this->redirectRoute('admin.teachers.index', navigate: true);
    }

    public function reset_preview(): void
    {
        $this->reset(['rows', 'previewed', 'file']);
    }

    public function render()
    {
        $valid = collect($this->rows)->where('valid', true)->count();
        $invalid = collect($this->rows)->where('valid', false)->count();
        return view('livewire.admin.teachers.bulk-import', compact('valid', 'invalid'));
    }
}
