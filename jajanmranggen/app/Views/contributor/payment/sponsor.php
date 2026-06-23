<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Promosikan Kuliner</h5>
            </div>
            <div class="card-body text-center p-5">
                <div class="mb-4">
                    <i class="bi bi-megaphone-fill text-warning" style="font-size: 3rem;"></i>
                </div>
                <h4>Sponsori: <?= esc($kuliner['name']) ?></h4>
                <p class="text-muted mt-3">
                    Jadikan kuliner Anda tampil paling atas dan direkomendasikan kepada semua pengunjung JajanMranggen.
                </p>
                <div class="bg-light p-3 rounded mb-4">
                    <strong>Paket Promosi: 7 Hari</strong><br>
                    <h3 class="text-primary mt-2">Rp 50.000</h3>
                </div>

                <form action="<?= base_url('contributor/payment/checkout') ?>" method="POST">
                    <input type="hidden" name="kuliner_id" value="<?= $kuliner['id'] ?>">
                    <a href="<?= base_url('contributor/kuliner') ?>" class="btn btn-outline-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary-custom">Bayar Sekarang</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
