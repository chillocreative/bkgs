<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Services\SendoraService;
use Database\Seeders\DefaultSettingsSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\WhatsappTemplateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SendoraSendTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, WhatsappTemplateSeeder::class, DefaultSettingsSeeder::class]);
        Setting::set('sendora_api_key', 'test-token', true);
        Setting::set('sendora_base_url', 'https://sendora.cc/api/v1');
    }

    public function test_sends_to_sendora_with_normalised_phone(): void
    {
        Http::fake([
            'sendora.cc/*' => Http::response([
                'success' => true,
                'message' => 'Message sent successfully.',
                'data' => ['phone' => '60123456789', 'message_id' => 'mid-1'],
            ], 200),
        ]);

        $svc = new SendoraService();
        $result = $svc->send('0123456789', 'Hello');

        $this->assertTrue($result['success']);
        $this->assertSame('mid-1', $result['provider_message_id']);

        Http::assertSent(function ($request) {
            $body = json_decode($request->body(), true);
            return str_contains($request->url(), '/messages/send')
                && $body['phone'] === '60123456789'
                && $body['message'] === 'Hello'
                && $request->hasHeader('Authorization', 'Bearer test-token');
        });
    }

    public function test_returns_failure_on_422_validation(): void
    {
        Http::fake([
            'sendora.cc/*' => Http::response([
                'success' => false,
                'message' => 'Invalid phone',
            ], 422),
        ]);

        $svc = new SendoraService();
        $result = $svc->send('60123456789', 'Hi');

        $this->assertFalse($result['success']);
        $this->assertSame('Invalid phone', $result['error']);
    }
}
