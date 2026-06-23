<?= $this->extend('layouts/public') ?>

<?= $this->section('extra_head') ?>
<style>
    .hero-detail {
        height: 400px;
        background: url('https://images.unsplash.com/photo-1504674900247-0877df9cc836?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover no-repeat;
        position: relative;
        margin-top: -80px; /* offset navbar */
        z-index: 0;
    }

    .hero-detail::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0; right: 0;
        height: 60%;
        background: linear-gradient(to top, var(--light-bg), transparent);
    }

    .detail-card {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.06);
        margin-top: -150px;
        position: relative;
        z-index: 10;
        border: 1px solid rgba(0,0,0,0.02);
    }

    .detail-title {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--dark);
        margin-bottom: 10px;
        line-height: 1.2;
    }

    .badge-category {
        background: rgba(249, 115, 22, 0.1);
        color: var(--primary);
        padding: 8px 16px;
        border-radius: 30px;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .rating-box {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .rating-score {
        background: var(--primary);
        color: white;
        font-size: 1.5rem;
        font-weight: 800;
        padding: 10px 15px;
        border-radius: 12px;
        line-height: 1;
        box-shadow: 0 8px 20px rgba(249, 115, 22, 0.3);
    }

    .info-list { list-style: none; padding: 0; margin: 30px 0; }
    .info-list li {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        margin-bottom: 20px;
        font-size: 1.05rem;
        color: var(--text-main);
    }
    
    .info-list i {
        color: var(--primary);
        font-size: 1.2rem;
        background: rgba(249, 115, 22, 0.1);
        width: 40px; height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        flex-shrink: 0;
    }

    /* Map */
    #map {
        height: 350px;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        margin-bottom: 30px;
        z-index: 1;
    }

    /* Reviews Section */
    .reviews-section { margin-top: 50px; }
    
    .review-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 20px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }

    .review-header { display: flex; justify-content: space-between; margin-bottom: 15px; }
    .review-author { font-weight: 700; color: var(--dark); font-size: 1.1rem; }
    .review-date { color: var(--text-muted); font-size: 0.85rem; }
    .stars { color: #f59e0b; font-size: 0.9rem; }

    /* Form Rating */
    .rating-input { display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: 5px; }
    .rating-input input { display: none; }
    .rating-input label { font-size: 2rem; color: #cbd5e1; cursor: pointer; transition: color 0.2s; }
    .rating-input input:checked ~ label, .rating-input label:hover, .rating-input label:hover ~ label { color: #f59e0b; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="hero-detail"></div>

<div class="container mb-5 pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="detail-card">
                <div class="row">
                    <div class="col-md-8">
                        <span class="badge-category mb-3 d-inline-block"><?= esc($kuliner['category_name']) ?></span>
                        <h1 class="detail-title">
                            <?= esc($kuliner['name']) ?>
                            <?php if (session()->get('logged_in')): ?>
                            <button id="btn-favorite" class="btn btn-link p-0 ms-2 text-decoration-none" data-id="<?= $kuliner['id'] ?>">
                                <i class="bi <?= $is_favorited ? 'bi-heart-fill text-danger' : 'bi-heart text-secondary' ?>" style="font-size: 2rem;"></i>
                            </button>
                            <?php endif; ?>
                        </h1>
                        <p class="text-muted mb-4">Dikontribusikan oleh: <strong><?= esc($kuliner['contributor_name']) ?></strong></p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0 d-flex justify-content-md-end align-items-start">
                        <div class="rating-box">
                            <div class="text-end me-2">
                                <div class="text-muted fw-bold" style="font-size: 0.85rem; text-transform:uppercase;">Rating Rata-rata</div>
                                <div class="stars">
                                    <?php for($i=1; $i<=5; $i++): ?>
                                        <i class="bi bi-star-fill <?= $i <= round($kuliner['average_rating']) ? '' : 'text-light' ?>"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div class="rating-score"><?= number_format($kuliner['average_rating'], 1) ?></div>
                        </div>
                    </div>
                </div>

                <hr class="my-4" style="border-color: #e2e8f0;">

                <div class="row">
                    <div class="col-md-6">
                        <ul class="info-list">
                            <li>
                                <i class="bi bi-geo-alt-fill"></i>
                                <div>
                                    <strong class="d-block text-dark mb-1">Alamat Lengkap</strong>
                                    <?= esc($kuliner['address']) ?>
                                </div>
                            </li>
                            <li>
                                <i class="bi bi-info-circle-fill"></i>
                                <div>
                                    <strong class="d-block text-dark mb-1">Deskripsi Tambahan</strong>
                                    Ini adalah kuliner terbaik yang ada di daerah Mranggen. Pastikan Anda berkunjung dan merasakan kenikmatannya langsung.
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <div id="map"></div>
                    </div>
                </div>
            </div>

            <!-- Review Section -->
            <div class="reviews-section">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                    <h3 class="fw-bold m-0 font-outfit">Ulasan Pengunjung</h3>
                    <span class="badge bg-secondary rounded-pill px-3 py-2"><?= count($reviews) ?> Ulasan</span>
                </div>

                <!-- Form Tambah Review -->
                <div class="card border-0 shadow-sm rounded-4 mb-5" style="background-color: rgba(249, 115, 22, 0.03);">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Tulis Ulasan Anda</h5>
                        
                        <?php if(session()->getFlashdata('errors')): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach(session()->getFlashdata('errors') as $error): ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if(session()->get('logged_in')): ?>
                            <form action="<?= base_url('kuliner/storeReview') ?>" method="POST">
                                <?= csrf_field() ?>
                                <input type="hidden" name="kuliner_id" value="<?= $kuliner['id'] ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label text-muted fw-bold">Pilih Rating</label>
                                    <div class="rating-input">
                                        <input type="radio" id="star5" name="rating" value="5" required/><label for="star5" class="bi bi-star-fill"></label>
                                        <input type="radio" id="star4" name="rating" value="4" /><label for="star4" class="bi bi-star-fill"></label>
                                        <input type="radio" id="star3" name="rating" value="3" /><label for="star3" class="bi bi-star-fill"></label>
                                        <input type="radio" id="star2" name="rating" value="2" /><label for="star2" class="bi bi-star-fill"></label>
                                        <input type="radio" id="star1" name="rating" value="1" /><label for="star1" class="bi bi-star-fill"></label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <textarea class="form-control" name="comment" rows="3" placeholder="Bagaimana rasa makanannya? Pelayanannya? Ceritakan di sini..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-custom px-4">Kirim Ulasan</button>
                            </form>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="bi bi-lock text-muted" style="font-size: 2rem;"></i>
                                <p class="mt-2 mb-3">Anda harus login terlebih dahulu untuk memberikan ulasan pada tempat ini.</p>
                                <a href="<?= base_url('login') ?>" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Masuk / Login Sekarang</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Daftar Review -->
                <?php if(empty($reviews)): ?>
                    <div class="text-center py-5">
                        <p class="text-muted">Belum ada ulasan untuk kuliner ini. Jadilah yang pertama!</p>
                    </div>
                <?php else: ?>
                    <?php foreach($reviews as $rev): ?>
                    <div class="review-card">
                        <div class="review-header">
                            <div>
                                <div class="review-author">
                                    <i class="bi bi-person-circle text-secondary me-2"></i><?= esc($rev['username']) ?>
                                    <?php if($rev['role'] === 'admin'): ?>
                                        <span class="badge bg-danger ms-1" style="font-size: 0.6rem;">Admin</span>
                                    <?php endif; ?>
                                </div>
                                <div class="stars mt-1">
                                    <?php for($i=1; $i<=5; $i++): ?>
                                        <i class="bi bi-star-fill <?= $i <= $rev['rating'] ? '' : 'text-light' ?>"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div class="review-date">
                                <?= date('d M Y', strtotime($rev['created_at'])) ?>
                            </div>
                        </div>
                        <p class="mb-0 text-secondary" style="line-height: 1.6;"><?= nl2br(esc($rev['comment'])) ?></p>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('extra_js') ?>
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Default fallback to center of Demak if lat/lng is missing
    var lat = <?= !empty($kuliner['latitude']) ? $kuliner['latitude'] : '-6.8948' ?>;
    var lng = <?= !empty($kuliner['longitude']) ? $kuliner['longitude'] : '110.6386' ?>;

    var map = L.map('map').setView([lat, lng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    L.marker([lat, lng]).addTo(map)
        .bindPopup('<b><?= esc($kuliner['name'], 'js') ?></b><br><?= esc($kuliner['address'], 'js') ?>')
        .openPopup();

    // Favorite Toggle
    const btnFavorite = document.getElementById('btn-favorite');
    if (btnFavorite) {
        btnFavorite.addEventListener('click', function() {
            const kulinerId = this.dataset.id;
            const icon = this.querySelector('i');
            
            fetch('<?= base_url('favorites/toggle') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: '<?= csrf_token() ?>=' + encodeURIComponent('<?= csrf_hash() ?>') + '&kuliner_id=' + kulinerId
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    if (data.action === 'added') {
                        icon.classList.remove('bi-heart', 'text-secondary');
                        icon.classList.add('bi-heart-fill', 'text-danger');
                    } else {
                        icon.classList.remove('bi-heart-fill', 'text-danger');
                        icon.classList.add('bi-heart', 'text-secondary');
                    }
                } else {
                    alert(data.message || 'Terjadi kesalahan.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Gagal menghubungi server.');
            });
        });
    }
});
</script>
<?= $this->endSection() ?>
