<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" lang="id" prefix="og: https://ogp.me/ns#">
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" lang="id" prefix="og: https://ogp.me/ns#">
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html lang="id" prefix="og: https://ogp.me/ns#" class="tcb">
<!--<![endif]-->
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    
    <!-- SEO Meta Tags -->
    <title>Tentang Kami - PT Madinah Iman Wisata | Travel Haji & Umroh Terpercaya</title>
    <meta name="description" content="⭐⭐⭐⭐⭐Kenali lebih dalam PT Madinah Iman Wisata, travel haji dan umroh terpercaya dengan pengalaman 15+ tahun melayani jamaah dengan penuh amanah dan profesionalisme."/>
    <meta name="robots" content="follow, index, max-snippet:-1, max-video-preview:-1, max-image-preview:large"/>
    <link rel="canonical" href="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" />
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:locale" content="id_ID" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Tentang Kami - PT Madinah Iman Wisata | Travel Haji & Umroh Terpercaya" />
    <meta property="og:description" content="⭐⭐⭐⭐⭐Kenali lebih dalam PT Madinah Iman Wisata, travel haji dan umroh terpercaya dengan pengalaman 15+ tahun melayani jamaah dengan penuh amanah dan profesionalisme." />
    <meta property="og:url" content="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" />
    <meta property="og:site_name" content="PT Madinah Iman Wisata" />
    <meta property="og:image" content="<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/miw_logo.png" />
    
    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Tentang Kami - PT Madinah Iman Wisata | Travel Haji & Umroh Terpercaya" />
    <meta name="twitter:description" content="⭐⭐⭐⭐⭐Kenali lebih dalam PT Madinah Iman Wisata, travel haji dan umroh terpercaya dengan pengalaman 15+ tahun melayani jamaah dengan penuh amanah dan profesionalisme." />
    <meta name="twitter:image" content="<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/miw_logo.png" />
    
    <!-- Favicon -->
    <link rel="icon" href="miw_logo.png" sizes="32x32" />
    <link rel="icon" href="miw_logo.png" sizes="192x192" />
    <link rel="apple-touch-icon" href="miw_logo.png" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Schema.org JSON-LD -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "TravelAgency",
        "name": "PT Madinah Iman Wisata",
        "url": "<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>",
        "logo": "<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/miw_logo.png",
        "description": "Travel haji dan umroh terpercaya dengan pengalaman 15+ tahun",
        "foundingDate": "2008",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "Jl. Raya Bandung No. 123",
            "addressLocality": "Bandung",
            "addressRegion": "Jawa Barat",
            "postalCode": "40264",
            "addressCountry": "ID"
        },
        "telephone": "(022) 1234-5678",
        "email": "info@miw.co.id",
        "sameAs": [
            "https://www.facebook.com/madinahimanwisata",
            "https://www.instagram.com/madinahimanwisata"
        ]
    }
    </script>
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-gold: #d4af37;
            --secondary-green: #228b22;
            --dark-blue: #1e3a8a;
            --light-gray: #f8f9fa;
            --white: #ffffff;
            --islamic-green: #006400;
            --warm-gold: #ffd700;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: #333;
        }
        
        /* Header Styles */
        .navbar {
            background: linear-gradient(135deg, var(--primary-gold) 0%, #b8860b 100%);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand img {
            height: 50px;
            margin-right: 10px;
        }
        
        .navbar-nav .nav-link {
            color: white !important;
            font-weight: 500;
            margin: 0 10px;
            transition: all 0.3s ease;
        }
        
        .navbar-nav .nav-link:hover {
            color: var(--dark-blue) !important;
            transform: translateY(-2px);
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(0, 100, 0, 0.85), rgba(30, 58, 138, 0.85)), url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 400"><defs><pattern id="islamicPattern" patternUnits="userSpaceOnUse" width="80" height="80"><rect width="80" height="80" fill="%23f8f9fa" opacity="0.1"/><path d="M40,10 Q50,20 40,30 Q30,20 40,10 M40,50 Q50,60 40,70 Q30,60 40,50" fill="%23ffd700" opacity="0.3"/></pattern></defs><rect width="100%" height="100%" fill="url(%23islamicPattern)"/></svg>');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 120px 0;
            text-align: center;
            position: relative;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><text y=".9em" font-size="90" opacity="0.1" fill="%23ffd700">☪</text></svg>') repeat;
            background-size: 150px 150px;
            opacity: 0.3;
        }
        
        .hero-content {
            position: relative;
            z-index: 1;
        }
        
        .hero-section h1 {
            font-family: 'Nunito', sans-serif;
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        
        .hero-section .lead {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.95;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        
        /* About Section */
        .about-section {
            padding: 80px 0;
            background: white;
        }
        
        .about-card {
            background: var(--light-gray);
            border-radius: 20px;
            padding: 40px;
            height: 100%;
            transition: all 0.3s ease;
            border-left: 5px solid var(--primary-gold);
        }
        
        .about-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .about-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, var(--islamic-green), var(--primary-gold));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
            color: white;
            font-size: 2.5rem;
            box-shadow: 0 10px 30px rgba(0,100,0,0.2);
        }
        
        /* Stats Section */
        .stats-section {
            padding: 80px 0;
            background: linear-gradient(135deg, var(--islamic-green), var(--dark-blue));
            color: white;
        }
        
        .stat-item {
            text-align: center;
            padding: 30px 20px;
        }
        
        .stat-number {
            font-size: 3.5rem;
            font-weight: 800;
            color: var(--warm-gold);
            margin-bottom: 10px;
            display: block;
        }
        
        .stat-label {
            font-size: 1.1rem;
            font-weight: 500;
            opacity: 0.9;
        }
        
        /* Team Section */
        .team-section {
            padding: 80px 0;
            background: var(--light-gray);
        }
        
        .team-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }
        
        .team-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--islamic-green), var(--primary-gold));
        }
        
        .team-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 25px 45px rgba(0,0,0,0.15);
        }
        
        .team-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin: 0 auto 20px;
            background: linear-gradient(45deg, var(--islamic-green), var(--primary-gold));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
            font-weight: bold;
        }
        
        .team-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--dark-blue);
            margin-bottom: 10px;
        }
        
        .team-position {
            color: var(--islamic-green);
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        /* Values Section */
        .values-section {
            padding: 80px 0;
            background: white;
        }
        
        .value-card {
            background: var(--light-gray);
            border-radius: 15px;
            padding: 40px 30px;
            text-align: center;
            height: 100%;
            transition: all 0.3s ease;
            border-top: 4px solid var(--primary-gold);
        }
        
        .value-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .value-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(45deg, var(--islamic-green), var(--primary-gold));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            color: white;
            font-size: 2.5rem;
            box-shadow: 0 10px 30px rgba(0,100,0,0.2);
        }
        
        /* Timeline Section */
        .timeline-section {
            padding: 80px 0;
            background: var(--light-gray);
        }
        
        .timeline {
            position: relative;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .timeline::after {
            content: '';
            position: absolute;
            width: 6px;
            background: linear-gradient(to bottom, var(--islamic-green), var(--primary-gold));
            top: 0;
            bottom: 0;
            left: 50%;
            margin-left: -3px;
        }
        
        .timeline-item {
            padding: 10px 40px;
            position: relative;
            background-color: inherit;
            width: 50%;
        }
        
        .timeline-item::after {
            content: '';
            position: absolute;
            width: 25px;
            height: 25px;
            right: -17px;
            background: var(--primary-gold);
            border: 4px solid var(--islamic-green);
            top: 15px;
            border-radius: 50%;
            z-index: 1;
        }
        
        .timeline-left {
            left: 0;
        }
        
        .timeline-right {
            left: 50%;
        }
        
        .timeline-right::after {
            left: -16px;
        }
        
        .timeline-content {
            padding: 20px 30px;
            background: white;
            position: relative;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .timeline-year {
            background: var(--islamic-green);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 15px;
        }
        
        /* Islamic Elements */
        .section-divider {
            text-align: center;
            margin: 60px 0;
        }
        
        .islamic-ornament {
            font-size: 3rem;
            color: var(--primary-gold);
            opacity: 0.7;
        }
        
        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, var(--islamic-green), var(--dark-blue));
            color: white;
            padding: 80px 0;
            text-align: center;
        }
        
        .btn-primary-custom {
            background: linear-gradient(45deg, var(--warm-gold), var(--primary-gold));
            border: none;
            padding: 18px 35px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            color: var(--dark-blue);
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.4);
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(255, 215, 0, 0.5);
            color: var(--dark-blue);
        }
        
        /* Footer */
        .footer {
            background: #1a1a1a;
            color: #fff;
            padding: 50px 0 20px;
        }
        
        .footer-brand {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .footer-brand img {
            height: 40px;
            margin-right: 15px;
        }
        
        .footer h5 {
            color: var(--primary-gold);
            margin-bottom: 20px;
        }
        
        .footer ul li {
            margin-bottom: 10px;
        }
        
        .footer ul li a {
            color: #ccc;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer ul li a:hover {
            color: var(--primary-gold);
        }
        
        .social-links a {
            color: #ccc;
            font-size: 1.5rem;
            margin-right: 15px;
            transition: color 0.3s ease;
        }
        
        .social-links a:hover {
            color: var(--primary-gold);
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 2.5rem;
            }
            
            .hero-section .lead {
                font-size: 1.1rem;
            }
            
            .stat-number {
                font-size: 2.5rem;
            }
            
            .timeline::after {
                left: 31px;
            }
            
            .timeline-item {
                width: 100%;
                padding-left: 70px;
                padding-right: 25px;
            }
            
            .timeline-item::before {
                left: 60px;
                border: medium solid white;
                border-width: 10px 10px 10px 0;
                border-color: transparent white transparent transparent;
            }
            
            .timeline-left::after, .timeline-right::after {
                left: 15px;
            }
            
            .timeline-right {
                left: 0%;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="miw_logo.png" alt="MIW Logo" class="me-2">
                <span class="fw-bold">PT Madinah Iman Wisata</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="umroh_page.php">Umroh</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="haji_page.php">Haji</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="tentang_kami.php">Tentang Kami</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1>Tentang Kami</h1>
                <p class="lead">Mengenal lebih dekat PT Madinah Iman Wisata, mitra terpercaya Anda dalam perjalanan spiritual haji dan umroh sejak 2008</p>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section">
        <div class="container">
            <div class="row align-items-center mb-5">
                <div class="col-lg-6">
                    <h2 class="display-4 fw-bold mb-4" style="color: var(--dark-blue);">Profil Perusahaan</h2>
                    <p class="lead text-muted mb-4">PT Madinah Iman Wisata adalah perusahaan travel haji dan umroh yang telah berpengalaman lebih dari 15 tahun dalam melayani jamaah Indonesia.</p>
                    <p class="mb-4">Didirikan pada tahun 2008 dengan visi menjadi travel haji dan umroh terdepan di Indonesia, kami berkomitmen memberikan pelayanan terbaik dengan penuh amanah dan profesionalisme.</p>
                    <p class="mb-4">Dengan izin resmi dari Kementerian Agama Republik Indonesia, kami telah melayani ribuan jamaah dari seluruh nusantara untuk menunaikan ibadah haji dan umroh.</p>
                </div>
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="about-card">
                                <div class="about-icon">
                                    <i class="bi bi-award"></i>
                                </div>
                                <h4 class="fw-bold mb-3" style="color: var(--dark-blue);">Berpengalaman</h4>
                                <p class="text-muted">15+ tahun melayani jamaah haji dan umroh dengan tingkat kepuasan yang tinggi</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="about-card">
                                <div class="about-icon">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                                <h4 class="fw-bold mb-3" style="color: var(--dark-blue);">Terpercaya</h4>
                                <p class="text-muted">Berlisensi resmi dari Kementerian Agama RI dan terdaftar di IATA</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="about-card">
                                <div class="about-icon">
                                    <i class="bi bi-heart"></i>
                                </div>
                                <h4 class="fw-bold mb-3" style="color: var(--dark-blue);">Amanah</h4>
                                <p class="text-muted">Melayani jamaah dengan penuh amanah seperti keluarga sendiri</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="about-card">
                                <div class="about-icon">
                                    <i class="bi bi-star"></i>
                                </div>
                                <h4 class="fw-bold mb-3" style="color: var(--dark-blue);">Berkualitas</h4>
                                <p class="text-muted">Fasilitas dan pelayanan berkualitas tinggi dengan harga yang kompetitif</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-item">
                        <span class="stat-number">15+</span>
                        <div class="stat-label">Tahun Pengalaman</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-item">
                        <span class="stat-number">5000+</span>
                        <div class="stat-label">Jamaah Terlayani</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-item">
                        <span class="stat-number">98%</span>
                        <div class="stat-label">Tingkat Kepuasan</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-item">
                        <span class="stat-number">50+</span>
                        <div class="stat-label">Paket Tersedia</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="values-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold mb-3" style="color: var(--dark-blue);">Nilai-Nilai Kami</h2>
                <p class="lead text-muted">Prinsip dan nilai yang menjadi fondasi kuat dalam setiap pelayanan kami</p>
            </div>
            
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="bi bi-hand-thumbs-up"></i>
                        </div>
                        <h4 class="fw-bold mb-3" style="color: var(--dark-blue);">Integritas</h4>
                        <p class="text-muted">Kami berkomitmen untuk selalu jujur, transparan, dan dapat dipercaya dalam setiap aspek pelayanan kepada jamaah.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="bi bi-gem"></i>
                        </div>
                        <h4 class="fw-bold mb-3" style="color: var(--dark-blue);">Kualitas</h4>
                        <p class="text-muted">Memberikan pelayanan berkualitas tinggi dengan standar internasional untuk kenyamanan perjalanan spiritual jamaah.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <h4 class="fw-bold mb-3" style="color: var(--dark-blue);">Kekeluargaan</h4>
                        <p class="text-muted">Melayani setiap jamaah dengan penuh kasih sayang dan perhatian seperti keluarga sendiri.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="bi bi-lightning"></i>
                        </div>
                        <h4 class="fw-bold mb-3" style="color: var(--dark-blue);">Responsif</h4>
                        <p class="text-muted">Siap melayani dan merespons kebutuhan jamaah dengan cepat dan tepat selama 24/7.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="bi bi-infinity"></i>
                        </div>
                        <h4 class="fw-bold mb-3" style="color: var(--dark-blue);">Berkelanjutan</h4>
                        <p class="text-muted">Terus berinovasi dan berkembang untuk memberikan pelayanan yang semakin baik dari waktu ke waktu.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="bi bi-heart-pulse"></i>
                        </div>
                        <h4 class="fw-bold mb-3" style="color: var(--dark-blue);">Spiritualitas</h4>
                        <p class="text-muted">Mendampingi perjalanan spiritual jamaah dengan penuh penghayatan dan kekhusyukan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Islamic Divider -->
    <div class="section-divider">
        <div class="islamic-ornament">☪ ❋ ☪</div>
    </div>

    <!-- Timeline Section -->
    <section class="timeline-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold mb-3" style="color: var(--dark-blue);">Perjalanan Kami</h2>
                <p class="lead text-muted">Melihat kembali milestone penting dalam perjalanan PT Madinah Iman Wisata</p>
            </div>
            
            <div class="timeline">
                <div class="timeline-item timeline-left">
                    <div class="timeline-content">
                        <div class="timeline-year">2008</div>
                        <h4 class="fw-bold mb-3" style="color: var(--dark-blue);">Pendirian Perusahaan</h4>
                        <p>PT Madinah Iman Wisata didirikan dengan visi menjadi travel haji dan umroh terdepan di Indonesia.</p>
                    </div>
                </div>
                
                <div class="timeline-item timeline-right">
                    <div class="timeline-content">
                        <div class="timeline-year">2010</div>
                        <h4 class="fw-bold mb-3" style="color: var(--dark-blue);">Izin Resmi Kemenag</h4>
                        <p>Memperoleh izin resmi dari Kementerian Agama RI untuk menyelenggarakan perjalanan haji dan umroh.</p>
                    </div>
                </div>
                
                <div class="timeline-item timeline-left">
                    <div class="timeline-content">
                        <div class="timeline-year">2012</div>
                        <h4 class="fw-bold mb-3" style="color: var(--dark-blue);">Ekspansi Layanan</h4>
                        <p>Membuka layanan umroh plus dan wisata religi ke berbagai negara di Timur Tengah.</p>
                    </div>
                </div>
                
                <div class="timeline-item timeline-right">
                    <div class="timeline-content">
                        <div class="timeline-year">2015</div>
                        <h4 class="fw-bold mb-3" style="color: var(--dark-blue);">Penghargaan Pertama</h4>
                        <p>Meraih penghargaan "Best Hajj & Umrah Travel Agent" dari asosiasi travel Indonesia.</p>
                    </div>
                </div>
                
                <div class="timeline-item timeline-left">
                    <div class="timeline-content">
                        <div class="timeline-year">2018</div>
                        <h4 class="fw-bold mb-3" style="color: var(--dark-blue);">Sertifikasi IATA</h4>
                        <p>Memperoleh sertifikasi IATA dan memperluas jaringan kemitraan dengan maskapai internasional.</p>
                    </div>
                </div>
                
                <div class="timeline-item timeline-right">
                    <div class="timeline-content">
                        <div class="timeline-year">2020</div>
                        <h4 class="fw-bold mb-3" style="color: var(--dark-blue);">Digitalisasi Layanan</h4>
                        <p>Meluncurkan platform digital untuk mempermudah jamaah dalam proses pendaftaran dan konsultasi.</p>
                    </div>
                </div>
                
                <div class="timeline-item timeline-left">
                    <div class="timeline-content">
                        <div class="timeline-year">2024</div>
                        <h4 class="fw-bold mb-3" style="color: var(--dark-blue);">Inovasi Berkelanjutan</h4>
                        <p>Terus berinovasi dengan teknologi terkini untuk memberikan pengalaman terbaik bagi jamaah.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold mb-3" style="color: var(--dark-blue);">Tim Kami</h2>
                <p class="lead text-muted">Orang-orang berpengalaman yang siap melayani perjalanan spiritual Anda</p>
            </div>
            
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="team-card">
                        <div class="team-photo">H.A</div>
                        <div class="team-name">H. Ahmad Hidayat</div>
                        <div class="team-position">Direktur Utama</div>
                        <p class="text-muted">Memimpin perusahaan dengan visi yang jelas dan pengalaman 20+ tahun di industri travel haji dan umroh.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="team-card">
                        <div class="team-photo">S.F</div>
                        <div class="team-name">Siti Fatimah, S.Ag</div>
                        <div class="team-position">Manajer Operasional</div>
                        <p class="text-muted">Mengelola operasional harian dengan dedikasi tinggi dan memastikan setiap jamaah mendapat pelayanan terbaik.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="team-card">
                        <div class="team-photo">M.R</div>
                        <div class="team-name">Muhammad Rizki, S.Par</div>
                        <div class="team-position">Manajer Marketing</div>
                        <p class="text-muted">Berpengalaman dalam industri pariwisata dan ahli dalam mengembangkan strategi pemasaran yang efektif.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="team-card">
                        <div class="team-photo">N.A</div>
                        <div class="team-name">Nurul Aini, S.E</div>
                        <div class="team-position">Manajer Keuangan</div>
                        <p class="text-muted">Mengelola keuangan perusahaan dengan transparansi dan akuntabilitas yang tinggi.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="team-card">
                        <div class="team-photo">A.S</div>
                        <div class="team-name">Abdullah Syafii, Lc</div>
                        <div class="team-position">Pembimbing Spiritual</div>
                        <p class="text-muted">Lulusan Al-Azhar dengan pengalaman mendampingi jamaah dalam perjalanan spiritual haji dan umroh.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="team-card">
                        <div class="team-photo">D.P</div>
                        <div class="team-name">Dr. Putri Handayani</div>
                        <div class="team-position">Dokter Pendamping</div>
                        <p class="text-muted">Dokter berpengalaman yang selalu siap memberikan pelayanan kesehatan terbaik untuk jamaah.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container text-center">
            <h2 class="display-4 fw-bold mb-4">Bergabunglah Bersama Kami</h2>
            <p class="lead mb-4">Wujudkan impian spiritual Anda bersama PT Madinah Iman Wisata. Kami siap menjadi mitra terpercaya dalam perjalanan haji dan umroh Anda.</p>
            <a href="umroh_page.php" class="btn-primary-custom me-3 mb-3">
                <i class="bi bi-airplane"></i>
                Lihat Paket Umroh
            </a>
            <a href="haji_page.php" class="btn btn-outline-light mb-3">
                <i class="bi bi-building"></i>
                Lihat Paket Haji
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="footer-brand">
                        <img src="miw_logo.png" alt="MIW Logo">
                        <h5 class="mb-0">PT Madinah Iman Wisata</h5>
                    </div>
                    <p class="text-muted mb-3">Travel haji dan umroh terpercaya dengan pengalaman lebih dari 15 tahun melayani jamaah dengan penuh amanah.</p>
                    <div class="social-links">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-whatsapp"></i></a>
                        <a href="#"><i class="bi bi-envelope"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Layanan</h5>
                    <ul class="list-unstyled">
                        <li><a href="umroh_page.php">Paket Umroh</a></li>
                        <li><a href="haji_page.php">Paket Haji</a></li>
                        <li><a href="#wisata">Wisata Religi</a></li>
                        <li><a href="#visa">Layanan Visa</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5>Tentang Kami</h5>
                    <ul class="list-unstyled">
                        <li><a href="tentang_kami.php">Profil Perusahaan</a></li>
                        <li><a href="#legalitas">Legalitas</a></li>
                        <li><a href="#penghargaan">Penghargaan</a></li>
                        <li><a href="#karir">Karir</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5>Kontak</h5>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-geo-alt me-2"></i>Jl. Raya Bandung No. 123, Bandung</li>
                        <li><i class="bi bi-telephone me-2"></i>(022) 1234-5678</li>
                        <li><i class="bi bi-envelope me-2"></i>info@miw.co.id</li>
                        <li><i class="bi bi-clock me-2"></i>Senin-Sabtu: 08:00-17:00</li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-muted">&copy; 2024 PT Madinah Iman Wisata. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#privacy" class="text-muted me-3">Privacy Policy</a>
                    <a href="#terms" class="text-muted">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Counter animation for stats
        function animateCounter(element) {
            const target = parseInt(element.textContent.replace(/[^\d]/g, ''));
            const increment = target / 50;
            let current = 0;
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                
                let displayValue = Math.ceil(current);
                if (element.textContent.includes('+')) {
                    element.textContent = displayValue + '+';
                } else if (element.textContent.includes('%')) {
                    element.textContent = displayValue + '%';
                } else {
                    element.textContent = displayValue;
                }
            }, 20);
        }

        // Add animation on scroll
        window.addEventListener('scroll', function() {
            const cards = document.querySelectorAll('.about-card, .value-card, .team-card, .timeline-content');
            const stats = document.querySelectorAll('.stat-number');
            
            cards.forEach(card => {
                const cardTop = card.getBoundingClientRect().top;
                const cardVisible = 150;
                
                if (cardTop < window.innerHeight - cardVisible) {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }
            });
            
            // Animate stats when visible
            stats.forEach(stat => {
                const statTop = stat.getBoundingClientRect().top;
                const statVisible = 150;
                
                if (statTop < window.innerHeight - statVisible && !stat.classList.contains('animated')) {
                    stat.classList.add('animated');
                    animateCounter(stat);
                }
            });
        });

        // Initialize card animations
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.about-card, .value-card, .team-card, .timeline-content');
            cards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(50px)';
                card.style.transition = 'all 0.6s ease';
            });
        });
    </script>
</body>
</html>