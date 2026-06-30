<?php
$conn = mysqli_connect("localhost", "root", "", "lostly_db");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$sql = "CREATE TABLE IF NOT EXISTS users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    foto VARCHAR(255) DEFAULT 'default.png',
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if (mysqli_query($conn, $sql)) {
    echo "Tabel users berhasil dibuat";
} else {
    echo "Error: " . mysqli_error($conn);
}

$sql = "CREATE TABLE IF NOT EXISTS barang_hilang (
    id_barang INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    nama_barang VARCHAR(150) NOT NULL,
    kategori ENUM('Elektronik', 'Dokumen', 'Aksesoris', 'Pakaian', 'Tas', 'Kunci', 'Buku', 'Lainnya') NOT NULL,
    lokasi VARCHAR(200) NOT NULL,
    tanggal_hilang DATE NOT NULL,
    deskripsi TEXT,
    foto VARCHAR(255) DEFAULT 'no-image.png',
    status ENUM('Hilang', 'Ditemukan', 'Dikembalikan') DEFAULT 'Hilang',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if (mysqli_query($conn, $sql)) {
    echo "Tabel barang_hilang berhasil dibuat";
} else {
    echo "Error: " . mysqli_error($conn);
}

$sql = "CREATE TABLE IF NOT EXISTS barang_temuan (
    id_temuan INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    nama_barang VARCHAR(150) NOT NULL,
    kategori ENUM('Elektronik', 'Dokumen', 'Aksesoris', 'Pakaian', 'Tas', 'Kunci', 'Buku', 'Lainnya') NOT NULL,
    lokasi VARCHAR(200) NOT NULL,
    tanggal_temuan DATE NOT NULL,
    deskripsi TEXT,
    foto VARCHAR(255) DEFAULT 'no-image.png',
    status ENUM('Disimpan', 'Diklaim', 'Dikembalikan') DEFAULT 'Disimpan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if (mysqli_query($conn, $sql)) {
    echo "Tabel barang_temuan berhasil dibuat";
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>