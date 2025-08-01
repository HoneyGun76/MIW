<?php
/**
 * MIW Travel Management System - Main Entry Point
 * 
 * This is the main landing page for the MIW Travel Management System
 * Railway Deployment Ready
 */

require_once 'config.php';

// Check if database connection is working
try {
    $stmt = $conn->query("SELECT 1");
    $db_status = "Connected";
    $db_color = "success";
} catch (Exception $e) {
    $db_status = "Connection Failed";
    $db_color = "danger";
}

// Get environment info
$environment = getCurrentEnvironment();
$is_production = isProduction();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MIW Travel Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0;
        }
        .service-card {
            transition: transform 0.3s;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .service-card:hover {
            transform: translateY(-5px);
        }
        .status-badge {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <!-- Status Badge -->
    <div class="status-badge">
        <span class="badge bg-<?php echo $db_color; ?>">
            <i class="bi bi-database"></i> <?php echo $db_status; ?>
        </span>
        <span class="badge bg-info">
            <i class="bi bi-gear"></i> <?php echo ucfirst($environment); ?>
        </span>
    </div>

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4">
                        <i class="bi bi-airplane-fill"></i>
                        MIW Travel Management System
                    </h1>
                    <p class="lead mb-5">
                        Sistem Manajemen Perjalanan Haji dan Umroh Terpadu
                    </p>
                    <?php if (!$is_production): ?>
                    <div class="alert alert-warning d-inline-block">
                        <i class="bi bi-exclamation-triangle"></i>
                        Development Mode - All features available
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold">Layanan Kami</h2>
                    <p class="text-muted">Pilih layanan sesuai kebutuhan perjalanan spiritual Anda</p>
                </div>
            </div>
            
            <div class="row g-4">
                <!-- Haji Registration -->
                <div class="col-md-6 col-lg-4">
                    <div class="card service-card h-100">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-building display-4 text-primary"></i>
                            </div>
                            <h5 class="card-title">Pendaftaran Haji</h5>
                            <p class="card-text">Daftar untuk perjalanan haji dengan berbagai paket pilihan</p>
                            <a href="form_haji.php" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Daftar Haji
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Umroh Registration -->
                <div class="col-md-6 col-lg-4">
                    <div class="card service-card h-100">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-moon-stars display-4 text-success"></i>
                            </div>
                            <h5 class="card-title">Pendaftaran Umroh</h5>
                            <p class="card-text">Daftar untuk perjalanan umroh dengan jadwal fleksibel</p>
                            <a href="form_umroh.php" class="btn btn-success">
                                <i class="bi bi-plus-circle"></i> Daftar Umroh
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Admin Panel -->
                <div class="col-md-6 col-lg-4">
                    <div class="card service-card h-100">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-gear-fill display-4 text-warning"></i>
                            </div>
                            <h5 class="card-title">Panel Admin</h5>
                            <p class="card-text">Kelola pendaftaran, manifest, dan data jamaah</p>
                            <a href="admin_dashboard.php" class="btn btn-warning">
                                <i class="bi bi-shield-lock"></i> Admin Panel
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Cancellation -->
                <div class="col-md-6 col-lg-4">
                    <div class="card service-card h-100">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-x-circle display-4 text-danger"></i>
                            </div>
                            <h5 class="card-title">Pembatalan</h5>
                            <p class="card-text">Ajukan pembatalan perjalanan dengan prosedur yang mudah</p>
                            <a href="form_pembatalan.php" class="btn btn-danger">
                                <i class="bi bi-file-text"></i> Ajukan Pembatalan
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Database Init -->
                <?php if (!$is_production): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card service-card h-100">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-database-gear display-4 text-info"></i>
                            </div>
                            <h5 class="card-title">Setup Database</h5>
                            <p class="card-text">Inisialisasi database untuk pertama kali</p>
                            <a href="init_database_universal.php" class="btn btn-info">
                                <i class="bi bi-database-add"></i> Setup DB
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- System Status -->
                <div class="col-md-6 col-lg-4">
                    <div class="card service-card h-100">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-activity display-4 text-secondary"></i>
                            </div>
                            <h5 class="card-title">Status Sistem</h5>
                            <p class="card-text">Cek status dan diagnostik sistem</p>
                            <a href="database_diagnostic.php" class="btn btn-secondary">
                                <i class="bi bi-clipboard-check"></i> Cek Status
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- System Info -->
    <section class="bg-light py-4">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Informasi Sistem</h6>
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <small class="text-muted">Environment</small>
                                    <div class="fw-bold"><?php echo ucfirst($environment); ?></div>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Database</small>
                                    <div class="fw-bold text-<?php echo $db_color; ?>"><?php echo $db_status; ?></div>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">PHP Version</small>
                                    <div class="fw-bold"><?php echo PHP_VERSION; ?></div>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Server Time</small>
                                    <div class="fw-bold"><?php echo date('H:i:s T'); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p class="mb-0">
                <i class="bi bi-c-circle"></i> 2024 MIW Travel Management System
                <?php if (!$is_production): ?>
                <span class="badge bg-warning text-dark ms-2">Development</span>
                <?php endif; ?>
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
