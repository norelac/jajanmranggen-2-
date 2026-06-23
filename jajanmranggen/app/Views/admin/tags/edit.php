<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<div class="mb-4">
    <a href="<?= base_url('admin/tags') ?>" class="text-decoration-none text-muted">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Tag
    </a>
</div>

<div class="card" style="max-width:600px;">
    <div class="card-header">
        <h5 class="mb-0 fw-bold"><i class="bi bi-pencil me-2 text-primary"></i>Edit Tag</h5>
    </div>
    <div class="card-body p-4">
        <form action="<?= base_url('admin/tags/update/'.$tag['id']) ?>" method="POST">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label class="form-label">Nama Tag <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-hash"></i></span>
                    <input type="text" name="name" class="form-control" required
                           value="<?= old('name', $tag['name']) ?>">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Slug (read-only)</label>
                <input type="text" class="form-control" value="<?= esc($tag['slug']) ?>" readonly disabled>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary-custom px-4">
                    <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                </button>
                <a href="<?= base_url('admin/tags') ?>" class="btn btn-outline-secondary px-4">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
