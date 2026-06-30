<?php
// INCLUDES/TOPBAR.PHP
// Pastikan file ini di-include setelah session distart dan data user tersedia ($user)
if (!isset($user)) {
    $user = getCurrentUser();
}
?>
<!-- Main Content -->
<div class="main-content">
    
    <!-- Topbar -->
    <header class="topbar">
        <!-- Toggle Button (Mobile Only) -->
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        
        <!-- Empty div for flex spacing if not mobile -->
        <div class="d-none d-lg-block"></div>

        <div class="topbar-right">
            <!-- User Dropdown -->
            <div class="dropdown">
                <div class="topbar-user" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="uploads/<?= htmlspecialchars($user['foto']) ?>" alt="Avatar" 
                         onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($user['nama']) ?>&background=3B82F6&color=fff'">
                    <div class="topbar-user-info">
                        <span><?= htmlspecialchars($user['nama']) ?></span>
                        <small><?= ucfirst(htmlspecialchars($user['role'])) ?></small>
                    </div>
                    <i class="bi bi-chevron-down d-none d-md-block text-white" style="font-size: 0.8rem;"></i>
                </div>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark" style="background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
                    <li><a class="dropdown-item" href="profil.php"><i class="bi bi-person me-2"></i> Profil Saya</a></li>
                    <li><hr class="dropdown-divider" style="border-color: rgba(255,255,255,0.1);"></li>
                    <li><a class="dropdown-item text-danger" href="#" onclick="confirmLogout()"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </header>
    
    <!-- Content Body (Ditutup di footer.php) -->
    <div class="content-body">
