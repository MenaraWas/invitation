<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\DeviceSession;
use App\Models\InvitationSetting;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Carbon\Carbon;

class InvitationController extends Controller
{
    public function show(string $token){
        $guest = Guest::where('token', $token)->first();

        if (!$guest || $guest->status === 'revoked') {
            return view('invitation.invalid');
        }

        $eventDeadline = Carbon::parse(config('app.event_date'))->addDay();
        if (now()->greaterThan($eventDeadline)) {
            return view('invitation.expired');
        }

        // Render halaman shell, fingerprint dicek lewat AJAX setelah load
        return view('invitation.gate', compact('guest', 'token'));
    }

    public function verify(Request $request, string $token)
    {
        $request->validate([
            'fingerprint' => 'required|string',
        ]);

        $guest = Guest::where('token', $token)->first();

        if (!$guest || $guest->status === 'revoked') {
            return response()->json(['allowed' => false, 'reason' => 'invalid'], 403);
        }

        $eventDeadline = Carbon::parse(config('app.event_date'))->addDay();
        if (now()->greaterThan($eventDeadline)) {
            return response()->json(['allowed' => false, 'reason' => 'expired'], 403);
        }

        $fingerprint = $request->input('fingerprint');
        $existing = $guest->deviceSession;

        if ($existing) {
            if ($existing->fingerprint_hash === $fingerprint) {
                $existing->update(['last_accessed_at' => now()]);
                return $this->successResponse();
            }

            return response()->json(['allowed' => false, 'reason' => 'device_mismatch'], 403);
        }

        // Belum ada device session, coba insert
        try {
            DeviceSession::create([
                'guest_id' => $guest->id,
                'fingerprint_hash' => $fingerprint,
                'user_agent' => $request->userAgent(),
                'ip_address' => $request->ip(),
                'first_accessed_at' => now(),
                'last_accessed_at' => now(),
            ]);

            return $this->successResponse();
        } catch (QueryException $e) {
            // Race condition: device session udah ke-create sama request lain
            // di waktu yang nyaris bersamaan. Re-fetch dan cek ulang.
            $existing = $guest->fresh()->deviceSession;

            if ($existing && $existing->fingerprint_hash === $fingerprint) {
                return $this->successResponse();
            }

            return response()->json(['allowed' => false, 'reason' => 'device_mismatch'], 403);
        }
    }

    /**
     * Response sukses dengan data content dari InvitationSetting.
     */
    private function successResponse(): \Illuminate\Http\JsonResponse
    {
        $setting = InvitationSetting::current();

        return response()->json([
            'allowed' => true,
            'content_url' => $setting->content_url,
            'content_type' => $setting->content_type,
        ]);
    }
}
