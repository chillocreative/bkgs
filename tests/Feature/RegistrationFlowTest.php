<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DefaultSettingsSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\WhatsappTemplateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, WhatsappTemplateSeeder::class, DefaultSettingsSeeder::class]);
    }

    public function test_registration_screen_renders(): void
    {
        $this->get('/register')->assertOk()->assertSeeText('Create teacher account');
    }

    public function test_user_can_register_as_teacher_with_normalised_phone(): void
    {
        $component = Livewire::test('pages.auth.register')
            ->set('name', 'Cikgu Ali')
            ->set('email', 'ali@school.test')
            ->set('phone', '0123456701')
            ->set('ic_number', '900101012345')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register');

        $component->assertHasNoErrors()
            ->assertRedirect('/invoices');

        $user = User::where('email', 'ali@school.test')->firstOrFail();
        $this->assertSame('60123456701', $user->phone);
        $this->assertSame('900101012345', $user->ic_number);
        $this->assertTrue($user->is_active);
        $this->assertTrue($user->hasRole('teacher'));
        $this->assertFalse($user->hasRole('admin'));
        $this->assertFalse($user->hasRole('super-admin'));
    }

    public function test_registration_rejects_bad_ic(): void
    {
        Livewire::test('pages.auth.register')
            ->set('name', 'Cikgu')
            ->set('email', 'x@school.test')
            ->set('phone', '0123456701')
            ->set('ic_number', '12345')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors(['ic_number']);
    }

    public function test_registration_rejects_bad_phone(): void
    {
        Livewire::test('pages.auth.register')
            ->set('name', 'Cikgu')
            ->set('email', 'y@school.test')
            ->set('phone', 'abcdef')
            ->set('ic_number', '900101012345')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors(['phone']);
    }

    public function test_registration_rejects_duplicate_ic(): void
    {
        $existing = User::create([
            'name' => 'Existing',
            'email' => 'existing@school.test',
            'password' => bcrypt('p'),
            'phone' => '60100000000',
            'ic_number' => '900101012345',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $existing->assignRole('teacher');

        Livewire::test('pages.auth.register')
            ->set('name', 'Other')
            ->set('email', 'other@school.test')
            ->set('phone', '0123456701')
            ->set('ic_number', '900101012345')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors(['ic_number']);
    }

    public function test_frontpage_renders_for_guest(): void
    {
        $this->get('/')->assertOk()->assertSeeText('Pay your monthly fees');
    }

    public function test_login_page_renders(): void
    {
        $this->get('/login')->assertOk()->assertSeeText('Welcome back');
    }

    public function test_forgot_password_page_renders(): void
    {
        $this->get('/forgot-password')->assertOk()->assertSeeText('Forgot your password?');
    }
}
