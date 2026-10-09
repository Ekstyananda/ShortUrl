<?php

return [
    /*
     * Alias yang tidak boleh dipakai karena bentrok dengan rute aplikasi
     * atau berkas publik. Pengecekan bersifat case-insensitive.
     */
    'reserved_aliases' => [
        'login', 'logout', 'links', 'admin', 'api', 'health', 'up',
        'storage', 'build', 'assets', 'css', 'js', 'img', 'vendor',
        'favicon.ico', 'robots.txt', 'index.php', 'dashboard', 'settings',
        'contact', 'kontak', 'messages',
    ],

    // Panjang alias acak bila pengguna tidak mengisi alias.
    'random_alias_length' => (int) env('SHORTLINK_RANDOM_ALIAS_LENGTH', 6),

    // Jumlah redirect maksimum per menit per IP.
    'redirect_rate_limit' => (int) env('SHORTLINK_REDIRECT_RATE_LIMIT', 120),

    // Tautan kontak langsung tambahan di bagian "Hubungi kami" (mis. https://wa.me/62xxx atau mailto:).
    // Form kontak selalu tampil; kosongkan untuk menyembunyikan tombol tambahan ini.
    'contact_url' => env('SHORTLINK_CONTACT_URL'),

    // Rentang hari pada grafik klik harian.
    'analytics_days' => 30,
];
