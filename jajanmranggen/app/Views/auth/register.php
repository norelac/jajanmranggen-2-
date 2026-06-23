<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - JajanMranggen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; display: flex; align-items: center; min-height: 100vh; padding: 40px 0; }
        .auth-card { border: none; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .btn-custom { background: linear-gradient(135deg, #f97316, #fcd34d); color: white; border: none; border-radius: 8px; font-weight: 600; padding: 12px; }
        .btn-custom:hover { opacity: 0.9; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-dark">Jajan<span style="color: #f97316;">Mranggen</span></h2>
                    <p class="text-muted">Daftar sebagai Kontributor</p>
                </div>
                
                <div class="card auth-card">
                    <div class="card-body p-4">
                        <?php if (session()->getFlashdata('errors')): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach (session()->getFlashdata('errors') as $e): ?>
                                        <li><?= esc($e) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('register') ?>" method="POST">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap / Username</label>
                                <input type="text" name="username" class="form-control" required value="<?= old('username') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required value="<?= old('email') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">No. Telepon / WhatsApp</label>
                                <input type="text" name="phone" class="form-control" value="<?= old('phone') ?>">
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-custom w-100">Daftar Sekarang</button>
                        </form>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <p class="text-muted">Sudah punya akun? <a href="<?= base_url('login') ?>" class="text-decoration-none" style="color: #f97316; font-weight: 600;">Login</a></p>
                    <a href="<?= base_url('/') ?>" class="text-decoration-none text-secondary">Kembali ke Beranda</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
