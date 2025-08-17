<?php
session_start();
include 'config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paket Umroh Agustus 2025 - Madinah Iman Wisata</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="⭐⭐⭐⭐⭐Paket Umroh Agustus 2025 Madinah Iman Wisata. ✈️ Berpengalaman lebih 15th memberangkatkan jama'ah haji & umroh. Hotel dekat Masjid Haram & Nabawi.">
    <meta name="keywords" content="umroh, umroh 2025, paket umroh, travel umroh, madinah iman wisata, umroh agustus">
    <meta name="robots" content="follow, index">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://miw.co.id/umroh">
    <meta property="og:title" content="Paket Umroh Agustus 2025 - Madinah Iman Wisata">
    <meta property="og:description" content="⭐⭐⭐⭐⭐Paket Umroh Agustus 2025 Madinah Iman Wisata. Hotel dekat Masjid Haram & Nabawi. Garuda Indonesia.">
    <meta property="og:image" content="https://miw.co.id/wp-content/uploads/2025/08/umroh-agustus-2.jpeg">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://miw.co.id/umroh">
    <meta property="twitter:title" content="Paket Umroh Agustus 2025 - Madinah Iman Wisata">
    <meta property="twitter:description" content="⭐⭐⭐⭐⭐Paket Umroh Agustus 2025 Madinah Iman Wisata. Hotel dekat Masjid Haram & Nabawi. Garuda Indonesia.">
    <meta property="twitter:image" content="https://miw.co.id/wp-content/uploads/2025/08/umroh-agustus-2.jpeg">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="miw_logo.png">
    
    <!-- CSS -->
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            color: #333;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Header */
        .header {
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }
        
        .logo img {
            height: 50px;
            width: auto;
        }
        
        .nav-links {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            gap: 30px;
        }
        
        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav-links a:hover {
            color: #39a3d1;
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #39a3d1 0%, #2c8eb3 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
        }
        
        .hero h1 {
            font-size: 3em;
            margin-bottom: 20px;
            font-weight: 700;
        }
        
        .hero p {
            font-size: 1.2em;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        
        /* License Section */
        .license-section {
            padding: 60px 0;
            background: #f8f9fa;
        }
        
        .license-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            align-items: center;
        }
        
        .license-box {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .license-icon {
            font-size: 3em;
            color: #39a3d1;
            margin-bottom: 20px;
        }
        
        /* Packages Section */
        .packages-section {
            padding: 80px 0;
        }
        
        .section-title {
            text-align: center;
            font-size: 2.5em;
            margin-bottom: 20px;
            color: #333;
        }
        
        .section-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 60px;
            font-size: 1.1em;
        }
        
        .packages-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 40px;
            margin-bottom: 60px;
        }
        
        .package-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s;
        }
        
        .package-card:hover {
            transform: translateY(-5px);
        }
        
        .package-header {
            padding: 30px;
            text-align: center;
        }
        
        .package-title {
            color: #39a3d1;
            font-size: 1.5em;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .package-type {
            color: #666;
            font-size: 1.1em;
        }
        
        .package-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }
        
        .package-features {
            padding: 30px;
        }
        
        .feature-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .feature-list li {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
            position: relative;
            padding-left: 30px;
        }
        
        .feature-list li:before {
            content: "✓";
            color: #28a745;
            font-weight: bold;
            position: absolute;
            left: 0;
        }
        
        /* Benefits Section */
        .benefits-section {
            padding: 80px 0;
            background: #f8f9fa;
        }
        
        .benefits-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }
        
        .benefits-image img {
            width: 100%;
            border-radius: 15px;
        }
        
        .benefits-content h3 {
            color: #39a3d1;
            font-size: 2em;
            margin-bottom: 30px;
        }
        
        .benefits-list {
            list-style: none;
            padding: 0;
        }
        
        .benefits-list li {
            padding: 10px 0;
            padding-left: 30px;
            position: relative;
        }
        
        .benefits-list li:before {
            content: "✓";
            color: #28a745;
            font-weight: bold;
            position: absolute;
            left: 0;
        }
        
        /* Award Section */
        .award-section {
            padding: 80px 0;
            text-align: center;
        }
        
        .award-title {
            font-size: 2.2em;
            margin-bottom: 40px;
            color: #333;
        }
        
        .award-image img {
            max-width: 400px;
            width: 100%;
            border-radius: 15px;
        }
        
        /* Testimonials */
        .testimonials-section {
            padding: 80px 0;
            background: #f8f9fa;
        }
        
        .testimonial-card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 40px;
        }
        
        .testimonial-content {
            font-style: italic;
            margin-bottom: 20px;
            font-size: 1.1em;
            line-height: 1.8;
        }
        
        .testimonial-author {
            font-weight: 600;
            color: #39a3d1;
        }
        
        /* Videos Section */
        .videos-section {
            padding: 80px 0;
        }
        
        .videos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 40px;
        }
        
        .video-wrapper {
            position: relative;
            width: 100%;
            height: 300px;
            border-radius: 15px;
            overflow: hidden;
        }
        
        .video-wrapper iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        
        /* Gallery Section */
        .gallery-section {
            padding: 80px 0;
            background: #f8f9fa;
        }
        
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            cursor: pointer;
            transition: transform 0.3s;
        }
        
        .gallery-item:hover {
            transform: scale(1.05);
        }
        
        .gallery-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        /* FAQ Section */
        .faq-section {
            padding: 80px 0;
        }
        
        .faq-item {
            background: white;
            margin-bottom: 20px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .faq-question {
            padding: 20px;
            background: #39a3d1;
            color: white;
            cursor: pointer;
            font-weight: 600;
        }
        
        .faq-answer {
            padding: 20px;
            display: none;
        }
        
        .faq-answer.active {
            display: block;
        }
        
        /* CTA Button */
        .cta-button {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            text-align: center;
            margin: 30px auto;
            transition: background 0.3s;
        }
        
        .cta-button:hover {
            background: #218838;
        }
        
        /* Footer */
        .footer {
            background: #333;
            color: white;
            padding: 40px 0;
            text-align: center;
        }
        
        .social-links {
            margin-top: 20px;
        }
        
        .social-links a {
            color: white;
            font-size: 1.5em;
            margin: 0 10px;
            text-decoration: none;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            
            .hero h1 {
                font-size: 2em;
            }
            
            .packages-grid {
                grid-template-columns: 1fr;
            }
            
            .benefits-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            
            .videos-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="nav">
                <div class="logo">
                    <img src="miw_logo.png" alt="Madinah Iman Wisata">
                </div>
                <ul class="nav-links">
                    <li><a href="index.php">Beranda</a></li>
                    <li><a href="umroh_page.php">Umroh</a></li>
                    <li><a href="haji_page.php">Haji</a></li>
                    <li><a href="tentang_kami.php">Tentang Kami</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Paket Umroh Agustus 2025</h1>
            <p>Alhamdulillah, 100% Jama'ah Berangkat Sesuai Jadwal</p>
            <p>Hotel dekat, cukup jalan kaki. Bimbingan sabar untuk lansia & anak-anak. Pastikan ibadah keluarga Anda berjalan lancar & khusyuk. Amankan kursi Anda!</p>
        </div>
    </section>

    <!-- License Section -->
    <section class="license-section">
        <div class="container">
            <div class="license-content">
                <div class="license-box">
                    <div class="license-icon">📜</div>
                    <h4>Izin resmi kemenag</h4>
                    <p><strong>Izin Sebagai Penyelenggara Umrah</strong>: SK Umroh No U.215 2021 & <strong>Izin Sebagai Penyelenggara Haji Khusus</strong>: SK Haji No. 319 2021</p>
                </div>
                <div class="license-box">
                    <div class="license-icon">🏆</div>
                    <h4>Penghargaan Nasional</h4>
                    <p>Travel akreditasi A. Penghargaan <strong>Indonesia Leading Umroh Travel Agent 2023/2024</strong></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Packages Section -->
    <section class="packages-section">
        <div class="container">
            <h2 class="section-title">Paket Umroh Agustus 2025</h2>
            <p class="section-subtitle">Pilih paket umroh terbaik untuk keluarga Anda</p>
            
            <div class="packages-grid">
                <!-- Package 1 -->
                <div class="package-card">
                    <div class="package-header">
                        <h3 class="package-title">01. Umroh Agustus</h3>
                        <p class="package-type">Reguler</p>
                    </div>
                    <img src="https://miw.co.id/wp-content/uploads/2025/08/umroh-agustus-2.jpeg" alt="Umroh Agustus" class="package-image">
                    <div class="package-features">
                        <ul class="feature-list">
                            <li>Tanggal 6 Agustus 2025 9D</li>
                            <li>Landing Madinah By Garuda Indonesia</li>
                            <li>Hotel Mekah Ayjad Makareem *4</li>
                            <li>Hotel Madinah Golden Tulip *4</li>
                        </ul>
                    </div>
                </div>

                <!-- Package 2 -->
                <div class="package-card">
                    <div class="package-header">
                        <h3 class="package-title">02. Umroh Agustus</h3>
                        <p class="package-type">Reguler</p>
                    </div>
                    <img src="https://miw.co.id/wp-content/uploads/2025/08/umroh-agustus-garuda-2.jpeg" alt="Umroh Agustus Garuda" class="package-image">
                    <div class="package-features">
                        <ul class="feature-list">
                            <li>Tanggal 27 Agustus 2025 9D</li>
                            <li>Direct Jeddah By Garuda Indonesia</li>
                            <li>Hotel Mekah Al Shohada *4</li>
                            <li>Hotel Madinah Golden Tulip *4</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="benefits-section">
        <div class="container">
            <div class="benefits-grid">
                <div class="benefits-image">
                    <img src="https://miw.co.id/wp-content/uploads/2025/08/umroh-plus-turki-madinah-iman-wisata-2.png" alt="Benefit Umroh">
                </div>
                <div class="benefits-content">
                    <h3>Benefit Lainnya</h3>
                    <p><strong>Travel umroh dengan Rating Google Terbaik</strong></p>
                    <img src="https://miw.co.id/wp-content/uploads/2025/08/testimoni-2.jpg" alt="Testimoni" style="width: 250px; margin: 20px 0;">
                    <ul class="benefits-list">
                        <li>Program Manasik Intensif sebelum keberangkatan</li>
                        <li>Fasilitasi hingga 2x umroh (bisa dihadiahkan untuk orang tua yang sudah wafat)</li>
                        <li>Sholat 5 waktu di masjid Nabawi & Haram</li>
                        <li>Kajian Islam selama di Mekah & Madinah</li>
                        <li>Berdoa di Raudhoh dengan jadwal terkordinasi</li>
                        <li>Bus full AC, Wi-Fi, dan nyaman selama city tour</li>
                        <li>Tersedia Program Pembiyaan Syariah Tanpa Bunga</li>
                        <li>Harga jujur & tidak ada Hidden Fee</li>
                        <li>Diskon grup/paket keluarga</li>
                        <li>Souvenir Cantik Madinah Iman Wisata</li>
                        <li>Dokumentasi perjalanan umroh</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section style="padding: 60px 0; text-align: center; background: #39a3d1; color: white;">
        <div class="container">
            <a href="https://miw.co.id/wa/madinah-iman-wisata/" class="cta-button" target="_blank">Konsultasi Paket Umroh Sekarang!</a>
        </div>
    </section>

    <!-- Award Section -->
    <section class="award-section">
        <div class="container">
            <h2 class="award-title">Alhamdulillah, PT Madinah Iman Wisata mendapatkan Penghargaan Nasional Sebagai Indonesia Leading Umrah Travel Agent 2023/2024</h2>
            <div class="award-image">
                <img src="https://miw.co.id/wp-content/uploads/2025/08/penghargaan-travel-umroh-terpercaya-2.jpg" alt="Penghargaan Travel Umroh Terpercaya">
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section">
        <div class="container">
            <h2 class="section-title">Testimoni Jama'ah</h2>
            
            <div class="testimonial-card">
                <div style="display: flex; align-items: center; margin-bottom: 20px;">
                    <img src="https://miw.co.id/wp-content/uploads/2025/08/ketua-koni-150x150-1-1-2.png" alt="Ketua KONI" style="width: 80px; height: 80px; border-radius: 50%; margin-right: 20px;">
                    <div>
                        <p class="testimonial-content">Atas Nama keluarga dan rekan-rekan pengurus KONI Provinsi Jawa Barat, saya mengucapkan terimakasih yang tak terhingga. Kami puas dengan pelayanan MIW Travel.</p>
                        <p class="testimonial-author">BRIGJEN H. AHMAD SAFUDIN, SE, MM - Ketua KONI Jabar</p>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <div style="display: flex; align-items: center; margin-bottom: 20px;">
                    <img src="https://miw.co.id/wp-content/uploads/2025/08/cut-yanti.jpg" alt="Cut Yanti" style="width: 80px; height: 80px; border-radius: 50%; margin-right: 20px;">
                    <div>
                        <p class="testimonial-content">Menurut saya MIW travel terbaik. Dulu, pas ke Raudhah singkat banget, tapi tadi malam saya dibimbing MIW bisa sholat 2 rakaat dengan nyaman di sana. Makanannya juga enak, tidurnya enak. Atas nama keluarga saya mengucapkan terima kasih kepada MIW Travel</p>
                        <p class="testimonial-author">Cut Yanti - Artis</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Videos Section -->
    <section class="videos-section">
        <div class="container">
            <h2 class="section-title">Video Testimoni & Pengalaman</h2>
            <div class="videos-grid">
                <div class="video-wrapper">
                    <iframe src="https://www.youtube.com/embed/eX7awpJFQCs" title="Testimoni Jamaah Umroh Madinah Iman Wisata" allowfullscreen></iframe>
                </div>
                <div class="video-wrapper">
                    <iframe src="https://www.youtube.com/embed/u9FS-zN-oPQ" title="Pengalaman Ibadah Umroh Bersama Madinah Iman Wisata" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="gallery-section">
        <div class="container">
            <h2 class="section-title">Dokumentasi Haji & Umroh</h2>
            <div class="gallery-grid">
                <div class="gallery-item">
                    <img src="https://miw.co.id/wp-content/uploads/2025/08/1-630x380.jpg" alt="Dokumentasi 1">
                </div>
                <div class="gallery-item">
                    <img src="https://miw.co.id/wp-content/uploads/2025/08/2-630x380.jpg" alt="Dokumentasi 2">
                </div>
                <div class="gallery-item">
                    <img src="https://miw.co.id/wp-content/uploads/2025/08/3-630x380.jpg" alt="Dokumentasi 3">
                </div>
                <div class="gallery-item">
                    <img src="https://miw.co.id/wp-content/uploads/2025/08/9-630x380.jpg" alt="Dokumentasi 4">
                </div>
                <div class="gallery-item">
                    <img src="https://miw.co.id/wp-content/uploads/2025/08/16-630x380.jpg" alt="Dokumentasi 5">
                </div>
                <div class="gallery-item">
                    <img src="https://miw.co.id/wp-content/uploads/2025/08/17-630x380.jpg" alt="Dokumentasi 6">
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <h2 class="section-title">Tanya Jawab Layanan Umroh</h2>
            
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    Berapa Jarak Hotel dengan Masjid Haram & Masjid Nabawi?
                </div>
                <div class="faq-answer">
                    <p>Kami mengutamakan kedekatan hotel dengan masjid. Baik saat di Mekah maupun di Madinah. Alhamdulillah MIW telah bekerjasama dengan beberapa hotel terdekat dengan masjid Haram atau masjid Nabawi. Jarak tempuh singkat walau hanya berjalan kaki.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    Apakah MIW mengadakan manasik untuk Haji atau Umroh?
                </div>
                <div class="faq-answer">
                    <p><strong>Manasik</strong> adalah agenda wajib yang harus ada sebelum keberangkatan jama'ah MIW.</p>
                    <p>Untuk kenyamanan dan dalam meningkatkan pemahaman jama'ah, MIW mengadakan manasik di dalam gedung pertemuan atau ruang pertemuan hotel</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Madinah Iman Wisata. All rights reserved.</p>
            <div class="social-links">
                <a href="#">📱</a>
                <a href="#">📧</a>
                <a href="#">📍</a>
            </div>
        </div>
    </footer>

    <script>
        function toggleFAQ(element) {
            const answer = element.nextElementSibling;
            answer.classList.toggle('active');
        }
    </script>
</body>
</html>
