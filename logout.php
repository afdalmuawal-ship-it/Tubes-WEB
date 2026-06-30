<?php

// Mulai session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hapus semua data session
$_SESSION = array();

// Hancurkan session
session_destroy();

// Hapus cookie remember me
if (isset($_COOKIE['remember_email'])) {
    setcookie('remember_email', '', time() - 3600, '/');
}

// Redirect ke halaman login dengan pesan alert
header("Location: login.php?alert=logout_success");
exit();
?>
