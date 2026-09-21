<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class WhatsAppStatusController extends Controller
{
    public function index()
    {
        $response = Http::get('http://localhost:3001/wa/status');

        $data = $response->successful() ? $response->json() : ['connected' => false, 'qr' => null];

        return view('wa-status', [
            'connected' => $data['connected'] ?? false,
            'qr' => $data['qr'] ?? null,
        ]);
    }

    public function reset()
    {
        $response = Http::timeout(15)->post('http://localhost:3001/wa/reset');

        return back()->with(
            $response->successful() ? 'success' : 'error',
            $response->json('message') ?? $response->json('error') ?? 'Gagal mereset WhatsApp.'
        );
    }
}
