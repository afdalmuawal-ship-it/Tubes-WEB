<?php
// INCLUDES/SIDEBAR.PHP
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="dashboard.php" class="sidebar-brand">
            <i class="bi bi-search-heart"></i> Lostly
        </a>
    </div>
    
    <div class="sidebar-nav">
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <!-- MENU ADMIN -->
            <div class="sidebar-nav-title">Menu Admin</div>
            <a href="admin_dashboard.php" class="sidebar-link <?= $current_page == 'admin_dashboard.php' ? 'active' : '' ?>">
                <i class="bi bi-grid"></i> Dashboard
            </a>
            <a href="admin_laporan.php" class="sidebar-link <?= $current_page == 'admin_laporan.php' ? 'active' : '' ?>">
                <i class="bi bi-card-list"></i> Kelola Laporan
            </a>
            <a href="admin_pengguna.php" class="sidebar-link <?= $current_page == 'admin_pengguna.php' ? 'active' : '' ?>">
                <i class="bi bi-people"></i> Kelola Pengguna
            </a>
            <a href="admin_kategori.php" class="sidebar-link <?= $current_page == 'admin_kategori.php' ? 'active' : '' ?>">
                <i class="bi bi-tags"></i> Kelola Kategori
            </a>
            <a href="admin_statistik.php" class="sidebar-link <?= $current_page == 'admin_statistik.php' ? 'active' : '' ?>">
                <i class="bi bi-bar-chart"></i> Statistik
            </a>
            <a href="admin_aktivitas.php" class="sidebar-link <?= $current_page == 'admin_aktivitas.php' ? 'active' : '' ?>">
                <i class="bi bi-clock-history"></i> Aktivitas
            </a>
            
            <div class="sidebar-nav-title mt-4">Pengaturan</div>
            <a href="profil.php" class="sidebar-link <?= $current_page == 'profil.php' ? 'active' : '' ?>">
                <i class="bi bi-person"></i> Profil Saya
            </a>
        <?php else: ?>
            <!-- MENU USER -->
            <div class="sidebar-nav-title">Menu Utama</div>
            <a href="dashboard.php" class="sidebar-link <?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
                <i class="bi bi-grid"></i> Dashboard
            </a>
            
            <div class="sidebar-nav-title mt-4">Pelaporan</div>
            <a href="lapor_hilang.php" class="sidebar-link <?= $current_page == 'lapor_hilang.php' ? 'active' : '' ?>">
                <i class="bi bi-exclamation-triangle"></i> Lapor Hilang
            </a>
            <a href="lapor_temuan.php" class="sidebar-link <?= $current_page == 'lapor_temuan.php' ? 'active' : '' ?>">
                <i class="bi bi-box-seam"></i> Lapor Temuan
            </a>
            
            <div class="sidebar-nav-title mt-4">Database</div>
            <a href="data_barang.php" class="sidebar-link <?= in_array($current_page, ['data_barang.php', 'detail_barang.php', 'edit_barang.php']) ? 'active' : '' ?>">
                <i class="bi bi-collection"></i> Data Barang
            </a>
            
            <div class="sidebar-nav-title mt-4">Pengaturan</div>
            <a href="profil.php" class="sidebar-link <?= $current_page == 'profil.php' ? 'active' : '' ?>">
                <i class="bi bi-person"></i> Profil Saya
            </a>
        <?php endif; ?>
    </div>
    
    <div class="sidebar-footer">
        <button onclick="confirmLogout()" class="btn btn-outline-glass w-100" style="color: #FCA5A5; border-color: rgba(239, 68, 68, 0.3);">
            <i class="bi bi-box-arrow-right"></i> Logout
        </button>
    </div>
</aside>
