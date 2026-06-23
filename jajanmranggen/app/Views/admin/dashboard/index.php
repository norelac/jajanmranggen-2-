<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row g-4 mb-4">
    <!-- Stat Cards -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
            <i class="bi bi-shop position-absolute opacity-10" style="right:15px;bottom:-5px;font-size:4rem;"></i>
            <div class="stat-icon mb-2"><i class="bi bi-shop"></i></div>
            <div class="stat-number"><?= $total_kuliner ?></div>
            <div class="stat-label">Total Kuliner</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #10b981, #059669);">
            <i class="bi bi-check-circle-fill position-absolute opacity-10" style="right:15px;bottom:-5px;font-size:4rem;"></i>
            <div class="stat-icon mb-2"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-number"><?= $total_approved ?></div>
            <div class="stat-label">Disetujui</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
            <i class="bi bi-hourglass-split position-absolute opacity-10" style="right:15px;bottom:-5px;font-size:4rem;"></i>
            <div class="stat-icon mb-2"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-number"><?= $total_pending ?></div>
            <div class="stat-label">Menunggu Review</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
            <i class="bi bi-people-fill position-absolute opacity-10" style="right:15px;bottom:-5px;font-size:4rem;"></i>
            <div class="stat-icon mb-2"><i class="bi bi-people-fill"></i></div>
            <div class="stat-number"><?= $total_users ?></div>
            <div class="stat-label">Total Users</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Secondary stats -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #e85d04, #f48c06);">
            <div class="stat-icon mb-2"><i class="bi bi-chat-square-text-fill"></i></div>
            <div class="stat-number"><?= $total_reviews ?></div>
            <div class="stat-label">Total Ulasan</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #06b6d4, #0891b2);">
            <div class="stat-icon mb-2"><i class="bi bi-tag-fill"></i></div>
            <div class="stat-number"><?= $total_categories ?></div>
            <div class="stat-label">Kategori</div>
        </div>
    </div>
    <!-- Quick actions -->
    <div class="col-xl-6">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-lightning-fill text-warning me-2"></i>Aksi Cepat</h6>
            </div>
            <div class="card-body d-flex flex-wrap gap-2 align-items-center">
                <a href="<?= base_url('admin/kuliner?status=pending') ?>" class="btn btn-sm btn-warning fw-semibold">
                    <i class="bi bi-hourglass me-1"></i> Kuliner Pending (<?= $total_pending ?>)
                </a>
                <a href="<?= base_url('admin/kuliner') ?>" class="btn btn-sm btn-primary fw-semibold">
                    <i class="bi bi-shop me-1"></i> Semua Kuliner
                </a>
                <a href="<?= base_url('admin/kategori/create') ?>" class="btn btn-sm btn-success fw-semibold">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Kategori
                </a>
                <a href="<?= base_url('admin/reviews') ?>" class="btn btn-sm btn-danger fw-semibold">
                    <i class="bi bi-chat-square-text me-1"></i> Moderasi Ulasan
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <!-- Kuliner Menunggu Persetujuan -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold"><i class="bi bi-hourglass-split text-warning me-2"></i>Menunggu Persetujuan</h6>
                <a href="<?= base_url('admin/kuliner?status=pending') ?>" class="btn btn-sm btn-outline-secondary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($pending_kuliner)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-check-circle" style="font-size:2rem;opacity:.3;"></i>
                        <p class="mt-2 mb-0">Tidak ada kuliner yang menunggu persetujuan</p>
                    </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Kuliner</th><th>Kontributor</th><th>Aksi</th></tr></thead>
                        <tbody>
                        <?php foreach ($pending_kuliner as $k): ?>
                        <tr>
                            <td>
                                <div class="fw-semibold"><?= esc($k['name']) ?></div>
                                <small class="text-muted"><?= esc($k['category_name']) ?></small>
                            </td>
                            <td><small><?= esc($k['contributor_name']) ?></small></td>
                            <td>
                                <form action="<?= base_url('admin/kuliner/approve/'.$k['id']) ?>" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-xs btn-success" style="padding:2px 8px;font-size:.75rem;">✓</button>
                                </form>
                                <form action="<?= base_url('admin/kuliner/reject/'.$k['id']) ?>" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-xs btn-danger" style="padding:2px 8px;font-size:.75rem;">✗</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Kuliner Terbaru -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history text-primary me-2"></i>Kuliner Terbaru</h6>
                <a href="<?= base_url('admin/kuliner') ?>" class="btn btn-sm btn-outline-secondary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Nama</th><th>Status</th><th>Tgl Tambah</th></tr></thead>
                        <tbody>
                        <?php foreach ($recent_kuliner as $k): ?>
                        <?php
                        $statusMap = [
                            'approved' => ['badge-approved','Disetujui'],
                            'pending'  => ['badge-pending','Pending'],
                            'rejected' => ['badge-rejected','Ditolak'],
                            'closed_permanent' => ['badge-closed','Tutup'],
                        ];
                        [$cls, $lbl] = $statusMap[$k['status']] ?? ['bg-secondary','?'];
                        ?>
                        <tr>
                            <td>
                                <div class="fw-semibold" style="font-size:.875rem;"><?= esc($k['name']) ?></div>
                            </td>
                            <td><span class="badge <?= $cls ?>"><?= $lbl ?></span></td>
                            <td><small class="text-muted"><?= date('d M Y', strtotime($k['created_at'])) ?></small></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
