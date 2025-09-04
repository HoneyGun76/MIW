<?php
/**
 * Flyer Gallery Component for Umroh Packages
 * Displays available umroh package flyers
 */

require_once 'config.php';

// Get flyer images from uploads directory first, then fallback to sample
$uploadsFlyerDirectory = getUploadsPath() . '/flyers/';
$sampleFlyerDirectory = 'sample/';
$flyers = [];

// First, check for uploaded flyers
if (is_dir($uploadsFlyerDirectory)) {
    $files = glob($uploadsFlyerDirectory . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    foreach ($files as $file) {
        $filename = basename($file);
        $title = ucwords(str_replace(['-', '.jpg', '.jpeg', '.png', '.gif'], [' ', '', '', '', ''], $filename));
        $flyers[] = [
            'file' => $file,
            'title' => $title,
            'filename' => $filename,
            'source' => 'uploads'
        ];
    }
}

// If no uploaded flyers found, use sample flyers
if (empty($flyers) && is_dir($sampleFlyerDirectory)) {
    $files = glob($sampleFlyerDirectory . 'umroh-*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    foreach ($files as $file) {
        $filename = basename($file);
        $title = ucwords(str_replace(['-', '.jpg', '.jpeg', '.png', '.gif'], [' ', '', '', '', ''], $filename));
        $flyers[] = [
            'file' => $file,
            'title' => $title,
            'filename' => $filename,
            'source' => 'sample'
        ];
    }
}
?>

<?php if (!empty($flyers)): ?>
<!-- Flyer Gallery Section -->
<section class="flyer-gallery py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="mb-3">Paket Umroh Tersedia</h2>
                <p class="text-muted">Pilih paket umroh yang sesuai dengan jadwal dan kebutuhan Anda</p>
            </div>
        </div>
        
        <div class="row g-4 justify-content-center">
            <?php foreach ($flyers as $index => $flyer): ?>
            <div class="col-12 col-lg-10 col-xl-8">
                <div class="flyer-card-large h-100">
                    <div class="row g-0 align-items-center">
                        <div class="col-md-6">
                            <div class="flyer-image-container-large">
                                <img src="<?= htmlspecialchars($flyer['file']) ?>" 
                                     alt="<?= htmlspecialchars($flyer['title']) ?>"
                                     class="flyer-image-large img-fluid"
                                     loading="lazy"
                                     data-bs-toggle="modal" 
                                     data-bs-target="#flyerModal<?= $index ?>">
                                <div class="flyer-overlay-large">
                                    <i class="fas fa-search-plus fa-3x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="flyer-card-body-large">
                                <h3 class="flyer-title-large mb-3"><?= htmlspecialchars($flyer['title']) ?></h3>
                                <p class="flyer-description text-muted mb-4">
                                    Paket umroh dengan fasilitas terbaik dan pelayanan berkualitas tinggi. 
                                    Dapatkan pengalaman spiritual yang tak terlupakan bersama kami.
                                </p>
                                <div class="flyer-actions-large">
                                    <button class="btn btn-primary btn-lg me-3" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#flyerModal<?= $index ?>">
                                        <i class="fas fa-eye me-2"></i> Lihat Detail
                                    </button>
                                    <a href="<?= htmlspecialchars($flyer['file']) ?>" 
                                       download="<?= htmlspecialchars($flyer['filename']) ?>"
                                       class="btn btn-outline-primary btn-lg">
                                        <i class="fas fa-download me-2"></i> Download
                                    </a>
                                </div>
                                <div class="mt-4">
                                    <a href="form_umroh.php" class="btn btn-success btn-lg w-100">
                                        <i class="fas fa-user-plus me-2"></i> Daftar Sekarang
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal for each flyer -->
            <div class="modal fade" id="flyerModal<?= $index ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?= htmlspecialchars($flyer['title']) ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="<?= htmlspecialchars($flyer['file']) ?>" 
                                 alt="<?= htmlspecialchars($flyer['title']) ?>"
                                 class="img-fluid rounded">
                        </div>
                        <div class="modal-footer">
                            <a href="<?= htmlspecialchars($flyer['file']) ?>" 
                               download="<?= htmlspecialchars($flyer['filename']) ?>"
                               class="btn btn-primary">
                                <i class="fas fa-download me-1"></i> Download Flyer
                            </a>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
/* Flyer Gallery Styles - Large Single Column Layout */
.flyer-gallery {
    background-color: #f8f9fa;
    padding: 3rem 0;
}

/* Large Card Styles */
.flyer-card-large {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    margin-bottom: 2rem;
}

.flyer-card-large:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
}

/* Large Image Container */
.flyer-image-container-large {
    position: relative;
    overflow: hidden;
    height: 400px;
    min-height: 400px;
}

.flyer-image-large {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
    cursor: pointer;
}

.flyer-image-container-large:hover .flyer-image-large {
    transform: scale(1.08);
}

.flyer-overlay-large {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(57, 163, 209, 0.85);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    cursor: pointer;
    color: white;
}

.flyer-image-container-large:hover .flyer-overlay-large {
    opacity: 1;
}

/* Large Card Body */
.flyer-card-body-large {
    padding: 3rem 2.5rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
    height: 400px;
}

.flyer-title-large {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.flyer-description {
    font-size: 1.1rem;
    line-height: 1.6;
    color: #6c757d;
}

.flyer-actions-large {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

/* Button Styles */
.btn-lg {
    padding: 0.75rem 2rem;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 8px;
}

.btn-primary {
    background-color: #39a3d1;
    border-color: #39a3d1;
}

.btn-primary:hover {
    background-color: #2a8bb8;
    border-color: #2a8bb8;
}

.btn-success {
    background-color: #28a745;
    border-color: #28a745;
    font-size: 1.2rem;
    padding: 1rem 2rem;
}

.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
}

/* Modal customizations */
.modal-lg {
    max-width: 90vw;
}

.modal-body img {
    max-height: 70vh;
    width: auto;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .flyer-card-large .row {
        flex-direction: column;
    }
    
    .flyer-card-body-large {
        height: auto;
        padding: 2rem 1.5rem;
        text-align: center;
    }
    
    .flyer-image-container-large {
        height: 300px;
        min-height: 300px;
    }
    
    .flyer-title-large {
        font-size: 1.5rem;
    }
    
    .flyer-actions-large {
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .btn-lg {
        width: 100%;
        padding: 0.875rem 1.5rem;
    }
}

@media (max-width: 576px) {
    .flyer-gallery {
        padding: 2rem 0;
    }
    
    .flyer-card-body-large {
        padding: 1.5rem 1rem;
    }
    
    .flyer-title-large {
        font-size: 1.3rem;
    }
    
    .flyer-description {
        font-size: 1rem;
    }
}
</style>
<?php endif; ?>
