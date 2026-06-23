<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Kelola Kuliner</h4>
        <p class="text-muted mb-0">Moderasi, persetujuan, dan pengelolaan data kuliner</p>
    </div>
</div>

<!-- Filter bar -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="<?= base_url('admin/kuliner') ?>" class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control" placeholder="Cari nama kuliner atau kontributor..."
                           value="<?= esc($search ?? '') ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending"   <?= ($status ?? '') === 'pending'   ? 'selected' : '' ?>>Pending</option>
                    <option value="approved"  <?= ($status ?? '') === 'approved'  ? 'selected' : '' ?>>Disetujui</option>
                    <option value="rejected"  <?= ($status ?? '') === 'rejected'  ? 'selected' : '' ?>>Ditolak</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary-custom flex-grow-1">Filter</button>
                <a href="<?= base_url('admin/kuliner') ?>" class="btn btn-outline-secondary">Reset</a>
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
                        <th>Nama Kuliner</th>
                        <th>Kategori</th>
                        <th>Kontributor</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th>Tgl Tambah</th>
                        <th style="width:160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($kuliners)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-shop" style="font-size:2.5rem;opacity:.3;"></i>
                            <p class="mt-2 mb-0">Tidak ada data kuliner ditemukan.</p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($kuliners as $k): ?>
                    <?php
                    $statusMap = [
                        'approved'         => ['badge-approved', 'Disetujui'],
                        'pending'          => ['badge-pending', 'Pending'],
                        'rejected'         => ['badge-rejected', 'Ditolak'],
                        'closed_permanent' => ['badge-closed', 'Tutup Permanen'],
                    ];
                    [$cls, $lbl] = $statusMap[$k['status']] ?? ['bg-secondary', 'Unknown'];
                    ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?= esc($k['name']) ?></div>
                            <small class="text-muted"><?= esc(substr($k['address'], 0, 50)) ?>...</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border"><?= esc($k['category_name'] ?? '-') ?></span>
                        </td>
                        <td><?= esc($k['contributor_name'] ?? '-') ?></td>
                        <td>
                            <i class="bi bi-star-fill text-warning"></i>
                            <?= number_format($k['average_rating'], 1) ?>
                        </td>
                        <td><span class="badge <?= $cls ?>"><?= $lbl ?></span></td>
                        <td class="text-muted small"><?= date('d M Y', strtotime($k['created_at'])) ?></td>
                        <td>
                            <?php if ($k['status'] === 'pending'): ?>
                            <form action="<?= base_url('admin/kuliner/approve/'.$k['id']) ?>" method="POST" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-success me-1" title="Setujui">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </form>
                            <form action="<?= base_url('admin/kuliner/reject/'.$k['id']) ?>" method="POST" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-warning me-1" title="Tolak">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                            <?php if ($k['status'] === 'rejected'): ?>
                            <form action="<?= base_url('admin/kuliner/approve/'.$k['id']) ?>" method="POST" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-success me-1" title="Setujui ulang">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                            <form action="<?= base_url('admin/kuliner/delete/'.$k['id']) ?>" method="POST" class="d-inline"
                                  onsubmit="return confirm('Hapus kuliner \'<?= esc($k['name'], 'js') ?>\'? Tindakan ini tidak bisa dibatalkan.')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
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
            <?= $pager->links('kuliner', 'default_full') ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
