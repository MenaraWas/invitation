<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Undangan</title>
</head>
<body>
    <div id="loading">Memuat undangan...</div>
    <div id="content" data-verify-url="{{ route('invitation.verify', $token) }}" style="display:none">
        <div id="content-container"></div>
    </div>
    <div id="denied" class="hidden min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
            </svg>
            <h1 class="text-xl font-semibold text-gray-800 mb-2">Akses Ditolak</h1>
            <p class="text-gray-500 text-sm">
                Perangkat ini tidak memiliki akses ke undangan ini. Silakan hubungi panitia jika Anda merasa ini adalah kesalahan.
            </p>
        </div>
    </div>

    @vite('resources/js/invitation-gate.js')
</body>
</html>