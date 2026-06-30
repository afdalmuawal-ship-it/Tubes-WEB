<?php

// SESSION.PHP - Manajemen Session & Auth Guard
// File ini memastikan hanya user yang sudah login
// yang dapat mengakses halaman protected

// -- Mulai session jika belum dimulai --
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include konfigurasi
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/koneksi.php';

// -- Cek Cookie Remember Me --
// Jika user belum login tapi ada cookie, otomatis login
if (!isset($_SESSION['id_user']) && isset($_COOKIE['remember_email'])) {
    $email = $conn->real_escape_string($_COOKIE['remember_email']);
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Set session dari data cookie
        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['foto'] = $user['foto'];
        $_SESSION['role'] = $user['role'];
    }
}

// -- Auth Guard: Redirect ke login jika belum login --
// Gunakan fungsi ini di halaman yang membutuhkan login
function requireLogin() {
    if (!isset($_SESSION['id_user'])) {
        // Simpan URL tujuan agar bisa redirect setelah login
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header("Location: login.php?alert=needlogin");
        exit();
    }
}

// -- Cek apakah user sudah login --
function isLoggedIn() {
    return isset($_SESSION['id_user']);
}

// -- Ambil data user yang sedang login --
function getCurrentUser() {
    if (isset($_SESSION['id_user'])) {
        return [
            'id_user' => $_SESSION['id_user'],
            'nama'    => $_SESSION['nama'],
            'email'   => $_SESSION['email'],
            'foto'    => $_SESSION['foto'],
            'role'    => $_SESSION['role']
        ];
    }
    return null;
}
?>
