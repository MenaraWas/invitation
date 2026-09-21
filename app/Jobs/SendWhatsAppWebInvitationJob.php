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

        $message = "Yth. Bapak/Ibu/Saudara/i {$guest->name},\n\n"
            . "Tanpa mengurangi rasa hormat, sehubungan dengan keterbatasan jarak dan waktu, "
            . "melalui pesan ini kami bermaksud mengundang Bapak/Ibu/Saudara/i untuk hadir dan "
            . "memberikan doa restu pada acara pernikahan kami:\n\n"
            . "🔗 Detail & RSVP: " . route('invitation.show', $guest->slug) . "\n"
            . "Anda mendapatkan formasi kursi sebanyak _*{$guest->seat_count} kursi*_\n\n"
            . "Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i "
            . "berkenan hadir dengan *mengisikan RSVP yang tersedia*.\n\n"
            . "Salam hangat,\n"
            . "Riza & Pierre";

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
