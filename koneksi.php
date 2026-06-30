<?php

// Include konfigurasi
require_once __DIR__ . '/config.php';

// -- Membuat koneksi ke database MySQL --
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// -- Cek koneksi berhasil atau gagal --
if ($conn->connect_error) {
    // Tampilkan pesan error jika gagal konek
    die("Koneksi database gagal: " . $conn->connect_error);
}

// -- Set charset ke utf8mb4 untuk mendukung emoji dan karakter khusus --
$conn->set_charset("utf8mb4");
?>
