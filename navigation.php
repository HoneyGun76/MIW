<?php
/**
 * Simple Navigation Component for MIW Landing Pages
 * Consistent navigation without dropdowns across all landing pages
 */
?>

<!-- Bootstrap Navigation for MIW Landing Pages -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-3">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand" href="beranda.html">
            <img src="miw_logo.png" alt="Madinah Iman Wisata" height="50" class="d-inline-block align-text-top">
        </a>

        <!-- Mobile menu toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Menu -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'beranda.html' ? 'active' : '' ?>" 
                       href="beranda.html">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'umroh.html' ? 'active' : '' ?>" 
                       href="umroh.html">Umroh</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'haji.html' ? 'active' : '' ?>" 
                       href="haji.html">Haji</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'profil.html' ? 'active' : '' ?>" 
                       href="profil.html">Tentang Kami</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
/* Custom styling to preserve existing appearance */
.navbar-nav .nav-link {
    color: #333 !important;
    font-weight: 500;
    padding: 0.5rem 1rem;
    text-transform: capitalize;
    font-size: 16px;
    transition: color 0.3s ease;
}

.navbar-nav .nav-link:hover {
    color: #39a3d1 !important;
}

.navbar-nav .nav-link.active {
    color: #39a3d1 !important;
    font-weight: 600;
}

.navbar-brand img {
    max-height: 50px;
    width: auto;
}

.navbar {
    background-color: #ffffff !important;
    box-shadow: 0 2px 30px rgba(0,0,0,.08) !important;
}

/* Mobile responsiveness */
@media (max-width: 991.98px) {
    .navbar-nav {
        text-align: center;
        padding-top: 1rem;
    }
    
    .navbar-nav .nav-link {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f8f9fa;
    }
    
    .navbar-nav .nav-link:last-child {
        border-bottom: none;
    }
}
</style>
