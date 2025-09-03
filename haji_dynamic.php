<?php
require_once "config.php";

// Get all Haji packages
$stmt = $conn->prepare("SELECT * FROM data_paket WHERE jenis_paket = 'Haji' ORDER BY tanggal_keberangkatan ASC");
$stmt->execute();
$hajiPackages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" lang="id">
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" lang="id">
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html lang="id" class="tcb">
<!--<![endif]-->
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Paket Haji Plus - Madinah Iman Wisata</title>
    
    <!-- Include the same CSS as static HTML -->
    <style type="text/css" id="tcb-style-base-page-2"  onLoad="typeof window.lightspeedOptimizeStylesheet === 'function' && window.lightspeedOptimizeStylesheet()" class="tcb-lightspeed-style">
        /* Include exact same styles from haji.html */
        :root{--tcb-color-0:rgb(26, 26, 26);--tcb-color-0-h:0;--tcb-color-0-s:0%;--tcb-color-0-l:10%;--tcb-color-0-a:1;--tcb-color-1:rgb(255, 204, 41);--tcb-color-1-h:45;--tcb-color-1-s:100%;--tcb-color-1-l:58%;--tcb-color-1-a:1;--tcb-color-2:rgb(239, 157, 67);--tcb-color-2-h:31;--tcb-color-2-s:84%;--tcb-color-2-l:60%;--tcb-color-2-a:1;--tcb-color-3:rgb(255, 255, 255);--tcb-color-3-h:0;--tcb-color-3-s:0%;--tcb-color-3-l:100%;--tcb-color-3-a:1;}
        
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }
        
        .thrv_wrapper {
            margin-top: 20px;
            margin-bottom: 20px;
            padding: 1px;
        }
        
        .thrv-page-section {
            position: relative;
            box-sizing: border-box !important;
        }
        
        .tve-page-section-out {
            position: absolute;
            width: 100%;
            height: 100%;
            left: 0px;
            top: 0px;
            box-sizing: border-box;
            transition: box-shadow 0.5s;
            overflow: hidden;
        }
        
        .tve-page-section-in {
            box-sizing: border-box;
            margin-right: auto;
            margin-left: auto;
            position: relative;
            z-index: 1;
            min-height: 40px;
            padding: 40px 20px;
        }
        
        .thrv_text_element h2 {
            text-align: center;
            color: var(--tcb-color-0);
            font-weight: bold;
            margin-bottom: 20px;
        }
        
        .package-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
            margin: 40px 0;
        }
        
        .package-item {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .package-item:hover {
            transform: translateY(-5px);
        }
        
        .package-item h3 {
            color: var(--tcb-color-0);
            font-weight: bold;
            margin: 20px 0 10px 0;
            font-size: 1.5rem;
        }
        
        .package-item h4 {
            color: #666;
            margin-bottom: 20px;
            font-size: 1rem;
        }
        
        .package-flyer {
            width: 100%;
            max-width: 800px;
            height: auto;
            display: block;
            margin: 0 auto;
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        
        .package-flyer:hover {
            transform: scale(1.02);
        }
        
        .tcb-button-link {
            background-color: rgb(26,188,156);
            padding: 12px 25px;
            font-size: 16px;
            box-sizing: border-box;
            display: inline-flex;
            align-items: center;
            overflow: hidden;
            text-align: center;
            line-height: 1.2em;
            color: rgb(255,255,255);
            text-decoration: none !important;
            border-radius: 5px;
            margin: 20px 0;
            transition: background-color 0.3s ease;
        }
        
        .tcb-button-link:hover {
            background-color: rgb(21,162,136);
        }
        
        .no-packages {
            text-align: center;
            padding: 40px;
            color: #666;
            font-size: 1.2rem;
        }
        
        @media (max-width: 768px) {
            .package-grid {
                flex-direction: column;
                align-items: center;
            }
            
            .package-item {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include 'navigation.php'; ?>
    
    <div class="thrv_wrapper thrv-page-section tve-height-update" data-inherit-lp-settings="1">
        <div class="tve-page-section-out"></div>
        <div class="tve-page-section-in" style="background-color: #ffffff; padding: 40px 20px;">
            
            <div class="thrv_wrapper thrv_text_element" style="text-align: center; margin-bottom: 40px;">
                <h2 class="" style="color: var(--tcb-color-0) !important; margin-bottom: 20px;">
                    <span><strong>Paket Haji Plus Pilihan</strong></span>
                </h2>
                <p style="color: #666; font-size: 1.1rem;">Menunaikan rukun Islam yang kelima dengan fasilitas terbaik</p>
            </div>
            
            <?php if (empty($hajiPackages)): ?>
            <div class="no-packages">
                <p>Belum ada paket Haji yang tersedia saat ini.</p>
                <p>Silakan hubungi kami untuk informasi lebih lanjut.</p>
                <a href="tentang_kami.php" class="tcb-button-link">Hubungi Kami</a>
            </div>
            <?php else: ?>
            
            <div class="package-grid">
                <?php 
                $counter = 1;
                foreach ($hajiPackages as $package): 
                ?>
                <div class="package-item">
                    <div class="thrv_wrapper thrv_text_element" style="">
                        <h3 class="" style="text-align: center; color: var(--tcb-color-0) !important; margin-bottom: 10px;">
                            <strong><?= sprintf("%02d. %s", $counter, htmlspecialchars($package['program_pilihan'])) ?></strong>
                        </h3>
                        <h4 class="" style="text-align: center; margin-bottom: 20px;">
                            <?php if (!empty($package['tanggal_keberangkatan'])): ?>
                                Keberangkatan: <?= date('d M Y', strtotime($package['tanggal_keberangkatan'])) ?>
                            <?php else: ?>
                                Masa Tunggu Singkat
                            <?php endif; ?>
                        </h4>
                    </div>
                    
                    <?php if (!empty($package['flyer_image'])): ?>
                    <div class="thrv_wrapper tve_image_caption tve_ea_thrive_zoom" style="text-align: center;">
                        <span class="tve_image_frame" style="display: inline-block; max-width: 800px; width: 100%;">
                            <img decoding="async" class="package-flyer tve_evt_manager_listen tve_et_click" 
                                 alt="<?= htmlspecialchars($package['program_pilihan']) ?>" 
                                 title="<?= htmlspecialchars($package['program_pilihan']) ?>" 
                                 src="<?= htmlspecialchars($package['flyer_image']) ?>" 
                                 style="width: 100%; height: auto; max-width: 800px;" 
                                 onclick="openImageModal('<?= htmlspecialchars($package['flyer_image']) ?>', '<?= htmlspecialchars($package['program_pilihan']) ?>')">
                        </span>
                    </div>
                    <?php else: ?>
                    <div style="background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%); padding: 40px; color: white;">
                        <h4><?= htmlspecialchars($package['program_pilihan']) ?></h4>
                        <p>Flyer akan segera tersedia</p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($package['base_price_quad'] > 0 || $package['base_price_triple'] > 0 || $package['base_price_double'] > 0): ?>
                    <div style="padding: 20px; background: #f8f9fa; margin: 10px;">
                        <h5 style="margin-bottom: 15px; color: var(--tcb-color-0);">Harga Paket:</h5>
                        <div style="display: flex; justify-content: space-around; text-align: center;">
                            <?php if ($package['base_price_quad'] > 0): ?>
                            <div>
                                <small style="color: #666;">Quad</small><br>
                                <strong style="color: var(--tcb-color-1);">
                                    <?= $package['currency'] == 'USD' ? '$' : 'Rp ' ?><?= number_format($package['base_price_quad'], 0, ',', '.') ?>
                                </strong>
                            </div>
                            <?php endif; ?>
                            <?php if ($package['base_price_triple'] > 0): ?>
                            <div>
                                <small style="color: #666;">Triple</small><br>
                                <strong style="color: var(--tcb-color-1);">
                                    <?= $package['currency'] == 'USD' ? '$' : 'Rp ' ?><?= number_format($package['base_price_triple'], 0, ',', '.') ?>
                                </strong>
                            </div>
                            <?php endif; ?>
                            <?php if ($package['base_price_double'] > 0): ?>
                            <div>
                                <small style="color: #666;">Double</small><br>
                                <strong style="color: var(--tcb-color-1);">
                                    <?= $package['currency'] == 'USD' ? '$' : 'Rp ' ?><?= number_format($package['base_price_double'], 0, ',', '.') ?>
                                </strong>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="thrv_wrapper thrv-button thrv-button-v2 tcb-local-vars-root" style="margin: 20px;">
                        <a href="form_haji.php?pak_id=<?= $package['pak_id'] ?>" 
                           class="tcb-button-link tcb-plain-text" 
                           style="" 
                           target="_blank">
                            <span class="tcb-button-texts">
                                <span class="tcb-button-text thrv-inline-text" style="">Daftar Sekarang</span>
                            </span>
                        </a>
                    </div>
                </div>
                <?php 
                $counter++;
                endforeach; 
                ?>
            </div>
            
            <?php endif; ?>
            
        </div>
    </div>
    
    <!-- Image Modal -->
    <div id="imageModal" style="display: none; position: fixed; z-index: 10000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.8);" onclick="closeImageModal()">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); max-width: 90%; max-height: 90%;">
            <img id="modalImage" src="" alt="" style="width: 100%; height: auto; border-radius: 10px;">
            <span style="position: absolute; top: -15px; right: -15px; color: white; background: black; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 20px;" onclick="closeImageModal()">&times;</span>
        </div>
    </div>

    <script>
        function openImageModal(src, alt) {
            document.getElementById('modalImage').src = src;
            document.getElementById('modalImage').alt = alt;
            document.getElementById('imageModal').style.display = 'block';
        }
        
        function closeImageModal() {
            document.getElementById('imageModal').style.display = 'none';
        }
        
        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>

    <!-- Floating Registration Button for Haji -->
    <div id="haji-registration-button" style="position: fixed; bottom: 30px; left: 30px; z-index: 999999; background-color: #ff6b35; color: white; padding: 12px 20px; border-radius: 30px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.3); cursor: pointer; transition: all 0.3s ease; text-decoration: none; font-weight: bold; font-size: 14px; min-width: 80px;" onclick="window.location.href='form_haji.php';">
        DAFTAR
    </div>

    <style>
    #haji-registration-button:hover {
        background-color: #e55a2b !important;
        transform: scale(1.05);
        box-shadow: 0 6px 16px rgba(0,0,0,0.4);
    }

    #haji-registration-button:active {
        transform: scale(0.95);
    }

    @media (max-width: 768px) {
        #haji-registration-button {
            bottom: 20px;
            left: 20px;
            padding: 10px 16px;
            font-size: 12px;
            min-width: 70px;
        }
    }
    </style>
</body>
</html>
