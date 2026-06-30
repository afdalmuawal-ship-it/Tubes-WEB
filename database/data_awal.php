<?php
$conn = mysqli_connect("localhost", "root", "", "lostly_db");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$sql = "INSERT INTO users (nama, email, password, foto, role) VALUES
('Admin Lostly', 'admin@lostly.com', 'admin123', 'default.png', 'admin'),
('Budi Santoso', 'budi@student.ac.id', 'user123', 'default.png', 'user'),
('Sari Dewi', 'sari@student.ac.id', 'sari12', 'default.png', 'user'),
('Afdal', 'afdal@gmail.com', '123456', 'default.png', 'user')";

if (mysqli_query($conn, $sql)) {
    echo "4 data pengguna berhasil ditambahkan";
} else {
    echo "Error: " . mysqli_error($conn);
}

$sql = "INSERT INTO barang_hilang (id_user, nama_barang, kategori, lokasi, tanggal_hilang, deskripsi, foto, status) VALUES
(2, 'Laptop ASUS VivoBook', 'Elektronik', 'Perpustakaan Lantai 2', '2026-06-25', 'Laptop warna silver, stiker kucing di bagian belakang. Terakhir terlihat di meja baca pojok kanan.', 'no-image.png', 'Hilang'),
(2, 'Dompet Kulit Coklat', 'Aksesoris', 'Kantin Utama', '2026-06-27', 'Dompet kulit warna coklat tua merk Hush Puppies. Berisi KTM dan kartu ATM.', 'no-image.png', 'Hilang'),
(3, 'Kunci Motor Honda', 'Kunci', 'Gedung Fakultas Teknik', '2026-06-28', 'Kunci motor Honda Beat warna hitam dengan gantungan kunci berbentuk bintang.', 'no-image.png', 'Ditemukan')";

if (mysqli_query($conn, $sql)) {
    echo "3 data barang hilang berhasil ditambahkan";
} else {
    echo "Error: " . mysqli_error($conn);
}

$sql = "INSERT INTO barang_temuan (id_user, nama_barang, kategori, lokasi, tanggal_temuan, deskripsi, foto, status) VALUES
(3, 'Earphone Bluetooth', 'Elektronik', 'Ruang Kelas A301', '2026-06-26', 'Earphone bluetooth warna putih ditemukan di bawah meja baris ketiga.', 'no-image.png', 'Disimpan'),
(2, 'Buku Kalkulus Jilid 2', 'Buku', 'Taman Kampus', '2026-06-28', 'Buku Kalkulus karangan Purcell, ada nama pemilik di halaman pertama tapi kurang jelas.', 'no-image.png', 'Disimpan'),
(3, 'Jaket Denim Biru', 'Pakaian', 'Aula Serbaguna', '2026-06-29', 'Jaket denim warna biru tua ukuran L, ditemukan di kursi baris belakang setelah acara seminar.', 'no-image.png', 'Diklaim')";

if (mysqli_query($conn, $sql)) {
    echo "3 data barang temuan berhasil ditambahkan";
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>