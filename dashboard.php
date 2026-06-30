<?php
// DASHBOARD.PHP - Halaman Dashboard User
// Menampilkan ringkasan data dan menu cepat

require_once 'session.php';
requireLogin(); // Auth guard - redirect jika belum login

// -- Ambil data user yang sedang login --
$user = getCurrentUser();

// -- Hitung statistik untuk dashboard --
$id_user = $_SESSION['id_user'];

// Total barang hilang yang dilaporkan user
$totalHilang = $conn->query("SELECT COUNT(*) as total FROM barang_hilang WHERE id_user = $id_user")->fetch_assoc()['total'];

// Total barang temuan yang dilaporkan user
$totalTemuan = $conn->query("SELECT COUNT(*) as total FROM barang_temuan WHERE id_user = $id_user")->fetch_assoc()['total'];

// Total barang yang berhasil dikembalikan (global)
$totalKembali = $conn->query("SELECT COUNT(*) as total FROM barang_hilang WHERE status = 'Dikembalikan'")->fetch_assoc()['total'];
$totalKembali += $conn->query("SELECT COUNT(*) as total FROM barang_temuan WHERE status = 'Dikembalikan'")->fetch_assoc()['total'];

// Barang terbaru (5 terakhir) - gabungan hilang dan temuan
$recentItems = $conn->query("
    (SELECT id_barang as id, nama_barang, kategori, lokasi, status, foto, created_at, 'hilang' as tipe FROM barang_hilang ORDER BY created_at DESC LIMIT 5)
    UNION ALL
    (SELECT id_temuan as id, nama_barang, kategori, lokasi, status, foto, created_at, 'temuan' as tipe FROM barang_temuan ORDER BY created_at DESC LIMIT 5)
    ORDER BY created_at DESC LIMIT 5
");

$page_title = 'Dashboard';
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/topbar.php';
?>

    <!--DASHBOARD CONTENT-->
    <div class="container-fluid dashboard-content">

        <!-- Welcome Card -->
        <div class="welcome-card animate-on-scroll">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3>Selamat Datang, <?= htmlspecialchars($user['nama']) ?>!</h3>
                    <p>Kelola laporan barang hilang dan temuan Anda melalui dashboard ini.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="data_barang.php" class="btn btn-outline-glass btn-sm-gradient me-2">
                        <i class="bi bi-search"></i> Cari
                    </a>

                    <a href="lapor_hilang.php" class="btn btn-outline-glass btn-sm-gradient me-2">
                        <i class="bi bi-plus-lg"></i> Lapor
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistik Cards -->
        <div class="row g-4 mb-4">
            <!-- Barang Hilang -->
            <div class="col-md-4 animate-on-scroll animate-delay-1">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div class="stat-number"><?= $totalHilang ?></div>
                    <div class="stat-label">Barang Hilang</div>
                </div>
            </div>

            <!-- Barang Temuan -->
            <div class="col-md-4 animate-on-scroll animate-delay-2">
                <div class="stat-card green">
                    <div class="stat-icon">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div class="stat-number"><?= $totalTemuan ?></div>
                    <div class="stat-label">Barang Temuan</div>
                </div>
            </div>

            <!-- Dikembalikan -->
            <div class="col-md-4 animate-on-scroll animate-delay-3">
                <div class="stat-card purple">
                    <div class="stat-icon">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="stat-number"><?= $totalKembali ?></div>
                    <div class="stat-label">Dikembalikan</div>
                </div>
            </div>
        </div>

        <!-- Menu Cepat -->
        <h5 class="mb-3" style="font-weight:700; color:var(--text-white);">
            <i class="bi bi-lightning me-2" style="color:#F59E0B;"></i>Menu Cepat
        </h5>
        <div class="row g-4 mb-5">
            <div class="col-6 col-md-3 animate-on-scroll animate-delay-1">
                <a href="lapor_hilang.php" class="quick-menu-card">
                    <div class="menu-icon blue">
                        <i class="bi bi-exclamation-triangle text-white"></i>
                    </div>
                    <h6>Lapor Hilang</h6>
                    <p>Laporkan barang hilang</p>
                </a>
            </div>
            <div class="col-6 col-md-3 animate-on-scroll animate-delay-2">
                <a href="lapor_temuan.php" class="quick-menu-card">
                    <div class="menu-icon green">
                        <i class="bi bi-box-seam text-white"></i>
                    </div>
                    <h6>Lapor Temuan</h6>
                    <p>Laporkan barang temuan</p>
                </a>
            </div>
            <div class="col-6 col-md-3 animate-on-scroll animate-delay-3">
                <a href="data_barang.php" class="quick-menu-card">
                    <div class="menu-icon orange">
                        <i class="bi bi-search text-white"></i>
                    </div>
                    <h6>Cari Barang</h6>
                    <p>Cari barang di database</p>
                </a>
            </div>
            <div class="col-6 col-md-3 animate-on-scroll animate-delay-4">
                <a href="profil.php" class="quick-menu-card">
                    <div class="menu-icon purple">
                        <i class="bi bi-person text-white"></i>
                    </div>
                    <h6>Profil Saya</h6>
                    <p>Kelola akun Anda</p>
                </a>
            </div>
        </div>

        <!-- Barang Terbaru -->
        <h5 class="mb-3" style="font-weight:700; color:var(--text-white);">
            <i class="bi bi-clock-history me-2" style="color:#06B6D4;"></i>Laporan Terbaru
        </h5>
        <div class="row g-4">
            <?php if ($recentItems->num_rows > 0): ?>
                <?php while ($item = $recentItems->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4 animate-on-scroll">
                        <div class="barang-card">
                            <!-- Gambar Barang -->
                            <div class="card-img-wrapper">
                                <?php if ($item['foto'] && $item['foto'] !== 'no-image.png'): ?>
                                    <img src="uploads/<?= htmlspecialchars($item['foto']) ?>" alt="<?= htmlspecialchars($item['nama_barang']) ?>">
                                <?php else: ?>
                                    <div class="no-image-placeholder">
                                        <i class="bi bi-image"></i>
                                    </div>
                                <?php endif; ?>
                                <!-- Badge Status -->
                                <span class="badge-status <?= 'badge-' . strtolower($item['status']) ?>">
                                    <?= htmlspecialchars($item['status']) ?>
                                </span>
                            </div>
                            <!-- Body Card -->
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="mb-0"><?= htmlspecialchars($item['nama_barang']) ?></h5>
                                    <span class="badge-type <?= $item['tipe'] === 'hilang' ? 'badge-type-hilang' : 'badge-type-temuan' ?>">
                                        <?= ucfirst($item['tipe']) ?>
                                    </span>
                                </div>
                                <div class="card-info">
                                    <i class="bi bi-tag"></i> <?= htmlspecialchars($item['kategori']) ?>
                                </div>
                                <div class="card-info">
                                    <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($item['lokasi']) ?>
                                </div>
                                <div class="card-info">
                                    <i class="bi bi-calendar3"></i> <?= date('d M Y', strtotime($item['created_at'])) ?>
                                </div>
                            </div>
                            <!-- Actions -->
                            <div class="card-actions">
                                <a href="detail_barang.php?id=<?= $item['id'] ?>&type=<?= $item['tipe'] ?>" class="btn btn-icon btn-icon-view" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <!-- Empty State -->
                <div class="col-12">
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <h5>Belum Ada Laporan</h5>
                        <p>Mulai buat laporan barang hilang atau temuan pertama Anda.</p>
                        <a href="lapor_hilang.php" class="btn btn-gradient mt-3">
                            <i class="bi bi-plus-lg"></i> Buat Laporan
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>
