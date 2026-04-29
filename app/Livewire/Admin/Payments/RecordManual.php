<?php

namespace App\Livewire\Admin\Payments;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Invoice;
use App\Models\Payment;
use App\Notifications\PaymentReceivedNotification;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Record Manual Payment')]
#[Layout('layouts.app')]
class RecordManual extends Component
{
    use WithFileUploads;

    public Invoice $invoice;

    public string $method = 'manual_cash';

    public string $amount = '';

    public string $reference = '';

    public string $paid_at = '';

    public $receipt;

    public string $notes = '';

    public function mount(Invoice $invoice): void
    {
        $this->invoice = $invoice;
        $this->amount = (string) $invoice->total;
        $this->paid_at = now()->format('Y-m-d');
    }

    protected function rules(): array
    {
        return [
            'method' => 'required|in:manual_cash,manual_transfer,manual_cheque',
            'amount' => 'required|numeric|min:0.01',
            'reference' => 'nullable|string|max:120',
            'paid_at' => 'required|date',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->invoice->isPaid()) {
            $this->dispatch('toast', message: __('This invoice is already paid.'));
            return;
        }

        $receiptPath = null;
        if ($this->receipt) {
            $receiptPath = $this->receipt->store('receipts', 'local');
        }

        $payment = Payment::create([
            'invoice_id' => $this->invoice->id,
            'user_id' => $this->invoice->user_id,
            'amount' => $data['amount'],
            'method' => $data['method'],
            'bayarcash_exchange_reference' => $data['reference'] ?: null,
            'status' => PaymentStatus::Successful->value,
            'receipt_path' => $receiptPath,
            'paid_at' => $data['paid_at'],
            'recorded_by' => auth()->id(),
        ]);

        $this->invoice->status = InvoiceStatus::Paid;
        $this->invoice->paid_at = $payment->paid_at;
        if (! empty($data['notes'])) {
            $this->invoice->notes = $data['notes'];
        }
        $this->invoice->save();

        if ($this->invoice->user) {
            $this->invoice->user->notify(new PaymentReceivedNotification($payment, $this->invoice));
        }

        session()->flash('status', __('Payment recorded.'));
        $this->redirectRoute('admin.invoices.show', $this->invoice, navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.payments.record-manual');
    }
}
