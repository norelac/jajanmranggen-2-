<?= $this->extend('layouts/public') ?>

<?= $this->section('extra_head') ?>
<style>
    /* Hero Section */
    .hero-section {
        background: linear-gradient(rgba(30, 41, 59, 0.8), rgba(30, 41, 59, 0.9)), url('https://images.unsplash.com/photo-1555939594-58d7cb561ad1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover;
        padding: 120px 0 100px;
        color: white;
        text-align: center;
        margin-top: -80px; /* Offset navbar */
        padding-top: 180px;
    }

    .hero-title {
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 20px;
        text-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }

    .hero-title span {
        color: var(--primary);
    }

    .hero-subtitle {
        font-size: 1.25rem;
        color: #cbd5e1;
        margin-bottom: 40px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.6;
    }

    /* Search Bar */
    .search-box {
        background: white;
        padding: 10px;
        border-radius: 50px;
        display: flex;
        max-width: 600px;
        margin: 0 auto;
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    }

    .search-box input {
        border: none;
        box-shadow: none;
        padding: 12px 24px;
        border-radius: 50px 0 0 50px;
        font-size: 1.05rem;
        flex-grow: 1;
    }

    .search-box input:focus {
        outline: none;
        box-shadow: none;
    }

    .search-box button {
        border-radius: 50px;
        padding: 12px 30px;
        font-size: 1.05rem;
    }

    /* Section Headings */
    .section-header {
        text-align: center;
        margin-bottom: 50px;
    }

    .section-title {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--dark);
        margin-bottom: 15px;
    }

    .section-subtitle {
        color: var(--text-muted);
        font-size: 1.1rem;
    }

    /* Featured Section */
    .featured-section {
        padding: 80px 0;
    }
    
    /* Call to action */
    .cta-section {
        background: linear-gradient(135deg, var(--primary), #fb923c);
        padding: 80px 0;
        color: white;
        text-align: center;
        border-radius: 30px;
        margin: 60px 15px 80px;
        box-shadow: 0 20px 40px rgba(249, 115, 22, 0.2);
    }

    .cta-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .hero-title { font-size: 2.5rem; }
        .hero-section { padding-top: 140px; padding-bottom: 60px; }
        .cta-section { margin: 40px 10px; padding: 50px 20px; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <h1 class="hero-title">Temukan Kuliner <span>Tersembunyi</span><br>di Mranggen</h1>
        <p class="hero-subtitle">Jelajahi berbagai hidangan lokal, tempat makan kekinian, dan warung legendaris yang wajib Anda coba di Demak.</p>
        
        <form action="<?= base_url('kuliner') ?>" method="GET">
            <div class="search-box">
                <input type="text" name="q" class="form-control" placeholder="Cari sate, bakso, kopi...">
                <button type="submit" class="btn btn-custom">Cari <i class="bi bi-search ms-1"></i></button>
            </div>
        </form>
    </div>
</section>

<!-- Featured Kuliner -->
<section class="featured-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Kuliner Spesial</h2>
            <p class="section-subtitle">Rekomendasi tempat makan terbaik untuk Anda hari ini</p>
        </div>

        <div class="row g-4">
            <?php if(empty($featured_kuliners)): ?>
                <div class="col-12 text-center text-muted">
                    <p>Belum ada kuliner yang tersedia saat ini.</p>
                </div>
            <?php else: ?>
                <?php foreach($featured_kuliners as $kuliner): ?>
                <div class="col-lg-4 col-md-6">
                    <a href="<?= base_url('kuliner/' . $kuliner['slug']) ?>" class="kuliner-card">
                        <div class="kuliner-img-wrap">
                            <!-- In a real app, use the photo from DB. Here we use a nice placeholder -->
                            <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="<?= esc($kuliner['name']) ?>" class="kuliner-img">
                            
                            <?php if(isset($kuliner['is_sponsored']) && $kuliner['is_sponsored']): ?>
                                <div class="sponsor-badge"><i class="bi bi-star-fill me-1"></i> SPONSOR</div>
                            <?php endif; ?>
                            
                            <div class="rating-badge">
                                <i class="bi bi-star-fill"></i>
                                <?= number_format($kuliner['average_rating'], 1) ?>
                            </div>
                        </div>
                        <div class="kuliner-content">
                            <div class="kuliner-category"><?= esc($kuliner['category_name'] ?? 'Kategori') ?></div>
                            <h3 class="kuliner-title"><?= esc($kuliner['name']) ?></h3>
                            <p class="kuliner-address"><i class="bi bi-geo-alt text-muted me-1"></i> <?= esc($kuliner['address']) ?></p>
                            
                            <div class="mt-auto d-flex align-items-center justify-content-between pt-3 border-top">
                                <span class="text-primary fw-bold" style="font-size:0.9rem;">Lihat Detail <i class="bi bi-arrow-right ms-1"></i></span>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-5">
            <a href="<?= base_url('kuliner') ?>" class="btn btn-outline-dark rounded-pill px-4 py-2 fw-bold">Lihat Semua Kuliner <i class="bi bi-arrow-right ms-2"></i></a>
        </div>
    </div>
</section>

<!-- Call to Action -->
<div class="container">
    <section class="cta-section">
        <h2 class="cta-title">Punya Usaha Kuliner di Mranggen?</h2>
        <p class="mb-4" style="font-size: 1.1rem; opacity: 0.9;">Daftarkan warung atau resto Anda sekarang dan jangkau lebih banyak pelanggan secara gratis!</p>
        <a href="<?= base_url('register') ?>" class="btn btn-light rounded-pill px-5 py-3 fw-bold text-primary" style="font-size: 1.1rem;">
            Daftar Sebagai Kontributor
        </a>
    </section>
</div>

<?= $this->endSection() ?>
