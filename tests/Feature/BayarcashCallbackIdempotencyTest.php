<?php

namespace Tests\Feature;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\FeeStructure;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use App\Services\BayarcashService;
use Database\Seeders\DefaultSettingsSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\WhatsappTemplateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Mockery;
use Tests\TestCase;

class BayarcashCallbackIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, WhatsappTemplateSeeder::class, DefaultSettingsSeeder::class]);
    }

    public function test_replayed_callback_does_not_create_duplicate_payment(): void
    {
        Notification::fake();

        $teacher = User::create([
            'name' => 'Cikgu', 'email' => 't@t.local',
            'password' => Hash::make('p'), 'phone' => '60123456789',
            'is_active' => true, 'email_verified_at' => now(),
        ]);
        $teacher->assignRole('teacher');

        $fs = FeeStructure::create(['name' => 'D', 'amount' => 100, 'due_day' => 5, 'is_default' => true]);
        $invoice = Invoice::create([
            'user_id' => $teacher->id, 'fee_structure_id' => $fs->id,
            'invoice_number' => 'INV-202604-0001',
            'period_month' => now()->startOfMonth()->toDateString(),
            'amount' => 100, 'late_fee' => 0, 'total' => 100,
            'due_date' => now()->startOfMonth()->day(5)->toDateString(),
            'status' => 'pending',
        ]);

        $payment = Payment::create([
            'invoice_id' => $invoice->id, 'user_id' => $teacher->id,
            'amount' => 100, 'method' => PaymentMethod::Bayarcash->value,
            'status' => PaymentStatus::Pending->value,
            'bayarcash_exchange_reference' => $invoice->invoice_number.'-'.uniqid(),
        ]);

        // Mock the SDK to return verified=true so we can focus on idempotency.
        $svc = Mockery::mock(BayarcashService::class)->makePartial();
        $svc->shouldReceive('verifyCallback')->andReturn(true);

        $callback = [
            'transaction_id' => 'TX123ABC',
            'order_number' => $payment->bayarcash_exchange_reference,
            'exchange_reference_number' => 'EXR-1',
            'amount' => '100.00',
            'status' => '3', // success
            'checksum' => 'fake',
        ];

        $svc->handleCallback($callback);
        $svc->handleCallback($callback); // replay

        $this->assertSame(1, Payment::count());
        $p = Payment::first();
        $this->assertSame(PaymentStatus::Successful, $p->status);
        $this->assertSame('TX123ABC', $p->bayarcash_transaction_id);

        $invoice->refresh();
        $this->assertSame(InvoiceStatus::Paid, $invoice->status);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
