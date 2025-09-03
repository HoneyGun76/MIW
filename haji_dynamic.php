<?php
require_once "config.php";

// Get all Haji packages
$stmt = $conn->prepare("SELECT * FROM data_paket WHERE jenis_paket = 'Haji' ORDER BY tanggal_keberangkatan ASC");
$stmt->execute();
$hajiPackages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paket Haji - MIW Travel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="landing_styles.css">
</head>
<body>
    <?php include 'navigation.php'; ?>
    
    <div class="container-fluid bg-light py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h1 class="display-4 text-success fw-bold">Paket Haji</h1>
                    <p class="lead">Menunaikan rukun Islam yang kelima</p>
                </div>
            </div>
            
            <div class="row">
                <?php foreach ($hajiPackages as $package): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <?php if (!empty($package['flyer_image'])): ?>
                        <img src="<?= htmlspecialchars($package['flyer_image']) ?>" 
                             class="card-img-top" 
                             alt="<?= htmlspecialchars($package['program_pilihan']) ?>"
                             style="height: 250px; object-fit: cover;">
                        <?php else: ?>
                        <div class="card-img-top bg-gradient d-flex align-items-center justify-content-center" 
                             style="height: 250px; background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);">
                            <div class="text-center text-white">
                                <i class="fas fa-mosque fa-3x mb-2"></i>
                                <h5><?= htmlspecialchars($package['program_pilihan']) ?></h5>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-success"><?= htmlspecialchars($package['program_pilihan']) ?></h5>
                            <p class="text-muted">
                                <i class="fas fa-calendar-alt"></i> 
                                <?= date('d M Y', strtotime($package['tanggal_keberangkatan'])) ?>
                            </p>
                            
                            <div class="pricing mb-3">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <small class="text-muted">Quad</small><br>
                                        <strong><?= $package['currency'] == 'USD' ? '$' : 'Rp ' ?><?= number_format($package['base_price_quad'], 0, ',', '.') ?></strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted">Triple</small><br>
                                        <strong><?= $package['currency'] == 'USD' ? '$' : 'Rp ' ?><?= number_format($package['base_price_triple'], 0, ',', '.') ?></strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted">Double</small><br>
                                        <strong><?= $package['currency'] == 'USD' ? '$' : 'Rp ' ?><?= number_format($package['base_price_double'], 0, ',', '.') ?></strong>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-auto">
                                <a href="form_haji.php?pak_id=<?= $package['pak_id'] ?>" 
                                   class="btn btn-success w-100">
                                    <i class="fas fa-paper-plane"></i> Daftar Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js"></script>
</body>
</html>
