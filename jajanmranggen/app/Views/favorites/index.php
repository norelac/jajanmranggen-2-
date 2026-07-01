<?= $this->extend('layouts/public') ?>

<?= $this->section('extra_head') ?>
<style>
    .page-header-fav {
        background: linear-gradient(135deg, var(--dark) 0%, #1a1a2e 100%);
        color: white;
        padding: 60px 0 40px;
        margin-bottom: 40px;
    }

    .favorite-float-btn {
        position: absolute;
        top: 15px;
        left: 15px;
        z-index: 3;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(4px);
        border: none;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        color: #e11d48;
        font-size: 1.15rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .favorite-float-btn:hover {
        transform: scale(1.15);
        background: white;
        box-shadow: 0 6px 15px rgba(225, 29, 72, 0.3);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header-fav text-center">
    <div class="container">
        <h1 class="fw-bold mb-3 font-outfit"><i class="bi bi-heart-fill text-danger me-2"></i>Favorit Saya</h1>
        <p class="text-light opacity-75">Koleksi tempat kuliner favorit yang telah Anda simpan.</p>
    </div>
</div>

<div class="container mb-5 pb-5">
    <?php if (empty($favorites)): ?>
    <div class="text-center py-5">
        <i class="bi bi-heart text-muted" style="font-size: 4rem; opacity: 0.2;"></i>
        <h4 class="mt-3 fw-bold">Belum Ada Favorit</h4>
        <p class="text-muted mb-4">Jelajahi kuliner di Mranggen dan simpan favorit Anda dengan menekan ikon <i class="bi bi-heart text-danger"></i> di halaman detail.</p>
        <a href="<?= base_url('/kuliner') ?>" class="btn btn-custom px-5 fw-bold">
            <i class="bi bi-map me-2"></i>Jelajahi Kuliner
        </a>
    </div>
    <?php else: ?>
    <div class="row g-4">
        <?php foreach ($favorites as $fav): ?>
        <div class="col-md-6 col-lg-4 d-flex fav-item">
            <a href="<?= base_url('kuliner/'.$fav['slug']) ?>" class="kuliner-card w-100">
                <div class="kuliner-img-wrap">
                    <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                         alt="<?= esc($fav['name']) ?>"
                         class="kuliner-img">
                    <button class="favorite-float-btn btn-unfavorite"
                            data-kuliner-id="<?= $fav['kuliner_id'] ?>"
                            title="Hapus dari favorit"
                            onclick="event.preventDefault(); event.stopPropagation();">
                        <i class="bi bi-heart-fill"></i>
                    </button>
                    <div class="rating-badge">
                        <i class="bi bi-star-fill"></i>
                        <?= number_format($fav['average_rating'], 1) ?>
                    </div>
                </div>
                <div class="kuliner-content">
                    <div class="kuliner-category"><?= esc($fav['category_name'] ?? 'Kuliner') ?></div>
                    <h3 class="kuliner-title"><?= esc($fav['name']) ?></h3>
                    <p class="kuliner-address"><i class="bi bi-geo-alt text-muted me-1"></i> <?= esc($fav['address']) ?></p>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('extra_js') ?>
<script>
document.querySelectorAll('.btn-unfavorite').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const kuliner_id = this.dataset.kuliner_id || this.dataset.kulinerId;
        if (!confirm('Hapus dari favorit?')) return;

        const card = this.closest('.fav-item');

        fetch('<?= base_url('favorites/toggle') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: '<?= csrf_token() ?>=' + encodeURIComponent('<?= csrf_hash() ?>') + '&kuliner_id=' + kuliner_id
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                card.style.transition = 'all 0.3s';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.9)';
                setTimeout(function() {
                    card.remove();
                    if (document.querySelectorAll('.fav-item').length === 0) {
                        location.reload();
                    }
                }, 300);
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
