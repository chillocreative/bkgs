<?php

namespace Tests\Feature;

use App\Models\FeeStructure;
use App\Models\Invoice;
use App\Models\User;
use App\Services\InvoiceGenerator;
use Database\Seeders\DefaultSettingsSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\WhatsappTemplateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class InvoiceGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, WhatsappTemplateSeeder::class, DefaultSettingsSeeder::class]);
    }

    public function test_generates_one_invoice_per_active_teacher_and_is_idempotent(): void
    {
        Notification::fake();

        FeeStructure::create(['name' => 'Default', 'amount' => 100, 'due_day' => 5, 'is_default' => true]);

        $a = $this->makeTeacher('a@t.local', '60123456701', true);
        $b = $this->makeTeacher('b@t.local', '60123456702', true);
        $inactive = $this->makeTeacher('c@t.local', '60123456703', false);

        $gen = app(InvoiceGenerator::class);

        $r1 = $gen->generateForMonth(now()->startOfMonth());
        $this->assertSame(2, $r1['created']);
        $this->assertSame(0, $r1['skipped']);

        // Second call: nothing new
        $r2 = $gen->generateForMonth(now()->startOfMonth());
        $this->assertSame(0, $r2['created']);
        $this->assertSame(2, $r2['skipped']);

        $this->assertSame(2, Invoice::count());
        $this->assertNull(Invoice::where('user_id', $inactive->id)->first());
    }

    public function test_invoice_uses_teacher_specific_amount_over_default(): void
    {
        Notification::fake();
        FeeStructure::create(['name' => 'Default', 'amount' => 100, 'due_day' => 5, 'is_default' => true]);

        $teacher = $this->makeTeacher('x@t.local', '60123456704', true);
        $teacher->update(['monthly_fee_amount' => 250]);

        app(InvoiceGenerator::class)->generateForMonth(now()->startOfMonth());

        $this->assertSame('250.00', Invoice::first()->total);
    }

    protected function makeTeacher(string $email, string $phone, bool $active): User
    {
        $u = User::create([
            'name' => $email,
            'email' => $email,
            'password' => Hash::make('password'),
            'phone' => $phone,
            'is_active' => $active,
            'email_verified_at' => now(),
        ]);
        $u->assignRole('teacher');
        return $u;
    }
}
