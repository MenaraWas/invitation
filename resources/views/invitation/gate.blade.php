<div id="loading">Memuat undangan...</div>
<div id="content" style="display:none">
    {{-- isi undangan asli di sini --}}
</div>
<div id="denied" style="display:none">
    Maaf, perangkat ini tidak memiliki akses ke undangan ini. Silakan hubungi panitia.
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/fingerprintjs/4.x.x/fp.min.js"></script>
<script>
    FingerprintJS.load().then(fp => fp.get()).then(result => {
        fetch('{{ route("invitation.verify", $token) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ fingerprint: result.visitorId }),
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('loading').style.display = 'none';
            if (data.allowed) {
                document.getElementById('content').style.display = 'block';
            } else {
                document.getElementById('denied').style.display = 'block';
            }
        });
    });
</script>