<?php

// REGISTER.PHP - Halaman Registrasi User Baru
// Proses pendaftaran dengan validasi & hashing password

// Mulai session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['id_user'])) {
    header("Location: dashboard.php");
    exit();
}

require_once 'koneksi.php';
require_once 'config.php';

// -- Proses Registrasi --
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil dan sanitasi data form
    $nama = $conn->real_escape_string(trim($_POST['nama']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validasi: Semua field harus terisi
    if (empty($nama) || empty($email) || empty($password) || empty($confirm_password)) {
        header("Location: register.php?alert=register_failed&msg=" . urlencode("Semua field harus diisi"));
        exit();
    }

    // Validasi: Format email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: register.php?alert=register_failed&msg=" . urlencode("Format email tidak valid"));
        exit();
    }

    // Validasi: Password minimal 6 karakter
    if (strlen($password) < 6) {
        header("Location: register.php?alert=register_failed&msg=" . urlencode("Password minimal 6 karakter"));
        exit();
    }

    // Validasi: Password dan Konfirmasi harus cocok
    if ($password !== $confirm_password) {
        header("Location: register.php?alert=register_failed&msg=" . urlencode("Password dan konfirmasi tidak cocok"));
        exit();
    }

    // Cek apakah email sudah terdaftar
    $cekEmail = $conn->query("SELECT id_user FROM users WHERE email = '$email'");
    if ($cekEmail->num_rows > 0) {
        header("Location: register.php?alert=email_exists");
        exit();
    }

    // Hash password menggunakan password_hash (bcrypt)
    $password = $_POST['password'];

    // Insert data user baru ke database
    $query = "INSERT INTO users (nama, email, password, foto, role) 
              VALUES ('$nama', '$email', '$password', 'default.png', 'user')";

    if ($conn->query($query)) {
        // Registrasi berhasil
        header("Location: login.php?alert=register_success");
        exit();
    } else {
        // Registrasi gagal (error database)
        header("Location: register.php?alert=register_failed&msg=" . urlencode("Terjadi kesalahan sistem"));
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Daftar akun Lostly untuk mulai melaporkan dan mencari barang hilang di kampus.">
    <title>Daftar - Lostly</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="style/style.css" rel="stylesheet">
</head>
<body>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
        <div class="loading-text">Memuat...</div>
    </div>

    <section class="auth-section">
        <div class="auth-card">
            <!-- Logo -->
            <div class="auth-logo">
                <img src="img/logo.png" class="auth-logo-img" alt="Lostly Logo">
            </div>
            <h2 class="auth-title">Buat Akun</h2>
            <p class="auth-subtitle">Daftar untuk mulai menggunakan Lostly</p>

            <!-- Form Register -->
            <form method="POST" action="register.php" id="registerForm" data-loading>
                <!-- Nama Lengkap -->
                <div class="form-floating-custom">
                    <i class="bi bi-person input-icon"></i>
                    <input type="text" 
                           class="form-control" 
                           id="nama" 
                           name="nama" 
                           placeholder="Nama Lengkap" 
                           required>
                </div>

                <!-- Email -->
                <div class="form-floating-custom">
                    <i class="bi bi-envelope input-icon"></i>
                    <input type="email" 
                           class="form-control" 
                           id="email" 
                           name="email" 
                           placeholder="Email Anda" 
                           required>
                </div>

                <!-- Password -->
                <div class="form-floating-custom">
                    <i class="bi bi-lock input-icon"></i>
                    <input type="password" 
                           class="form-control" 
                           id="password" 
                           name="password" 
                           placeholder="Password (min. 6 karakter)" 
                           minlength="6"
                           required>
                    <button type="button" class="toggle-password">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>

                <!-- Konfirmasi Password -->
                <div class="form-floating-custom">
                    <i class="bi bi-lock-fill input-icon"></i>
                    <input type="password" 
                           class="form-control" 
                           id="confirm_password" 
                           name="confirm_password" 
                           placeholder="Konfirmasi Password" 
                           required>
                    <button type="button" class="toggle-password">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>

                <!-- Tombol Register -->
                <button type="submit" class="btn btn-gradient w-100 mb-3">
                    <i class="bi bi-person-plus"></i> Daftar Sekarang
                </button>
            </form>

            <!-- Link ke Login -->
            <div class="auth-footer">
                Sudah punya akun? <a href="login.php">Masuk</a>
            </div>

            <!-- Link kembali ke Home -->
            <div class="text-center mt-3">
                <a href="index.php" style="color: rgba(255,255,255,0.5); font-size:0.85rem;">
                    <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </section>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/script.js"></script>
</body>
</html>
