(function () {
    // Tombol salin short URL.
    document.addEventListener('click', function (event) {
        const button = event.target.closest('.js-copy');
        if (!button) return;

        const text = button.dataset.copy;
        const done = function () {
            const icon = button.querySelector('i');
            if (icon) {
                icon.className = 'bi bi-check2';
                setTimeout(function () { icon.className = 'bi bi-clipboard'; }, 1500);
            }
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

    // Ganti tema terang/gelap.
    document.addEventListener('click', function (event) {
        if (!event.target.closest('.js-theme-toggle')) return;
        const root = document.documentElement;
        const next = root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
        root.setAttribute('data-bs-theme', next);
        try { localStorage.setItem('theme', next); } catch (e) {}
        document.dispatchEvent(new CustomEvent('themechange'));
    });

    // Tampilkan/sembunyikan password.
    document.addEventListener('click', function (event) {
        const button = event.target.closest('.js-toggle-password');
        if (!button) return;
        const input = document.getElementById(button.dataset.target);
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        button.querySelector('i').className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
        button.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
    });

    // Konfirmasi sebelum tindakan berisiko (hapus, nonaktifkan akun).
    document.addEventListener('submit', function (event) {
        const form = event.target.closest('.js-confirm');
        if (form && !window.confirm(form.dataset.confirm)) {
            event.preventDefault();
        }
    });

    // Garis bawah navbar landing saat di-scroll.
    const nav = document.querySelector('.js-site-nav');
    if (nav) {
        const onScroll = function () { nav.classList.toggle('scrolled', window.scrollY > 8); };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }
})();

(function () {
    // Isi modal QR sesuai link yang dipilih.
    const modal = document.getElementById('qrModal');
    if (modal) {
        modal.addEventListener('show.bs.modal', function (event) {
            const b = event.relatedTarget;
            const img = modal.querySelector('.js-qr-modal-img');
            img.src = b.dataset.qrSrc;
            img.alt = 'QR code untuk ' + b.dataset.qrUrl;
            modal.querySelector('.js-qr-modal-url').textContent = b.dataset.qrUrl;
            modal.querySelector('.js-qr-modal-svg').href = b.dataset.qrDownload;
            const png = modal.querySelector('.js-qr-modal-png');
            png.dataset.src = b.dataset.qrSrc;
            png.dataset.filename = 'qr-' + b.dataset.qrAlias + '.png';
        });
    }

    // Unduh QR sebagai PNG resolusi tinggi (SVG digambar ulang ke canvas).
    document.addEventListener('click', function (event) {
        const button = event.target.closest('.js-qr-png');
        if (!button) return;

        const size = 1024;
        const img = new Image();
        img.onload = function () {
            const canvas = document.createElement('canvas');
            canvas.width = canvas.height = size;
            const ctx = canvas.getContext('2d');
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, size, size);
            ctx.imageSmoothingEnabled = false;
            ctx.drawImage(img, 0, 0, size, size);
            canvas.toBlob(function (blob) {
                const a = document.createElement('a');
                a.href = URL.createObjectURL(blob);
                a.download = button.dataset.filename || 'qr.png';
                document.body.appendChild(a);
                a.click();
                a.remove();
                setTimeout(function () { URL.revokeObjectURL(a.href); }, 1000);
            }, 'image/png');
        };
        img.src = button.dataset.src;
    });
})();
