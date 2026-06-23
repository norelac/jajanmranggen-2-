<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<div class="mb-4">
    <a href="<?= base_url('admin/kategori') ?>" class="text-decoration-none text-muted">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Kategori
    </a>
</div>

<div class="card" style="max-width:600px;">
    <div class="card-header">
        <h5 class="mb-0 fw-bold"><i class="bi bi-pencil me-2 text-primary"></i>Edit Kategori</h5>
    </div>
    <div class="card-body p-4">
        <form action="<?= base_url('admin/kategori/update/'.$kategori['id']) ?>" method="POST">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required
                       value="<?= old('name', $kategori['name']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Slug (read-only)</label>
                <input type="text" class="form-control" value="<?= esc($kategori['slug']) ?>" readonly disabled>
            </div>

            <div class="mb-4">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-control" rows="3"><?= old('description', $kategori['description'] ?? '') ?></textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary-custom px-4">
                    <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                </button>
                <a href="<?= base_url('admin/kategori') ?>" class="btn btn-outline-secondary px-4">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
