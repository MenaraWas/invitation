<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\InvitationSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_name_is_shown_on_invitation_gate(): void
    {
        InvitationSetting::create([
            'event_name' => 'Pernikahan Budi & Rina',
            'content_type' => 'website',
            'content_url' => 'https://example.com/invitation',
            'event_date' => now()->addDays(10),
        ]);

        $guest = Guest::factory()->create([
            'name' => 'Budi Santoso',
            'token' => 'abc123tokenguest',
            'status' => 'active',
        ]);

        $response = $this->get('/inv/' . $guest->token);

        $response->assertOk();
        $response->assertSee('Halo, Budi Santoso');
        $response->assertSee('Pernikahan Budi & Rina');
    }

    public function test_verified_device_can_access_invitation(): void
    {
        InvitationSetting::create([
            'event_name' => 'Pernikahan Budi & Rina',
            'content_type' => 'website',
            'content_url' => 'https://example.com/invitation',
            'event_date' => now()->addDays(10),
        ]);

        $guest = Guest::factory()->create([
            'name' => 'Siti Nurhaliza',
            'token' => 'device-token-123',
            'status' => 'active',
        ]);

        $fingerprint = 'demo-fingerprint-123';

        $guest->deviceSession()->create([
            'fingerprint_hash' => $fingerprint,
            'user_agent' => 'Mozilla/5.0',
            'ip_address' => '127.0.0.1',
            'first_accessed_at' => now(),
            'last_accessed_at' => now(),
        ]);

        $response = $this->postJson('/inv/' . $guest->token . '/verify', [
            'fingerprint' => $fingerprint,
        ]);

        $response->assertOk();
        $response->assertJsonPath('allowed', true);
    }
}
