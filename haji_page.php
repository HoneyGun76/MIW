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
    <title>Paket Haji Plus & Reguler - PT Madinah Iman Wisata</title>
    <meta name="description" content="⭐⭐⭐⭐⭐Paket Haji Plus & Reguler terbaik dari PT Madinah Iman Wisata. ✈️ Berpengalaman 15+ tahun memberangkatkan jamaah haji dengan pelayanan terbaik seperti keluarga sendiri."/>
    <meta name="robots" content="follow, index, max-snippet:-1, max-video-preview:-1, max-image-preview:large"/>
    <link rel="canonical" href="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" />
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:locale" content="id_ID" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Paket Haji Plus & Reguler - PT Madinah Iman Wisata" />
    <meta property="og:description" content="⭐⭐⭐⭐⭐Paket Haji Plus & Reguler terbaik dari PT Madinah Iman Wisata. ✈️ Berpengalaman 15+ tahun memberangkatkan jamaah haji dengan pelayanan terbaik seperti keluarga sendiri." />
    <meta property="og:url" content="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" />
    <meta property="og:site_name" content="PT Madinah Iman Wisata" />
    <meta property="og:image" content="<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/miw_logo.png" />
    
    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Paket Haji Plus & Reguler - PT Madinah Iman Wisata" />
    <meta name="twitter:description" content="⭐⭐⭐⭐⭐Paket Haji Plus & Reguler terbaik dari PT Madinah Iman Wisata. ✈️ Berpengalaman 15+ tahun memberangkatkan jamaah haji dengan pelayanan terbaik seperti keluarga sendiri." />
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
        
        /* Islamic Badge */
        .islamic-badge {
            background: linear-gradient(45deg, var(--islamic-green), #228b22);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 20px;
        }
        
        /* Packages Section */
        .packages-section {
            padding: 80px 0;
            background: var(--light-gray);
        }
        
        .package-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }
        
        .package-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--islamic-green), var(--primary-gold));
        }
        
        .package-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 25px 45px rgba(0,0,0,0.15);
        }
        
        .package-premium {
            border: 3px solid var(--primary-gold);
            transform: scale(1.05);
        }
        
        .package-badge {
            background: var(--primary-gold);
            color: var(--dark-blue);
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            position: absolute;
            top: 20px;
            right: 20px;
        }
        
        .package-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, var(--islamic-green), var(--primary-gold));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 2rem;
        }
        
        .package-title {
            font-family: 'Nunito', sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark-blue);
            margin-bottom: 15px;
        }
        
        .package-price {
            font-size: 2.8rem;
            font-weight: 800;
            color: var(--islamic-green);
            margin: 20px 0;
        }
        
        .package-price small {
            font-size: 1rem;
            color: #666;
            font-weight: 400;
        }
        
        .package-features {
            list-style: none;
            padding: 0;
            margin: 30px 0;
        }
        
        .package-features li {
            padding: 12px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
        }
        
        .package-features li:last-child {
            border-bottom: none;
        }
        
        .package-features li::before {
            content: '✓';
            color: var(--islamic-green);
            font-weight: bold;
            margin-right: 10px;
        }
        
        /* Features Section */
        .features-section {
            padding: 80px 0;
            background: white;
        }
        
        .feature-card {
            text-align: center;
            padding: 30px 20px;
            border-radius: 15px;
            transition: all 0.3s ease;
        }
        
        .feature-card:hover {
            background: var(--light-gray);
            transform: translateY(-5px);
        }
        
        .feature-icon {
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
        
        /* Testimonials */
        .testimonial-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            position: relative;
        }
        
        .testimonial-card::before {
            content: '"';
            font-size: 4rem;
            color: var(--primary-gold);
            position: absolute;
            top: -10px;
            left: 20px;
            font-family: serif;
        }
        
        .testimonial-author {
            display: flex;
            align-items: center;
            margin-top: 20px;
        }
        
        .testimonial-author img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 15px;
        }
        
        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, var(--islamic-green), var(--dark-blue));
            color: white;
            padding: 80px 0;
            text-align: center;
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
            
            .package-price {
                font-size: 2.2rem;
            }
            
            .package-card {
                margin-bottom: 20px;
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
                        <a class="nav-link active" href="haji_page.php">Haji</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tentang_kami.php">Tentang Kami</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <div class="islamic-badge">
                    <i class="bi bi-heart-fill"></i> Rukun Islam Kelima
                </div>
                <h1>Paket Haji Terpercaya</h1>
                <p class="lead">Wujudkan impian suci Anda dengan paket haji terbaik dari PT Madinah Iman Wisata. Berpengalaman 15+ tahun melayani jamaah haji dengan penuh amanah.</p>
                <a href="form_haji.php" class="btn-primary-custom">
                    <i class="bi bi-calendar-check"></i>
                    Daftar Sekarang
                </a>
            </div>
        </div>
    </section>

    <!-- Packages Section -->
    <section class="packages-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold mb-3" style="color: var(--dark-blue);">Paket Haji Kami</h2>
                <p class="lead text-muted">Pilih paket haji yang sesuai dengan kebutuhan dan kemampuan Anda</p>
            </div>
            
            <div class="row">
                <!-- Haji Reguler -->
                <div class="col-lg-4 col-md-6">
                    <div class="package-card">
                        <div class="package-icon">
                            <i class="bi bi-building-check"></i>
                        </div>
                        <h3 class="package-title">Haji Reguler</h3>
                        <div class="package-price">
                            Rp 35.000.000
                            <small>/orang</small>
                        </div>
                        <ul class="package-features">
                            <li>Tiket pesawat PP</li>
                            <li>Visa haji</li>
                            <li>Hotel bintang 3-4</li>
                            <li>Makan 3x sehari</li>
                            <li>Transport AC</li>
                            <li>Perlengkapan haji</li>
                            <li>Bimbingan manasik</li>
                            <li>Mutawif berpengalaman</li>
                        </ul>
                        <a href="form_haji.php?paket=reguler" class="btn btn-outline-primary w-100 mt-3">Pilih Paket</a>
                    </div>
                </div>

                <!-- Haji Plus -->
                <div class="col-lg-4 col-md-6">
                    <div class="package-card package-premium">
                        <div class="package-badge">POPULER</div>
                        <div class="package-icon">
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <h3 class="package-title">Haji Plus</h3>
                        <div class="package-price">
                            Rp 85.000.000
                            <small>/orang</small>
                        </div>
                        <ul class="package-features">
                            <li>Semua fasilitas Haji Reguler</li>
                            <li>Hotel bintang 5</li>
                            <li>Kamar dekat Masjidil Haram</li>
                            <li>Kamar dekat Masjid Nabawi</li>
                            <li>Pemandu wisata khusus</li>
                            <li>City tour Madinah & Mekah</li>
                            <li>Ziarah makam Rasulullah</li>
                            <li>Asuransi perjalanan</li>
                        </ul>
                        <a href="form_haji.php?paket=plus" class="btn btn-primary w-100 mt-3">Pilih Paket</a>
                    </div>
                </div>

                <!-- Haji VIP -->
                <div class="col-lg-4 col-md-6">
                    <div class="package-card">
                        <div class="package-icon">
                            <i class="bi bi-gem"></i>
                        </div>
                        <h3 class="package-title">Haji VIP</h3>
                        <div class="package-price">
                            Rp 125.000.000
                            <small>/orang</small>
                        </div>
                        <ul class="package-features">
                            <li>Semua fasilitas Haji Plus</li>
                            <li>Hotel mewah bintang 5</li>
                            <li>Suite room dengan balkon</li>
                            <li>Private transport</li>
                            <li>Butler service</li>
                            <li>Spa & wellness</li>
                            <li>Exclusive dining</li>
                            <li>Meet & greet di airport</li>
                        </ul>
                        <a href="form_haji.php?paket=vip" class="btn btn-outline-primary w-100 mt-3">Pilih Paket</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold mb-3" style="color: var(--dark-blue);">Mengapa Memilih Kami?</h2>
                <p class="lead text-muted">Keunggulan layanan haji PT Madinah Iman Wisata</p>
            </div>
            
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Terpercaya</h4>
                        <p class="text-muted">Berlisensi resmi dari Kementerian Agama RI dan telah melayani ribuan jamaah</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Berpengalaman</h4>
                        <p class="text-muted">15+ tahun pengalaman memberangkatkan jamaah haji dengan tingkat kepuasan tinggi</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-heart-fill"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Pelayanan Prima</h4>
                        <p class="text-muted">Melayani jamaah seperti keluarga sendiri dengan penuh kasih sayang dan perhatian</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-headset"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Support 24/7</h4>
                        <p class="text-muted">Tim customer service siap membantu Anda kapan saja selama perjalanan haji</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Islamic Divider -->
    <div class="section-divider">
        <div class="islamic-ornament">☪ ❋ ☪</div>
    </div>

    <!-- Testimonials Section -->
    <section class="packages-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold mb-3" style="color: var(--dark-blue);">Testimoni Jamaah</h2>
                <p class="lead text-muted">Apa kata jamaah yang telah berangkat haji bersama kami</p>
            </div>
            
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="testimonial-card">
                        <p class="mb-4">"Alhamdulillah, perjalanan haji bersama MIW sangat berkesan. Pelayanannya luar biasa, seperti keluarga sendiri. Terima kasih telah membantu mewujudkan impian saya."</p>
                        <div class="testimonial-author">
                            <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='50' fill='%23d4af37'/><text x='50' y='60' text-anchor='middle' font-size='40' fill='white'>H</text></svg>" alt="Haji Ahmad">
                            <div>
                                <h6 class="mb-0 fw-bold">H. Ahmad Wijaya</h6>
                                <small class="text-muted">Haji 2023</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="testimonial-card">
                        <p class="mb-4">"Subhanallah, perjalanan yang sangat spirituil. Fasilitas hotel dekat Masjidil Haram memudahkan ibadah. Mutawif yang berpengalaman membuat perjalanan semakin khusyuk."</p>
                        <div class="testimonial-author">
                            <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='50' fill='%23228b22'/><text x='50' y='60' text-anchor='middle' font-size='40' fill='white'>S</text></svg>" alt="Hajjah Siti">
                            <div>
                                <h6 class="mb-0 fw-bold">Hj. Siti Nurhaliza</h6>
                                <small class="text-muted">Haji 2023</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="testimonial-card">
                        <p class="mb-4">"Pelayanan yang profesional dan penuh perhatian. Dari awal pendaftaran hingga kembali ke Indonesia, semuanya diatur dengan baik. Highly recommended!"</p>
                        <div class="testimonial-author">
                            <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='50' fill='%231e3a8a'/><text x='50' y='60' text-anchor='middle' font-size='40' fill='white'>B</text></svg>" alt="Haji Budi">
                            <div>
                                <h6 class="mb-0 fw-bold">H. Budi Santoso</h6>
                                <small class="text-muted">Haji 2022</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container text-center">
            <h2 class="display-4 fw-bold mb-4">Siap Menunaikan Ibadah Haji?</h2>
            <p class="lead mb-4">Jangan tunda lagi, daftarkan diri Anda sekarang dan wujudkan impian suci Anda bersama PT Madinah Iman Wisata</p>
            <a href="form_haji.php" class="btn-primary-custom me-3 mb-3">
                <i class="bi bi-calendar-check"></i>
                Daftar Haji Sekarang
            </a>
            <a href="tentang_kami.php" class="btn btn-outline-light mb-3">
                <i class="bi bi-info-circle"></i>
                Pelajari Lebih Lanjut
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

        // Add animation on scroll
        window.addEventListener('scroll', function() {
            const cards = document.querySelectorAll('.package-card, .feature-card, .testimonial-card');
            cards.forEach(card => {
                const cardTop = card.getBoundingClientRect().top;
                const cardVisible = 150;
                
                if (cardTop < window.innerHeight - cardVisible) {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }
            });
        });

        // Initialize card animations
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.package-card, .feature-card, .testimonial-card');
            cards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(50px)';
                card.style.transition = 'all 0.6s ease';
            });
        });
    </script>
</body>
</html>
