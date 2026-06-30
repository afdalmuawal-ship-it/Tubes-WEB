<?php

// CONFIG.PHP - Konfigurasi Utama Website Lostly
// File ini berisi konstanta konfigurasi database
// dan pengaturan dasar website

// -- Konfigurasi Database --
define('DB_HOST', 'localhost'); 
define('DB_USER', 'root'); 
define('DB_PASS', ''); 
define('DB_NAME', 'lostly_db');

// -- Konfigurasi Website --
define('BASE_URL', 'http://localhost/Lostly/'); // Base URL website
define('SITE_NAME', 'Lostly');                  // Nama website
define('UPLOAD_DIR', __DIR__ . '/uploads/');     // Direktori upload file

// -- Konfigurasi Cookie --
define('COOKIE_EXPIRY', 60 * 60 * 24 * 7); // Cookie berlaku 7 hari (dalam detik)

// -- Kategori Barang --
define('KATEGORI_LIST', serialize([
    'Elektronik', 'Dokumen', 'Aksesoris', 'Pakaian',
    'Tas', 'Kunci', 'Buku', 'Lainnya'
]));
?>