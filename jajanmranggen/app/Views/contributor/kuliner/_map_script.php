<!-- Tambahkan di <head> -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Tambahkan di form -->
<div class="mb-3">
    <label class="form-label">Koordinat Lokasi</label>
    <div class="input-group mb-2">
        <input type="text" name="address" id="address_input" 
               class="form-control" placeholder="Ketik alamat lengkap..."
               value="<?= old('address', $kuliner['address'] ?? '') ?>">
        <button type="button" class="btn btn-outline-primary" id="btn_geocode">
            Cari Koordinat
        </button>
    </div>
    <div id="map" style="height: 350px; border-radius: 8px;"></div>
    <input type="hidden" name="latitude" id="lat_input" value="<?= old('latitude', $kuliner['latitude'] ?? '') ?>">
    <input type="hidden" name="longitude" id="lng_input" value="<?= old('longitude', $kuliner['longitude'] ?? '') ?>">
    <small class="text-muted">Geser marker untuk menyesuaikan posisi tepat.</small>
</div>

<script>
const defaultLat = parseFloat(document.getElementById('lat_input').value) || -6.9917;
const defaultLng = parseFloat(document.getElementById('lng_input').value) || 110.4897;

const map = L.map('map').setView([defaultLat, defaultLng], 15);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

const marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

marker.on('dragend', function(e) {
    const pos = e.target.getLatLng();
    document.getElementById('lat_input').value = pos.lat.toFixed(7);
    document.getElementById('lng_input').value = pos.lng.toFixed(7);
});

document.getElementById('btn_geocode').addEventListener('click', function() {
    const address = document.getElementById('address_input').value;
    if (!address) return alert('Isi alamat terlebih dahulu!');

    this.disabled = true;
    this.textContent = 'Mencari...';

    fetch(`/contributor/kuliner/geocode?q=${encodeURIComponent(address)}`)
        .then(r => r.json())
        .then(data => {
            if (data.error) {
                alert('Koordinat tidak ditemukan: ' + data.error);
            } else {
                const lat = parseFloat(data.lat);
                const lng = parseFloat(data.lng);
                map.setView([lat, lng], 17);
                marker.setLatLng([lat, lng]);
                document.getElementById('lat_input').value = lat.toFixed(7);
                document.getElementById('lng_input').value = lng.toFixed(7);
            }
        })
        .catch(() => alert('Terjadi kesalahan. Coba atur marker secara manual.'))
        .finally(() => {
            this.disabled = false;
            this.textContent = 'Cari Koordinat';
        });
});
</script>