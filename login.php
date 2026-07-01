<?php

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

// -- Cek cookie Remember Me untuk pre-fill email --
$remembered_email = isset($_COOKIE['remember_email']) ? $_COOKIE['remember_email'] : '';

// -- Proses Login --
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = trim($_POST['password']);
    $remember = isset($_POST['remember']) ? true : false;

    // Validasi input tidak kosong
    if (empty($email) || empty($password)) {
        header("Location: login.php?alert=login_failed&msg=" . urlencode("Email dan password harus diisi"));
        exit();
    }

    // Query cek user berdasarkan email
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifikasi password dengan password_verify (jika di awal demo menggunakan hash biasa, pakai yang sesuai)
        if($password == $user['password']) {
            // Cek jika akun dinonaktifkan
            if (isset($user['status']) && $user['status'] == 'Nonaktif') {
                header("Location: login.php?alert=disabled");
                exit();
            }

            // -- Login Berhasil --

            // Set Session
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['foto'] = $user['foto'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['status'] = $user['status'] ?? 'Aktif';

            // Set Cookie jika Remember Me dicentang
            if ($remember) {
                setcookie('remember_email', $email, time() + COOKIE_EXPIRY, '/');
            } else {
                // Hapus cookie jika tidak dicentang
                setcookie('remember_email', '', time() - 3600, '/');
            }

            // Catat aktivitas login
            $id_user = $user['id_user'];
            $conn->query("INSERT INTO aktivitas (id_user, aksi) VALUES ($id_user, 'User login')");

            // Redirect sesuai role dengan alert sukses
            if ($user['role'] == 'admin') {
                header("Location: admin_dashboard.php?alert=login_success");
            } else {
                header("Location: dashboard.php?alert=login_success");
            }
            exit();
        } else {
            // Password salah
            header("Location: login.php?alert=login_failed");
            exit();
        }
    } else {
        // Email tidak ditemukan
        header("Location: login.php?alert=login_failed");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login ke akun Lostly Anda untuk mengelola laporan barang hilang dan temuan.">
    <title>Login - Lostly</title>

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

    <!-- LOGIN SECTION -->
    <section class="auth-section">
        <div class="auth-card">
            <!-- Logo -->
            <div class="auth-logo">
                <i class="bi bi-search-heart"></i>
            </div>
            <h2 class="auth-title">Selamat Datang</h2>
            <p class="auth-subtitle">Masuk ke akun Lostly Anda</p>

            <!-- Form Login -->
            <form method="POST" action="login.php" id="loginForm" data-loading>
                <!-- Email -->
                <div class="form-floating-custom">
                    <i class="bi bi-envelope input-icon"></i>
                    <input type="email" 
                           class="form-control" 
                           id="email" 
                           name="email" 
                           placeholder="Email Anda" 
                           value="<?= htmlspecialchars($remembered_email) ?>"
                           required>
                </div>

                <!-- Password -->
                <div class="form-floating-custom">
                    <i class="bi bi-lock input-icon"></i>
                    <input type="password" 
                           class="form-control" 
                           id="password" 
                           name="password" 
                           placeholder="Password" 
                           required>
                    <button type="button" class="toggle-password">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>

                <!-- Remember Me -->
                <div class="form-check-custom">
                    <input type="checkbox" 
                           class="form-check-input" 
                           id="remember" 
                           name="remember"
                           <?= $remembered_email ? 'checked' : '' ?>>
                    <label class="form-check-label" for="remember">
                        Ingat Saya 
                    </label>
                </div>

                <!-- Tombol Login -->
                <button type="submit" class="btn btn-gradient w-100 mb-3">
                    <i class="bi bi-box-arrow-in-right"></i> Masuk
                </button>
            </form>

            <!-- Link ke Register -->
            <div class="auth-footer">
                Belum punya akun? <a href="register.php">Daftar Sekarang</a>
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
