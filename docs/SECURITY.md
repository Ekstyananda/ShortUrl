# Keamanan

## Validasi tujuan
`app/Rules/SafeUrl.php` hanya menerima URL absolut `http`/`https` dengan hostname. Ditolak: `javascript:`, `data:`, `file:`, `ftp:`, URL tanpa host, URL relatif/protocol-relative, URL berisi `user:pass@`, spasi/karakter kontrol, dan URL yang mengarah ke domain shortener sendiri (cegah loop). Hostname tujuan selalu ditampilkan di dashboard.

> Validasi format tidak menjamin situs tujuan aman. Kontrol utama adalah: hanya anggota tim yang bisa membuat link, audit, dan kemampuan admin menonaktifkan link/akun.

## Alias
Huruf kecil, angka, `-`, `_` (2–64 karakter, diawali huruf/angka), unik, dan bukan alias terlarang (`login`, `logout`, `links`, `admin`, `api`, `health`, `vendor`, …) — lihat `config/shortlink.php`.

## Autentikasi & otorisasi
- Tidak ada pendaftaran publik; akun dibuat admin (`/admin/users`) atau lewat `php artisan app:create-admin`.
- Password minimal 10 karakter, di-hash bcrypt.
- Login dibatasi 5 percobaan gagal per email+IP per menit, dan 10 request/menit per IP.
- Akun nonaktif tidak bisa login, sesi aktifnya diakhiri pada request berikutnya, dan link miliknya berhenti mengalihkan.
- `ShortLinkPolicy`: anggota hanya bisa melihat/mengubah/menghapus/statistik link miliknya; admin semua link.
- Area `/admin/*` hanya untuk admin. Admin tidak bisa menonaktifkan atau menurunkan peran dirinya sendiri.
- CSRF aktif untuk semua form.

## Sesi & HTTP
- `SESSION_SECURE_COOKIE=true`, `SESSION_ENCRYPT=true`, cookie `HttpOnly`, `SameSite=Lax`.
- Proxy dipercaya (`trustProxies('*')`) karena container hanya dapat dijangkau dari jaringan Docker NPM.
- Header: `X-Frame-Options: DENY`, `X-Content-Type-Options: nosniff`, `Referrer-Policy`, `Permissions-Policy`. Redirect memakai `X-Robots-Tag: noindex` dan `Cache-Control: no-store`.
- Nginx menolak dotfile (`.env`, `.git`) dan semua `*.php` selain `index.php`.
- `APP_DEBUG=false` di produksi.

## Privasi statistik
Tidak menyimpan IP. Referer hanya hostname (query string/token dibuang). User-Agent hanya disimpan sebagai nama keluarga browser.

## Rate limit
- Redirect: 120 request/menit per IP (`SHORTLINK_REDIRECT_RATE_LIMIT`).
- Login: lihat di atas.

## Audit
Dicatat: login/logout, link dibuat/diubah/diaktifkan/dinonaktifkan/dihapus, akun dibuat/diubah/diaktifkan/dinonaktifkan. Metadata tidak memuat password.

## Menangani link yang disalahgunakan
Admin membuka link → **Nonaktifkan** (redirect langsung berhenti, data tetap untuk investigasi) → cek `/admin/audit` → bila perlu nonaktifkan akun pembuatnya.

## Jaringan
Port 3306 dan port aplikasi tidak dipublikasikan ke host. Jaringan `internal` tidak punya akses keluar.
