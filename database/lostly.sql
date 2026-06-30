
-- DATABASE: Lostly - Sistem Pelaporan Barang Hilang & Temuan
-- Dibuat untuk lingkungan kampus


-- Buat database
CREATE DATABASE IF NOT EXISTS lostly_db;
USE lostly_db;

-- TABEL: users
-- Menyimpan data pengguna yang terdaftar
CREATE TABLE IF NOT EXISTS users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    foto VARCHAR(255) DEFAULT 'default.png',
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- TABEL: barang_hilang
-- Menyimpan laporan barang yang hilang
CREATE TABLE IF NOT EXISTS barang_hilang (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- TABEL: barang_temuan
-- Menyimpan laporan barang yang ditemukan
CREATE TABLE IF NOT EXISTS barang_temuan (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- DATA SAMPLE: User demo
-- Password: password123 (hashed dengan password_hash)
INSERT INTO users (nama, email, password, foto, role) VALUES
('Admin Lostly', 'admin@lostly.com', 'admin123', 'default.png', 'admin'),
('Budi Santoso', 'budi@student.ac.id', 'user123', 'default.png', 'user'),
('Sari Dewi', 'sari@student.ac.id', 'sari12', 'default.png', 'user');
-- DATA SAMPLE: Barang Hilang
INSERT INTO barang_hilang (id_user, nama_barang, kategori, lokasi, tanggal_hilang, deskripsi, foto, status) VALUES
(2, 'Laptop ASUS VivoBook', 'Elektronik', 'Perpustakaan Lantai 2', '2026-06-25', 'Laptop warna silver, stiker kucing di bagian belakang. Terakhir terlihat di meja baca pojok kanan.', 'no-image.png', 'Hilang'),
(2, 'Dompet Kulit Coklat', 'Aksesoris', 'Kantin Utama', '2026-06-27', 'Dompet kulit warna coklat tua merk Hush Puppies. Berisi KTM dan kartu ATM.', 'no-image.png', 'Hilang'),
(3, 'Kunci Motor Honda', 'Kunci', 'Gedung Fakultas Teknik', '2026-06-28', 'Kunci motor Honda Beat warna hitam dengan gantungan kunci berbentuk bintang.', 'no-image.png', 'Ditemukan');

-- DATA SAMPLE: Barang Temuan
INSERT INTO barang_temuan (id_user, nama_barang, kategori, lokasi, tanggal_temuan, deskripsi, foto, status) VALUES
(3, 'Earphone Bluetooth', 'Elektronik', 'Ruang Kelas A301', '2026-06-26', 'Earphone bluetooth warna putih ditemukan di bawah meja baris ketiga.', 'no-image.png', 'Disimpan'),
(2, 'Buku Kalkulus Jilid 2', 'Buku', 'Taman Kampus', '2026-06-28', 'Buku Kalkulus karangan Purcell, ada nama pemilik di halaman pertama tapi kurang jelas.', 'no-image.png', 'Disimpan'),
(3, 'Jaket Denim Biru', 'Pakaian', 'Aula Serbaguna', '2026-06-29', 'Jaket denim warna biru tua ukuran L, ditemukan di kursi baris belakang setelah acara seminar.', 'no-image.png', 'Diklaim');
