<?php
$conn = mysqli_connect("localhost", "root", "");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$sql = "CREATE DATABASE IF NOT EXISTS lostly_db";

if (mysqli_query($conn, $sql)) {
    echo "Database lostly_db berhasil dibuat";
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>