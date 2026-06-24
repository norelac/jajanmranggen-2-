<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">Riwayat Transaksi Sponsor</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Invoice</th>
                        <th>Kontributor</th>
                        <th>Kuliner</th>
                        <th>Nominal</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($payments)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                Belum ada riwayat transaksi.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($payments as $p): ?>
                            <tr>
                                <td>
                                    <div class="text-dark fw-semibold"><?= date('d M Y', strtotime($p['created_at'])) ?></div>
                                    <div class="text-muted" style="font-size: 0.8rem;"><?= date('H:i', strtotime($p['created_at'])) ?> WIB</div>
                                </td>
                                <td><span class="font-monospace bg-light border px-2 py-1 rounded" style="font-size: 0.85rem;"><?= esc($p['invoice_number']) ?></span></td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= esc($p['username'] ?? 'User Dihapus') ?></div>
                                    <div class="text-muted" style="font-size: 0.8rem;"><?= esc($p['email'] ?? '') ?></div>
                                </td>
                                <td>
                                    <?php if(!empty($p['kuliner_name'])): ?>
                                        <?= esc($p['kuliner_name']) ?>
                                    <?php else: ?>
                                        <span class="text-muted fst-italic">Kuliner Dihapus</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="fw-bold" style="color: #e85d04;">Rp <?= number_format($p['amount'], 0, ',', '.') ?></span></td>
                                <td>
                                    <?php if($p['status'] === 'paid'): ?>
                                        <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-check-circle me-1"></i> Berhasil</span>
                                    <?php elseif($p['status'] === 'failed' || $p['status'] === 'expired' || $p['status'] === 'canceled'): ?>
                                        <span class="badge bg-danger rounded-pill px-3 py-2"><i class="bi bi-x-circle me-1"></i> Gagal</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2"><i class="bi bi-clock-history me-1"></i> Pending</span>
                                    <?php endif; ?>
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
