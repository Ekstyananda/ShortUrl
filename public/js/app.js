(function () {
    // Tombol salin short URL.
    document.addEventListener('click', function (event) {
        const button = event.target.closest('.js-copy');
        if (!button) return;

        const text = button.dataset.copy;
        const done = function () {
            const toastEl = document.getElementById('copyToast');
            if (toastEl && window.bootstrap) bootstrap.Toast.getOrCreateInstance(toastEl).show();
        };

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(done);
        } else {
            const area = document.createElement('textarea');
            area.value = text;
            area.setAttribute('readonly', '');
            area.style.position = 'absolute';
            area.style.left = '-9999px';
            document.body.appendChild(area);
            area.select();
            document.execCommand('copy');
            area.remove();
            done();
        }
    });

    // Konfirmasi sebelum tindakan berisiko (hapus, nonaktifkan akun).
    document.addEventListener('submit', function (event) {
        const form = event.target.closest('.js-confirm');
        if (form && !window.confirm(form.dataset.confirm)) {
            event.preventDefault();
        }
    });
})();
