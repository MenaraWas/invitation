import FingerprintJS from '@fingerprintjs/fingerprintjs';

let verificationInProgress = false;

document.addEventListener('DOMContentLoaded', async () => {
    if (verificationInProgress) return;
    verificationInProgress = true;

    const loadingEl = document.getElementById('loading');
    const contentEl = document.getElementById('content');
    const deniedEl = document.getElementById('denied');
    const verifyUrl = contentEl.dataset.verifyUrl;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    try {
        const fp = await FingerprintJS.load();
        const result = await fp.get();

        const response = await fetch(verifyUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ fingerprint: result.visitorId }),
        });

        const data = await response.json();

        loadingEl.style.display = 'none';

        if (data.allowed) {
            contentEl.style.display = 'block';
        } else {
            deniedEl.style.display = 'block';
        }
    } catch (error) {
        loadingEl.style.display = 'none';
        deniedEl.style.display = 'block';
        deniedEl.textContent = 'Terjadi kesalahan. Silakan hubungi panitia.';
    }
});