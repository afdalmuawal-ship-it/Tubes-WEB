<?php

require_once 'session.php';
requireLogin();

$kategori_list = [];
$res_kat = $conn->query("SELECT nama_kategori FROM kategori ORDER BY nama_kategori ASC");
if ($res_kat) {
    while ($r = $res_kat->fetch_assoc()) {
        $kategori_list[] = $r['nama_kategori'];
    }
}

// Konfigurasi Pagination
$limit = 9; // Jumlah item per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filter Data
$where_clause = "status_verifikasi = 'Disetujui'";
$params = [];
$types = "";

if (isset($_GET['kategori']) && !empty($_GET['kategori'])) {
    $where_clause .= " AND kategori = ?";
    $params[] = $_GET['kategori'];
    $types .= "s";
}

if (isset($_GET['status']) && !empty($_GET['status'])) {
    $where_clause .= " AND status = ?";
    $params[] = $_GET['status'];
    $types .= "s";
}

// Mengambil total data untuk pagination
$count_query = "SELECT COUNT(*) as total FROM (
    SELECT id_barang as id, kategori, status, status_verifikasi FROM barang_hilang
    UNION ALL
    SELECT id_temuan as id, kategori, status, status_verifikasi FROM barang_temuan
) as all_items WHERE $where_clause";

$stmt_count = $conn->prepare($count_query);
if(!empty($params)) {
     $stmt_count->bind_param($types, ...$params);
}
$stmt_count->execute();
$total_data = $stmt_count->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_data / $limit);

// Mengambil data barang
$query = "SELECT * FROM (
    SELECT id_barang as id, nama_barang, kategori, lokasi, tanggal_hilang as tanggal, deskripsi, foto, status, status_verifikasi, created_at, 'hilang' as tipe 
    FROM barang_hilang
    UNION ALL
    SELECT id_temuan as id, nama_barang, kategori, lokasi, tanggal_temuan as tanggal, deskripsi, foto, status, status_verifikasi, created_at, 'temuan' as tipe 
    FROM barang_temuan
) as all_items 
WHERE $where_clause 
ORDER BY created_at DESC 
LIMIT ?, ?";

$stmt = $conn->prepare($query);
if(!empty($params)) {
    $params[] = $offset;
    $params[] = $limit;
    $types .= "ii";
    $stmt->bind_param($types, ...$params);
} else {
    $stmt->bind_param("ii", $offset, $limit);
}

$stmt->execute();
$items = $stmt->get_result();

$page_title = 'Data Barang';
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/topbar.php';
?>

    <div class="container-fluid dashboard-content">
        
        <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2>Data Barang</h2>
                <p>Cari dan temukan barang yang hilang atau ditemukan</p>
            </div>
            <div>
                 <a href="lapor_hilang.php" class="btn btn-gradient btn-sm-gradient me-2"><i class="bi bi-plus"></i> Lapor Hilang</a>
                 <a href="lapor_temuan.php" class="btn btn-outline-glass btn-sm-gradient"><i class="bi bi-plus"></i> Lapor Temuan</a>
            </div>
        </div>

        <!-- Search & Filter Bar -->
        <div class="search-filter-bar animate-on-scroll">
            <div class="row g-3">
                <div class="col-md-5">
                    <div class="position-relative">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" class="search-input" id="searchBarang" placeholder="Cari nama barang, deskripsi, atau lokasi...">
                    </div>
                </div>
                <div class="col-md-7">
                    <form method="GET" action="data_barang.php" class="d-flex gap-2 flex-wrap flex-md-nowrap">
                        <select name="kategori" class="form-select form-select-custom" id="filterKategori" onchange="this.form.submit()">
                            <option value="">Semua Kategori</option>
                            <?php foreach($kategori_list as $kat): ?>
                                <option value="<?= $kat ?>" <?= (isset($_GET['kategori']) && $_GET['kategori'] == $kat) ? 'selected' : '' ?>><?= $kat ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="status" class="form-select form-select-custom" id="filterStatus" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="Hilang" <?= (isset($_GET['status']) && $_GET['status'] == 'Hilang') ? 'selected' : '' ?>>Hilang</option>
                            <option value="Ditemukan" <?= (isset($_GET['status']) && $_GET['status'] == 'Ditemukan') ? 'selected' : '' ?>>Ditemukan (Laporan Hilang)</option>
                            <option value="Disimpan" <?= (isset($_GET['status']) && $_GET['status'] == 'Disimpan') ? 'selected' : '' ?>>Disimpan (Laporan Temuan)</option>
                            <option value="Diklaim" <?= (isset($_GET['status']) && $_GET['status'] == 'Diklaim') ? 'selected' : '' ?>>Diklaim</option>
                            <option value="Dikembalikan" <?= (isset($_GET['status']) && $_GET['status'] == 'Dikembalikan') ? 'selected' : '' ?>>Dikembalikan</option>
                        </select>
                        <a href="data_barang.php" class="btn btn-outline-glass px-3" title="Reset Filter"><i class="bi bi-arrow-counterclockwise"></i></a>
                    </form>
                </div>
            </div>
        </div>

        <!-- Empty State Search (Hidden by default) -->
        <div id="emptySearchState" style="display: none;">
            <div class="empty-state">
                <i class="bi bi-search"></i>
                <h5>Tidak ada hasil</h5>
                <p>Barang yang Anda cari tidak ditemukan. Coba kata kunci lain.</p>
                <button type="button" class="btn btn-outline-glass mt-2" onclick="resetFilter()">Reset Pencarian</button>
            </div>
        </div>

        <!-- Grid Data Barang -->
        <div class="row g-4" id="barangGrid">
            <?php if ($items->num_rows > 0): ?>
                <?php while ($item = $items->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4 barang-card-wrapper animate-on-scroll" 
                         data-kategori="<?= htmlspecialchars($item['kategori']) ?>" 
                         data-status="<?= htmlspecialchars($item['status']) ?>">
                        <div class="barang-card">
                            <div class="card-img-wrapper">
                                <?php if ($item['foto'] && $item['foto'] !== 'no-image.png'): ?>
                                    <img src="uploads/<?= htmlspecialchars($item['foto']) ?>" alt="<?= htmlspecialchars($item['nama_barang']) ?>">
                                <?php else: ?>
                                    <div class="no-image-placeholder">
                                        <i class="bi bi-image"></i>
                                    </div>
                                <?php endif; ?>
                                <span class="badge-status <?= 'badge-' . strtolower($item['status']) ?>">
                                    <?= htmlspecialchars($item['status']) ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="mb-0 text-truncate" title="<?= htmlspecialchars($item['nama_barang']) ?>"><?= htmlspecialchars($item['nama_barang']) ?></h5>
                                    <span class="badge-type <?= $item['tipe'] === 'hilang' ? 'badge-type-hilang' : 'badge-type-temuan' ?>">
                                        <?= ucfirst($item['tipe']) ?>
                                    </span>
                                </div>
                                <div class="card-info">
                                    <i class="bi bi-tag"></i> <?= htmlspecialchars($item['kategori']) ?>
                                </div>
                                <div class="card-info text-truncate">
                                    <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($item['lokasi']) ?>
                                </div>
                                <div class="card-info">
                                    <i class="bi bi-calendar3"></i> <?= date('d M Y', strtotime($item['tanggal'])) ?>
                                </div>
                                
                                <!-- Hidden description for search -->
                                <span class="d-none"><?= htmlspecialchars($item['deskripsi']) ?></span>
                            </div>
                            <div class="card-actions">
                                <a href="detail_barang.php?id=<?= $item['id'] ?>&type=<?= $item['tipe'] ?>" class="btn btn-icon btn-icon-view" title="Lihat Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <h5>Belum Ada Data</h5>
                        <p>Tidak ada laporan barang dengan filter yang dipilih.</p>
                        <a href="data_barang.php" class="btn btn-outline-glass mt-3">Reset Filter</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation" class="mt-5">
                <ul class="pagination pagination-custom justify-content-center">
                    
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page - 1 ?><?= isset($_GET['kategori']) ? '&kategori='.$_GET['kategori'] : '' ?><?= isset($_GET['status']) ? '&status='.$_GET['status'] : '' ?>">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>

                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?><?= isset($_GET['kategori']) ? '&kategori='.$_GET['kategori'] : '' ?><?= isset($_GET['status']) ? '&status='.$_GET['status'] : '' ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page + 1 ?><?= isset($_GET['kategori']) ? '&kategori='.$_GET['kategori'] : '' ?><?= isset($_GET['status']) ? '&status='.$_GET['status'] : '' ?>">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>

    </div>

<?php include 'includes/footer.php'; ?>
