<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Kelola Tag</h4>
        <p class="text-muted mb-0">Kelola master data tag/label untuk tempat kuliner</p>
    </div>
    <a href="<?= base_url('admin/tags/create') ?>" class="btn btn-primary-custom px-4">
        <i class="bi bi-plus-lg me-2"></i>Tambah Tag
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:50px;">#</th>
                        <th>Nama Tag</th>
                        <th>Slug</th>
                        <th>Dibuat</th>
                        <th style="width:120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tags)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-tags" style="font-size:2.5rem;opacity:.3;"></i>
                            <p class="mt-2 mb-0">Belum ada tag. <a href="<?= base_url('admin/tags/create') ?>">Tambahkan sekarang!</a></p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($tags as $i => $t): ?>
                    <tr>
                        <td class="text-muted"><?= $i + 1 ?></td>
                        <td class="fw-semibold">
                            <span class="badge bg-light text-dark border"><i class="bi bi-hash text-primary me-1"></i><?= esc($t['name']) ?></span>
                        </td>
                        <td><code class="text-primary"><?= esc($t['slug']) ?></code></td>
                        <td class="text-muted small"><?= date('d M Y', strtotime($t['created_at'])) ?></td>
                        <td>
                            <a href="<?= base_url('admin/tags/edit/'.$t['id']) ?>" class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="<?= base_url('admin/tags/delete/'.$t['id']) ?>" method="POST" class="d-inline"
                                  onsubmit="return confirm('Hapus tag \'<?= esc($t['name'], 'js') ?>\'?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger">
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
    </div>
</div>

<?= $this->endSection() ?>
