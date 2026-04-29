<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class DefaultSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'school_name' => ['My School', false],
            'school_address' => ['', false],
            'school_email' => ['', false],
            'school_phone' => ['', false],
            'school_registration_number' => ['', false],
            'receipt_footer' => ['Thank you for your payment.', false],
            'logo_original' => [null, false],
            'logo_small' => [null, false],
            'logo_large' => [null, false],

            // Provider settings — managed in Settings UI by super-admin
            'bayarcash_api_token' => [null, true],
            'bayarcash_api_secret_key' => [null, true],
            'bayarcash_portal_key' => [null, true],
            'bayarcash_sandbox' => ['1', false],

            'sendora_api_key' => [null, true],
            'sendora_base_url' => ['https://sendora.cc/api/v1', false],
            'sendora_device_id' => [null, false],
        ];

        foreach ($defaults as $key => [$value, $encrypted]) {
            if (! Setting::where('key', $key)->exists()) {
                Setting::set($key, $value, $encrypted);
            }
        }
    }
}
