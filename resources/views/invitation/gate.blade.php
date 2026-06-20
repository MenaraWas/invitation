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
    <div id="denied" style="display:none">
        Maaf, perangkat ini tidak memiliki akses ke undangan ini. Silakan hubungi panitia.
    </div>

    @vite('resources/js/invitation-gate.js')
</body>
</html>