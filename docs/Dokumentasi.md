# Rancangan aplikasi short URL — `s.krayna.id`

Saya sarankan kita membangun layanan short URL internal untuk tim, mirip Bitly, dengan Laravel + Blade + Bootstrap 5 + MySQL, berjalan di Docker dan memakai Nginx Proxy Manager (NPM) yang sudah ada.

Prinsip utamanya: aplikasi dibuat sebagai layanan baru yang terisolasi, tidak mengganti container NPM, tidak mengambil alih port 80/443, dan tidak mengubah layanan lain di VPS.

## 1. Spesifikasi MVP

Akun dan tim

Login, logout, akun yang dibuat admin, peran admin dan anggota, serta pembatasan akses berdasarkan kepemilikan link.

Manajemen short URL

Buat alias khusus seperti `s.krayna.id/modul-sbd`, edit tujuan, aktifkan/nonaktifkan, dan hapus link.

Analitik

Total klik, grafik harian, referer, waktu klik, dan statistik per link. Data pribadi pengunjung diminimalkan.

Keamanan dan audit

Validasi URL, proteksi login, rate limit, audit aktivitas penting, dan kemampuan menonaktifkan link yang disalahgunakan.

### Hak akses pengguna

| Fitur                           | Admin      | Anggota      |
| ------------------------------- | ---------- | ------------ |
| Membuat short URL               | Ya         | Ya           |
| Mengelola link sendiri          | Ya         | Ya           |
| Mengelola semua link tim        | Ya         | Tidak        |
| Melihat statistik               | Semua link | Link sendiri |
| Membuat atau menonaktifkan akun | Ya         | Tidak        |
| Melihat audit aktivitas         | Ya         | Tidak        |

Keputusan MVP: setiap anggota hanya bisa mengelola link miliknya, sementara admin dapat mengelola seluruh link.

## 2. Skema database MySQL

Saya menyarankan empat tabel utama dan satu tabel opsional untuk audit. Gunakan foreign key, indeks, dan migration Laravel agar skema mudah dikembangkan.

`users`

Akun tim

`id`, `name`, `email`, `password`, `role`, `is_active`, `created_at`, `updated_at`

`short_links`

Data URL pendek

`id`, `user_id`, `alias`, `destination_url`, `title`, `is_active`, `expires_at`, `created_at`, `updated_at`

`click_events`

Satu catatan untuk setiap klik yang dipilih untuk dihitung

`id`, `short_link_id`, `clicked_at`, `referer_host`, `user_agent_family`, `country_code` (opsional)

`audit_logs`

Catatan tindakan penting

`id`, `actor_user_id`, `action`, `subject_type`, `subject_id`, `metadata`, `created_at`

### Detail relasi

- Satu pengguna dapat memiliki banyak short link: `users 1:N short_links`.
- Satu short link dapat memiliki banyak catatan klik: `short_links 1:N click_events`.
- Pengguna dapat menghasilkan banyak catatan audit: `users 1:N audit_logs`.
- Gunakan `UNIQUE(alias)` pada `short_links` agar alias tidak bentrok.
- Gunakan indeks pada `short_links.user_id`, `click_events(short_link_id, clicked_at)`, dan `audit_logs(actor_user_id, created_at)`.

Catatan desain: `role` dapat berupa `admin` atau `member`. Simpan password dalam bentuk hash, bukan teks biasa. Untuk statistik MVP, simpan hostname referer saja, bukan URL referer lengkap yang mungkin berisi token atau informasi sensitif.

Jika ingin menghitung klik unik, itu sebaiknya fitur lanjutan. Jangan menganggap setiap request sebagai pengunjung unik.

## 3. Alur pembuatan dan redirect

Anggota login

Membuka dashboard dan mengisi URL tujuan

Validasi Laravel

Cek format URL, protokol, alias, dan izin pengguna

MySQL

Simpan alias dan URL tujuan

Pengunjung membuka `/modul-sbd`

Cari alias → cek status → catat klik → redirect ke tujuan

Untuk MVP, gunakan HTTP 302 agar redirect sementara tidak terlalu lama tersimpan di cache browser. Ini juga memudahkan perubahan tujuan URL di kemudian hari.

Rute yang disarankan:

| Method             | Rute                    | Fungsi                       |
| ------------------ | ----------------------- | ---------------------------- |
| `GET`              | `/`                     | Dashboard atau halaman login |
| `GET`              | `/links`                | Daftar link pengguna         |
| `GET/POST`         | `/links/create`         | Form dan pembuatan link      |
| `GET/PATCH/DELETE` | `/links/{id}`           | Lihat, ubah, dan hapus link  |
| `GET`              | `/links/{id}/analytics` | Statistik link               |
| `GET`              | `/{alias}`              | Redirect publik              |

Rute `/{alias}` harus menjadi fallback yang tidak mengambil alih rute dashboard. Hindari alias yang bentrok dengan `login`, `logout`, `links`, `admin`, `api`, dan `health`.

## 4. Keamanan redirect dan aplikasi

Short URL bisa disalahgunakan untuk menyamarkan tujuan berbahaya. Karena itu, keamanan harus menjadi bagian dari MVP.

Validasi tujuan URL

Terima hanya URL absolut dengan protokol `https` atau `http`. Tolak `javascript:`, `data:`, `file:`, URL tanpa hostname, dan input yang bukan URL valid.

Jangan membuat open redirect tanpa kontrol

Redirect hanya ke URL tujuan yang sudah tersimpan dan disetujui saat link dibuat atau diedit. Tampilkan hostname tujuan di dashboard agar anggota dapat memeriksanya.

Autentikasi dan otorisasi

Gunakan middleware `auth`, policy untuk setiap link, validasi server-side, CSRF protection, dan pembatasan percobaan login. Laravel menyediakan fasilitas autentikasi dan rate limiting yang dapat digunakan untuk ini.&#x20;

[image](https://www.google.com/s2/favicons?domain=https://laravel.com\&sz=32)

Laravel

+1



Perlindungan statistik

Batasi ukuran `User-Agent` dan referer, jangan simpan IP mentah secara default, serta berikan rate limit pada endpoint redirect dan login. Siapkan cara untuk menonaktifkan link yang dilaporkan.

Amankan akun tim

Pendaftaran publik dimatikan. Admin membuat akun anggota dan menonaktifkan akun yang tidak lagi diperlukan. Gunakan session cookie yang aman melalui HTTPS.

Satu batasan penting: validasi format URL saja tidak menjamin situs tujuan aman. Jika layanan hanya untuk tim, kontrol akses, pembatasan pembuatan link, dan mekanisme laporan merupakan langkah awal yang lebih masuk akal daripada langsung membuka pendaftaran ke publik.

## 5. Rencana deployment di VPS

Domain publik yang akan digunakan adalah `s.krayna.id`. Aplikasi berjalan sebagai layanan Docker baru dan memakai NPM yang sudah tersedia.

Pengunjung / Anggota Tim

`https://s.krayna.id`

Nginx Proxy Manager yang sudah ada

TLS/HTTPS dan routing domain

Web server + Laravel

Container aplikasi baru, tanpa port publik tersendiri

MySQL

Database khusus aplikasi dan akun database tersendiri

### Struktur proyek

Saya sarankan menaruh berkas proyek di direktori aplikasi VPS, misalnya:

```
/DATA/AppData/Project/shorturl/
├── app/                 # Source Laravel
├── docker/
│   ├── nginx/
│   └── php/
├── docker-compose.yml
├── .env                 # Rahasia, jangan masuk Git
├── .env.example
├── Dockerfile
├── docs/
│   ├── ARCHITECTURE.md
│   ├── DATABASE.md
│   ├── SECURITY.md
│   └── DEPLOYMENT.md
└── README.md
```

### Tahapan implementasi

1. Persiapan dan isolasi

   Periksa jaringan Docker NPM, ketersediaan MySQL, dan kapasitas penyimpanan. Buat direktori aplikasi dan jaringan khusus jika diperlukan. Jangan mengubah atau membuat ulang container NPM.
2. Bangun aplikasi Laravel

   Implementasikan autentikasi, manajemen pengguna, CRUD short link, custom alias, redirect, statistik klik, dan audit. Gunakan migration dan seeder untuk data awal.
3. Siapkan Docker

   Buat layanan web server dan PHP untuk Laravel. Gunakan jaringan Docker bersama NPM untuk layanan web, serta jaringan internal terpisah untuk komunikasi aplikasi dengan MySQL. Jangan memublikasikan port aplikasi atau database ke host kecuali memang diperlukan.
4. Hubungkan domain

   Buat record DNS `A` untuk `s.krayna.id` menuju IP VPS. Tambahkan Proxy Host di NPM dengan tujuan hostname container web dan port internalnya. Terbitkan sertifikat HTTPS.
5. Migrasi dan verifikasi

   Jalankan migration, buat akun admin pertama melalui mekanisme yang aman, lalu uji login, kepemilikan link, redirect, statistik, dan HTTPS.
6. Backup dan pemeliharaan

   Jadwalkan backup database serta konfigurasi penting, simpan salinan di luar VPS, dan dokumentasikan prosedur restore sebelum layanan digunakan tim.

Catatan MySQL: jangan langsung menghubungkan aplikasi ke container database yang ada sebelum memeriksa jaringan dan konfigurasi aksesnya. Jika database yang tersedia dapat digunakan dengan aman, buat database dan user khusus untuk aplikasi ini. Jangan mengekspos port `3306` ke internet.

## 6. Strategi statistik agar aplikasi tetap ringan

Untuk versi awal, setiap klik yang memenuhi kriteria akan mencatat satu event ke `click_events`. Grafik harian dapat dihitung dengan mengelompokkan data berdasarkan tanggal dan ID link.

Namun, pencatatan setiap klik secara sinkron dapat menambah beban database. Maka:

- Indeks database disiapkan sejak awal.
- Referer disimpan sebagai hostname, bukan URL lengkap.
- Statistik hanya dapat diakses oleh pemilik link dan admin sesuai hak akses.
- Jika trafik meningkat, pencatatan dapat dipindahkan ke queue atau tabel agregasi harian.

Untuk tahap MVP, belum perlu menambahkan Redis atau layanan analitik eksternal jika volume trafik masih rendah.

## 7. Kriteria selesai (acceptance criteria)

Sebelum dianggap siap digunakan, pastikan:

- `https://s.krayna.id` dapat diakses dengan sertifikat valid.
- NPM dan seluruh aplikasi lama tetap berjalan.
- Anggota dapat login dan membuat custom alias.
- Alias duplikat ditolak.
- Anggota tidak bisa mengedit atau melihat statistik link milik anggota lain.
- Redirect menuju tujuan yang benar dan link nonaktif tidak melakukan redirect.
- Statistik klik, grafik harian, dan referer tampil dengan benar.
- URL berbahaya dan input tidak valid ditolak.
- Port database tidak terbuka untuk akses publik.
- Backup database berhasil dibuat dan prosedur restore telah diuji.

## 8. Keputusan teknis yang saya rekomendasikan

| Aspek                | Keputusan MVP                              |
| -------------------- | ------------------------------------------ |
| Framework            | Laravel + Blade                            |
| UI                   | Bootstrap 5, desain responsif              |
| Database             | MySQL, database khusus                     |
| Autentikasi          | Admin dan anggota tim                      |
| Alias                | Custom alias unik                          |
| Redirect             | HTTP 302                                   |
| Statistik            | Klik harian dan referer                    |
| Analitik             | Tanpa penyimpanan IP mentah secara default |
| Deployment           | Docker Compose                             |
| HTTPS                | NPM yang sudah ada                         |
| Domain               | `s.krayna.id`                              |
| Port publik aplikasi | Tidak diperlukan                           |
| Pendaftaran publik   | Nonaktif                                   |
| Backup               | Database dan konfigurasi penting           |

Dengan rancangan ini, kamu mendapatkan fondasi yang cukup untuk shortener internal tanpa menambah banyak komponen infrastruktur. Laravel menyediakan fondasi autentikasi dan rate limiting, sedangkan kebutuhan tim, statistik, dan keamanan redirect dibangun sebagai fitur aplikasi.&#x20;

[image](https://www.google.com/s2/favicons?domain=https://laravel.com\&sz=32)

Laravel

+2



Langkah implementasi berikutnya adalah membuat spesifikasi teknis final dan Docker Compose yang sesuai dengan kondisi VPS aktual. Sebelum menulis konfigurasi deployment, periksa dulu nama container MySQL, jaringan Docker yang tersedia, dan struktur Compose NPM agar tidak mengganggu layanan yang sudah aktif.