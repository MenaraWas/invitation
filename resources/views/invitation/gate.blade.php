<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Undangan - {{ $guest->name }}</title>
    @vite('resources/css/app.css')
    <style>
        /* Smooth fade transitions */
        .gate-fade {
            transition: opacity 0.4s ease;
        }
        .gate-fade.is-hidden {
            opacity: 0;
            pointer-events: none;
            position: absolute;
            inset: 0;
        }
        .gate-fade.is-visible {
            opacity: 1;
        }
        /* Pulse animation for the loading ring */
        @keyframes pulse-ring {
            0% { transform: scale(0.8); opacity: 1; }
            50% { transform: scale(1); opacity: 0.5; }
            100% { transform: scale(0.8); opacity: 1; }
        }
        .pulse-ring {
            animation: pulse-ring 2s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen m-0 p-0 font-sans antialiased">

    {{-- ============ LOADING STATE ============ --}}
    <div id="loading" class="gate-fade is-visible min-h-screen flex items-center justify-center">
        <div class="text-center">
            <div class="relative w-16 h-16 mx-auto mb-5">
                {{-- Outer pulsing ring --}}
                <div class="pulse-ring absolute inset-0 rounded-full border-2 border-gray-200"></div>
                {{-- Spinning loader --}}
                <svg class="animate-spin w-16 h-16 text-gray-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </div>
            <p class="text-gray-500 text-sm font-medium tracking-wide">Memuat undangan...</p>
            <p class="text-gray-400 text-xs mt-1">Mohon tunggu sebentar</p>
        </div>
    </div>

    {{-- ============ CONTENT (shown when verified) ============ --}}
    <div id="content"
         data-verify-url="{{ route('invitation.verify', $token) }}"
         class="gate-fade is-hidden"
         style="display:none">
        <div id="content-container"></div>
    </div>

    {{-- ============ DENIED STATE ============ --}}
    <div id="denied" class="gate-fade is-hidden min-h-screen flex items-center justify-center px-4" style="display:none">
        <div class="max-w-sm text-center">
            <div class="w-20 h-20 mx-auto mb-5 rounded-full bg-red-50 flex items-center justify-center">
                <svg class="w-10 h-10 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
            </div>
            <h1 class="text-xl font-semibold text-gray-800 mb-2">Akses Ditolak</h1>
            <p class="text-gray-500 text-sm leading-relaxed">
                Perangkat ini tidak memiliki akses ke undangan ini.
                Silakan hubungi panitia jika Anda merasa ini adalah kesalahan.
            </p>
        </div>
    </div>

    @vite('resources/js/invitation-gate.js')
</body>
</html>