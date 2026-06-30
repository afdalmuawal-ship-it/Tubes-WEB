<?php
// HAPUS_BARANG.PHP - Script Proses Hapus Data Laporan

require_once 'session.php';
requireLogin();

if (isset($_GET['id']) && isset($_GET['type'])) {
    $id = (int)$_GET['id'];
    $type = $_GET['type'];
    $current_user_id = $_SESSION['id_user'];

    if ($type === 'hilang') {
        // Ambil data foto dulu sebelum dihapus
        $query = "SELECT foto FROM barang_hilang WHERE id_barang = ? AND id_user = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $id, $current_user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $item = $result->fetch_assoc();
            // Hapus file foto
            if ($item['foto'] !== 'no-image.png' && file_exists(UPLOAD_DIR . $item['foto'])) {
                unlink(UPLOAD_DIR . $item['foto']);
            }
            // Hapus data dari db
            $conn->query("DELETE FROM barang_hilang WHERE id_barang = $id");
        }
    } else if ($type === 'temuan') {
        // Ambil data foto dulu sebelum dihapus
        $query = "SELECT foto FROM barang_temuan WHERE id_temuan = ? AND id_user = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $id, $current_user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $item = $result->fetch_assoc();
            // Hapus file foto
            if ($item['foto'] !== 'no-image.png' && file_exists(UPLOAD_DIR . $item['foto'])) {
                unlink(UPLOAD_DIR . $item['foto']);
            }
            // Hapus data dari db
            $conn->query("DELETE FROM barang_temuan WHERE id_temuan = $id");
        }
    }
}

// Selalu redirect ke data_barang (berhasil atau gagal tidak perlu ditunjukkan detail)
header("Location: data_barang.php?alert=delete_success");
exit();
?>
