<?= $this->extend('layouts/public') ?>

<?= $this->section('extra_head') ?>
<style>
    .page-header {
        background: var(--dark);
        color: white;
        padding: 60px 0 40px;
        margin-bottom: 40px;
    }

    .filter-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        border: 1px solid rgba(0,0,0,0.03);
        position: sticky;
        top: 90px;
    }

    .filter-title {
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f1f5f9;
    }

    .category-link {
        display: flex;
        align-items: center;
        padding: 10px 15px;
        color: var(--text-main);
        text-decoration: none;
        border-radius: 8px;
        margin-bottom: 5px;
        transition: all 0.2s;
    }

    .category-link:hover, .category-link.active {
        background: rgba(249, 115, 22, 0.1);
        color: var(--primary);
        font-weight: 600;
    }

    .category-link i {
        margin-right: 10px;
        font-size: 1.1rem;
    }
    
    /* Pagination styling */
    .pagination { justify-content: center; margin-top: 40px; }
    .page-link { color: var(--dark); border: none; padding: 10px 16px; margin: 0 4px; border-radius: 8px; font-weight: 600; }
    .page-link:hover { background: rgba(249, 115, 22, 0.1); color: var(--primary); }
    .page-item.active .page-link { background: var(--primary); color: white; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header text-center">
    <div class="container">
        <h1 class="fw-bold mb-3 font-outfit">Eksplorasi Rasa di Mranggen</h1>
        <p class="text-light opacity-75">Temukan berbagai tempat makan enak yang sudah dikurasi.</p>
    </div>
</div>

<div class="container mb-5 pb-5">
    <div class="row">
        <!-- Sidebar Filter -->
        <div class="col-lg-3 mb-4">
            <div class="filter-card">
                <h4 class="filter-title">Cari & Filter</h4>
                
                <form action="<?= base_url('kuliner') ?>" method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="Cari kuliner..." value="<?= esc($search) ?>">
                        <?php if($current_category): ?>
                            <input type="hidden" name="kategori" value="<?= esc($current_category) ?>">
                        <?php endif; ?>
                        <button class="btn btn-primary text-white" type="submit" style="background-color: var(--primary); border: none;">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>

                <h5 class="fw-bold mb-3 fs-6 mt-4">Kategori</h5>
                <div class="category-list">
                    <a href="<?= base_url('kuliner') . ($search ? '?q='.$search : '') ?>" class="category-link <?= empty($current_category) ? 'active' : '' ?>">
                        <i class="bi bi-grid-fill"></i> Semua Kategori
                    </a>
                    <?php foreach($categories as $cat): ?>
                        <a href="<?= base_url('kuliner') ?>?kategori=<?= $cat['slug'] ?><?= $search ? '&q='.$search : '' ?>" class="category-link <?= $current_category == $cat['slug'] ? 'active' : '' ?>">
                            <i class="bi bi-tag"></i> <?= esc($cat['name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Kuliner Grid -->
        <div class="col-lg-9">
            <?php if($search || $current_category): ?>
                <div class="mb-4 pb-2 border-bottom d-flex justify-content-between align-items-center">
                    <span class="text-muted">Hasil pencarian untuk: <strong class="text-dark"><?= esc($search ?: 'Semua') ?></strong> di kategori <strong class="text-dark"><?= esc($current_category ?: 'Semua') ?></strong></span>
                    <a href="<?= base_url('kuliner') ?>" class="btn btn-sm btn-outline-secondary rounded-pill">Reset Filter</a>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <?php if(empty($kuliners)): ?>
                    <div class="col-12 py-5 text-center">
                        <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
                        <h4 class="mt-3">Tidak ada hasil</h4>
                        <p class="text-muted">Maaf, kami tidak menemukan kuliner yang sesuai dengan pencarian Anda.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($kuliners as $kuliner): ?>
                    <div class="col-md-6 col-lg-4 d-flex">
                        <a href="<?= base_url('kuliner/' . $kuliner['slug']) ?>" class="kuliner-card w-100">
                            <div class="kuliner-img-wrap">
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
                                <div class="kuliner-category"><?= esc($kuliner['category_name']) ?></div>
                                <h3 class="kuliner-title"><?= esc($kuliner['name']) ?></h3>
                                <p class="kuliner-address"><i class="bi bi-geo-alt text-muted me-1"></i> <?= esc($kuliner['address']) ?></p>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?= $pager->links('kuliners', 'default_full') ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
