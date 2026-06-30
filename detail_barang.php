<?php
// DETAIL_BARANG.PHP - Halaman Detail Laporan Barang

require_once 'session.php';
requireLogin();

// Validasi parameter ID dan Tipe
if (!isset($_GET['id']) || !isset($_GET['type']) || ($_GET['type'] !== 'hilang' && $_GET['type'] !== 'temuan')) {
    header("Location: data_barang.php");
    exit();
}

$id = (int)$_GET['id'];
$type = $_GET['type'];
$current_user_id = $_SESSION['id_user'];

// Ambil data barang beserta informasi pelapor
if ($type === 'hilang') {
    $query = "SELECT b.*, u.nama as nama_pelapor, u.email as email_pelapor, u.foto as foto_pelapor, 
              'hilang' as tipe, b.tanggal_hilang as tanggal 
              FROM barang_hilang b 
              JOIN users u ON b.id_user = u.id_user 
              WHERE b.id_barang = ?";
} else {
    $query = "SELECT b.*, u.nama as nama_pelapor, u.email as email_pelapor, u.foto as foto_pelapor, 
              'temuan' as tipe, b.tanggal_temuan as tanggal 
              FROM barang_temuan b 
              JOIN users u ON b.id_user = u.id_user 
              WHERE b.id_temuan = ?";
}

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: data_barang.php");
    exit();
}

$item = $result->fetch_assoc();

// Cek apakah user yang login adalah pemilik laporan
$is_owner = ($item['id_user'] == $current_user_id);

$page_title = 'Detail Barang';
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/topbar.php';
?>

    <div class="container-fluid dashboard-content">
        
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4 animate-on-scroll">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="data_barang.php">Data Barang</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail</li>
            </ol>
        </nav>

        <div class="row g-4 justify-content-center">
            
            <!-- Kolom Foto -->
            <div class="col-lg-5 animate-on-scroll slide-left">
                <div class="detail-card p-3">
                    <?php if ($item['foto'] && $item['foto'] !== 'no-image.png'): ?>
                        <img src="uploads/<?= htmlspecialchars($item['foto']) ?>" alt="<?= htmlspecialchars($item['nama_barang']) ?>" class="detail-image">
                    <?php else: ?>
                        <div class="no-image-placeholder detail-image" style="min-height: 300px; border-radius: var(--radius-md);">
                            <i class="bi bi-image"></i>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Kolom Informasi -->
            <div class="col-lg-7 animate-on-scroll slide-right">
                <div class="detail-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h2 style="font-weight: 700; color: var(--text-white);"><?= htmlspecialchars($item['nama_barang']) ?></h2>
                        <span class="badge-status <?= 'badge-' . strtolower($item['status']) ?> px-3 py-2" style="font-size: 0.9rem;">
                            <?= htmlspecialchars($item['status']) ?>
                        </span>
                    </div>

                    <div class="mb-4">
                        <span class="badge-type <?= $item['tipe'] === 'hilang' ? 'badge-type-hilang' : 'badge-type-temuan' ?> me-2">
                            Laporan Barang <?= ucfirst($item['tipe']) ?>
                        </span>
                        <small style="color: var(--text-muted);"><i class="bi bi-clock me-1"></i> Dilaporkan pada <?= date('d M Y, H:i', strtotime($item['created_at'])) ?></small>
                    </div>

                    <h5 class="mb-3" style="color: var(--accent-cyan);">Informasi Barang</h5>
                    
                    <div class="detail-info-item">
                        <i class="bi bi-tag"></i>
                        <div>
                            <div class="info-label">Kategori</div>
                            <div class="info-value"><?= htmlspecialchars($item['kategori']) ?></div>
                        </div>
                    </div>
                    
                    <div class="detail-info-item">
                        <i class="bi bi-geo-alt"></i>
                        <div>
                            <div class="info-label">Lokasi <?= $item['tipe'] === 'hilang' ? 'Terakhir Terlihat' : 'Penemuan' ?></div>
                            <div class="info-value"><?= htmlspecialchars($item['lokasi']) ?></div>
                        </div>
                    </div>

                    <div class="detail-info-item">
                        <i class="bi bi-calendar-event"></i>
                        <div>
                            <div class="info-label">Tanggal <?= ucfirst($item['tipe']) ?></div>
                            <div class="info-value"><?= date('d F Y', strtotime($item['tanggal'])) ?></div>
                        </div>
                    </div>

                    <div class="detail-info-item border-0 pb-0">
                        <i class="bi bi-card-text"></i>
                        <div>
                            <div class="info-label">Deskripsi & Kondisi</div>
                            <div class="info-value mt-2" style="line-height: 1.6; color: var(--text-light);">
                                <?= nl2br(htmlspecialchars($item['deskripsi'])) ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Informasi Pelapor & Aksi -->
            <div class="col-12 animate-on-scroll">
                <div class="detail-card p-4">
                    <div class="row align-items-center">
                        <div class="col-md-7 mb-4 mb-md-0">
                            <h5 class="mb-3" style="color: var(--accent-purple);">Informasi Pelapor</h5>
                            <div class="d-flex align-items-center gap-3">
                                <img src="uploads/<?= htmlspecialchars($item['foto_pelapor']) ?>" alt="Avatar" class="user-avatar" style="width: 60px; height: 60px;" 
                                     onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($item['nama_pelapor']) ?>&background=3B82F6&color=fff'">
                                <div>
                                    <h6 class="mb-1 text-white" style="font-weight: 600;"><?= htmlspecialchars($item['nama_pelapor']) ?></h6>
                                    <div style="font-size: 0.9rem; color: var(--text-muted);">
                                        <i class="bi bi-envelope me-1"></i> <?= htmlspecialchars($item['email_pelapor']) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-5 text-md-end border-md-start" style="border-color: rgba(255,255,255,0.1) !important; padding-left: 1.5rem;">
                            <?php if ($is_owner): ?>
                                <!-- Aksi untuk Pemilik Laporan -->
                                <h6 class="mb-3" style="color: var(--text-light);">Aksi Anda</h6>
                                <div class="d-flex gap-2 justify-content-md-end flex-wrap">
                                    <a href="edit_barang.php?id=<?= $id ?>&type=<?= $type ?>" class="btn btn-outline-glass btn-sm-gradient">
                                        <i class="bi bi-pencil-square me-1"></i> Edit Data
                                    </a>
                                    <button type="button" class="btn btn-sm btn-gradient" style="background: linear-gradient(135deg, #EF4444, #B91C1C);" 
                                            onclick="confirmDelete(<?= $id ?>, '<?= $type ?>')">
                                        <i class="bi bi-trash me-1"></i> Hapus
                                    </button>
                                </div>
                            <?php else: ?>
                                <!-- Aksi untuk User Lain -->
                                <h6 class="mb-3" style="color: var(--text-light);">Hubungi Pelapor</h6>
                                <a href="mailto:<?= htmlspecialchars($item['email_pelapor']) ?>?subject=Terkait Laporan <?= ucfirst($item['tipe']) ?>: <?= htmlspecialchars($item['nama_barang']) ?>" class="btn btn-gradient">
                                    <i class="bi bi-envelope-paper me-2"></i> Kirim Email
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>
