<?php
// PROFIL.PHP - Halaman Profil & Pengaturan Akun

require_once 'session.php';
requireLogin();

$user = getCurrentUser();
$id_user = $user['id_user'];

// Proses Update Profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profil'])) {
    $nama = $conn->real_escape_string(trim($_POST['nama']));
    $foto_name = $user['foto'];

    // Proses upload foto profil
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
        $file_name = $_FILES['foto']['name'];
        $file_tmp = $_FILES['foto']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_ext)) {
            $new_name = 'avatar_' . $id_user . '_' . time() . '.' . $file_ext;
            $upload_path = UPLOAD_DIR . $new_name;

            if (!is_dir(UPLOAD_DIR)) {
                mkdir(UPLOAD_DIR, 0777, true);
            }

            if (move_uploaded_file($file_tmp, $upload_path)) {
                // Hapus foto lama jika bukan default
                if ($user['foto'] !== 'default.png' && file_exists(UPLOAD_DIR . $user['foto'])) {
                    unlink(UPLOAD_DIR . $user['foto']);
                }
                $foto_name = $new_name;
            }
        }
    }

    $update_query = "UPDATE users SET nama = '$nama', foto = '$foto_name' WHERE id_user = $id_user";
    if ($conn->query($update_query)) {
        // Update session
        $_SESSION['nama'] = $nama;
        $_SESSION['foto'] = $foto_name;
        header("Location: profil.php?alert=profil_success");
        exit();
    } else {
        $error = "Terjadi kesalahan saat mengupdate profil.";
    }
}

// Proses Ganti Password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ganti_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Ambil password lama dari DB
    $query = "SELECT password FROM users WHERE id_user = $id_user";
    $result = $conn->query($query);
    $db_password = $result->fetch_assoc()['password'];

    // Verifikasi password lama
    if (password_verify($old_password, $db_password)) {
        if (strlen($new_password) >= 6) {
            if ($new_password === $confirm_password) {
                // Hash password baru
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_pass = "UPDATE users SET password = '$hashed_password' WHERE id_user = $id_user";
                
                if ($conn->query($update_pass)) {
                    header("Location: profil.php?alert=password_success");
                    exit();
                } else {
                    $error_pass = "Terjadi kesalahan sistem.";
                }
            } else {
                header("Location: profil.php?alert=password_failed&msg=" . urlencode("Konfirmasi password tidak cocok"));
                exit();
            }
        } else {
            header("Location: profil.php?alert=password_failed&msg=" . urlencode("Password baru minimal 6 karakter"));
            exit();
        }
    } else {
        header("Location: profil.php?alert=password_failed&msg=" . urlencode("Password lama salah"));
        exit();
    }
}

$page_title = 'Profil Saya';
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/topbar.php';
?>

    <div class="container-fluid dashboard-content">
        <div class="row justify-content-center">
            
            <div class="col-lg-10">
                <div class="page-header text-center">
                    <h2>Pengaturan Akun</h2>
                    <p>Kelola profil dan keamanan akun Anda</p>
                </div>

                <div class="row g-4">
                    <!-- Sidebar Profil -->
                    <div class="col-md-4 animate-on-scroll slide-left">
                        <div class="form-card text-center profile-card h-100">
                            <img src="uploads/<?= htmlspecialchars($user['foto']) ?>" alt="Avatar" class="profile-avatar" id="avatarPreview"
                                 onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($user['nama']) ?>&background=3B82F6&color=fff'">
                            <h4 class="profile-name"><?= htmlspecialchars($user['nama']) ?></h4>
                            <p class="profile-email"><?= htmlspecialchars($user['email']) ?></p>
                            
                            <span class="badge-status badge-dikembalikan mb-4">
                                <i class="bi bi-check-circle me-1"></i> Terverifikasi
                            </span>

                            <div class="d-grid mt-4">
                                <button type="button" class="btn btn-outline-glass mb-2" onclick="document.getElementById('fotoInput').click()">
                                    <i class="bi bi-camera me-1"></i> Ganti Foto
                                </button>
                                <button type="button" class="btn btn-sm btn-gradient" style="background: linear-gradient(135deg, #EF4444, #B91C1C);" onclick="confirmLogout()">
                                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Form Edit -->
                    <div class="col-md-8 animate-on-scroll slide-right">
                        
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger mb-4" style="background: rgba(239, 68, 68, 0.15); color: #FCA5A5; border: 1px solid rgba(239, 68, 68, 0.3);">
                                <?= $error ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(isset($error_pass)): ?>
                            <div class="alert alert-danger mb-4" style="background: rgba(239, 68, 68, 0.15); color: #FCA5A5; border: 1px solid rgba(239, 68, 68, 0.3);">
                                <?= $error_pass ?>
                            </div>
                        <?php endif; ?>

                        <!-- Form Informasi Dasar -->
                        <div class="form-card mb-4">
                            <h5 class="mb-4 text-white" style="font-weight:600;"><i class="bi bi-person me-2 text-primary"></i>Informasi Dasar</h5>
                            <form method="POST" action="" enctype="multipart/form-data" id="formProfil">
                                <input type="hidden" name="update_profil" value="1">
                                <input type="file" name="foto" id="fotoInput" style="display:none;" accept="image/*" onchange="previewAvatar(this)">
                                
                                <div class="mb-3">
                                    <label class="form-label-custom">Nama Lengkap</label>
                                    <input type="text" class="form-control form-control-custom" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label-custom">Alamat Email (Tidak dapat diubah)</label>
                                    <input type="email" class="form-control form-control-custom" value="<?= htmlspecialchars($user['email']) ?>" disabled style="background: rgba(255,255,255,0.03); opacity: 0.7;">
                                </div>

                                <button type="submit" class="btn btn-gradient px-4">
                                    <i class="bi bi-save me-2"></i> Simpan Profil
                                </button>
                            </form>
                        </div>

                        <!-- Form Keamanan -->
                        <div class="form-card">
                            <h5 class="mb-4 text-white" style="font-weight:600;"><i class="bi bi-shield-lock me-2 text-primary"></i>Keamanan Akun</h5>
                            <form method="POST" action="" id="formPassword" data-loading onsubmit="return validateForm('formPassword')">
                                <input type="hidden" name="ganti_password" value="1">
                                
                                <div class="mb-3 form-floating-custom">
                                    <label class="form-label-custom">Password Lama</label>
                                    <div class="position-relative mt-1">
                                        <i class="bi bi-lock input-icon" style="top: 22px;"></i>
                                        <input type="password" class="form-control form-control-custom" name="old_password" required>
                                        <button type="button" class="toggle-password" style="top: 22px;"><i class="bi bi-eye"></i></button>
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6 form-floating-custom">
                                        <label class="form-label-custom">Password Baru</label>
                                        <div class="position-relative mt-1">
                                            <i class="bi bi-key input-icon" style="top: 22px;"></i>
                                            <input type="password" class="form-control form-control-custom" name="new_password" id="password" required minlength="6">
                                            <button type="button" class="toggle-password" style="top: 22px;"><i class="bi bi-eye"></i></button>
                                        </div>
                                    </div>
                                    <div class="col-md-6 form-floating-custom">
                                        <label class="form-label-custom">Konfirmasi Password Baru</label>
                                        <div class="position-relative mt-1">
                                            <i class="bi bi-key-fill input-icon" style="top: 22px;"></i>
                                            <input type="password" class="form-control form-control-custom" name="confirm_password" id="confirm_password" required>
                                            <button type="button" class="toggle-password" style="top: 22px;"><i class="bi bi-eye"></i></button>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-outline-glass px-4">
                                    <i class="bi bi-shield-check me-2"></i> Update Password
                                </button>
                            </form>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>
<script>
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatarPreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
