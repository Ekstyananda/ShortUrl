# Arsitektur

```
Pengunjung / Anggota ──HTTPS──▶ Nginx Proxy Manager (container lama, jaringan "proxy")
                                     │ http://shorturl-web:80
                                     ▼
                         shorturl-web (nginx)  ── jaringan "proxy" + "internal"
                                     │ FastCGI app:9000
                                     ▼
                         shorturl-app (PHP-FPM 8.4 / Laravel 12) ── "internal"
                                     │ 3306
                                     ▼
                         shorturl-db (MySQL 8.4, volume db_data) ── "internal"
```

- Jaringan `internal` bersifat `internal: true` (tanpa akses keluar/masuk dari luar Docker).
- Tidak ada container aplikasi yang memublikasikan port ke host.
- Satu `Dockerfile` multi-stage menghasilkan dua image: `app` (PHP-FPM + kode) dan `web` (nginx + `public/`).
- Saat start, entrypoint `app` menjalankan `config:cache`, `route:cache`, `view:cache`, lalu `migrate --force` (bisa dimatikan via `RUN_MIGRATIONS=false`).

## Alur redirect
`GET /{alias}` → alias dinormalisasi ke huruf kecil → cari `short_links` → 404 bila tidak ada / nonaktif / kedaluwarsa / pemilik nonaktif → catat `click_events` (hostname referer + keluarga browser) → `302` ke URL tujuan dengan `Cache-Control: no-store`.

Rute redirect didaftarkan **paling akhir** di `routes/web.php`, dan alias yang bentrok dengan rute aplikasi ditolak (`config/shortlink.php`).

## Struktur kode penting
| Lokasi | Isi |
|---|---|
| `routes/web.php` | Semua rute |
| `app/Http/Controllers/ShortLinkController.php` | CRUD link |
| `app/Http/Controllers/RedirectController.php` | Redirect publik + pencatatan klik |
| `app/Http/Controllers/AnalyticsController.php` | Statistik per link |
| `app/Http/Controllers/Admin/*` | Kelola pengguna & audit |
| `app/Http/Controllers/Auth/LoginController.php` | Login/logout + pembatasan percobaan |
| `app/Policies/ShortLinkPolicy.php` | Admin semua link; anggota hanya miliknya |
| `app/Rules/SafeUrl.php`, `AllowedAlias.php` | Validasi URL & alias |
| `app/Services/AuditLogger.php` | Pencatatan audit |
| `app/Console/Commands/CreateAdmin.php` | `php artisan app:create-admin` |
| `config/shortlink.php` | Alias terlarang, panjang alias acak, rate limit |

## Pengembangan lanjutan
- Bila trafik tinggi: pindahkan pencatatan klik ke queue atau tabel agregasi harian.
- Klik unik, QR code, negara (`country_code` sudah disiapkan), laporan penyalahgunaan.
