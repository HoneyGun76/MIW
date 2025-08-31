<?php
/**
 * Flyer Gallery Component for Umroh Packages
 * Displays available umroh package flyers
 */

// Get flyer images from sample directory
$flyerDirectory = 'sample/';
$flyers = [];

if (is_dir($flyerDirectory)) {
    $files = glob($flyerDirectory . 'umroh-*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    foreach ($files as $file) {
        $filename = basename($file);
        $title = ucwords(str_replace(['-', '.jpg', '.jpeg', '.png', '.gif'], [' ', '', '', '', ''], $filename));
        $flyers[] = [
            'file' => $file,
            'title' => $title,
            'filename' => $filename
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
        
        <div class="row g-4">
            <?php foreach ($flyers as $index => $flyer): ?>
            <div class="col-lg-4 col-md-6">
                <div class="flyer-card h-100">
                    <div class="flyer-image-container">
                        <img src="<?= htmlspecialchars($flyer['file']) ?>" 
                             alt="<?= htmlspecialchars($flyer['title']) ?>"
                             class="flyer-image img-fluid"
                             loading="lazy"
                             data-bs-toggle="modal" 
                             data-bs-target="#flyerModal<?= $index ?>">
                        <div class="flyer-overlay">
                            <i class="fas fa-search-plus fa-2x"></i>
                        </div>
                    </div>
                    <div class="flyer-card-body">
                        <h5 class="flyer-title"><?= htmlspecialchars($flyer['title']) ?></h5>
                        <div class="flyer-actions">
                            <button class="btn btn-primary btn-sm" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#flyerModal<?= $index ?>">
                                <i class="fas fa-eye me-1"></i> Lihat Detail
                            </button>
                            <a href="<?= htmlspecialchars($flyer['file']) ?>" 
                               download="<?= htmlspecialchars($flyer['filename']) ?>"
                               class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-download me-1"></i> Download
                            </a>
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
/* Flyer Gallery Styles */
.flyer-gallery {
    background-color: #f8f9fa;
}

.flyer-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.flyer-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.flyer-image-container {
    position: relative;
    overflow: hidden;
    aspect-ratio: 16/9;
}

.flyer-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
    cursor: pointer;
}

.flyer-image-container:hover .flyer-image {
    transform: scale(1.05);
}

.flyer-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(57, 163, 209, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    cursor: pointer;
    color: white;
}

.flyer-image-container:hover .flyer-overlay {
    opacity: 1;
}

.flyer-card-body {
    padding: 1.5rem;
}

.flyer-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 1rem;
    text-align: center;
}

.flyer-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
}

.btn-primary {
    background-color: #39a3d1;
    border-color: #39a3d1;
}

.btn-primary:hover {
    background-color: #2a8bb8;
    border-color: #2a8bb8;
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
    .flyer-actions {
        flex-direction: column;
    }
    
    .flyer-actions .btn {
        width: 100%;
    }
    
    .modal-lg {
        max-width: 95vw;
        margin: 0.5rem;
    }
}
</style>
<?php endif; ?>
