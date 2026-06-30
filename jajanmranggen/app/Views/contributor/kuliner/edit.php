<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<?php $page_title = 'Edit Data Kuliner'; ?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-700"><i class="bi bi-pencil-square me-2 text-primary"></i>Form Edit Kuliner</h5>
                <span class="badge bg-secondary">Status saat ini: <?= esc(ucfirst($kuliner['status'])) ?></span>
            </div>
            <div class="card-body">
                <form action="<?= base_url('contributor/kuliner/update/' . $kuliner['id']) ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nama Kuliner <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= old('name', $kuliner['name']) ?>" required placeholder="Contoh: Bakso Pak Harto">
                        </div>

                        <div class="col-md-6">
                            <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= old('category_id', $kuliner['category_id']) == $cat['id'] ? 'selected' : '' ?>>
                                        <?= esc($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Masukkan deskripsi menarik tentang kuliner ini..."><?= old('description', $kuliner['description']) ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="photo" class="form-label">Upload Foto Baru (Opsional)</label>
                        <input class="form-control" type="file" id="photo" name="photo" accept="image/*">
                        <small class="text-muted d-block mt-1">Format: JPG, PNG. Maksimal 2MB. Mengunggah foto baru akan menambah koleksi foto kuliner ini.</small>
                    </div>

                    <?php if (!empty($photos)): ?>
                        <div class="mb-4">
                            <label class="form-label">Koleksi Foto Saat Ini</label>
                            <div class="row g-2">
                                <?php foreach ($photos as $p): ?>
                                    <div class="col-3 col-sm-2 position-relative">
                                        <img src="<?= base_url('uploads/thumbnails/' . $p['filename']) ?>" class="img-thumbnail w-100" style="height: 80px; object-fit: cover;" alt="Foto Kuliner">
                                        <?php if ($p['is_primary']): ?>
                                            <span class="position-absolute top-0 start-0 m-1 badge bg-primary" style="font-size: 0.6rem;">Utama</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Leaflet CSS (inline agar pasti ter-load) -->
                    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
                    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

                    <!-- Map & Geocoding Section -->
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3"><i class="bi bi-map me-2 text-secondary"></i>Lokasi Geografis & Alamat</h6>
                            
                            <div class="mb-3">
                                <label for="address_input" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                                <div class="position-relative">
                                    <div class="input-group">
                                        <input type="text" name="address" id="address_input" 
                                               class="form-control" placeholder="Contoh: Jl. Raya Mranggen - Demak"
                                               value="<?= old('address', $kuliner['address']) ?>" required autocomplete="off">
                                        <button type="button" class="btn btn-primary" id="btn_geocode">
                                            📍 Cari Koordinat
                                        </button>
                                    </div>
                                    <div id="search_suggestions" class="list-group position-absolute w-100 shadow-lg d-none" style="z-index: 1050; max-height: 250px; overflow-y: auto;"></div>
                                </div>
                                <small class="text-muted">Ketik nama jalan/daerah lalu pilih rekomendasi alamat lengkap yang muncul agar lokasi lebih akurat.</small>
                            </div>

                            <div class="mb-3">
                                <div id="map" style="height: 350px; border-radius: 8px;" class="shadow-sm"></div>
                            </div>

                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label">Latitude</label>
                                    <input type="text" name="latitude" id="lat_input" class="form-control bg-white" readonly value="<?= old('latitude', $kuliner['latitude']) ?>" placeholder="-6.9917">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Longitude</label>
                                    <input type="text" name="longitude" id="lng_input" class="form-control bg-white" readonly value="<?= old('longitude', $kuliner['longitude']) ?>" placeholder="110.4897">
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2">Anda juga dapat menggeser marker merah pada peta secara manual untuk menyesuaikan koordinat.</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('contributor/kuliner') ?>" class="btn btn-light border px-4">Batal</a>
                        <button type="submit" class="btn btn-primary-custom px-4">Simpan Perubahan</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

    <script>
        // Koordinat default dari database atau default Mranggen
        const defaultLat = parseFloat(document.getElementById('lat_input').value) || -6.9917;
        const defaultLng = parseFloat(document.getElementById('lng_input').value) || 110.4897;

        const map = L.map('map').setView([defaultLat, defaultLng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        const marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

        // Update input koordinat saat marker digeser
        marker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            document.getElementById('lat_input').value = pos.lat.toFixed(7);
            document.getElementById('lng_input').value = pos.lng.toFixed(7);
        });

        const addressInput = document.getElementById('address_input');
        const suggestionsContainer = document.getElementById('search_suggestions');
        let debounceTimer;

        // Fungsi untuk merender daftar rekomendasi
        function renderSuggestions(data) {
            suggestionsContainer.innerHTML = '';
            if (data.error || !Array.isArray(data) || data.length === 0) {
                const item = document.createElement('div');
                item.className = 'list-group-item list-group-item-action text-muted small disabled';
                item.textContent = 'Alamat tidak ditemukan';
                suggestionsContainer.appendChild(item);
                suggestionsContainer.classList.remove('d-none');
                return;
            }

            data.forEach(item => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'list-group-item list-group-item-action text-start small py-2';
                btn.innerHTML = `<i class="bi bi-geo-alt-fill text-primary me-2"></i>${item.display_name}`;
                btn.addEventListener('click', () => {
                    addressInput.value = item.display_name;
                    const lat = parseFloat(item.lat);
                    const lng = parseFloat(item.lng);
                    document.getElementById('lat_input').value = lat.toFixed(7);
                    document.getElementById('lng_input').value = lng.toFixed(7);
                    map.setView([lat, lng], 17);
                    marker.setLatLng([lat, lng]);
                    suggestionsContainer.classList.add('d-none');
                });
                suggestionsContainer.appendChild(btn);
            });
            suggestionsContainer.classList.remove('d-none');
        }

        // Event listener saat pengguna mengetik alamat (auto-complete dengan debounce)
        addressInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const query = this.value.trim();
            if (query.length < 4) {
                suggestionsContainer.classList.add('d-none');
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`/contributor/kuliner/geocode?q=${encodeURIComponent(query)}`)
                    .then(r => r.json())
                    .then(data => {
                        renderSuggestions(data);
                    })
                    .catch(() => {
                        console.error('Gagal memuat rekomendasi alamat');
                    });
            }, 600);
        });

        // Event listener saat mengklik tombol 'Cari Koordinat'
        document.getElementById('btn_geocode').addEventListener('click', function() {
            const address = addressInput.value.trim();
            if (!address) return alert('Isi alamat terlebih dahulu!');

            this.disabled = true;
            this.textContent = 'Mencari...';

            fetch(`/contributor/kuliner/geocode?q=${encodeURIComponent(address)}`)
                .then(r => r.json())
                .then(data => {
                    if (data.error || !Array.isArray(data) || data.length === 0) {
                        alert('Koordinat tidak ditemukan.');
                        suggestionsContainer.classList.add('d-none');
                    } else if (data.length === 1) {
                        // Jika hanya ada 1 hasil, langsung pilih
                        const item = data[0];
                        addressInput.value = item.display_name;
                        const lat = parseFloat(item.lat);
                        const lng = parseFloat(item.lng);
                        document.getElementById('lat_input').value = lat.toFixed(7);
                        document.getElementById('lng_input').value = lng.toFixed(7);
                        map.setView([lat, lng], 17);
                        marker.setLatLng([lat, lng]);
                        suggestionsContainer.classList.add('d-none');
                    } else {
                        // Jika banyak hasil, tampilkan daftarnya
                        renderSuggestions(data);
                        alert('Ditemukan beberapa lokasi yang mirip. Silakan pilih salah satu alamat lengkap dari daftar di bawah kolom alamat.');
                    }
                })
                .catch(() => alert('Terjadi kesalahan koneksi. Silakan atur marker secara manual.'))
                .finally(() => {
                    this.disabled = false;
                    this.textContent = '📍 Cari Koordinat';
                });
        });

        // Klik di luar rekomendasi untuk menutup list
        document.addEventListener('click', function(e) {
            if (!addressInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                suggestionsContainer.classList.add('d-none');
            }
        });
    </script>

<?= $this->endSection() ?>
