<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<?php $page_title = 'Dashboard Kontributor'; ?>

<div class="row g-4 mb-4">
    <!-- Stat Cards -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background: linear-gradient(135deg,#e85d04,#f48c06)">
            <div class="stat-icon mb-2"><i class="bi bi-shop"></i></div>
            <div class="stat-number"><?= $total_kuliner ?></div>
            <div class="stat-label">Total Kuliner</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background: linear-gradient(135deg,#10b981,#059669)">
            <div class="stat-icon mb-2"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-number"><?= $approved ?></div>
            <div class="stat-label">Disetujui</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background: linear-gradient(135deg,#f59e0b,#d97706)">
            <div class="stat-icon mb-2"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-number"><?= $pending ?></div>
            <div class="stat-label">Menunggu Review</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background: linear-gradient(135deg,#ef4444,#dc2626)">
            <div class="stat-icon mb-2"><i class="bi bi-x-circle-fill"></i></div>
            <div class="stat-number"><?= $rejected ?></div>
            <div class="stat-label">Ditolak</div>
        </div>
    </div>
</div>

<!-- Recent Kuliner Table -->
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0 fw-700" style="font-weight:700"><i class="bi bi-clock-history me-2 text-warning"></i>Kuliner Terbaru</h5>
        <a href="<?= base_url('contributor/kuliner/create') ?>" class="btn btn-sm btn-primary-custom px-3">
            <i class="bi bi-plus-lg me-1"></i> Tambah Kuliner
        </a>
    </div>
    <div class="card-body p-0">
        <?php if (empty($recent_kuliner)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-shop" style="font-size:3rem;opacity:.3"></i>
                <p class="mt-2">Belum ada kuliner. <a href="<?= base_url('contributor/kuliner/create') ?>">Tambahkan sekarang!</a></p>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Kuliner</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Rating</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_kuliner as $i => $k): ?>
                    <tr>
                        <td class="text-muted"><?= $i + 1 ?></td>
                        <td>
                            <div class="fw-600" style="font-weight:600"><?= esc($k['name']) ?></div>
                            <small class="text-muted"><?= esc(substr($k['address'], 0, 50)) ?>...</small>
                        </td>
                        <td><span class="badge bg-light text-dark border"><?= esc($k['category_name'] ?? '-') ?></span></td>
                        <td>
                            <?php
                            $statusMap = [
                                'approved' => ['badge-approved','Disetujui'],
                                'pending'  => ['badge-pending','Menunggu'],
                                'rejected' => ['badge-rejected','Ditolak'],
                                'closed_permanent' => ['badge-closed','Ditutup'],
                            ];
                            [$cls, $lbl] = $statusMap[$k['status']] ?? ['bg-secondary','Unknown'];
                            ?>
                            <span class="badge <?= $cls ?>"><?= $lbl ?></span>
                        </td>
                        <td>
                            <i class="bi bi-star-fill text-warning"></i>
                            <?= number_format($k['average_rating'], 1) ?>
                        </td>
                        <td>
                            <a href="<?= base_url('contributor/kuliner/edit/' . $k['id']) ?>" class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="<?= base_url('contributor/kuliner/delete/' . $k['id']) ?>" class="d-inline"
                                  onsubmit="return confirm('Hapus kuliner ini?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
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

<?= $this->endSection() ?>
