<?php
// Include configuration
require_once 'config.php';

// Set current page for navigation highlighting
$current_page = 'profil';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>About Us - Madinah Iman Wisata Pusat</title>
    <meta name="description" content="⭐⭐⭐⭐⭐about us: Madinah Iman Wisata Travel ✈️ Berpengalaman lebih 15th memberangkatkan jamaah haji & umroh. Melayani lebih baik.">
    <meta name="keywords" content="about us madinah iman wisata, travel haji umroh, iTTA award winning, perusahaan travel terpercaya, PPIU, PIHK">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:locale" content="id_ID">
    <meta property="og:type" content="article">
    <meta property="og:title" content="About Us - Madinah Iman Wisata Pusat">
    <meta property="og:description" content="⭐⭐⭐⭐⭐about us: Madinah Iman Wisata Travel ✈️ Berpengalaman lebih 15th memberangkatkan jamaah haji & umroh. Melayani lebih baik.">
    <meta property="og:url" content="https://miw.co.id/profil.php">
    <meta property="og:site_name" content="Madinah Iman Wisata Bandung">
    <meta property="og:image" content="https://miw.co.id/wp-content/uploads/2025/08/1.jpg">
    
    <!-- CSS Styles -->
    <style>
        @import url("//fonts.googleapis.com/css?family=Nunito:400,400i,600,600i&subset=latin");
        @import url("//fonts.googleapis.com/css?family=Jost:400,400i,600,600i,500&subset=latin");
        
        :root {
            --tcb-color-0: rgb(246, 8, 8);
            --tcb-color-1: rgb(6, 6, 6);
            --tcb-color-9: rgb(255, 204, 41);
            --tcb-color-10: rgb(239, 157, 67);
            --tcb-color-11: rgb(26, 26, 26);
            --tcb-tpl-color-4: rgb(30, 31, 39);
            --tcb-tpl-color-5: rgb(246, 177, 39);
            --tcb-tpl-color-8: rgb(255, 255, 255);
            --tcb-tpl-color-11: rgb(108, 110, 122);
        }
        
        body {
            font-family: 'Nunito', sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            color: var(--tcb-tpl-color-4);
            background-color: #fff;
        }
        
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url("https://miw.co.id/wp-content/uploads/2025/08/18.jpg");
            background-size: cover;
            background-position: 50% 94%;
            color: white;
            padding: 4rem 0;
            text-align: center;
            min-height: 521px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .hero-logo {
            width: 100px;
            height: 100px;
            border: 2px solid #fff;
            border-radius: 50%;
            margin-bottom: 2rem;
        }
        
        .hero-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .hero-subtitle {
            letter-spacing: 10px;
            font-size: 18px;
            text-transform: uppercase;
            font-weight: 400;
            margin-bottom: 1rem;
            color: hsla(39, 100%, 95%, 1);
        }
        
        .hero-title {
            font-size: 3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            max-width: 80%;
        }
        
        .hero-description {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            max-width: 50%;
        }
        
        /* Orange Section */
        .orange-section {
            background-color: var(--tcb-color-10);
            padding: 82px 40px 55px;
            text-align: center;
        }
        
        .orange-section h2 {
            font-size: 45px;
            font-weight: 600;
            color: var(--tcb-tpl-color-8);
            margin-bottom: 1rem;
        }
        
        .orange-section .subtitle {
            font-size: 32px;
            font-weight: 600;
            color: var(--tcb-color-11);
            margin-bottom: 1rem;
        }
        
        .orange-section p {
            font-size: 18px;
            color: var(--tcb-color-11);
            max-width: 850px;
            margin: 0 auto;
            line-height: 1.6;
        }
        
        /* Partners Section */
        .partners-section {
            padding: 40px;
            text-align: center;
        }
        
        .partners-img {
            max-width: 656px;
            margin: 0 auto;
            box-shadow: rgba(0,0,0,0.25) 0px 8px 12px 0px;
        }
        
        .partners-img img {
            width: 100%;
            height: auto;
        }
        
        /* Vision Mission Section */
        .vision-mission-section {
            padding: 40px;
        }
        
        .vm-container {
            max-width: 850px;
            margin: 0 auto;
        }
        
        .vm-title {
            font-size: 38px;
            font-weight: 600;
            color: var(--tcb-tpl-color-4);
            margin-bottom: 1.5rem;
        }
        
        .vm-text {
            font-size: 17px;
            color: var(--tcb-tpl-color-4);
            margin-bottom: 2rem;
            line-height: 1.75;
        }
        
        .mission-list {
            list-style: none;
            padding: 0;
        }
        
        .mission-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        
        .mission-icon {
            color: var(--tcb-color-0);
            font-size: 26px;
            margin-right: 14px;
            margin-top: 0.2rem;
            flex-shrink: 0;
        }
        
        .mission-text {
            font-size: 17px;
            line-height: 1.75;
            color: var(--tcb-tpl-color-4);
        }
        
        /* Legal Section */
        .legal-section {
            background: linear-gradient(var(--tcb-color-10), var(--tcb-color-10));
            padding: 40px;
        }
        
        .legal-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            max-width: 1123px;
            margin: 0 auto;
        }
        
        .legal-column h3 {
            font-size: 38px;
            font-weight: 600;
            color: var(--tcb-tpl-color-4);
            margin-bottom: 1.5rem;
        }
        
        .legal-list {
            list-style: none;
            padding: 0;
            counter-reset: legal-counter;
        }
        
        .legal-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            counter-increment: legal-counter;
        }
        
        .legal-number {
            background: var(--tcb-tpl-color-4);
            color: white;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 1rem;
            flex-shrink: 0;
            font-size: 14px;
        }
        
        .legal-number::before {
            content: counter(legal-counter);
        }
        
        .legal-text {
            font-size: 17px;
            line-height: 1.6;
            color: var(--tcb-tpl-color-4);
        }
        
        /* Gallery Section */
        .gallery-section {
            padding: 40px;
            text-align: center;
        }
        
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-top: 30px;
            max-width: 1123px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .gallery-item {
            border-radius: 8px;
            overflow: hidden;
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
        
        /* Yellow Section */
        .yellow-section {
            background-color: var(--tcb-color-9);
            padding: 60px 40px;
        }
        
        .yellow-content {
            display: grid;
            grid-template-columns: 54.8% 45.2%;
            gap: 2rem;
            align-items: center;
        }
        
        .yellow-text {
            padding-right: 40px;
        }
        
        .yellow-text h3 {
            font-size: 38px;
            font-weight: 600;
            color: var(--tcb-tpl-color-4);
            margin-bottom: 1.5rem;
        }
        
        .yellow-text p {
            font-size: 18px;
            color: var(--tcb-tpl-color-8);
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        
        .yellow-image {
            text-align: center;
        }
        
        .yellow-image img {
            max-width: 100%;
            height: auto;
        }
        
        /* Contact Section */
        .contact-section {
            background: #333;
            color: white;
            padding: 3rem 0;
        }
        
        .contact-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
        }
        
        .contact-info h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .contact-info p {
            margin-bottom: 0.5rem;
        }
        
        .map-container {
            border-radius: 8px;
            overflow: hidden;
        }
        
        .map-container iframe {
            width: 100%;
            height: 300px;
            border: none;
        }
        
        /* Responsive Design */
        @media (max-width: 1023px) {
            .hero-title {
                font-size: 2.5rem;
                max-width: 100%;
            }
            
            .hero-description {
                max-width: 80%;
            }
            
            .orange-section {
                padding: 40px;
            }
            
            .legal-container {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .yellow-content {
                grid-template-columns: 39% 61%;
            }
            
            .yellow-text {
                padding-right: 0;
                margin-bottom: 0;
            }
            
            .gallery-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        @media (max-width: 767px) {
            .hero-section {
                min-height: 346px;
                padding: 2rem 0;
            }
            
            .hero-subtitle {
                letter-spacing: 7px;
                font-size: 11px;
            }
            
            .hero-title {
                font-size: 2rem;
                max-width: 90%;
            }
            
            .hero-description {
                max-width: 80%;
            }
            
            .orange-section {
                padding: 42px 20px 30px;
            }
            
            .orange-section h2 {
                font-size: 36px;
            }
            
            .orange-section .subtitle {
                font-size: 21px;
            }
            
            .partners-section {
                padding: 30px 20px;
            }
            
            .partners-img {
                max-width: 289px;
            }
            
            .vision-mission-section {
                padding: 20px;
            }
            
            .vm-title {
                font-size: 32px;
            }
            
            .legal-section {
                padding: 20px;
            }
            
            .yellow-section {
                padding: 20px;
            }
            
            .yellow-content {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .yellow-image {
                margin-bottom: 30px;
            }
            
            .gallery-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .contact-content {
                grid-template-columns: 1fr;
            }
    </style>
</head>
<body>
    <?php include 'navigation.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-logo">
                <img src="https://miw.co.id/wp-content/uploads/2025/08/1.jpg" alt="MIW Logo">
            </div>
            <div class="hero-subtitle">tentang kami</div>
            <h1 class="hero-title">MADINAH IMAN WISATA TRAVEL</h1>
            <p class="hero-description">Kami telah berpengalaman lebih dari 15 tahun memberangkatkan jamaah haji & umroh</p>
        </div>
    </section>

    <!-- Orange Section -->
    <section class="orange-section">
        <div class="container">
            <h2>TENTANG KAMI</h2>
            <div class="subtitle">Madinah Iman Wisata Travel</div>
            <p>merupakan biro travel yang bergerak di bidang perjalanan, baik domestik maupun internasional, khususnya perjalanan ibadah haji dan umroh ke tanah suci Makkah dan Madinah. Kami telah berpengalaman lebih dari 15 tahun memberangkatkan jamaah haji dan umroh dengan pelayanan terbaik dan memiliki izin resmi dari pemerintah.</p>
        </div>
    </section>

    <!-- Partners Section -->
    <section class="partners-section">
        <div class="container">
            <div class="partners-img">
                <img src="https://miw.co.id/wp-content/uploads/2025/08/15.jpg" alt="Partners">
            </div>
        </div>
    </section>

    <!-- Vision Mission Section -->
    <section class="vision-mission-section">
        <div class="container">
            <div class="vm-container">
                <h3 class="vm-title">VISI & MISI</h3>
                <p class="vm-text">Menjadi biro perjalanan haji dan umroh terpercaya yang memberikan pelayanan terbaik untuk membantu jamaah mencapai ibadah yang mabrur dan berkah. Dengan komitmen untuk terus berkembang dan berinovasi dalam memberikan pengalaman spiritual yang berkesan bagi setiap jamaah.</p>
                
                <h3 class="vm-title">MISI KAMI:</h3>
                <ul class="mission-list">
                    <li class="mission-item">
                        <span class="mission-icon">⭐</span>
                        <span class="mission-text">Memberikan pelayanan terbaik dan berkualitas tinggi kepada seluruh jamaah haji dan umroh</span>
                    </li>
                    <li class="mission-item">
                        <span class="mission-icon">⭐</span>
                        <span class="mission-text">Menyediakan paket perjalanan yang terjangkau tanpa mengurangi kualitas pelayanan</span>
                    </li>
                    <li class="mission-item">
                        <span class="mission-icon">⭐</span>
                        <span class="mission-text">Membantu jamaah mempersiapkan diri secara spiritual dan praktis untuk menunaikan ibadah</span>
                    </li>
                    <li class="mission-item">
                        <span class="mission-icon">⭐</span>
                        <span class="mission-text">Menjalin kemitraan yang kuat dengan berbagai pihak untuk kemudahan dan kenyamanan jamaah</span>
                    </li>
                    <li class="mission-item">
                        <span class="mission-icon">⭐</span>
                        <span class="mission-text">Mengutamakan keamanan, kenyamanan, dan kepuasan jamaah selama perjalanan</span>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Legal Section -->
    <section class="legal-section">
        <div class="container">
            <div class="legal-container">
                <div class="legal-column">
                    <h3>LEGALITAS</h3>
                    <ul class="legal-list">
                        <li class="legal-item">
                            <span class="legal-number"></span>
                            <span class="legal-text">Izin PPIU (Penyelenggara Perjalanan Ibadah Umroh) No. U.215/2022</span>
                        </li>
                        <li class="legal-item">
                            <span class="legal-number"></span>
                            <span class="legal-text">Izin PIHK (Penyelenggara Ibadah Haji Khusus) No. 319/2021</span>
                        </li>
                        <li class="legal-item">
                            <span class="legal-number"></span>
                            <span class="legal-text">SIUP (Surat Izin Usaha Perdagangan)</span>
                        </li>
                        <li class="legal-item">
                            <span class="legal-number"></span>
                            <span class="legal-text">TDP (Tanda Daftar Perusahaan)</span>
                        </li>
                        <li class="legal-item">
                            <span class="legal-number"></span>
                            <span class="legal-text">NPWP (Nomor Pokok Wajib Pajak)</span>
                        </li>
                    </ul>
                </div>
                
                <div class="legal-column">
                    <h3>KEANGGOTAAN</h3>
                    <ul class="legal-list">
                        <li class="legal-item">
                            <span class="legal-number"></span>
                            <span class="legal-text">Anggota AMPHURI (Asosiasi Muslim Penyelenggara Haji dan Umroh Republik Indonesia)</span>
                        </li>
                        <li class="legal-item">
                            <span class="legal-number"></span>
                            <span class="legal-text">Anggota ASITA (Association of the Indonesian Tours and Travel Agencies)</span>
                        </li>
                        <li class="legal-item">
                            <span class="legal-number"></span>
                            <span class="legal-text">Anggota IATA (International Air Transport Association)</span>
                        </li>
                        <li class="legal-item">
                            <span class="legal-number"></span>
                            <span class="legal-text">Anggota HIMPUH (Himpunan Penyelenggara Umroh dan Haji)</span>
                        </li>
                        <li class="legal-item">
                            <span class="legal-number"></span>
                            <span class="legal-text">Anggota KAN (Konsorsium Agen Naik Haji)</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="gallery-section">
        <div class="container">
            <h3 class="vm-title">GALERI KEGIATAN</h3>
            <div class="gallery-grid">
                <div class="gallery-item">
                    <img src="https://miw.co.id/wp-content/uploads/2025/08/2.jpg" alt="Gallery 1">
                </div>
                <div class="gallery-item">
                    <img src="https://miw.co.id/wp-content/uploads/2025/08/3.jpg" alt="Gallery 2">
                </div>
                <div class="gallery-item">
                    <img src="https://miw.co.id/wp-content/uploads/2025/08/4.jpg" alt="Gallery 3">
                </div>
                <div class="gallery-item">
                    <img src="https://miw.co.id/wp-content/uploads/2025/08/5.jpg" alt="Gallery 4">
                </div>
                <div class="gallery-item">
                    <img src="https://miw.co.id/wp-content/uploads/2025/08/6.jpg" alt="Gallery 5">
                </div>
                <div class="gallery-item">
                    <img src="https://miw.co.id/wp-content/uploads/2025/08/7.jpg" alt="Gallery 6">
                </div>
                <div class="gallery-item">
                    <img src="https://miw.co.id/wp-content/uploads/2025/08/8.jpg" alt="Gallery 7">
                </div>
                <div class="gallery-item">
                    <img src="https://miw.co.id/wp-content/uploads/2025/08/9.jpg" alt="Gallery 8">
                </div>
            </div>
        </div>
    </section>

    <!-- Yellow Section -->
    <section class="yellow-section">
        <div class="container">
            <div class="yellow-content">
                <div class="yellow-text">
                    <h3>KOMITMEN KAMI</h3>
                    <p>Kami berkomitmen untuk memberikan pelayanan terbaik kepada setiap jamaah dengan standar kualitas internasional. Kepercayaan jamaah adalah amanah yang kami jaga dengan sepenuh hati.</p>
                    <p>Dengan tim yang berpengalaman dan berdedikasi, kami siap membantu Anda mewujudkan impian menunaikan ibadah haji dan umroh dengan khusyuk dan tentram.</p>
                </div>
                <div class="yellow-image">
                    <img src="https://miw.co.id/wp-content/uploads/2025/08/16.jpg" alt="Commitment">
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <div class="contact-content">
                <div class="contact-info">
                    <h3>HUBUNGI KAMI</h3>
                    <p><strong>Alamat:</strong> Jl. Cijerah No. 125, Bandung Kulon, Kota Bandung, Jawa Barat 40213</p>
                    <p><strong>Telepon:</strong> (022) 6031698</p>
                    <p><strong>WhatsApp:</strong> +62 812-2222-1998</p>
                    <p><strong>Email:</strong> madinahimanwisata@gmail.com</p>
                    <p><strong>Website:</strong> www.miw.co.id</p>
                </div>
                <div class="map-container">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3961.0!2d107.5745!3d-6.9389!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e9b36b3f8d1f%3A0x4a35d2b5e3a8c9d2!2sJl.%20Cijerah%20No.125%2C%20Bandung%20Kulon%2C%20Kota%20Bandung%2C%20Jawa%20Barat%2040213!5e0!3m2!1sid!2sid!4v1640995200000!5m2!1sid!2sid"
                        allowfullscreen="" 
                        loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
