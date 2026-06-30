<?php
// INDEX.PHP - Landing Page Website Lostly
// Halaman utama yang tampil untuk semua pengunjung

// Mulai session untuk cek status login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'koneksi.php';

// -- Ambil statistik untuk ditampilkan --
$totalUsers = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$totalHilang = $conn->query("SELECT COUNT(*) as total FROM barang_hilang")->fetch_assoc()['total'];
$totalTemuan = $conn->query("SELECT COUNT(*) as total FROM barang_temuan")->fetch_assoc()['total'];
$totalKembali = $conn->query("SELECT COUNT(*) as total FROM barang_hilang WHERE status='Dikembalikan'")->fetch_assoc()['total'];
$totalKembali += $conn->query("SELECT COUNT(*) as total FROM barang_temuan WHERE status='Dikembalikan'")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Lostly - Platform pelaporan dan pencarian barang hilang & temuan di lingkungan kampus. Temukan barangmu yang hilang dengan mudah.">
    <title>Lostly - Temukan Barang Hilangmu</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="style/style.css" rel="stylesheet">
</head>
<body>

    <!--  LOADING OVERLAY -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
        <div class="loading-text">Memuat Lostly...</div>
    </div>

    <!--  NAVBAR - Navigasi Utama -->
    <nav class="navbar navbar-expand-lg navbar-lostly fixed-top">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-search-heart"></i> Lostly
            </a>

            <!-- Hamburger Menu (Mobile) -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu Items -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#hero">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#tentang">Tentang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#cara-kerja">Cara Kerja</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#keunggulan">Keunggulan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#faq">FAQ</a>
                    </li>
                </ul>

                <!-- Tombol Login/Dashboard -->
                <div class="d-flex gap-2">
                    <?php if (isset($_SESSION['id_user'])): ?>
                        <!-- Jika sudah login, tampilkan tombol Dashboard -->
                        <a href="dashboard.php" class="btn btn-nav">
                            <i class="bi bi-grid-fill me-1"></i> Dashboard
                        </a>
                    <?php else: ?>
                        <!-- Jika belum login, tampilkan tombol Login & Register -->
                        <a href="login.php" class="btn btn-nav">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                        </a>
                        <a href="register.php" class="btn btn-gradient btn-sm-gradient">
                            Daftar <i class="bi bi-arrow-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero-section" id="hero">
        <div class="container">
            <div class="row align-items-center">
                <!-- Konten Hero (Kiri) -->
                <div class="col-lg-6 hero-content">
                    <!-- Badge -->
                    <div class="hero-badge">
                        <i class="bi bi-lightning-fill"></i>
                        Platform Pencarian #1 di Kampus
                    </div>

                    <!-- Judul Hero -->
                    <h1 class="hero-title">
                        Kehilangan Barang?<br>
                        <span class="highlight">Temukan di Lostly.</span>
                    </h1>

                    <!-- Subjudul -->
                    <p class="hero-subtitle">
                        Platform pelaporan dan pencarian barang hilang & temuan terpercaya 
                        di lingkungan kampus. Laporkan, cari, dan temukan barangmu dengan mudah.
                    </p>

                    <!-- Tombol CTA -->
                    <div class="hero-buttons">
                        <a href="register.php" class="btn btn-gradient">
                            <i class="bi bi-rocket-takeoff"></i> Mulai Sekarang
                        </a>
                        <a href="#cara-kerja" class="btn btn-outline-glass">
                            <i class="bi bi-play-circle"></i> Cara Kerja
                        </a>
                    </div>
                </div>

                <!-- Ilustrasi Hero (Kanan) -->
                <div class="col-lg-6 mt-5 mt-lg-0">
                    <div class="hero-illustration">
                        <div class="hero-card-float">
                            <!-- Icon kartu -->
                            <div class="card-icon">
                                <i class="bi bi-search-heart text-white"></i>
                            </div>
                            <h4>Laporan Terbaru</h4>
                            <p>Barang yang baru dilaporkan</p>

                            <!-- Item preview -->
                            <div class="hero-card-item">
                                <i class="bi bi-laptop"></i>
                                <div>
                                    <strong>Laptop ASUS</strong>
                                    <small class="d-block" style="color: rgba(255,255,255,0.5)">Perpustakaan • Hari ini</small>
                                </div>
                            </div>
                            <div class="hero-card-item">
                                <i class="bi bi-wallet2"></i>
                                <div>
                                    <strong>Dompet Kulit</strong>
                                    <small class="d-block" style="color: rgba(255,255,255,0.5)">Kantin • Kemarin</small>
                                </div>
                            </div>
                            <div class="hero-card-item">
                                <i class="bi bi-key"></i>
                                <div>
                                    <strong>Kunci Motor</strong>
                                    <small class="d-block" style="color: rgba(255,255,255,0.5)">Fakultas Teknik • 2 hari lalu</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--  TENTANG LOSTLY -->
    <section class="section about-section" id="tentang">
        <div class="container">
            <div class="row align-items-center">
                <!-- Konten Tentang (Kiri) -->
                <div class="col-lg-6 mb-5 mb-lg-0 animate-on-scroll slide-left">
                    <div class="hero-badge mb-3">
                        <i class="bi bi-info-circle"></i> Tentang Kami
                    </div>
                    <h2 class="section-title text-start mb-3">
                        Apa Itu <span class="text-gradient">Lostly</span>?
                    </h2>
                    <p class="text-start" style="color: rgba(255,255,255,0.7); font-size: 1.05rem; line-height: 1.8; margin-bottom: 30px;">
                        Lostly adalah platform digital yang membantu civitas akademika kampus 
                        dalam melaporkan dan mencari barang hilang serta barang temuan. 
                        Dengan sistem yang modern dan mudah digunakan, kami menghubungkan 
                        pemilik barang dengan penemu secara cepat dan efisien.
                    </p>

                    <!-- Fitur Highlights -->
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-start gap-3">
                                <div class="about-icon-box flex-shrink-0" style="width:45px;height:45px;font-size:1rem;">
                                    <i class="bi bi-shield-check text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1" style="font-weight:600;">Aman & Terpercaya</h6>
                                    <small style="color: rgba(255,255,255,0.5);">Data dilindungi dengan sistem keamanan</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-start gap-3">
                                <div class="about-icon-box flex-shrink-0" style="width:45px;height:45px;font-size:1rem; background: linear-gradient(135deg, #8B5CF6, #EC4899);">
                                    <i class="bi bi-lightning-fill text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1" style="font-weight:600;">Cepat & Mudah</h6>
                                    <small style="color: rgba(255,255,255,0.5);">Lapor dan cari dalam hitungan menit</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-start gap-3">
                                <div class="about-icon-box flex-shrink-0" style="width:45px;height:45px;font-size:1rem; background: linear-gradient(135deg, #10B981, #06B6D4);">
                                    <i class="bi bi-people-fill text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1" style="font-weight:600;">Komunitas Kampus</h6>
                                    <small style="color: rgba(255,255,255,0.5);">Khusus untuk civitas akademika</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-start gap-3">
                                <div class="about-icon-box flex-shrink-0" style="width:45px;height:45px;font-size:1rem; background: linear-gradient(135deg, #F59E0B, #EF4444);">
                                    <i class="bi bi-phone-fill text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1" style="font-weight:600;">Responsive</h6>
                                    <small style="color: rgba(255,255,255,0.5);">Akses dari perangkat apapun</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ilustrasi (Kanan) -->
                <div class="col-lg-6 animate-on-scroll slide-right">
                    <div class="glass-card p-4 text-center">
                        <i class="bi bi-search-heart" style="font-size: 6rem; opacity: 0.3; display:block; margin-bottom:20px;"></i>
                        <h4 style="font-weight:700; margin-bottom:10px;">Misi Kami</h4>
                        <p style="color:rgba(255,255,255,0.6); font-size:0.95rem;">
                            Menghubungkan setiap barang hilang dengan pemiliknya melalui 
                            teknologi dan kolaborasi komunitas kampus.
                        </p>
                        <div class="row g-3 mt-3">
                            <div class="col-4">
                                <div style="font-size:1.8rem; font-weight:800;" class="text-gradient"><?= $totalUsers ?></div>
                                <small style="color:rgba(255,255,255,0.5)">Pengguna</small>
                            </div>
                            <div class="col-4">
                                <div style="font-size:1.8rem; font-weight:800;" class="text-gradient"><?= $totalHilang + $totalTemuan ?></div>
                                <small style="color:rgba(255,255,255,0.5)">Laporan</small>
                            </div>
                            <div class="col-4">
                                <div style="font-size:1.8rem; font-weight:800;" class="text-gradient"><?= $totalKembali ?></div>
                                <small style="color:rgba(255,255,255,0.5)">Kembali</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--  CARA KERJA -->
    <section class="section" id="cara-kerja">
        <div class="container">
            <h2 class="section-title animate-on-scroll">
                Bagaimana <span class="text-gradient">Cara Kerjanya</span>?
            </h2>
            <p class="section-subtitle animate-on-scroll">
                Tiga langkah mudah untuk menemukan barang hilangmu
            </p>

            <div class="row g-4">
                <!-- Langkah 1 -->
                <div class="col-md-4 animate-on-scroll animate-delay-1">
                    <div class="glass-card text-center h-100 position-relative">
                        <div class="step-number">1</div>
                        <h5 style="font-weight:700; margin-bottom:12px;">Buat Laporan</h5>
                        <p style="color: rgba(255,255,255,0.6); font-size:0.9rem;">
                            Daftarkan akun dan buat laporan barang hilang atau barang temuan 
                            dengan detail lengkap dan foto.
                        </p>
                        <i class="bi bi-pencil-square" style="font-size:2.5rem; opacity:0.15; margin-top:10px;"></i>
                        <!-- Connector line -->
                        <div class="step-connector d-none d-md-block"></div>
                    </div>
                </div>

                <!-- Langkah 2 -->
                <div class="col-md-4 animate-on-scroll animate-delay-2">
                    <div class="glass-card text-center h-100 position-relative">
                        <div class="step-number">2</div>
                        <h5 style="font-weight:700; margin-bottom:12px;">Cari & Temukan</h5>
                        <p style="color: rgba(255,255,255,0.6); font-size:0.9rem;">
                            Gunakan fitur pencarian dan filter untuk menemukan barang yang 
                            cocok dengan deskripsi barangmu.
                        </p>
                        <i class="bi bi-search" style="font-size:2.5rem; opacity:0.15; margin-top:10px;"></i>
                        <div class="step-connector d-none d-md-block"></div>
                    </div>
                </div>

                <!-- Langkah 3 -->
                <div class="col-md-4 animate-on-scroll animate-delay-3">
                    <div class="glass-card text-center h-100">
                        <div class="step-number">3</div>
                        <h5 style="font-weight:700; margin-bottom:12px;">Ambil Kembali</h5>
                        <p style="color: rgba(255,255,255,0.6); font-size:0.9rem;">
                            Hubungi pelapor dan ambil kembali barangmu. 
                            Update status laporan setelah barang ditemukan.
                        </p>
                        <i class="bi bi-hand-thumbs-up" style="font-size:2.5rem; opacity:0.15; margin-top:10px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- KEUNGGULAN -->
    <section class="section about-section" id="keunggulan">
        <div class="container">
            <h2 class="section-title animate-on-scroll">
                Keunggulan <span class="text-gradient">Lostly</span>
            </h2>
            <p class="section-subtitle animate-on-scroll">
                Fitur-fitur unggulan yang membuat Lostly berbeda
            </p>

            <div class="row g-4">
                <!-- Keunggulan 1 -->
                <div class="col-md-6 col-lg-4 animate-on-scroll animate-delay-1">
                    <div class="glass-card h-100">
                        <div class="feature-icon blue">
                            <i class="bi bi-search text-white"></i>
                        </div>
                        <h5 style="font-weight:700; margin-bottom:10px;">Pencarian Cepat</h5>
                        <p style="color: rgba(255,255,255,0.6); font-size:0.9rem;">
                            Fitur pencarian real-time dengan filter kategori, lokasi, dan status untuk menemukan barang dengan cepat.
                        </p>
                    </div>
                </div>

                <!-- Keunggulan 2 -->
                <div class="col-md-6 col-lg-4 animate-on-scroll animate-delay-2">
                    <div class="glass-card h-100">
                        <div class="feature-icon green">
                            <i class="bi bi-camera text-white"></i>
                        </div>
                        <h5 style="font-weight:700; margin-bottom:10px;">Upload Foto</h5>
                        <p style="color: rgba(255,255,255,0.6); font-size:0.9rem;">
                            Sertakan foto barang untuk memudahkan identifikasi dan verifikasi pemilik yang sesungguhnya.
                        </p>
                    </div>
                </div>

                <!-- Keunggulan 3 -->
                <div class="col-md-6 col-lg-4 animate-on-scroll animate-delay-3">
                    <div class="glass-card h-100">
                        <div class="feature-icon purple">
                            <i class="bi bi-bell text-white"></i>
                        </div>
                        <h5 style="font-weight:700; margin-bottom:10px;">Notifikasi Status</h5>
                        <p style="color: rgba(255,255,255,0.6); font-size:0.9rem;">
                            Pantau status laporan barangmu secara real-time, dari dilaporkan hingga dikembalikan.
                        </p>
                    </div>
                </div>

                <!-- Keunggulan 4 -->
                <div class="col-md-6 col-lg-4 animate-on-scroll animate-delay-1">
                    <div class="glass-card h-100">
                        <div class="feature-icon orange">
                            <i class="bi bi-shield-lock text-white"></i>
                        </div>
                        <h5 style="font-weight:700; margin-bottom:10px;">Keamanan Data</h5>
                        <p style="color: rgba(255,255,255,0.6); font-size:0.9rem;">
                            Password terenkripsi dan akses terlindungi dengan sistem session & autentikasi.
                        </p>
                    </div>
                </div>

                <!-- Keunggulan 5 -->
                <div class="col-md-6 col-lg-4 animate-on-scroll animate-delay-2">
                    <div class="glass-card h-100">
                        <div class="feature-icon cyan">
                            <i class="bi bi-phone text-white"></i>
                        </div>
                        <h5 style="font-weight:700; margin-bottom:10px;">Responsive Design</h5>
                        <p style="color: rgba(255,255,255,0.6); font-size:0.9rem;">
                            Akses dari smartphone, tablet, atau laptop. Tampilan menyesuaikan di semua perangkat.
                        </p>
                    </div>
                </div>

                <!-- Keunggulan 6 -->
                <div class="col-md-6 col-lg-4 animate-on-scroll animate-delay-3">
                    <div class="glass-card h-100">
                        <div class="feature-icon pink">
                            <i class="bi bi-people text-white"></i>
                        </div>
                        <h5 style="font-weight:700; margin-bottom:10px;">Komunitas Kampus</h5>
                        <p style="color: rgba(255,255,255,0.6); font-size:0.9rem;">
                            Eksklusif untuk lingkungan kampus, memastikan barang kembali ke pemilik yang tepat.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- STATISTIK -->
    <section class="section stats-section" id="statistik">
        <div class="container">
            <h2 class="section-title animate-on-scroll">
                Lostly dalam <span class="text-gradient">Angka</span>
            </h2>
            <p class="section-subtitle animate-on-scroll">
                Statistik yang menunjukkan dampak Lostly di kampus
            </p>

            <div class="row g-4 text-center">
                <!-- Statistik 1 -->
                <div class="col-6 col-md-3 animate-on-scroll animate-delay-1">
                    <div class="glass-card py-4">
                        <div class="stat-counter counter-value" data-target="<?= $totalUsers ?>">0</div>
                        <div class="stat-counter-label">Pengguna Aktif</div>
                    </div>
                </div>

                <!-- Statistik 2 -->
                <div class="col-6 col-md-3 animate-on-scroll animate-delay-2">
                    <div class="glass-card py-4">
                        <div class="stat-counter counter-value" data-target="<?= $totalHilang ?>">0</div>
                        <div class="stat-counter-label">Barang Hilang</div>
                    </div>
                </div>

                <!-- Statistik 3 -->
                <div class="col-6 col-md-3 animate-on-scroll animate-delay-3">
                    <div class="glass-card py-4">
                        <div class="stat-counter counter-value" data-target="<?= $totalTemuan ?>">0</div>
                        <div class="stat-counter-label">Barang Temuan</div>
                    </div>
                </div>

                <!-- Statistik 4 -->
                <div class="col-6 col-md-3 animate-on-scroll animate-delay-4">
                    <div class="glass-card py-4">
                        <div class="stat-counter counter-value" data-target="<?= $totalKembali ?>">0</div>
                        <div class="stat-counter-label">Dikembalikan</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ - Frequently Asked Questions -->
    <section class="section" id="faq">
        <div class="container">
            <h2 class="section-title animate-on-scroll">
                Pertanyaan <span class="text-gradient">Umum</span>
            </h2>
            <p class="section-subtitle animate-on-scroll">
                Jawaban untuk pertanyaan yang sering diajukan
            </p>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion faq-accordion animate-on-scroll" id="faqAccordion">
                        <!-- FAQ 1 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    <i class="bi bi-question-circle me-2"></i> Bagaimana cara melaporkan barang hilang?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Setelah mendaftar dan login, klik menu "Lapor Barang Hilang", lalu isi form dengan detail 
                                    barang seperti nama, kategori, lokasi terakhir terlihat, tanggal hilang, deskripsi, dan foto jika ada.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 2 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    <i class="bi bi-question-circle me-2"></i> Apakah Lostly gratis digunakan?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Ya! Lostly sepenuhnya gratis untuk seluruh civitas akademika kampus. 
                                    Cukup daftar dengan email kampus Anda dan mulai gunakan semua fitur.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 3 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    <i class="bi bi-question-circle me-2"></i> Bagaimana jika saya menemukan barang orang lain?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Anda bisa melaporkan barang temuan melalui menu "Lapor Barang Temuan". 
                                    Isi detail barang yang Anda temukan agar pemiliknya bisa mengenali dan mengklaim barang tersebut.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 4 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    <i class="bi bi-question-circle me-2"></i> Apakah data saya aman?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Tentu! Kami menggunakan enkripsi password dan sistem autentikasi yang aman. 
                                    Data pribadi Anda dilindungi dan hanya dapat diakses oleh Anda sendiri.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 5 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                    <i class="bi bi-question-circle me-2"></i> Bagaimana cara mengubah status laporan?
                                </button>
                            </h2>
                            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Masuk ke halaman "Data Barang", cari laporan Anda, lalu klik tombol "Edit". 
                                    Anda bisa mengubah status menjadi "Ditemukan" atau "Dikembalikan" sesuai kondisi terbaru.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA SECTION - Ajakan Mendaftar -->
    <section class="section cta-section">
        <div class="container">
            <div class="cta-box animate-on-scroll">
                <h2>Siap Menemukan Barang Hilangmu?</h2>
                <p>Bergabung dengan komunitas Lostly dan bantu satu sama lain menemukan barang yang hilang.</p>
                <div class="d-flex gap-3 justify-content-center flex-wrap position-relative" style="z-index:2;">
                    <a href="register.php" class="btn btn-gradient">
                        <i class="bi bi-rocket-takeoff"></i> Daftar Sekarang
                    </a>
                    <a href="login.php" class="btn btn-outline-glass">
                        <i class="bi bi-box-arrow-in-right"></i> Masuk
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container">
            <div class="row g-4">
                <!-- Brand Info -->
                <div class="col-lg-4 col-md-6">
                    <div class="footer-brand">
                        <i class="bi bi-search-heart"></i> Lostly
                    </div>
                    <p class="footer-desc">Platform pelaporan dan pencarian barang hilang & temuan terpercaya di lingkungan kampus.
                    </p>
                    <div class="social-links">
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-twitter-x"></i></a>
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-envelope"></i></a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6">
                    <h6>Navigasi</h6>
                    <ul class="footer-links">
                        <li><a href="#hero">Beranda</a></li>
                        <li><a href="#tentang">Tentang</a></li>
                        <li><a href="#cara-kerja">Cara Kerja</a></li>
                        <li><a href="#keunggulan">Keunggulan</a></li>
                    </ul>
                </div>

                <!-- Fitur -->
                <div class="col-lg-3 col-md-6">
                    <h6>Fitur</h6>
                    <ul class="footer-links">
                        <li><a href="login.php">Lapor Barang Hilang</a></li>
                        <li><a href="login.php">Lapor Barang Temuan</a></li>
                        <li><a href="login.php">Cari Barang</a></li>
                        <li><a href="login.php">Dashboard</a></li>
                    </ul>
                </div>

                <!-- Kontak -->
                <div class="col-lg-3 col-md-6">
                    <h6>Kontak</h6>
                    <ul class="footer-links">
                        <li><i class="bi bi-envelope me-2"></i> info@lostly.com</li>
                        <li><i class="bi bi-telephone me-2"></i> +62 858 0744 4742</li>
                        <li><i class="bi bi-geo-alt me-2"></i> Samata Kab. Gowa</li>
                    </ul>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> Lostly. All rights reserved. Made with <i class="bi bi-heart-fill text-danger"></i> for Campus Community.</p>
            </div>
        </div>
    </footer>

    <!--SCRIPTS-->
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom JS -->
    <script src="js/script.js"></script>
</body>
</html>
