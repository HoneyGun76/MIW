<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to beranda.html as the main landing page
header('Location: beranda.html');
exit();
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
    <title>PT Madinah Iman Wisata - Travel Haji Dan Umroh Terbaik</title>
    <meta name="description" content="⭐⭐⭐⭐⭐PT Madinah Iman Wisata Travel Haji & Umroh Terbaik. ✈️ Berpengalaman lebih 15th memberangkatkan jama'ah haji & umroh. Melayani jamaah lebih baik."/>
    <meta name="robots" content="follow, index, max-snippet:-1, max-video-preview:-1, max-image-preview:large"/>
    <link rel="canonical" href="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" />
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:locale" content="id_ID" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="PT Madinah Iman Wisata - Travel Haji Dan Umroh Terbaik" />
    <meta property="og:description" content="⭐⭐⭐⭐⭐PT Madinah Iman Wisata Travel Haji & Umroh Terbaik. ✈️ Berpengalaman lebih 15th memberangkatkan jama'ah haji & umroh. Melayani jamaah lebih baik." />
    <meta property="og:url" content="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" />
    <meta property="og:site_name" content="PT Madinah Iman Wisata" />
    <meta property="og:image" content="<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/miw_logo.png" />
    
    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="PT Madinah Iman Wisata - Travel Haji Dan Umroh Terbaik" />
    <meta name="twitter:description" content="⭐⭐⭐⭐⭐PT Madinah Iman Wisata Travel Haji & Umroh Terbaik. ✈️ Berpengalaman lebih 15th memberangkatkan jama'ah haji & umroh. Melayani jamaah lebih baik." />
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
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-gold: #d4af37;
            --secondary-green: #228b22;
            --dark-blue: #1e3a8a;
            --light-gray: #f8f9fa;
            --white: #ffffff;
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
            background: linear-gradient(rgba(30, 58, 138, 0.8), rgba(30, 58, 138, 0.8)), url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 300"><defs><pattern id="kaaba" patternUnits="userSpaceOnUse" width="100" height="100"><rect width="100" height="100" fill="%23f8f9fa" opacity="0.1"/><circle cx="50" cy="50" r="20" fill="%23d4af37" opacity="0.3"/></pattern></defs><rect width="100%" height="100%" fill="url(%23kaaba)"/></svg>');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 120px 0;
            text-align: center;
        }
        
        .hero-section h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .hero-section .lead {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        .btn-primary-custom {
            background: linear-gradient(45deg, var(--primary-gold), #b8860b);
            border: none;
            padding: 15px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(212, 175, 55, 0.3);
            color: white;
        }
        
        /* Services Section */
        .services-section {
            padding: 80px 0;
            background: var(--light-gray);
        }
        
        .service-card {
            background: white;
            border-radius: 15px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .service-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, var(--primary-gold), #b8860b);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
            color: white;
        }
        
        .service-card h4 {
            color: var(--dark-blue);
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .service-card p {
            color: #666;
            margin-bottom: 25px;
        }
        
        .btn-service {
            background: var(--secondary-green);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-service:hover {
            background: #1e7e1e;
            transform: scale(1.05);
            color: white;
        }
        
        /* Why Choose Us Section */
        .why-choose-section {
            padding: 80px 0;
            background: white;
        }
        
        .feature-box {
            text-align: center;
            padding: 30px 20px;
        }
        
        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(45deg, var(--secondary-green), #1e7e1e);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 1.5rem;
            color: white;
        }
        
        /* Contact Section */
        .contact-section {
            padding: 80px 0;
            background: var(--dark-blue);
            color: white;
        }
        
        .contact-info {
            background: rgba(255,255,255,0.1);
            padding: 30px;
            border-radius: 15px;
            backdrop-filter: blur(10px);
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .contact-item i {
            width: 40px;
            height: 40px;
            background: var(--primary-gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.2rem;
        }
        
        /* Footer */
        .footer {
            background: #1a1a1a;
            color: white;
            padding: 50px 0 30px;
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
        
        .footer a {
            color: #ccc;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer a:hover {
            color: var(--primary-gold);
        }
        
        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: var(--primary-gold);
            color: white;
            border-radius: 50%;
            margin-right: 10px;
            transition: all 0.3s ease;
        }
        
        .social-links a:hover {
            background: var(--secondary-green);
            transform: translateY(-3px);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 2.5rem;
            }
            
            .hero-section .lead {
                font-size: 1.1rem;
            }
            
            .service-card {
                margin-bottom: 30px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="./">
                <img src="miw_logo.png" alt="MIW Logo" height="50">
                <span class="fw-bold text-white">PT. Madinah Iman Wisata</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="./">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="haji_page.php">Haji</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="umroh_page.php">Umroh</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tentang_kami.php">Tentang Kami</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_dashboard.php">Admin</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="mb-4">Selamat Datang di PT. Madinah Iman Wisata</h1>
            <p class="lead mb-4">Travel Haji & Umroh Terpercaya dengan Pengalaman Lebih dari 15 Tahun</p>
            <p class="mb-5">Melayani jamaah dengan penuh kekeluargaan menuju Tanah Suci</p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="haji_page.php" class="btn-primary-custom">
                    <i class="bi bi-building"></i>
                    Paket Haji
                </a>
                <a href="umroh_page.php" class="btn-primary-custom">
                    <i class="bi bi-geo-alt"></i>
                    Paket Umroh
                </a>
            </div>
        </div>
    </section>
    
    <!-- Services Section -->
    <section class="services-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold" style="color: var(--dark-blue);">Layanan Kami</h2>
                <p class="text-muted">Pilihan paket perjalanan spiritual terbaik untuk Anda</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="bi bi-building"></i>
                        </div>
                        <h4>Paket Haji</h4>
                        <p>Paket haji reguler dan plus dengan fasilitas terbaik dan pembimbing berpengalaman</p>
                        <a href="haji_page.php" class="btn-service">
                            <i class="bi bi-arrow-right"></i>
                            Lihat Paket
                        </a>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <h4>Paket Umroh</h4>
                        <p>Berbagai pilihan paket umroh dengan jadwal fleksibel dan harga terjangkau</p>
                        <a href="umroh_page.php" class="btn-service">
                            <i class="bi bi-arrow-right"></i>
                            Lihat Paket
                        </a>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <h4>Konsultasi Gratis</h4>
                        <p>Tim profesional siap membantu Anda memilih paket yang sesuai kebutuhan</p>
                        <a href="tentang_kami.php" class="btn-service">
                            <i class="bi bi-telephone"></i>
                            Hubungi Kami
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Why Choose Us Section -->
    <section class="why-choose-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold" style="color: var(--dark-blue);">Mengapa Memilih Kami?</h2>
                <p class="text-muted">Keunggulan yang membuat kami berbeda</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="bi bi-award"></i>
                        </div>
                        <h5>Berpengalaman</h5>
                        <p>Lebih dari 15 tahun melayani jamaah haji dan umroh</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h5>Terpercaya</h5>
                        <p>Berizin resmi dan terdaftar di Kementerian Agama</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="bi bi-heart"></i>
                        </div>
                        <h5>Pelayanan Prima</h5>
                        <p>Melayani jamaah dengan sepenuh hati seperti keluarga</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                        <h5>Harga Bersaing</h5>
                        <p>Paket dengan harga terjangkau tanpa mengurangi kualitas</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-4">Hubungi Kami Sekarang</h2>
                    <p class="mb-4">Siap melayani Anda untuk perjalanan spiritual yang tak terlupakan</p>
                    <div class="contact-info">
                        <div class="contact-item">
                            <i class="bi bi-telephone"></i>
                            <div>
                                <strong>Telepon</strong><br>
                                (022) 1234-5678
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="bi bi-whatsapp"></i>
                            <div>
                                <strong>WhatsApp</strong><br>
                                +62 812-3456-7890
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="bi bi-envelope"></i>
                            <div>
                                <strong>Email</strong><br>
                                info@miw.co.id
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="bi bi-geo-alt"></i>
                            <div>
                                <strong>Alamat</strong><br>
                                Jl. Raya Bandung No. 123, Bandung
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-center">
                        <img src="miw_logo.png" alt="MIW Logo" style="max-width: 300px; opacity: 0.8;">
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="footer-brand">
                        <img src="miw_logo.png" alt="MIW Logo">
                        <span class="h5 mb-0">PT. Madinah Iman Wisata</span>
                    </div>
                    <p>Travel haji dan umroh terpercaya dengan pelayanan terbaik untuk perjalanan spiritual Anda.</p>
                    <div class="social-links">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-whatsapp"></i></a>
                        <a href="#"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-3 mb-4">
                    <h5>Layanan</h5>
                    <ul class="list-unstyled">
                        <li><a href="haji_page.php">Paket Haji</a></li>
                        <li><a href="umroh_page.php">Paket Umroh</a></li>
                        <li><a href="tentang_kami.php">Konsultasi</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 mb-4">
                    <h5>Informasi</h5>
                    <ul class="list-unstyled">
                        <li><a href="tentang_kami.php">Tentang Kami</a></li>
                        <li><a href="form_pembatalan.php">Pembatalan</a></li>
                        <li><a href="admin_dashboard.php">Login Admin</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 mb-4">
                    <h5>Kontak</h5>
                    <p class="mb-1">(022) 1234-5678</p>
                    <p class="mb-1">info@miw.co.id</p>
                    <p>Bandung, Indonesia</p>
                </div>
            </div>
            
            <hr style="border-color: #444;">
            
            <div class="text-center">
                <p class="mb-0">&copy; 2025 PT. Madinah Iman Wisata. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
        
        // Navbar background change on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.background = 'linear-gradient(135deg, rgba(212, 175, 55, 0.95) 0%, rgba(184, 134, 11, 0.95) 100%)';
                navbar.style.backdropFilter = 'blur(10px)';
            } else {
                navbar.style.background = 'linear-gradient(135deg, var(--primary-gold) 0%, #b8860b 100%)';
                navbar.style.backdropFilter = 'none';
            }
        });
    </script>
</body>
</html>
