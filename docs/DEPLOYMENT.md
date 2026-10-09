# Deployment

Lokasi: `/DATA/AppData/Bitly`. Stack Compose bernama `shorturl` (container `shorturl-web`, `shorturl-app`, `shorturl-db`). NPM (`nginxproxymanager`) **tidak diubah**; aplikasi hanya bergabung ke jaringan Docker `proxy` yang sudah ada.

## 1. Konfigurasi
```bash
cp .env.example .env
chmod 600 .env
```
Isi nilai acak:
```bash
echo "APP_KEY=base64:$(openssl rand -base64 32)"
```
```bash
openssl rand -hex 24
```
(gunakan untuk `DB_PASSWORD` dan `DB_ROOT_PASSWORD`). `DB_*` hanya dibaca MySQL saat volume `db_data` pertama kali dibuat.

## 2. Build & jalankan
```bash
docker compose up -d --build
```
```bash
docker compose ps
```
Migration dijalankan otomatis oleh entrypoint `app`.

## 3. Akun admin pertama
```bash
docker compose exec app php artisan app:create-admin
```

## 4. DNS & Nginx Proxy Manager
1. DNS: record **A** `s` (`s.krayna.id`) → IP publik VPS.
2. Buka UI NPM (port 81) → **Hosts → Proxy Hosts → Add Proxy Host**:
   - Domain Names: `s.krayna.id`
   - Scheme: `http`, Forward Hostname/IP: `shorturl-web`, Forward Port: `80`
   - Aktifkan **Block Common Exploits**
3. Tab **SSL**: Request a new Let's Encrypt certificate, aktifkan **Force SSL** dan **HTTP/2 Support** (HSTS opsional setelah semua berjalan).
4. Uji: buka `https://s.krayna.id/health` → `{"status":"ok","database":"ok"}`, lalu login.

## 5. Update aplikasi
```bash
docker compose up -d --build
```
Cache konfigurasi/rute/view dibangun ulang otomatis saat container start.

## 6. Backup
`scripts/backup.sh` membuat `backups/db-YYYYmmdd-HHMMSS.sql.gz` (diverifikasi utuh) dan salinan `.env`, menghapus file lebih tua dari 14 hari (`RETENTION_DAYS`).

Pasang cron harian (`crontab -e`):
```
30 2 * * * /DATA/AppData/Bitly/scripts/backup.sh >> /DATA/AppData/Bitly/backups/backup.log 2>&1
```
**Salin juga ke luar VPS** (mis. `rclone copy /DATA/AppData/Bitly/backups remote:shorturl-backups` atau `rsync` ke server lain). File `env-*` berisi rahasia — simpan di tempat terenkripsi.

## 7. Restore
```bash
docker compose exec -T db sh -c 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" -e "DROP DATABASE IF EXISTS shorturl; CREATE DATABASE shorturl; GRANT ALL ON shorturl.* TO \"shorturl\"@\"%\";"'
```
```bash
zcat backups/db-YYYYmmdd-HHMMSS.sql.gz | docker compose exec -T db sh -c 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" shorturl'
```
```bash
docker compose restart app
```
Jika memulihkan ke server baru, kembalikan juga `.env` dari backup (APP_KEY diperlukan untuk sesi terenkripsi).

## 8. Perintah berguna
```bash
docker compose logs -f app web
```
```bash
docker compose exec app php artisan tinker
```

## Checklist penerimaan
- [ ] `https://s.krayna.id` dengan sertifikat valid
- [ ] NPM & aplikasi lain tetap berjalan (`docker ps`)
- [ ] Login anggota, buat alias khusus, alias duplikat ditolak
- [ ] Anggota tidak bisa melihat/mengubah link anggota lain
- [ ] Redirect benar; link nonaktif → 404
- [ ] Statistik, grafik harian, referer tampil
- [ ] URL berbahaya ditolak
- [ ] `docker ps` tidak menunjukkan port 3306/aplikasi yang dipublikasikan
- [ ] Backup dibuat dan restore diuji
