<?php

namespace App\Jobs;

use App\Models\Guest;
use App\Services\WhatsAppWebService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsAppWebInvitationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public function __construct(public int $guestId)
    {
    }

    public function handle(): void
    {
        $guest = Guest::find($this->guestId);

        if (! $guest || blank($guest->phone)) {
            return;
        }

        $status = (new WhatsAppWebService())->status();

        if (!($status['connected'] ?? false)) {
            $guest->update(['whatsapp_status' => 'failed']);
            report(new \RuntimeException('WA Web not connected for guest ' . $guest->id . ': ' . json_encode($status)));
            return;
        }

        $guest->update(['whatsapp_status' => 'sending']);

        $message = "Halo {$guest->name}, berikut link undangan Anda:\n" . route('invitation.show', $guest->slug);

        $service = new WhatsAppWebService();
        $response = $service->send($guest->phone, $message);

        if (($response['success'] ?? false) === true) {
            $guest->update(['whatsapp_status' => 'sent']);
            return;
        }

        $guest->update(['whatsapp_status' => 'failed']);
        report(new \RuntimeException('WA Web send failed for guest ' . $guest->id . ': ' . json_encode($response)));
    }
}
