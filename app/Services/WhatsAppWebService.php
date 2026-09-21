<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppWebService
{
    public function __construct(
        protected string $baseUrl = 'http://localhost:3001'
    ) {
    }

    public function status(): array
    {
        try {
            $response = Http::timeout(5)->get($this->baseUrl . '/wa/status');

            return $response->successful() ? $response->json() : ['connected' => false, 'qr' => null];
        } catch (\Throwable $e) {
            return ['connected' => false, 'qr' => null, 'error' => $e->getMessage()];
        }
    }

    public function send(string $phone, string $message): array
    {
        $status = $this->status();

        if (!($status['connected'] ?? false)) {
            return [
                'success' => false,
                'reason' => 'wa_not_connected',
                'message' => 'WhatsApp belum login / service belum aktif.',
            ];
        }

        $to = preg_replace('/[^0-9]/', '', $phone) ?? '';

        // Baileys requires the international WhatsApp number format.
        if (str_starts_with($to, '0')) {
            $to = '62' . ltrim($to, '0');
        }

        if (blank($to) || strlen($to) < 10) {
            return [
                'success' => false,
                'reason' => 'invalid_phone',
                'message' => 'Nomor WhatsApp tidak valid.',
            ];
        }

        try {
            $response = Http::timeout(15)->post($this->baseUrl . '/wa/send', [
                'to' => $to,
                'message' => $message,
            ]);

            $data = $response->json();

            if ($response->successful() && (($data['success'] ?? false) === true)) {
                return ['success' => true, 'data' => $data];
            }

            return [
                'success' => false,
                'reason' => 'wa_send_failed',
                'message' => $data['error'] ?? 'Gagal mengirim WA.',
                'raw' => $data,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'reason' => 'connection_error',
                'message' => $e->getMessage(),
            ];
        }
    }
}
