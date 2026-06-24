<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'JajanMranggen' ?> — JajanMranggen</title>
    <meta name="description" content="Direktori kuliner Mranggen & Demak terlengkap">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary:      #e85d04;
            --primary-dark: #c44d00;
            --secondary:    #f48c06;
            --bg-dark:      #1a1a2e;
            --sidebar-bg:   #16213e;
            --sidebar-text: #a8b2d8;
            --sidebar-active: #e85d04;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f0f2f5;
            margin: 0;
        }

        /* ── Sidebar ── */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: 260px; height: 100vh;
            background: var(--sidebar-bg);
            overflow-y: auto;
            z-index: 1000;
            transition: transform .3s ease;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 22px 20px 18px;
            border-bottom: 1px solid rgba(255,255,255,.06);
            text-decoration: none;
        }

        .sidebar-brand .brand-icon {
            font-size: 28px;
        }

        .sidebar-brand .brand-name {
            font-weight: 800;
            font-size: 1.15rem;
            color: #fff;
            line-height: 1.1;
        }

        .sidebar-brand .brand-sub {
            font-size: .7rem;
            color: var(--secondary);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .sidebar-section {
            padding: 20px 16px 6px;
            font-size: .7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(168,178,216,.4);
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            color: var(--sidebar-text);
            border-radius: 8px;
            margin: 2px 8px;
            font-size: .875rem;
            font-weight: 500;
            transition: background .2s, color .2s;
            text-decoration: none;
        }

        .sidebar-nav .nav-link:hover {
            background: rgba(232,93,4,.12);
            color: #fff;
        }

        .sidebar-nav .nav-link.active {
            background: var(--sidebar-active);
            color: #fff;
        }

        .sidebar-nav .nav-link i {
            font-size: 1.05rem;
            width: 20px;
            text-align: center;
        }

        /* ── Main Content ── */
        .main-wrapper {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Topbar ── */
        .topbar {
            position: sticky;
            top: 0;
            z-index: 900;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }

        .topbar .page-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }

        .topbar .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .topbar .user-avatar {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: .9rem;
        }

        /* ── Page Content ── */
        .page-content {
            flex: 1;
            padding: 28px;
        }

        /* ── Cards ── */
        .card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0,0,0,.06);
        }

        .card-header {
            border-bottom: 1px solid #f0f2f5;
            padding: 18px 24px;
            background: transparent;
            border-radius: 14px 14px 0 0 !important;
        }

        .stat-card {
            border-radius: 14px;
            padding: 22px;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: -20px; right: -20px;
            width: 80px; height: 80px;
            background: rgba(255,255,255,.1);
            border-radius: 50%;
        }

        .stat-card .stat-icon {
            font-size: 2rem;
            opacity: .85;
        }

        .stat-card .stat-number {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1;
        }

        .stat-card .stat-label {
            font-size: .8rem;
            opacity: .85;
            font-weight: 500;
        }

        /* ── Status badges ── */
        .badge-approved  { background: #10b981; }
        .badge-pending   { background: #f59e0b; }
        .badge-rejected  { background: #ef4444; }
        .badge-closed    { background: #6b7280; }

        /* ── Alerts ── */
        .alert { border-radius: 10px; border: none; }

        /* ── Buttons ── */
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            color: #fff;
            font-weight: 600;
            border-radius: 8px;
            transition: opacity .2s, transform .15s;
        }

        .btn-primary-custom:hover {
            opacity: .92;
            transform: translateY(-1px);
            color: #fff;
        }

        /* ── Table ── */
        .table thead th {
            background: #f8fafc;
            font-size: .78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #64748b;
            border-bottom: 1px solid #e5e7eb;
            padding: 12px 16px;
        }

        .table tbody td {
            padding: 14px 16px;
            vertical-align: middle;
            font-size: .875rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .table tbody tr:last-child td { border-bottom: none; }

        /* ── Form ── */
        .form-label { font-weight: 600; font-size: .875rem; color: #374151; }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1.5px solid #e5e7eb;
            padding: 10px 14px;
            font-size: .9rem;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(232,93,4,.12);
        }

        /* Leaflet map */
        #map { border-radius: 12px; border: 2px solid #e5e7eb; }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
        }
    </style>
    <?= $extra_head ?? '' ?>
</head>
<body>

<!-- ── Sidebar ── -->
<aside class="sidebar" id="sidebar">
    <a href="<?= base_url(session()->get('role') === 'admin' ? 'admin/dashboard' : 'contributor/dashboard') ?>" class="sidebar-brand">
        <span class="brand-icon">🍜</span>
        <div>
            <div class="brand-name">JajanMranggen</div>
            <div class="brand-sub"><?= ucfirst(session()->get('role') ?? 'Panel') ?></div>
        </div>
    </a>

    <nav class="sidebar-nav mt-2">
        <!-- Back to homepage always visible -->
        <a href="<?= base_url('/') ?>" class="nav-link" style="color:#f48c06;font-weight:600;">
            <i class="bi bi-house-fill"></i> Beranda Publik
        </a>

        <?php if (session()->get('role') === 'admin'): ?>
        <div class="sidebar-section">Menu Admin</div>
        <a href="<?= base_url('admin/dashboard') ?>" class="nav-link <?= (uri_string() === 'admin/dashboard') ? 'active' : '' ?>">
            <i class="bi bi-grid-fill"></i> Dashboard
        </a>
        <a href="<?= base_url('admin/kuliner') ?>" class="nav-link <?= str_starts_with(uri_string(), 'admin/kuliner') ? 'active' : '' ?>">
            <i class="bi bi-shop"></i> Kelola Kuliner
        </a>
        <a href="<?= base_url('admin/kategori') ?>" class="nav-link <?= str_starts_with(uri_string(), 'admin/kategori') ? 'active' : '' ?>">
            <i class="bi bi-tag-fill"></i> Kategori
        </a>
        <a href="<?= base_url('admin/tags') ?>" class="nav-link <?= str_starts_with(uri_string(), 'admin/tags') ? 'active' : '' ?>">
            <i class="bi bi-hash"></i> Tag / Label
        </a>
        <a href="<?= base_url('admin/reviews') ?>" class="nav-link <?= str_starts_with(uri_string(), 'admin/reviews') ? 'active' : '' ?>">
            <i class="bi bi-chat-square-text-fill"></i> Moderasi Ulasan
        </a>
        <a href="<?= base_url('admin/users') ?>" class="nav-link <?= str_starts_with(uri_string(), 'admin/users') ? 'active' : '' ?>">
            <i class="bi bi-people-fill"></i> Pengguna
        </a>
        <a href="<?= base_url('admin/payments') ?>" class="nav-link <?= str_starts_with(uri_string(), 'admin/payments') ? 'active' : '' ?>">
            <i class="bi bi-wallet2"></i> Data Pembayaran
        </a>
        <?php else: ?>
        <div class="sidebar-section">Menu Kontributor</div>
        <a href="<?= base_url('contributor/dashboard') ?>" class="nav-link <?= (uri_string() === 'contributor/dashboard') ? 'active' : '' ?>">
            <i class="bi bi-grid-fill"></i> Dashboard
        </a>
        <a href="<?= base_url('contributor/kuliner') ?>" class="nav-link <?= str_starts_with(uri_string(), 'contributor/kuliner') ? 'active' : '' ?>">
            <i class="bi bi-shop"></i> Kuliner Saya
        </a>
        <a href="<?= base_url('contributor/kuliner/create') ?>" class="nav-link <?= (uri_string() === 'contributor/kuliner/create') ? 'active' : '' ?>">
            <i class="bi bi-plus-circle-fill"></i> Tambah Kuliner
        </a>
        <a href="<?= base_url('favorites') ?>" class="nav-link <?= str_starts_with(uri_string(), 'favorites') ? 'active' : '' ?>">
            <i class="bi bi-heart-fill"></i> Favorit Saya
        </a>
        <?php endif; ?>

        <div class="sidebar-section mt-3">Akun</div>
        <a href="<?= base_url('logout') ?>" class="nav-link text-danger">
            <i class="bi bi-box-arrow-left"></i> Logout
        </a>
    </nav>
</aside>

<!-- ── Main Wrapper ── -->
<div class="main-wrapper">

    <!-- Topbar -->
    <div class="topbar">
        <h1 class="page-title"><?= $page_title ?? 'Dashboard' ?></h1>
        <div class="user-info">
            <div class="user-avatar">
                <?= strtoupper(substr(session()->get('username') ?? 'U', 0, 1)) ?>
            </div>
            <div>
                <div style="font-weight:600;font-size:.875rem;color:#1f2937"><?= esc(session()->get('username')) ?></div>
                <div style="font-size:.75rem;color:#6b7280"><?= ucfirst(session()->get('role') ?? '') ?></div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    <div class="px-4 pt-3">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Perbaiki kesalahan berikut:</strong>
                <ul class="mb-0 mt-1">
                    <?php foreach ((array) session()->getFlashdata('errors') as $e): ?>
                        <li><?= esc($e) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Page Content -->
    <main class="page-content">
        <?= $this->renderSection('content') ?>
    </main>

</div><!-- .main-wrapper -->

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?= $extra_js ?? '' ?>
</body>
</html>
