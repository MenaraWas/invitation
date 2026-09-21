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

    /**
     * Fade out an element and fade in another.
     */
    function showState(hideEl, showEl) {
        hideEl.classList.add('is-hidden');
        hideEl.classList.remove('is-visible');
        setTimeout(() => {
            hideEl.style.display = 'none';
            showEl.style.display = '';
            showEl.classList.remove('is-hidden');
            showEl.classList.add('is-visible');
        }, 400); // match CSS transition duration
    }

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

        if (data.allowed) {
            window.location.replace(data.content_url);
        } else {
            showState(loadingEl, deniedEl);
        }
    } catch (error) {
        showState(loadingEl, deniedEl);
    }
});