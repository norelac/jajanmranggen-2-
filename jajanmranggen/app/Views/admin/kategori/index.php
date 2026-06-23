<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Kategori Kuliner</h4>
        <p class="text-muted mb-0">Kelola kategori untuk mengklasifikasikan tempat kuliner</p>
    </div>
    <a href="<?= base_url('admin/kategori/create') ?>" class="btn btn-primary-custom px-4">
        <i class="bi bi-plus-lg me-2"></i>Tambah Kategori
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:50px;">#</th>
                        <th>Nama Kategori</th>
                        <th>Slug</th>
                        <th>Deskripsi</th>
                        <th>Dibuat</th>
                        <th style="width:120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($kategori)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-tag" style="font-size:2.5rem;opacity:.3;"></i>
                            <p class="mt-2 mb-0">Belum ada kategori. <a href="<?= base_url('admin/kategori/create') ?>">Tambahkan sekarang!</a></p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($kategori as $i => $k): ?>
                    <tr>
                        <td class="text-muted"><?= $i + 1 ?></td>
                        <td class="fw-semibold"><?= esc($k['name']) ?></td>
                        <td><code class="text-primary"><?= esc($k['slug']) ?></code></td>
                        <td class="text-muted"><?= esc(substr($k['description'] ?? '-', 0, 60)) ?></td>
                        <td class="text-muted small"><?= date('d M Y', strtotime($k['created_at'])) ?></td>
                        <td>
                            <a href="<?= base_url('admin/kategori/edit/'.$k['id']) ?>" class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="<?= base_url('admin/kategori/delete/'.$k['id']) ?>" method="POST" class="d-inline"
                                  onsubmit="return confirm('Hapus kategori \'<?= esc($k['name'], 'js') ?>\'? Pastikan tidak ada kuliner yang menggunakan kategori ini.')">
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
