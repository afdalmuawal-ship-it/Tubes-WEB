<?php
// ADMIN_PENGGUNA.PHP - Kelola Semua Pengguna
require_once 'session.php';
requireAdmin();

// Handle Actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = (int)$_GET['id'];

    if ($action == 'status' && isset($_GET['status'])) {
        $status = $conn->real_escape_string($_GET['status']);
        $conn->query("UPDATE users SET status = '$status' WHERE id_user = $id");
        
        $id_admin = $_SESSION['id_user'];
        $conn->query("INSERT INTO aktivitas (id_user, aksi) VALUES ($id_admin, 'Admin mengubah status user ID: $id menjadi $status')");
        
        header("Location: admin_pengguna.php?alert=success");
        exit();
    }
    
    if ($action == 'delete') {
        $conn->query("DELETE FROM users WHERE id_user = $id AND id_user != " . $_SESSION['id_user']); // Cegah hapus diri sendiri
        
        $id_admin = $_SESSION['id_user'];
        $conn->query("INSERT INTO aktivitas (id_user, aksi) VALUES ($id_admin, 'Admin menghapus user ID: $id')");
        
        header("Location: admin_pengguna.php?alert=deleted");
        exit();
    }
}

// Handle Update Role (via Modal POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
    $id = (int)$_POST['id_user'];
    $role = $conn->real_escape_string($_POST['role']);
    
    if ($id != $_SESSION['id_user']) { // Cegah ubah role sendiri
        $conn->query("UPDATE users SET role = '$role' WHERE id_user = $id");
        $id_admin = $_SESSION['id_user'];
        $conn->query("INSERT INTO aktivitas (id_user, aksi) VALUES ($id_admin, 'Admin mengubah role user ID: $id menjadi $role')");
    }
    
    header("Location: admin_pengguna.php?alert=success");
    exit();
}

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filter
$where = "1=1";
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
if ($search) {
    $where .= " AND (nama LIKE '%$search%' OR email LIKE '%$search%')";
}

$total_data = $conn->query("SELECT COUNT(*) as total FROM users WHERE $where")->fetch_assoc()['total'];
$total_pages = ceil($total_data / $limit);

$users = $conn->query("SELECT * FROM users WHERE $where ORDER BY created_at DESC LIMIT $offset, $limit");

$page_title = 'Kelola Pengguna';
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/topbar.php';
?>

<div class="container-fluid dashboard-content">
    <div class="page-header mb-4">
        <h2>Kelola Pengguna</h2>
        <p>Manajemen data akun admin dan user</p>
    </div>

    <!-- Filter & Search -->
    <div class="card bg-glass mb-4 p-3 animate-on-scroll">
        <form method="GET" action="admin_pengguna.php" class="row g-3">
            <div class="col-md-8">
                <input type="text" name="search" class="form-control" placeholder="Cari nama atau email..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-gradient w-100">Cari</button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="table-responsive bg-glass p-3 rounded animate-on-scroll animate-delay-1">
        <table class="table table-dark table-hover align-middle">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Terdaftar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($users->num_rows > 0): ?>
                    <?php while($row = $users->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <img src="uploads/<?= htmlspecialchars($row['foto']) ?>" alt="Foto" width="40" height="40" class="rounded-circle" style="object-fit: cover;">
                            </td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td>
                                <?php if($row['role'] == 'admin'): ?>
                                    <span class="badge bg-primary">Admin</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">User</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if(isset($row['status']) && $row['status'] == 'Aktif'): ?>
                                    <span class="badge bg-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                            <td>
                                <?php if($row['id_user'] != $_SESSION['id_user']): ?>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-glass dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Aksi
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-dark">
                                            <li><a class="dropdown-item text-info" href="#" data-bs-toggle="modal" data-bs-target="#editRoleModal<?= $row['id_user'] ?>"><i class="bi bi-person-gear"></i> Ubah Role</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <?php if(!isset($row['status']) || $row['status'] == 'Aktif'): ?>
                                                <li><a class="dropdown-item text-warning" href="#" onclick="confirmAction('admin_pengguna.php?action=status&id=<?= $row['id_user'] ?>&status=Nonaktif', 'Nonaktifkan user ini?')"><i class="bi bi-person-x"></i> Nonaktifkan</a></li>
                                            <?php else: ?>
                                                <li><a class="dropdown-item text-success" href="#" onclick="confirmAction('admin_pengguna.php?action=status&id=<?= $row['id_user'] ?>&status=Aktif', 'Aktifkan kembali user ini?')"><i class="bi bi-person-check"></i> Aktifkan</a></li>
                                            <?php endif; ?>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="confirmAction('admin_pengguna.php?action=delete&id=<?= $row['id_user'] ?>', 'Hapus user ini secara permanen?')"><i class="bi bi-trash"></i> Hapus</a></li>
                                        </ul>
                                    </div>

                                    <!-- Modal Edit Role -->
                                    <div class="modal fade" id="editRoleModal<?= $row['id_user'] ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content bg-glass">
                                                <div class="modal-header border-0">
                                                    <h5 class="modal-title text-white">Ubah Role: <?= htmlspecialchars($row['nama']) ?></h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form method="POST" action="admin_pengguna.php">
                                                    <div class="modal-body text-white">
                                                        <input type="hidden" name="id_user" value="<?= $row['id_user'] ?>">
                                                        <input type="hidden" name="update_role" value="1">
                                                        <div class="mb-3">
                                                            <label class="form-label">Role</label>
                                                            <select name="role" class="form-select">
                                                                <option value="user" <?= $row['role'] == 'user' ? 'selected' : '' ?>>User</option>
                                                                <option value="admin" <?= $row['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-gradient">Simpan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small">Akun Anda</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Belum ada pengguna.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
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
