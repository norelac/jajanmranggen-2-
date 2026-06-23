<?= $this->extend('layouts/app') ?>

<?= $this->section('extra_head') ?>
    <script type="text/javascript" src="<?= env('midtrans.isProduction') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' ?>" data-client-key="<?= $client_key ?>"></script>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body text-center p-5">
                <div class="mb-4">
                    <i class="bi bi-wallet2 text-success" style="font-size: 3rem;"></i>
                </div>
                <h4>Selesaikan Pembayaran</h4>
                <p class="text-muted mt-2">
                    Invoice: <strong><?= $invoice ?></strong>
                </p>
                <p>Klik tombol di bawah ini untuk memproses pembayaran via Midtrans.</p>
                
                <button id="pay-button" class="btn btn-primary-custom btn-lg mt-3 w-100">Proses Pembayaran</button>
                <a href="<?= base_url('contributor/kuliner') ?>" class="btn btn-link mt-3 text-muted">Kembali ke Daftar Kuliner</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('extra_js') ?>
<script type="text/javascript">
    document.getElementById('pay-button').onclick = function () {
        snap.pay('<?= $snap_token ?>', {
            onSuccess: function (result) {
                alert("Pembayaran Berhasil!");
                window.location.href = "<?= base_url('contributor/kuliner') ?>";
            },
            onPending: function (result) {
                alert("Menunggu pembayaran Anda.");
                window.location.href = "<?= base_url('contributor/kuliner') ?>";
            },
            onError: function (result) {
                alert("Pembayaran Gagal.");
                window.location.href = "<?= base_url('contributor/kuliner') ?>";
            },
            onClose: function () {
                alert('Anda menutup popup tanpa menyelesaikan pembayaran');
            }
        });
    };
</script>
<?= $this->endSection() ?>
