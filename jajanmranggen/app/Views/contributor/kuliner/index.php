<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<?php $page_title = 'Daftar Kuliner Saya'; ?>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0 fw-700" style="font-weight: 700;">
            <i class="bi bi-shop me-2 text-primary"></i>Daftar Kuliner Saya
        </h5>
        <a href="<?= base_url('contributor/kuliner/create') ?>" class="btn btn-primary-custom px-3">
            <i class="bi bi-plus-lg me-1"></i> Tambah Kuliner Baru
        </a>
    </div>
    <div class="card-body p-0">
        <?php if (empty($kuliner)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-shop-window" style="font-size: 3rem; opacity: .3;"></i>
                <p class="mt-2">Belum ada data kuliner yang Anda tambahkan.</p>
                <a href="<?= base_url('contributor/kuliner/create') ?>" class="btn btn-primary btn-sm">Mulai Tambah Sekarang</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Kuliner</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Promosi</th>
                            <th>Rating</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($kuliner as $index => $k): ?>
                            <tr>
                                <td class="text-muted"><?= $index + 1 ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <div class="fw-bold text-dark"><?= esc($k['name']) ?></div>
                                            <small class="text-muted"><i class="bi bi-geo-alt-fill me-1"></i><?= esc(substr($k['address'], 0, 60)) ?><?= strlen($k['address']) > 60 ? '...' : '' ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border"><?= esc($k['category_name'] ?? 'Umum') ?></span>
                                </td>
                                <td>
                                    <?php
                                    $statusMap = [
                                        'pending'          => ['badge-pending', 'Menunggu Persetujuan'],
                                        'approved'         => ['badge-approved', 'Disetujui'],
                                        'rejected'         => ['badge-rejected', 'Ditolak'],
                                        'closed_permanent' => ['badge-closed', 'Tutup Permanen'],
                                    ];
                                    [$cls, $lbl] = $statusMap[$k['status']] ?? ['bg-secondary', esc($k['status'])];
                                    ?>
                                    <span class="badge <?= $cls ?>"><?= $lbl ?></span>
                                </td>
                                <td>
                                    <?php if ($k['is_promoted']): ?>
                                        <span class="badge bg-success" title="Promosi aktif sampai: <?= $k['promoted_until'] ?>">
                                            <i class="bi bi-graph-up-arrow me-1"></i> Promoted
                                        </span>
                                    <?php else: ?>
                                        <?php if ($k['status'] === 'approved'): ?>
                                            <a href="<?= base_url('contributor/payment/sponsor/' . $k['id']) ?>" class="btn btn-xs btn-outline-warning py-0 px-2" style="font-size: 0.75rem;">
                                                <i class="bi bi-star"></i> Promosikan
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted" style="font-size: 0.8rem;">-</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <span class="fw-semibold"><?= number_format($k['average_rating'], 1) ?></span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2 px-3">
                                        <a href="<?= base_url('contributor/kuliner/edit/' . $k['id']) ?>" class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                        <form action="<?= base_url('contributor/kuliner/delete/' . $k['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kuliner ini?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
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
