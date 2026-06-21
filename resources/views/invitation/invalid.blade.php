<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undangan Tidak Ditemukan</title>
    @vite('resources/css/app.css')
    <style>
        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in-up {
            animation: fade-in-up 0.6s ease-out both;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4 font-sans antialiased">
    <div class="max-w-sm text-center fade-in-up">
        <div class="w-20 h-20 mx-auto mb-5 rounded-full bg-amber-50 flex items-center justify-center">
            <svg class="w-10 h-10 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <h1 class="text-xl font-semibold text-gray-800 mb-2">Undangan Tidak Ditemukan</h1>
        <p class="text-gray-500 text-sm leading-relaxed">
            Link yang Anda akses tidak valid. Mohon periksa kembali link yang Anda terima,
            atau hubungi pihak yang mengundang Anda.
        </p>
    </div>
</body>
</html>