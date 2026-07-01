<?php
require_once __DIR__ . '/../koneksi.php';

echo "Memulai migrasi database...\n";

// 1. Tambah kolom status di users
$sql = "ALTER TABLE users ADD COLUMN IF NOT EXISTS status ENUM('Aktif', 'Nonaktif') DEFAULT 'Aktif'";
if ($conn->query($sql)) {
    echo "1. Kolom status berhasil ditambahkan ke tabel users.\n";
} else {
    echo "Error 1: " . $conn->error . "\n";
}

// 2. Buat tabel kategori
$sql = "CREATE TABLE IF NOT EXISTS kategori (
    id_kategori INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
if ($conn->query($sql)) {
    echo "2. Tabel kategori berhasil dibuat.\n";
    
    // Insert default data jika kosong
    $result = $conn->query("SELECT COUNT(*) as total FROM kategori");
    if ($result && $result->fetch_assoc()['total'] == 0) {
        $kategori_defaults = ['Elektronik', 'Dokumen', 'Aksesoris', 'Pakaian', 'Tas', 'Kunci', 'Buku', 'Lainnya'];
        $values = [];
        foreach ($kategori_defaults as $k) {
            $values[] = "('" . $conn->real_escape_string($k) . "')";
        }
        $conn->query("INSERT IGNORE INTO kategori (nama_kategori) VALUES " . implode(',', $values));
        echo "   Data awal kategori ditambahkan.\n";
    }
} else {
    echo "Error 2: " . $conn->error . "\n";
}

// 3. Update barang_hilang
$sql = "ALTER TABLE barang_hilang MODIFY COLUMN kategori VARCHAR(100) NOT NULL";
if ($conn->query($sql)) {
    echo "3a. Kolom kategori di barang_hilang diubah menjadi VARCHAR.\n";
} else {
    echo "Error 3a: " . $conn->error . "\n";
}

$sql = "ALTER TABLE barang_hilang ADD COLUMN IF NOT EXISTS status_verifikasi ENUM('Menunggu Verifikasi', 'Disetujui', 'Ditolak') DEFAULT 'Menunggu Verifikasi'";
if ($conn->query($sql)) {
    echo "3b. Kolom status_verifikasi ditambahkan ke barang_hilang.\n";
    // Set existing records to 'Disetujui'
    $conn->query("UPDATE barang_hilang SET status_verifikasi = 'Disetujui' WHERE status_verifikasi = 'Menunggu Verifikasi'");
} else {
    echo "Error 3b: " . $conn->error . "\n";
}

// 4. Update barang_temuan
$sql = "ALTER TABLE barang_temuan MODIFY COLUMN kategori VARCHAR(100) NOT NULL";
if ($conn->query($sql)) {
    echo "4a. Kolom kategori di barang_temuan diubah menjadi VARCHAR.\n";
} else {
    echo "Error 4a: " . $conn->error . "\n";
}

$sql = "ALTER TABLE barang_temuan ADD COLUMN IF NOT EXISTS status_verifikasi ENUM('Menunggu Verifikasi', 'Disetujui', 'Ditolak') DEFAULT 'Menunggu Verifikasi'";
if ($conn->query($sql)) {
    echo "4b. Kolom status_verifikasi ditambahkan ke barang_temuan.\n";
    // Set existing records to 'Disetujui'
    $conn->query("UPDATE barang_temuan SET status_verifikasi = 'Disetujui' WHERE status_verifikasi = 'Menunggu Verifikasi'");
} else {
    echo "Error 4b: " . $conn->error . "\n";
}

// 5. Buat tabel aktivitas
$sql = "CREATE TABLE IF NOT EXISTS aktivitas (
    id_aktivitas INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    aksi VARCHAR(255) NOT NULL,
    waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
if ($conn->query($sql)) {
    echo "5. Tabel aktivitas berhasil dibuat.\n";
} else {
    echo "Error 5: " . $conn->error . "\n";
}

echo "Migrasi selesai.\n";
?>
