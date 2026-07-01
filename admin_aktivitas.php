<?php

require_once 'session.php';
requireAdmin();

// Pagination
$limit = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total_data = $conn->query("SELECT COUNT(*) as total FROM aktivitas")->fetch_assoc()['total'];
$total_pages = ceil($total_data / $limit);

$query = "SELECT a.*, u.nama, u.foto, u.role FROM aktivitas a LEFT JOIN users u ON a.id_user = u.id_user ORDER BY a.waktu DESC LIMIT $offset, $limit";
$aktivitas = $conn->query($query);

$page_title = 'Aktivitas Terbaru';
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/topbar.php';
?>

<div class="container-fluid dashboard-content">
    <div class="page-header mb-4">
        <h2>Aktivitas Terbaru</h2>
        <p>Log semua aksi pengguna dan admin di dalam sistem</p>
    </div>

    <!-- Timeline Aktivitas -->
    <div class="bg-glass p-4 rounded animate-on-scroll">
        <?php if ($aktivitas->num_rows > 0): ?>
            <div class="activity-timeline">
                <?php while($row = $aktivitas->fetch_assoc()): ?>
                    <div class="activity-item d-flex align-items-start mb-4 pb-3 border-bottom border-secondary">
                        <!-- Avatar -->
                        <div class="flex-shrink-0 me-3">
                            <?php if($row['foto']): ?>
                                <img src="uploads/<?= htmlspecialchars($row['foto']) ?>" alt="User" class="rounded-circle" width="45" height="45" style="object-fit:cover;">
                            <?php else: ?>
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                    <i class="bi bi-person text-white"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Konten -->
                        <div class="flex-grow-1">
                            <h6 class="mb-1 text-white">
                                <?= $row['nama'] ? htmlspecialchars($row['nama']) : '<span class="text-muted">User Terhapus</span>' ?>
                                <?php if($row['role'] == 'admin'): ?>
                                    <span class="badge bg-primary ms-2" style="font-size: 0.7rem;">Admin</span>
                                <?php endif; ?>
                            </h6>
                            <p class="mb-1" style="color: rgba(255, 255, 255, 0.8);">
                                <?= htmlspecialchars($row['aksi']) ?>
                            </p>
                            <small class="text-muted"><i class="bi bi-clock"></i> <?= date('d M Y H:i:s', strtotime($row['waktu'])) ?></small>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center text-muted py-5">
                <i class="bi bi-clock-history fs-1"></i>
                <p class="mt-3">Belum ada aktivitas terekam.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination pagination-custom justify-content-center">
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<style>
.activity-timeline {
    position: relative;
}
.activity-item:last-child {
    border-bottom: none !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}
</style>

<?php include 'includes/footer.php'; ?>
