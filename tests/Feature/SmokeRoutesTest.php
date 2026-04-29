<?php

namespace Tests\Feature;

use App\Models\FeeStructure;
use App\Models\Invoice;
use App\Models\User;
use Database\Seeders\DefaultSettingsSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\WhatsappTemplateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SmokeRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, WhatsappTemplateSeeder::class, DefaultSettingsSeeder::class]);
    }

    public function test_admin_routes_render_for_super_admin(): void
    {
        $admin = User::create([
            'name' => 'Super',
            'email' => 'super@test.local',
            'password' => Hash::make('password'),
            'phone' => '60123456789',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('super-admin');

        $teacher = User::create([
            'name' => 'Cikgu',
            'email' => 't1@test.local',
            'password' => Hash::make('password'),
            'phone' => '60123456788',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $teacher->assignRole('teacher');

        $fee = FeeStructure::create(['name' => 'Default', 'amount' => 50, 'due_day' => 5, 'is_default' => true]);
        $invoice = Invoice::create([
            'user_id' => $teacher->id,
            'fee_structure_id' => $fee->id,
            'invoice_number' => 'INV-202604-0001',
            'period_month' => now()->startOfMonth()->toDateString(),
            'amount' => 50,
            'late_fee' => 0,
            'total' => 50,
            'due_date' => now()->startOfMonth()->day(5)->toDateString(),
            'status' => 'pending',
        ]);

        $routes = [
            ['get', '/admin'],
            ['get', '/admin/teachers'],
            ['get', '/admin/teachers/create'],
            ['get', "/admin/teachers/{$teacher->id}"],
            ['get', "/admin/teachers/{$teacher->id}/edit"],
            ['get', '/admin/teachers-import'],
            ['get', '/admin/fee-structures'],
            ['get', '/admin/invoices'],
            ['get', '/admin/invoices/generate'],
            ['get', "/admin/invoices/{$invoice->id}"],
            ['get', '/admin/payments'],
            ['get', "/admin/payments/record/{$invoice->id}"],
            ['get', '/admin/notifications'],
            ['get', '/admin/templates'],
            ['get', '/admin/settings'],
            ['get', '/admin/settings/branding'],
            ['get', '/admin/settings/bayarcash'],
            ['get', '/admin/settings/sendora'],
        ];

        $this->actingAs($admin);
        foreach ($routes as [$method, $url]) {
            $resp = $this->{$method}($url);
            $this->assertSame(200, $resp->status(), "$method $url failed: ".substr(strip_tags($resp->getContent()), 0, 400));
        }
    }

    public function test_teacher_routes_render(): void
    {
        $teacher = User::create([
            'name' => 'Cikgu',
            'email' => 't2@test.local',
            'password' => Hash::make('password'),
            'phone' => '60123456788',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $teacher->assignRole('teacher');

        $fee = FeeStructure::create(['name' => 'Default', 'amount' => 50, 'due_day' => 5, 'is_default' => true]);
        $invoice = Invoice::create([
            'user_id' => $teacher->id,
            'fee_structure_id' => $fee->id,
            'invoice_number' => 'INV-202604-0001',
            'period_month' => now()->startOfMonth()->toDateString(),
            'amount' => 50,
            'late_fee' => 0,
            'total' => 50,
            'due_date' => now()->startOfMonth()->day(5)->toDateString(),
            'status' => 'pending',
        ]);

        $this->actingAs($teacher);
        $this->get('/invoices')->assertOk();
        $this->get("/invoices/{$invoice->id}")->assertOk();
        $this->get('/dashboard')->assertOk();
    }
}
