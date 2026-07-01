<?php
// EDIT_BARANG.PHP - Halaman Edit Data Laporan

require_once 'session.php';
requireLogin();

// Ambil kategori dari database
$kategori_list = [];
$res_kat = $conn->query("SELECT nama_kategori FROM kategori ORDER BY nama_kategori ASC");
if ($res_kat) {
    while ($r = $res_kat->fetch_assoc()) {
        $kategori_list[] = $r['nama_kategori'];
    }
}

// Validasi parameter ID dan Tipe
if (!isset($_GET['id']) || !isset($_GET['type']) || ($_GET['type'] !== 'hilang' && $_GET['type'] !== 'temuan')) {
    header("Location: data_barang.php");
    exit();
}

$id = (int)$_GET['id'];
$type = $_GET['type'];
$current_user_id = $_SESSION['id_user'];

// Ambil data barang (Hanya pemilik yang bisa ambil datanya)
if ($type === 'hilang') {
    $query = "SELECT * FROM barang_hilang WHERE id_barang = ? AND id_user = ?";
} else {
    $query = "SELECT * FROM barang_temuan WHERE id_temuan = ? AND id_user = ?";
}

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $id, $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Tidak ditemukan atau bukan pemiliknya
    header("Location: data_barang.php");
    exit();
}

$item = $result->fetch_assoc();
$tanggal_field = ($type === 'hilang') ? 'tanggal_hilang' : 'tanggal_temuan';
$id_field = ($type === 'hilang') ? 'id_barang' : 'id_temuan';

// Proses Update Data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_barang = $conn->real_escape_string(trim($_POST['nama_barang']));
    $kategori = $conn->real_escape_string(trim($_POST['kategori']));
    $lokasi = $conn->real_escape_string(trim($_POST['lokasi']));
    $tanggal = $conn->real_escape_string(trim($_POST['tanggal']));
    $deskripsi = $conn->real_escape_string(trim($_POST['deskripsi']));
    $status = $conn->real_escape_string(trim($_POST['status']));
    
    $foto_name = $item['foto']; // Default foto lama

    // Proses upload foto baru jika ada
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
        $file_name = $_FILES['foto']['name'];
        $file_tmp = $_FILES['foto']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_ext)) {
            $new_name = $type . '_' . time() . '.' . $file_ext;
            $upload_path = UPLOAD_DIR . $new_name;

            if (move_uploaded_file($file_tmp, $upload_path)) {
                // Hapus foto lama jika bukan default
                if ($item['foto'] !== 'no-image.png' && file_exists(UPLOAD_DIR . $item['foto'])) {
                    unlink(UPLOAD_DIR . $item['foto']);
                }
                $foto_name = $new_name;
            }
        }
    }

    if ($type === 'hilang') {
        $update_query = "UPDATE barang_hilang SET 
                         nama_barang = '$nama_barang', kategori = '$kategori', 
                         lokasi = '$lokasi', tanggal_hilang = '$tanggal', 
                         deskripsi = '$deskripsi', foto = '$foto_name', status = '$status',
                         status_verifikasi = 'Menunggu Verifikasi'
                         WHERE id_barang = $id AND id_user = $current_user_id";
    } else {
        $update_query = "UPDATE barang_temuan SET 
                         nama_barang = '$nama_barang', kategori = '$kategori', 
                         lokasi = '$lokasi', tanggal_temuan = '$tanggal', 
                         deskripsi = '$deskripsi', foto = '$foto_name', status = '$status',
                         status_verifikasi = 'Menunggu Verifikasi'
                         WHERE id_temuan = $id AND id_user = $current_user_id";
    }

    if ($conn->query($update_query)) {
        // Log aktivitas
        $conn->query("INSERT INTO aktivitas (id_user, aksi) VALUES ($current_user_id, 'User mengedit laporan barang $type (ID: $id)')");
        
        header("Location: detail_barang.php?id=$id&type=$type&alert=edit_success");
        exit();
    } else {
        $error = "Terjadi kesalahan saat mengupdate data.";
    }
}

$page_title = 'Edit Data Barang';
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/topbar.php';
?>

    <div class="container-fluid dashboard-content">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <div class="page-header text-center">
                    <h2>Edit Data Laporan</h2>
                    <p>Perbarui informasi atau ubah status laporan barang</p>
                </div>

                <?php if(isset($error)): ?>
                    <div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.15); color: #FCA5A5; border: 1px solid rgba(239, 68, 68, 0.3);">
                        <i class="bi bi-exclamation-circle me-2"></i> <?= $error ?>
                    </div>
                <?php endif; ?>

                <div class="form-card animate-on-scroll">
                    <form method="POST" action="" enctype="multipart/form-data" id="formEditBarang" data-loading onsubmit="return validateForm('formEditBarang')">
                        
                        <!-- Status Update Highlight -->
                        <div class="p-3 mb-4 rounded" style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.3);">
                            <label class="form-label-custom text-white"><i class="bi bi-arrow-repeat me-2 text-primary"></i>Update Status Laporan</label>
                            <select class="form-select form-select-custom mt-2" name="status" required style="border-color: rgba(59, 130, 246, 0.5);">
                                <?php if($type === 'hilang'): ?>
                                    <option value="Hilang" <?= $item['status'] == 'Hilang' ? 'selected' : '' ?>>Masih Hilang</option>
                                    <option value="Ditemukan" <?= $item['status'] == 'Ditemukan' ? 'selected' : '' ?>>Telah Ditemukan (Belum diambil)</option>
                                    <option value="Dikembalikan" <?= $item['status'] == 'Dikembalikan' ? 'selected' : '' ?>>Sudah Dikembalikan/Kembali ke Saya</option>
                                <?php else: ?>
                                    <option value="Disimpan" <?= $item['status'] == 'Disimpan' ? 'selected' : '' ?>>Masih Disimpan (Belum ada yang klaim)</option>
                                    <option value="Diklaim" <?= $item['status'] == 'Diklaim' ? 'selected' : '' ?>>Sedang Diklaim (Proses verifikasi)</option>
                                    <option value="Dikembalikan" <?= $item['status'] == 'Dikembalikan' ? 'selected' : '' ?>>Sudah Dikembalikan ke Pemilik</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Upload Foto -->
                        <div class="mb-4 text-center">
                            <label class="form-label-custom d-block mb-3">Ganti Foto Barang (Biarkan kosong jika tidak ingin ganti)</label>
                            <div class="upload-area" id="uploadArea">
                                <i class="bi bi-image"></i>
                                <p class="mb-0">Klik atau drag file foto baru ke sini</p>
                                <input type="file" class="file-upload-input" name="foto" id="foto" accept="image/*" style="display: none;" data-preview="previewFoto">
                                <div id="previewFoto" class="mt-3">
                                    <?php if($item['foto'] !== 'no-image.png'): ?>
                                        <div class="mb-2"><small class="text-muted">Foto saat ini:</small></div>
                                        <img src="uploads/<?= htmlspecialchars($item['foto']) ?>" class="preview-image" alt="Current Photo">
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-custom">Nama Barang *</label>
                                <input type="text" class="form-control form-control-custom" name="nama_barang" value="<?= htmlspecialchars($item['nama_barang']) ?>" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label-custom">Kategori *</label>
                                <select class="form-select form-select-custom" name="kategori" required>
                                    <option value="">Pilih Kategori...</option>
                                    <?php foreach($kategori_list as $kat): ?>
                                        <option value="<?= $kat ?>" <?= $item['kategori'] == $kat ? 'selected' : '' ?>><?= $kat ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">Lokasi <?= $type === 'hilang' ? 'Terakhir Terlihat' : 'Penemuan' ?> *</label>
                                <input type="text" class="form-control form-control-custom" name="lokasi" value="<?= htmlspecialchars($item['lokasi']) ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">Tanggal <?= ucfirst($type) ?> *</label>
                                <input type="date" class="form-control form-control-custom" name="tanggal" value="<?= $item[$tanggal_field] ?>" required max="<?= date('Y-m-d') ?>">
                            </div>

                            <div class="col-12">
                                <label class="form-label-custom">Deskripsi / Kondisi Barang *</label>
                                <textarea class="form-control form-control-custom" name="deskripsi" rows="4" required><?= htmlspecialchars($item['deskripsi']) ?></textarea>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top" style="border-color: rgba(255,255,255,0.1) !important;">
                            <button type="submit" class="btn btn-gradient w-100 py-3" style="font-size: 1.05rem;">
                                <i class="bi bi-save me-2"></i> Simpan Perubahan
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>
