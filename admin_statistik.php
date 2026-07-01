<?php
// ADMIN_STATISTIK.PHP - Grafik Statistik
require_once 'session.php';
requireAdmin();

// Data untuk Bar Chart (Laporan 6 bulan terakhir)
$months = [];
$lostCounts = [];
$foundCounts = [];

for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $months[] = date('M Y', strtotime($month . '-01'));
    
    $hilang = $conn->query("SELECT COUNT(*) as total FROM barang_hilang WHERE DATE_FORMAT(created_at, '%Y-%m') = '$month'")->fetch_assoc()['total'];
    $lostCounts[] = $hilang;
    
    $temuan = $conn->query("SELECT COUNT(*) as total FROM barang_temuan WHERE DATE_FORMAT(created_at, '%Y-%m') = '$month'")->fetch_assoc()['total'];
    $foundCounts[] = $temuan;
}

// Data untuk Pie Chart
$totalHilang = $conn->query("SELECT COUNT(*) as total FROM barang_hilang")->fetch_assoc()['total'];
$totalTemuan = $conn->query("SELECT COUNT(*) as total FROM barang_temuan")->fetch_assoc()['total'];

$totalKembaliHilang = $conn->query("SELECT COUNT(*) as total FROM barang_hilang WHERE status = 'Dikembalikan'")->fetch_assoc()['total'];
$totalKembaliTemuan = $conn->query("SELECT COUNT(*) as total FROM barang_temuan WHERE status = 'Dikembalikan'")->fetch_assoc()['total'];
$totalKembali = $totalKembaliHilang + $totalKembaliTemuan;

// Untuk chart, kita pisahkan Hilang Belum Kembali, Temuan Belum Kembali, dan Dikembalikan
$aktifHilang = max(0, $totalHilang - $totalKembaliHilang);
$aktifTemuan = max(0, $totalTemuan - $totalKembaliTemuan);

$page_title = 'Statistik';
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/topbar.php';
?>

<!-- Tambahkan Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container-fluid dashboard-content">
    <div class="page-header mb-4">
        <h2>Statistik Sistem</h2>
        <p>Visualisasi data laporan barang hilang dan temuan</p>
    </div>

    <div class="row g-4">
        <!-- Bar Chart -->
        <div class="col-lg-8 animate-on-scroll">
            <div class="card bg-glass p-3 h-100">
                <h5 class="text-white mb-4">Laporan 6 Bulan Terakhir</h5>
                <canvas id="barChart"></canvas>
            </div>
        </div>
        
        <!-- Pie Chart -->
        <div class="col-lg-4 animate-on-scroll animate-delay-1">
            <div class="card bg-glass p-3 h-100">
                <h5 class="text-white mb-4">Status Barang Global</h5>
                <canvas id="pieChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
// Konfigurasi umum Chart.js untuk dark theme
Chart.defaults.color = 'rgba(255, 255, 255, 0.7)';
Chart.defaults.scale.grid.color = 'rgba(255, 255, 255, 0.1)';

// Bar Chart
const barCtx = document.getElementById('barChart').getContext('2d');
new Chart(barCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($months) ?>,
        datasets: [
            {
                label: 'Barang Hilang',
                data: <?= json_encode($lostCounts) ?>,
                backgroundColor: 'rgba(239, 68, 68, 0.8)', // Merah
                borderColor: 'rgba(239, 68, 68, 1)',
                borderWidth: 1
            },
            {
                label: 'Barang Temuan',
                data: <?= json_encode($foundCounts) ?>,
                backgroundColor: 'rgba(34, 197, 94, 0.8)', // Hijau
                borderColor: 'rgba(34, 197, 94, 1)',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Pie Chart
const pieCtx = document.getElementById('pieChart').getContext('2d');
new Chart(pieCtx, {
    type: 'doughnut',
    data: {
        labels: ['Barang Hilang', 'Barang Temuan', 'Dikembalikan'],
        datasets: [{
            data: [<?= $aktifHilang ?>, <?= $aktifTemuan ?>, <?= $totalKembali ?>],
            backgroundColor: [
                'rgba(239, 68, 68, 0.8)', // Merah
                'rgba(34, 197, 94, 0.8)', // Hijau
                'rgba(168, 85, 247, 0.8)' // Ungu
            ],
            borderColor: [
                'rgba(239, 68, 68, 1)',
                'rgba(34, 197, 94, 1)',
                'rgba(168, 85, 247, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>
