<?php
// ADMIN_KATEGORI.PHP - Kelola Kategori
require_once 'session.php';
requireAdmin();

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $nama = $conn->real_escape_string(trim($_POST['nama_kategori']));
        if (!empty($nama)) {
            $conn->query("INSERT IGNORE INTO kategori (nama_kategori) VALUES ('$nama')");
            header("Location: admin_kategori.php?alert=success");
            exit();
        }
    }
    
    if (isset($_POST['edit'])) {
        $id = (int)$_POST['id_kategori'];
        $nama = $conn->real_escape_string(trim($_POST['nama_kategori']));
        if (!empty($nama)) {
            $conn->query("UPDATE kategori SET nama_kategori = '$nama' WHERE id_kategori = $id");
            header("Location: admin_kategori.php?alert=success");
            exit();
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = (int)$_GET['id'];
    $conn->query("DELETE FROM kategori WHERE id_kategori = $id");
    header("Location: admin_kategori.php?alert=deleted");
    exit();
}

$kategori = $conn->query("SELECT * FROM kategori ORDER BY nama_kategori ASC");

$page_title = 'Kelola Kategori';
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/topbar.php';
?>

<div class="container-fluid dashboard-content">
    <div class="page-header mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2>Kelola Kategori</h2>
            <p>Manajemen kategori barang</p>
        </div>
        <button class="btn btn-gradient" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus"></i> Tambah Kategori
        </button>
    </div>

    <!-- Table -->
    <div class="table-responsive bg-glass p-3 rounded animate-on-scroll">
        <table class="table table-dark table-hover align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Kategori</th>
                    <th width="150">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($kategori->num_rows > 0): ?>
                    <?php while($row = $kategori->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id_kategori'] ?></td>
                            <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-info me-1" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id_kategori'] ?>" title="Edit"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-danger" onclick="confirmAction('admin_kategori.php?action=delete&id=<?= $row['id_kategori'] ?>', 'Hapus kategori ini?')" title="Hapus"><i class="bi bi-trash"></i></button>

                                <!-- Modal Edit -->
                                <div class="modal fade" id="editModal<?= $row['id_kategori'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content bg-glass">
                                            <div class="modal-header border-0">
                                                <h5 class="modal-title text-white">Edit Kategori</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form method="POST" action="admin_kategori.php">
                                                <div class="modal-body text-white">
                                                    <input type="hidden" name="id_kategori" value="<?= $row['id_kategori'] ?>">
                                                    <input type="hidden" name="edit" value="1">
                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Kategori</label>
                                                        <input type="text" name="nama_kategori" class="form-control" value="<?= htmlspecialchars($row['nama_kategori']) ?>" required>
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
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">Belum ada kategori.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Add -->
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-glass">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white">Tambah Kategori</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="admin_kategori.php">
                <div class="modal-body text-white">
                    <input type="hidden" name="add" value="1">
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori</label>
                        <input type="text" name="nama_kategori" class="form-control" required>
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
