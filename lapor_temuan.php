<?php
// LAPOR_TEMUAN.PHP - Halaman Pelaporan Barang Temuan

require_once 'session.php';
requireLogin();

// Array kategori dari config
$kategori_list = unserialize(KATEGORI_LIST);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = $_SESSION['id_user'];
    $nama_barang = $conn->real_escape_string(trim($_POST['nama_barang']));
    $kategori = $conn->real_escape_string(trim($_POST['kategori']));
    $lokasi = $conn->real_escape_string(trim($_POST['lokasi']));
    $tanggal_temuan = $conn->real_escape_string(trim($_POST['tanggal_temuan']));
    $deskripsi = $conn->real_escape_string(trim($_POST['deskripsi']));
    
    $foto_name = 'no-image.png';

    // Proses upload foto jika ada
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
        $file_name = $_FILES['foto']['name'];
        $file_tmp = $_FILES['foto']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_ext)) {
            $new_name = 'temuan_' . time() . '.' . $file_ext;
            $upload_path = UPLOAD_DIR . $new_name;

            // Buat folder uploads jika belum ada
            if (!is_dir(UPLOAD_DIR)) {
                mkdir(UPLOAD_DIR, 0777, true);
            }

            if (move_uploaded_file($file_tmp, $upload_path)) {
                $foto_name = $new_name;
            }
        }
    }

    $query = "INSERT INTO barang_temuan (id_user, nama_barang, kategori, lokasi, tanggal_temuan, deskripsi, foto, status) 
              VALUES ($id_user, '$nama_barang', '$kategori', '$lokasi', '$tanggal_temuan', '$deskripsi', '$foto_name', 'Disimpan')";

    if ($conn->query($query)) {
        header("Location: data_barang.php?alert=tambah_success");
        exit();
    } else {
        $error = "Terjadi kesalahan saat menyimpan data.";
    }
}

$page_title = 'Lapor Barang Temuan';
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/topbar.php';
?>

    <div class="container-fluid dashboard-content">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <div class="page-header text-center">
                    <h2>Lapor Barang Temuan</h2>
                    <p>Bantu pemilik menemukan barangnya yang hilang dengan mengisi form ini</p>
                </div>

                <?php if(isset($error)): ?>
                    <div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.15); color: #FCA5A5; border: 1px solid rgba(239, 68, 68, 0.3);">
                        <i class="bi bi-exclamation-circle me-2"></i> <?= $error ?>
                    </div>
                <?php endif; ?>

                <div class="form-card animate-on-scroll">
                    <form method="POST" action="" enctype="multipart/form-data" id="formLaporTemuan" data-loading onsubmit="return validateForm('formLaporTemuan')">
                        
                        <!-- Upload Foto -->
                        <div class="mb-4 text-center">
                            <label class="form-label-custom d-block mb-3">Foto Barang (Opsional namun sangat disarankan)</label>
                            <div class="upload-area" id="uploadArea">
                                <i class="bi bi-camera"></i>
                                <p class="mb-0">Klik atau drag file foto ke sini</p>
                                <input type="file" class="file-upload-input" name="foto" id="foto" accept="image/*" style="display: none;" data-preview="previewFoto">
                                <div id="previewFoto"></div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-custom">Nama Barang *</label>
                                <input type="text" class="form-control form-control-custom" name="nama_barang" required placeholder="Contoh: Kunci Motor Honda">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label-custom">Kategori *</label>
                                <select class="form-select form-select-custom" name="kategori" required>
                                    <option value="">Pilih Kategori...</option>
                                    <?php foreach($kategori_list as $kat): ?>
                                        <option value="<?= $kat ?>"><?= $kat ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">Lokasi Penemuan *</label>
                                <input type="text" class="form-control form-control-custom" name="lokasi" required placeholder="Contoh: Halaman Parkir">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">Tanggal Ditemukan *</label>
                                <input type="date" class="form-control form-control-custom" name="tanggal_temuan" required max="<?= date('Y-m-d') ?>">
                            </div>

                            <div class="col-12">
                                <label class="form-label-custom">Deskripsi / Kondisi Barang *</label>
                                <textarea class="form-control form-control-custom" name="deskripsi" rows="4" required placeholder="Sebutkan ciri-ciri khusus barang atau kondisi saat ditemukan..."></textarea>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top" style="border-color: rgba(255,255,255,0.1) !important;">
                            <button type="submit" class="btn btn-gradient w-100 py-3" style="font-size: 1.05rem; background: linear-gradient(135deg, #10B981, #06B6D4);">
                                <i class="bi bi-box-seam me-2"></i> Laporkan Barang Temuan
                            </button>
                        </div>

                    </form>
                </div>

            </div>

        </div>
    </div>
<?php include 'includes/footer.php'; ?>