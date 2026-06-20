<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div id="loading">Memuat undangan...</div>
    <div id="content" data-verify-url="{{ route('invitation.verify', $token) }}" style="display:none">
        {{-- isi undangan --}}
    </div>
    <div id="denied" style="display:none">
        Maaf, perangkat ini tidak memiliki akses ke undangan ini. Silakan hubungi panitia.
    </div>

    @vite('resources/js/invitation-gate.js')
</body>