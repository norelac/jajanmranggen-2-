<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center p-5">
                    <?php if ($payment['status'] === 'paid'): ?>
                        <div class="mb-4">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="fw-bold">Pembayaran Berhasil!</h3>
<p class="text-muted mt-3 fs-5">
                            Terima kasih, pembayaran untuk invoice <strong><?= esc($payment['invoice_number']) ?></strong> telah kami terima. Kuliner Anda kini telah dipromosikan.
                        </p>
                        <div class="mt-3">
                            <a href="<?= base_url('payment/invoice/' . $payment['invoice_number']) ?>" class="btn btn-outline-success btn-sm" target="_blank">
                                <i class="bi bi-receipt"></i> Lihat / Cetak Bukti Pembayaran
                            </a>
                        </div>
                    <?php elseif ($payment['status'] === 'failed'): ?>
                        <div class="mb-4">
                            <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="fw-bold">Pembayaran Gagal</h3>
                        <p class="text-muted mt-3 fs-5">
                            Mohon maaf, pembayaran untuk invoice <strong><?= esc($payment['invoice_number']) ?></strong> gagal atau kadaluarsa. Silakan lakukan proses sponsor ulang jika ingin mencoba kembali.
                        </p>
                    <?php else: ?>
                        <div class="mb-4">
                            <i class="bi bi-clock-history text-warning" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="fw-bold">Menunggu Verifikasi</h3>
                        <p class="text-muted mt-3 fs-5">
                            Transaksi dengan invoice <strong><?= esc($payment['invoice_number']) ?></strong> telah dibuat. 
                        </p>
                        <p class="text-muted">
                            Jika Anda sudah melakukan pembayaran di halaman DOKU, mohon tunggu beberapa saat hingga sistem memverifikasi pembayaran Anda secara otomatis. Anda juga akan menerima notifikasi via WhatsApp/Email saat pembayaran berhasil.
                        </p>
                        <p class="mt-3">
                            <a href="<?= base_url('payment/invoice/' . $payment['invoice_number']) ?>" class="btn btn-outline-info btn-sm" target="_blank">
                                <i class="bi bi-receipt"></i> Lihat Status Pembayaran
                            </a>
                            <button onclick="window.location.reload();" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-arrow-clockwise"></i> Cek Status Manual
                            </button>
                        </p>
                    <?php endif; ?>

                    <div class="mt-5">
                        <a href="<?= base_url('contributor/kuliner') ?>" class="btn btn-primary-custom px-4 py-2" style="border-radius: 8px;">
                            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Kuliner
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($payment['status'] === 'pending'): ?>
<script>
    const invoiceNumber = '<?= esc($payment['invoice_number']) ?>';
    const checkUrl = '<?= base_url('contributor/payment/check-status/') ?>' + invoiceNumber;

    setInterval(() => {
        fetch(checkUrl)
            .then(response => response.json())
            .then(data => {
                if (data.status !== 'pending') {
                    // Status berubah (menjadi paid/failed), reload halaman otomatis
                    window.location.reload();
                }
            })
            .catch(error => console.error('Error checking status:', error));
    }, 5000); // Cek setiap 5 detik
</script>
<?php endif; ?>

<?= $this->endSection() ?>
