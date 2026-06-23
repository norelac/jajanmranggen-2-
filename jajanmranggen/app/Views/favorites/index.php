<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h2 class="fw-bold mb-1">❤️ Favorit Saya</h2>
            <p class="text-muted mb-4">Tempat kuliner yang telah Anda simpan</p>

            <?php if (empty($favorites)): ?>
            <div class="text-center py-5">
                <i class="bi bi-heart" style="font-size:4rem;color:#e85d04;opacity:.3;"></i>
                <h5 class="mt-3 text-muted">Belum ada favorit</h5>
                <p class="text-muted">Temukan kuliner yang menarik dan simpan ke favorit Anda.</p>
                <a href="<?= base_url('/kuliner') ?>" class="btn btn-custom px-4 fw-bold">
                    <i class="bi bi-map me-2"></i>Jelajahi Kuliner
                </a>
            </div>
            <?php else: ?>
            <div class="row g-4">
                <?php foreach ($favorites as $fav): ?>
                <div class="col-sm-6">
                    <div class="card border-0 shadow-sm h-100" style="border-radius:16px;overflow:hidden;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge" style="background:rgba(249,115,22,.12);color:#e85d04;font-weight:600;padding:6px 12px;border-radius:20px;">
                                    <?= esc($fav['category_name'] ?? 'Kuliner') ?>
                                </span>
                                <div>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <span class="fw-bold ms-1"><?= number_format($fav['average_rating'], 1) ?></span>
                                </div>
                            </div>
                            <h5 class="fw-bold mb-2"><?= esc($fav['name']) ?></h5>
                            <p class="text-muted small mb-3">
                                <i class="bi bi-geo-alt-fill me-1" style="color:#e85d04;"></i>
                                <?= esc(substr($fav['address'], 0, 80)) ?>...
                            </p>
                            <div class="d-flex gap-2">
                                <a href="<?= base_url('kuliner/'.$fav['slug']) ?>" class="btn btn-sm btn-custom flex-grow-1 fw-bold">
                                    <i class="bi bi-eye me-1"></i> Lihat Detail
                                </a>
                                <button class="btn btn-sm btn-outline-danger btn-unfavorite"
                                        data-kuliner-id="<?= $fav['kuliner_id'] ?>"
                                        title="Hapus dari favorit">
                                    <i class="bi bi-heart-fill"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('extra_js') ?>
<script>
document.querySelectorAll('.btn-unfavorite').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const kuliner_id = this.dataset.kuliner_id || this.dataset.kulinerId;
        if (!confirm('Hapus dari favorit?')) return;

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
                // Remove the card
                this.closest('.col-sm-6').remove();
                // Check if empty
                if (document.querySelectorAll('.col-sm-6').length === 0) {
                    location.reload();
                }
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
