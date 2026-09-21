<?php

namespace App\Jobs;

use App\Models\Guest;
use App\Models\InvitationSetting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SendWhatsAppInvitationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public function __construct(public int $guestId)
    {
    }

    public function handle(): void
    {
        $guest = Guest::find($guestId);

        if (! $guest || blank($guest->phone)) {
            return;
        }

        $guest->update(['whatsapp_status' => 'sending']);

        $settings = InvitationSetting::current();
        $link = route('invitation.show', $guest->token);
        $message = trim(config('services.whatsapp.default_message', 'Halo, berikut undangan kami:') . "\n\n" . $guest->name . "\n" . $link);

        $phone = preg_replace('/[^0-9]/', '', $guest->phone);
        $phone = ltrim($phone, '0');

        $response = Http::acceptJson()
            ->withToken(config('services.whatsapp.access_token'))
            ->post(
                sprintf(
                    '%s/%s/%s/messages',
                    rtrim(config('services.whatsapp.api_url', 'https://graph.facebook.com'), '/'),
                    config('services.whatsapp.api_version', 'v19.0'),
                    config('services.whatsapp.phone_number_id')
                ),
                [
                    'messaging_product' => 'whatsapp',
                    'to' => $phone,
                    'type' => 'template',
                    'template' => [
                        'name' => 'hello_world',
                        'language' => ['code' => 'id'],
                        'components' => [
                            [
                                'type' => 'body',
                                'parameters' => [
                                    ['type' => 'text', 'text' => $guest->name],
                                    ['type' => 'text', 'text' => $link],
                                ],
                            ],
                        ],
                    ],
                ]
            );

        if ($response->successful()) {
            $guest->update(['whatsapp_status' => 'sent']);
            return;
        }

        $guest->update([
            'whatsapp_status' => 'failed',
        ]);

        report(new \RuntimeException('WhatsApp send failed for guest ' . $guest->id . ': ' . $response->body()));
    }
}
