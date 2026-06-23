<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'JajanMranggen - Direktori Kuliner Terbaik' ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Leaflet CSS (Load always for simplicity or selectively) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        :root {
            --primary: #f97316; /* Vibrant Orange */
            --primary-hover: #ea580c;
            --secondary: #fcd34d; /* Warm Yellow */
            --dark: #1e293b;
            --light-bg: #f8fafc;
            --text-main: #334155;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--light-bg);
            color: var(--text-main);
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        h1, h2, h3, h4, h5, h6, .brand-logo {
            font-family: 'Outfit', sans-serif;
        }

        /* Navbar Styling */
        .navbar-custom {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1030;
            transition: all 0.3s ease;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--primary) !important;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .navbar-brand span {
            color: var(--dark);
        }

        .nav-link {
            font-weight: 600;
            color: var(--dark) !important;
            padding: 8px 16px !important;
            transition: all 0.3s ease;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--primary) !important;
            transform: translateY(-2px);
        }

        .btn-login {
            background-color: var(--primary);
            color: white !important;
            font-weight: 600;
            border-radius: 50px;
            padding: 10px 24px;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-login:hover {
            background-color: var(--primary-hover);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(249, 115, 22, 0.2);
        }

        /* Buttons Custom */
        .btn-custom {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px 28px;
            font-weight: 600;
            font-family: 'Outfit', sans-serif;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(249, 115, 22, 0.3);
        }
        
        .btn-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(249, 115, 22, 0.4);
            color: white;
        }

        /* Footer */
        footer {
            background: var(--dark);
            color: white;
            padding: 60px 0 30px;
            margin-top: auto;
        }

        .footer-brand {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 1.8rem;
            color: white;
            margin-bottom: 20px;
            display: inline-block;
        }

        .footer-brand span {
            color: var(--primary);
        }

        .footer-text {
            color: #94a3b8;
            line-height: 1.8;
        }

        /* Card Kuliner Styling (Used across pages) */
        .kuliner-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.04);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            height: 100%;
            border: 1px solid rgba(0,0,0,0.02);
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
        }

        .kuliner-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(249, 115, 22, 0.12);
        }

        .kuliner-img-wrap {
            position: relative;
            height: 220px;
            overflow: hidden;
        }

        .kuliner-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .kuliner-card:hover .kuliner-img {
            transform: scale(1.1);
        }

        .sponsor-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: linear-gradient(135deg, #ffd700, #ff8c00);
            color: white;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 1px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            z-index: 2;
        }

        .rating-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(4px);
            color: var(--dark);
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 4px;
            z-index: 2;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        .rating-badge i { color: #f59e0b; }

        .kuliner-content {
            padding: 24px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .kuliner-category {
            color: var(--primary);
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .kuliner-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 12px;
            color: var(--dark);
            line-height: 1.3;
        }

        .kuliner-address {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex-grow: 1;
        }

    </style>
    <?= $this->renderSection('extra_head') ?>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('/') ?>">
                <i class="bi bi-shop"></i> Jajan<span>Mranggen</span>
            </a>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="bi bi-list fs-1 text-dark"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?= uri_string() == '' ? 'active' : '' ?>" href="<?= base_url('/') ?>">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= str_starts_with(uri_string(), 'kuliner') ? 'active' : '' ?>" href="<?= base_url('/kuliner') ?>">Eksplor Kuliner</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <?php if(session()->get('logged_in')): ?>
                        <a href="<?= base_url(session()->get('role') === 'admin' ? 'admin/dashboard' : 'contributor/dashboard') ?>" class="nav-link fw-bold text-primary">
                            <i class="bi bi-grid-fill me-1"></i> Dashboard
                        </a>
                        <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger rounded-pill px-4 fw-bold">Logout</a>
                    <?php else: ?>
                        <a href="<?= base_url('login') ?>" class="nav-link">Masuk</a>
                        <a href="<?= base_url('register') ?>" class="btn btn-login">Daftar Kontributor</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-5 pe-lg-5">
                    <a href="#" class="footer-brand text-decoration-none">Jajan<span>Mranggen</span></a>
                    <p class="footer-text">
                        Platform direktori kuliner terbaik untuk menemukan makanan lezat tersembunyi di area Mranggen, Demak, dan sekitarnya. 
                        Bergabunglah menjadi kontributor dan promosikan usaha kuliner Anda.
                    </p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white mb-4 fw-bold">Tautan Cepat</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="<?= base_url('/') ?>" class="text-decoration-none footer-text hover-white">Beranda</a></li>
                        <li class="mb-2"><a href="<?= base_url('/kuliner') ?>" class="text-decoration-none footer-text hover-white">Eksplor Kuliner</a></li>
                        <li class="mb-2"><a href="<?= base_url('/login') ?>" class="text-decoration-none footer-text hover-white">Login Kontributor</a></li>
                        <li class="mb-2"><a href="<?= base_url('/register') ?>" class="text-decoration-none footer-text hover-white">Daftar Baru</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h5 class="text-white mb-4 fw-bold">Hubungi Kami</h5>
                    <ul class="list-unstyled footer-text">
                        <li class="mb-3"><i class="bi bi-geo-alt-fill text-primary me-2"></i> Jl. Raya Mranggen, Demak, Jawa Tengah</li>
                        <li class="mb-3"><i class="bi bi-envelope-fill text-primary me-2"></i> info@jajanmranggen.com</li>
                        <li class="mb-3"><i class="bi bi-telephone-fill text-primary me-2"></i> +62 812 3456 7890</li>
                    </ul>
                    <div class="d-flex gap-3 mt-4">
                        <a href="#" class="btn btn-outline-light rounded-circle"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="btn btn-outline-light rounded-circle"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="btn btn-outline-light rounded-circle"><i class="bi bi-twitter-x"></i></a>
                    </div>
                </div>
            </div>
            <div class="row mt-5 pt-4 border-top border-secondary">
                <div class="col-12 text-center footer-text">
                    &copy; <?= date('Y') ?> JajanMranggen. All rights reserved.
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('extra_js') ?>
</body>
</html>
