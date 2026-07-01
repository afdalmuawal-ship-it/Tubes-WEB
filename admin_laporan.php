<?php
// ADMIN_LAPORAN.PHP - Kelola Semua Laporan
require_once 'session.php';
requireAdmin();

// Handle Actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = (int)$_GET['id'];
    $type = $_GET['type'];
    $table = ($type == 'hilang') ? 'barang_hilang' : 'barang_temuan';
    $id_col = ($type == 'hilang') ? 'id_barang' : 'id_temuan';

    if ($action == 'verify' && isset($_GET['status_verif'])) {
        $status_verif = $conn->real_escape_string($_GET['status_verif']);
        $conn->query("UPDATE $table SET status_verifikasi = '$status_verif' WHERE $id_col = $id");
        
        $id_admin = $_SESSION['id_user'];
        $conn->query("INSERT INTO aktivitas (id_user, aksi) VALUES ($id_admin, 'Admin memverifikasi laporan $type (ID: $id) menjadi $status_verif')");
        
        header("Location: admin_laporan.php?alert=success");
        exit();
    }
    
    if ($action == 'status' && isset($_GET['status'])) {
        $status = $conn->real_escape_string($_GET['status']);
        $conn->query("UPDATE $table SET status = '$status' WHERE $id_col = $id");
        
        $id_admin = $_SESSION['id_user'];
        $conn->query("INSERT INTO aktivitas (id_user, aksi) VALUES ($id_admin, 'Admin mengubah status laporan $type (ID: $id) menjadi $status')");
        
        header("Location: admin_laporan.php?alert=success");
        exit();
    }
    
    if ($action == 'delete') {
        $conn->query("DELETE FROM $table WHERE $id_col = $id");
        
        $id_admin = $_SESSION['id_user'];
        $conn->query("INSERT INTO aktivitas (id_user, aksi) VALUES ($id_admin, 'Admin menghapus laporan $type (ID: $id)')");
        
        header("Location: admin_laporan.php?alert=deleted");
        exit();
    }
}

// Konfigurasi Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filter
$where = "1=1";
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
if ($search) {
    $where .= " AND (nama_barang LIKE '%$search%' OR u.nama LIKE '%$search%')";
}
if (isset($_GET['status_verifikasi']) && $_GET['status_verifikasi'] !== '') {
    $sv = $conn->real_escape_string($_GET['status_verifikasi']);
    $where .= " AND status_verifikasi = '$sv'";
}

// Total Data
$count_query = "SELECT COUNT(*) as total FROM (
    SELECT id_barang as id, nama_barang, b.status_verifikasi, u.nama 
    FROM barang_hilang b JOIN users u ON b.id_user = u.id_user
    UNION ALL
    SELECT id_temuan as id, nama_barang, b.status_verifikasi, u.nama 
    FROM barang_temuan b JOIN users u ON b.id_user = u.id_user
) as all_items WHERE $where";
$total_data = $conn->query($count_query)->fetch_assoc()['total'];
$total_pages = ceil($total_data / $limit);

// Ambil Data
$query = "SELECT * FROM (
    SELECT id_barang as id, u.nama as pelapor, nama_barang, kategori, tanggal_hilang as tanggal, b.status, b.status_verifikasi, b.created_at, 'hilang' as tipe 
    FROM barang_hilang b JOIN users u ON b.id_user = u.id_user
    UNION ALL
    SELECT id_temuan as id, u.nama as pelapor, nama_barang, kategori, tanggal_temuan as tanggal, b.status, b.status_verifikasi, b.created_at, 'temuan' as tipe 
    FROM barang_temuan b JOIN users u ON b.id_user = u.id_user
) as all_items 
WHERE $where 
ORDER BY created_at DESC 
LIMIT $offset, $limit";
$items = $conn->query($query);

$page_title = 'Kelola Laporan';
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/topbar.php';
?>

<div class="container-fluid dashboard-content">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Kelola Laporan</h2>
            <p>Manajemen semua laporan barang hilang dan temuan</p>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="card bg-glass mb-4 p-3 animate-on-scroll">
        <form method="GET" action="admin_laporan.php" class="row g-3 align-items-center">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Cari barang atau pelapor..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-4">
                <select name="status_verifikasi" class="form-select">
                    <option value="">Semua Verifikasi</option>
                    <option value="Menunggu Verifikasi" <?= (isset($_GET['status_verifikasi']) && $_GET['status_verifikasi'] == 'Menunggu Verifikasi') ? 'selected' : '' ?>>Menunggu Verifikasi</option>
                    <option value="Disetujui" <?= (isset($_GET['status_verifikasi']) && $_GET['status_verifikasi'] == 'Disetujui') ? 'selected' : '' ?>>Disetujui</option>
                    <option value="Ditolak" <?= (isset($_GET['status_verifikasi']) && $_GET['status_verifikasi'] == 'Ditolak') ? 'selected' : '' ?>>Ditolak</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-gradient w-100">Cari & Filter</button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="table-responsive bg-glass p-3 rounded animate-on-scroll animate-delay-1">
        <table class="table table-dark table-hover align-middle">
            <thead>
                <tr>
                    <th>Pelapor</th>
                    <th>Tipe</th>
                    <th>Barang</th>
                    <th>Kategori</th>
                    <th>Verifikasi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($items->num_rows > 0): ?>
                    <?php while($row = $items->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['pelapor']) ?></td>
                            <td>
                                <span class="badge-type <?= $row['tipe'] === 'hilang' ? 'badge-type-hilang' : 'badge-type-temuan' ?>">
                                    <?= ucfirst($row['tipe']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                            <td><?= htmlspecialchars($row['kategori']) ?></td>
                            <td>
                                <?php if($row['status_verifikasi'] == 'Menunggu Verifikasi'): ?>
                                    <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> Menunggu</span>
                                <?php elseif($row['status_verifikasi'] == 'Disetujui'): ?>
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Disetujui</span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Ditolak</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge-status <?= 'badge-' . strtolower($row['status']) ?>">
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>
                            </td>
                            <td>
                                <!-- Tombol Aksi -->
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-glass dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        Aksi
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-dark">
                                        <li><a class="dropdown-item text-info" href="detail_barang.php?id=<?= $row['id'] ?>&type=<?= $row['tipe'] ?>"><i class="bi bi-eye"></i> Detail</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><h6 class="dropdown-header">Verifikasi</h6></li>
                                        <li><a class="dropdown-item text-success" href="#" onclick="confirmAction('admin_laporan.php?action=verify&id=<?= $row['id'] ?>&type=<?= $row['tipe'] ?>&status_verif=Disetujui', 'Setujui laporan ini?')"><i class="bi bi-check"></i> Approve</a></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="confirmAction('admin_laporan.php?action=verify&id=<?= $row['id'] ?>&type=<?= $row['tipe'] ?>&status_verif=Ditolak', 'Tolak laporan ini?')"><i class="bi bi-x"></i> Reject</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><h6 class="dropdown-header">Ubah Status</h6></li>
                                        <?php if($row['tipe'] == 'hilang'): ?>
                                            <li><a class="dropdown-item" href="?action=status&id=<?= $row['id'] ?>&type=<?= $row['tipe'] ?>&status=Hilang">Hilang</a></li>
                                            <li><a class="dropdown-item" href="?action=status&id=<?= $row['id'] ?>&type=<?= $row['tipe'] ?>&status=Ditemukan">Ditemukan</a></li>
                                            <li><a class="dropdown-item" href="?action=status&id=<?= $row['id'] ?>&type=<?= $row['tipe'] ?>&status=Dikembalikan">Dikembalikan</a></li>
                                        <?php else: ?>
                                            <li><a class="dropdown-item" href="?action=status&id=<?= $row['id'] ?>&type=<?= $row['tipe'] ?>&status=Disimpan">Disimpan</a></li>
                                            <li><a class="dropdown-item" href="?action=status&id=<?= $row['id'] ?>&type=<?= $row['tipe'] ?>&status=Diklaim">Diklaim</a></li>
                                            <li><a class="dropdown-item" href="?action=status&id=<?= $row['id'] ?>&type=<?= $row['tipe'] ?>&status=Dikembalikan">Dikembalikan</a></li>
                                        <?php endif; ?>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="confirmAction('admin_laporan.php?action=delete&id=<?= $row['id'] ?>&type=<?= $row['tipe'] ?>', 'Hapus laporan ini permanen?')"><i class="bi bi-trash"></i> Hapus</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Belum ada laporan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination pagination-custom justify-content-center">
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?><?= isset($_GET['status_verifikasi']) ? '&status_verifikasi='.$_GET['status_verifikasi'] : '' ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<script>
function confirmAction(url, message) {
    Swal.fire({
        title: 'Konfirmasi',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal',
        background: '#052659',
        color: '#fff'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    })
}
</script>

<?php include 'includes/footer.php'; ?>
