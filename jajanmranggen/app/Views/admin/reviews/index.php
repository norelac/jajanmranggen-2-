<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Moderasi Ulasan</h4>
        <p class="text-muted mb-0">Hapus ulasan yang tidak pantas atau melanggar kebijakan</p>
    </div>
</div>

<!-- Search -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="<?= base_url('admin/reviews') ?>" class="row g-2 align-items-center">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control" placeholder="Cari berdasarkan nama user, kuliner, atau isi komentar..."
                           value="<?= esc($search ?? '') ?>">
                </div>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary-custom flex-grow-1">Cari</button>
                <a href="<?= base_url('admin/reviews') ?>" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Reviewer</th>
                        <th>Kuliner</th>
                        <th>Rating</th>
                        <th>Komentar</th>
                        <th>Tgl Ulasan</th>
                        <th style="width:80px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reviews)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-chat-square-text" style="font-size:2.5rem;opacity:.3;"></i>
                            <p class="mt-2 mb-0">Tidak ada ulasan ditemukan.</p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($reviews as $r): ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?= esc($r['username'] ?? 'Unknown') ?></div>
                        </td>
                        <td>
                            <a href="<?= base_url('kuliner/'.$r['kuliner_slug']) ?>" target="_blank" class="text-decoration-none">
                                <?= esc($r['kuliner_name'] ?? '-') ?>
                                <i class="bi bi-box-arrow-up-right text-muted ms-1" style="font-size:.7rem;"></i>
                            </a>
                        </td>
                        <td>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="bi bi-star-fill <?= $i <= $r['rating'] ? 'text-warning' : 'text-light' ?>" style="font-size:.75rem;"></i>
                            <?php endfor; ?>
                            <span class="ms-1 fw-semibold"><?= $r['rating'] ?></span>
                        </td>
                        <td>
                            <span style="font-size:.875rem;"><?= esc(substr($r['comment'] ?? '', 0, 100)) ?><?= strlen($r['comment'] ?? '') > 100 ? '...' : '' ?></span>
                        </td>
                        <td class="text-muted small"><?= date('d M Y H:i', strtotime($r['created_at'])) ?></td>
                        <td>
                            <form action="<?= base_url('admin/reviews/delete/'.$r['id']) ?>" method="POST"
                                  onsubmit="return confirm('Hapus ulasan ini? Tindakan ini tidak bisa dibatalkan.')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Ulasan">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if (!empty($pager)): ?>
        <div class="px-4 py-3">
            <?= $pager->links('reviews', 'default_full') ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
