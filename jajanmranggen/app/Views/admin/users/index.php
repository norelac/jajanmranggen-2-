<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Kelola Pengguna</h4>
        <p class="text-muted mb-0">Daftar semua pengguna yang terdaftar di JajanMranggen</p>
    </div>
</div>

<!-- Search/Filter -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="<?= base_url('admin/users') ?>" class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control" placeholder="Cari username atau email..."
                           value="<?= esc($search ?? '') ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select">
                    <option value="">Semua Role</option>
                    <option value="admin"       <?= ($role ?? '') === 'admin'       ? 'selected' : '' ?>>Admin</option>
                    <option value="contributor" <?= ($role ?? '') === 'contributor' ? 'selected' : '' ?>>Kontributor</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary-custom flex-grow-1">Filter</button>
                <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary">Reset</a>
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
                        <th>#</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>No. Telp</th>
                        <th>Role</th>
                        <th>Bergabung</th>
                        <th style="width:80px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-people" style="font-size:2.5rem;opacity:.3;"></i>
                            <p class="mt-2 mb-0">Tidak ada pengguna ditemukan.</p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($users as $i => $u): ?>
                    <tr>
                        <td class="text-muted"><?= $i + 1 ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:34px;height:34px;background:linear-gradient(135deg,#e85d04,#f48c06);
                                    border-radius:50%;display:flex;align-items:center;justify-content:center;
                                    color:#fff;font-weight:700;font-size:.85rem;">
                                    <?= strtoupper(substr($u['username'], 0, 1)) ?>
                                </div>
                                <span class="fw-semibold"><?= esc($u['username']) ?></span>
                                <?php if ($u['id'] == session()->get('user_id')): ?>
                                    <span class="badge bg-info" style="font-size:.65rem;">Anda</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><?= esc($u['email']) ?></td>
                        <td class="text-muted"><?= esc($u['phone'] ?? '-') ?></td>
                        <td>
                            <span class="badge <?= $u['role'] === 'admin' ? 'bg-danger' : 'bg-primary' ?>">
                                <?= ucfirst($u['role']) ?>
                            </span>
                        </td>
                        <td class="text-muted small"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                        <td>
                            <?php if ($u['id'] != session()->get('user_id') && $u['role'] !== 'admin'): ?>
                            <form action="<?= base_url('admin/users/delete/'.$u['id']) ?>" method="POST"
                                  onsubmit="return confirm('Hapus user \'<?= esc($u['username'], 'js') ?>\'?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus User">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            <?php else: ?>
                            <span class="text-muted small">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if (!empty($pager)): ?>
        <div class="px-4 py-3">
            <?= $pager->links('users', 'default_full') ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
