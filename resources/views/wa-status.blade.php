<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status WhatsApp</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-6">
    <div class="max-w-lg w-full bg-white rounded-2xl shadow-lg p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Status WhatsApp</h1>

        @if ($connected)
            <div class="rounded-xl bg-green-50 border border-green-200 p-4 text-green-700">
                WhatsApp sudah login dan siap kirim pesan.
            </div>
        @else
            <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 text-amber-700">
                WhatsApp belum login. Silakan scan QR code dari service WA.
            </div>
        @endif

        @if ($qr)
            <div class="mt-6 flex justify-center">
                <img src="{{ $qr }}" alt="QR WhatsApp" class="w-64 h-64 rounded-xl border border-gray-200 bg-white p-3" />
            </div>
        @endif

        <div class="mt-6 text-sm text-gray-500">
            Service WA: <span class="font-medium text-gray-700">http://localhost:3001</span>
        </div>
    </div>
</body>
</html>
