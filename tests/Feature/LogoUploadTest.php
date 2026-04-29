<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Database\Seeders\DefaultSettingsSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\WhatsappTemplateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class LogoUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, WhatsappTemplateSeeder::class, DefaultSettingsSeeder::class]);
    }

    public function test_admin_can_upload_logo_and_settings_record_paths(): void
    {
        Storage::fake('public');

        $admin = User::create([
            'name' => 'Super', 'email' => 'super@t.local',
            'password' => Hash::make('p'), 'phone' => '60100000000',
            'is_active' => true, 'email_verified_at' => now(),
        ]);
        $admin->assignRole('super-admin');

        $this->actingAs($admin);

        $file = UploadedFile::fake()->image('logo.png', 500, 500);

        Livewire::test(\App\Livewire\Admin\Settings\Branding::class)
            ->set('logo', $file)
            ->call('upload')
            ->assertHasNoErrors();

        $this->assertNotNull(Setting::get('logo_original'));
        $this->assertNotNull(Setting::get('logo_small'));
        $this->assertNotNull(Setting::get('logo_large'));
        Storage::disk('public')->assertExists(Setting::get('logo_small'));
        Storage::disk('public')->assertExists(Setting::get('logo_large'));
    }
}
