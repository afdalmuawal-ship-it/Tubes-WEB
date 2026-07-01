<?php

require_once 'session.php';
requireAdmin();

$user = getCurrentUser();

// Statistik
$totalUsers = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'")->fetch_assoc()['total'];
$totalHilang = $conn->query("SELECT COUNT(*) as total FROM barang_hilang")->fetch_assoc()['total'];
$totalTemuan = $conn->query("SELECT COUNT(*) as total FROM barang_temuan")->fetch_assoc()['total'];
$totalLaporan = $totalHilang + $totalTemuan;

$totalKembali = $conn->query("SELECT COUNT(*) as total FROM barang_hilang WHERE status = 'Dikembalikan'")->fetch_assoc()['total'];
$totalKembali += $conn->query("SELECT COUNT(*) as total FROM barang_temuan WHERE status = 'Dikembalikan'")->fetch_assoc()['total'];

$page_title = 'Admin Dashboard';
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/topbar.php';
?>

<div class="container-fluid dashboard-content">
    <div class="welcome-card animate-on-scroll">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h3>Selamat Datang, Admin <?= htmlspecialchars($user['nama']) ?>!</h3>
                <p>Kelola sistem Lostly melalui dashboard admin ini.</p>
            </div>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-4 mb-4">
        <div class="col animate-on-scroll animate-delay-1">
            <div class="stat-card" style="background: rgba(14, 165, 233, 0.1); border-color: rgba(14, 165, 233, 0.3); height: 100%;">
                <div class="stat-icon" style="color: #0EA5E9;">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-number"><?= $totalUsers ?></div>
                <div class="stat-label">Total Pengguna</div>
            </div>
        </div>

        <div class="col animate-on-scroll animate-delay-2">
            <div class="stat-card" style="background: rgba(245, 158, 11, 0.1); border-color: rgba(245, 158, 11, 0.3); height: 100%;">
                <div class="stat-icon" style="color: #F59E0B;">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div class="stat-number"><?= $totalLaporan ?></div>
                <div class="stat-label">Total Laporan</div>
            </div>
        </div>

        <div class="col animate-on-scroll animate-delay-3">
            <div class="stat-card" style="background: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.3); height: 100%;">
                <div class="stat-icon" style="color: #EF4444;">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="stat-number"><?= $totalHilang ?></div>
                <div class="stat-label">Barang Hilang</div>
            </div>
        </div>

        <div class="col animate-on-scroll animate-delay-4">
            <div class="stat-card green" style="height: 100%;">
                <div class="stat-icon">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="stat-number"><?= $totalTemuan ?></div>
                <div class="stat-label">Barang Temuan</div>
            </div>
        </div>

        <div class="col animate-on-scroll animate-delay-5">
            <div class="stat-card purple" style="height: 100%;">
                <div class="stat-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-number"><?= $totalKembali ?></div>
                <div class="stat-label">Dikembalikan</div>
            </div>
        </div>
    </div>
    
    <div class="row g-4 mb-4">
        <div class="col-12 text-center animate-on-scroll">
            <a href="admin_laporan.php" class="btn btn-outline-glass me-2">Kelola Laporan</a>
            <a href="admin_pengguna.php" class="btn btn-outline-glass me-2">Kelola Pengguna</a>
            <a href="admin_statistik.php" class="btn btn-outline-glass">Lihat Statistik</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
