<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h4 class="fw-bold">Bukti Pembayaran Sponsor</h4>
                        <p class="text-muted">JajanMranggen</p>
                    </div>

                    <div class="text-center mb-4">
                        <?php if ($payment['status'] === 'paid'): ?>
                            <div class="d-inline-block bg-success bg-opacity-10 text-success rounded-circle p-3 mb-2">
                                <i class="bi bi-check-circle-fill" style="font-size: 2.5rem;"></i>
                            </div>
                            <h5 class="text-success fw-bold">Pembayaran Berhasil</h5>
                        <?php elseif ($payment['status'] === 'failed' || $payment['status'] === 'expired'): ?>
                            <div class="d-inline-block bg-danger bg-opacity-10 text-danger rounded-circle p-3 mb-2">
                                <i class="bi bi-x-circle-fill" style="font-size: 2.5rem;"></i>
                            </div>
                            <h5 class="text-danger fw-bold">Pembayaran Gagal</h5>
                        <?php else: ?>
                            <div class="d-inline-block bg-warning bg-opacity-10 text-warning rounded-circle p-3 mb-2">
                                <i class="bi bi-clock-history" style="font-size: 2.5rem;"></i>
                            </div>
                            <h5 class="text-warning fw-bold">Menunggu Pembayaran</h5>
                        <?php endif; ?>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-sm-5 text-muted">Invoice</div>
                        <div class="col-sm-7 fw-semibold font-monospace"><?= esc($payment['invoice_number']) ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-5 text-muted">Nama Kontributor</div>
                        <div class="col-sm-7 fw-semibold"><?= esc($user['username'] ?? '-') ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-5 text-muted">Email</div>
                        <div class="col-sm-7"><?= esc($user['email'] ?? '-') ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-5 text-muted">Kuliner</div>
                        <div class="col-sm-7 fw-semibold"><?= esc($kuliner['name'] ?? 'Kuliner telah dihapus') ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-5 text-muted">Jumlah</div>
                        <div class="col-sm-7 fw-bold" style="color: #e85d04;">Rp <?= number_format($payment['amount'], 0, ',', '.') ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-5 text-muted">Status</div>
                        <div class="col-sm-7">
                            <?php if ($payment['status'] === 'paid'): ?>
                                <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-check-circle me-1"></i> Berhasil</span>
                            <?php elseif ($payment['status'] === 'failed' || $payment['status'] === 'expired'): ?>
                                <span class="badge bg-danger rounded-pill px-3 py-2"><i class="bi bi-x-circle me-1"></i> Gagal</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark rounded-pill px-3 py-2"><i class="bi bi-clock-history me-1"></i> Pending</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if ($payment['status'] === 'paid' && !empty($payment['payment_method'])): ?>
                    <div class="row mb-3">
                        <div class="col-sm-5 text-muted">Metode Pembayaran</div>
                        <div class="col-sm-7"><?= esc($payment['payment_method']) ?></div>
                    </div>
                    <?php endif; ?>
                    <div class="row mb-3">
                        <div class="col-sm-5 text-muted">Tanggal Dibuat</div>
                        <div class="col-sm-7"><?= date('d M Y H:i', strtotime($payment['created_at'])) ?> WIB</div>
                    </div>
                    <?php if ($payment['status'] === 'paid' && !empty($payment['updated_at'])): ?>
                    <div class="row mb-3">
                        <div class="col-sm-5 text-muted">Tanggal Dibayar</div>
                        <div class="col-sm-7"><?= date('d M Y H:i', strtotime($payment['updated_at'])) ?> WIB</div>
                    </div>
                    <?php endif; ?>

                    <hr>

                    <div class="text-center text-muted small">
                        <p class="mb-1">URL ini adalah bukti pembayaran resmi dari JajanMranggen</p>
                        <p class="mb-0">Status pembayaran diperbarui secara otomatis oleh sistem</p>
                    </div>

                    <div class="text-center mt-4">
                        <button onclick="window.print()" class="btn btn-outline-primary me-2">
                            <i class="bi bi-printer"></i> Cetak
                        </button>
                        <a href="<?= base_url('/') ?>" class="btn btn-primary-custom">
                            <i class="bi bi-house"></i> Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
