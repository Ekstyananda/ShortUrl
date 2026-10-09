# Krayna Short URL — `s.krayna.id`

Layanan short URL internal tim (mirip Bitly): Laravel 12 + Blade + Bootstrap 5 + MySQL 8.4, berjalan di Docker Compose di belakang Nginx Proxy Manager (NPM) yang sudah ada.

## Fitur
- Login akun tim (tanpa pendaftaran publik), peran **admin** & **anggota**.
- Buat short URL dengan alias khusus (`s.krayna.id/modul-sbd`) atau alias acak, edit tujuan, aktif/nonaktif, kedaluwarsa, hapus.
- Statistik per link: total klik, grafik harian 30 hari, referer (hostname saja), browser, klik terbaru.
- Admin: kelola semua link, kelola akun (buat/ubah/nonaktifkan), lihat audit aktivitas.
- Keamanan: validasi URL (hanya http/https), alias terlarang, rate limit login & redirect, CSRF, cookie aman, tanpa penyimpanan IP.

## Mulai cepat
```bash
cp .env.example .env            # isi APP_KEY, DB_PASSWORD, DB_ROOT_PASSWORD
docker compose up -d --build    # migration jalan otomatis
docker compose exec app php artisan app:create-admin
```
Lalu tambahkan Proxy Host di NPM: `s.krayna.id` → `shorturl-web` port `80` (lihat [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md)).

## Menjalankan test
```bash
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php artisan test
```
(Butuh `vendor/` dengan dependensi dev: `docker run --rm -v "$PWD":/app -w /app composer:2 install`.)

## Dokumentasi
- [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) — komponen, alur, struktur kode
- [docs/DATABASE.md](docs/DATABASE.md) — skema tabel
- [docs/SECURITY.md](docs/SECURITY.md) — kontrol keamanan
- [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md) — deploy, NPM, backup & restore
- [docs/Dokumentasi.md](docs/Dokumentasi.md) — rancangan awal
