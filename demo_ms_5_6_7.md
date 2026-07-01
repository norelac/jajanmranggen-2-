# 🎓 Panduan Demo — Milestone 5, 6, 7
## Proyek JajanMranggen

---

## 🚀 Cara Menjalankan Project

### Prasyarat

| Software | Versi Minimal | Keterangan |
|----------|--------------|------------|
| **PHP** | 8.1+ | Termasuk ekstensi: `curl`, `mbstring`, `mysqli`, `intl`, `openssl` |
| **Composer** | 2.x | Dependency manager PHP |
| **MySQL / MariaDB** | 5.7+ / 10.4+ | Database server |
| **Web Server** | Apache / Nginx | Bisa pakai Laragon (all-in-one) |
| **Git** | - | Untuk clone repository |
| **Postman** | - | Untuk demo Milestone 6 (opsional, bisa pakai browser) |
| **Ngrok** | - | Untuk demo Milestone 7 (webhook DOKU) |

### Langkah-Langkah

#### 1. Clone Repository
```bash
git clone https://github.com/norelac/jajanmranggen-2-.git
cd jajanmranggen-2-
```
> Atau ekstrak folder project jika mendapat file ZIP.

#### 2. Install Dependency (Composer)
```bash
cd jajanmranggen
composer install
```

#### 3. Konfigurasi Database
Buka phpMyAdmin (atau MySQL CLI), buat database baru:
```sql
CREATE DATABASE jajanmranggen CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

#### 4. Konfigurasi Environment (`.env`)
```bash
cp env .env
```
Lalu edit `.env`, sesuaikan nilai berikut:

```ini
# Database
database.default.hostname = localhost
database.default.database = jajanmranggen
database.default.username = root
database.default.password =

# (Jika pakai XAMPP/Laragon, biarkan password kosong)

# API Keys (sudah terisi, tinggal pakai)
fonnte.token = 1Wp1GiSBDBcf6VwSYPAL

# DOKU Sandbox (sudah terisi)
doku.clientId = MCH-0123-9876543210
doku.sharedKey = SK-abcdef1234567890abcdef1234567890

# Email (Mailtrap untuk testing)
email.SMTPHost = sandbox.smtp.mailtrap.io
email.SMTPUser = a0578f1c7c375b
email.SMTPPass = 42f65527070815
email.SMTPPort = 2525
email.SMTPTimeout = 300
email.SMTPCrypto = tls
email.fromEmail = a0578f1c7c375b@mailslurp.net
```

> **Catatan:** DOKU sandbox dan Fonnte token di atas sudah terisi dan siap pakai.

#### 5. Jalankan Migration & Seeder
```bash
php spark migrate
php spark db:seed "App\Database\Seeds\MainSeeder"
```

Perintah di atas akan:
- Membuat tabel-tabel: `users`, `categories`, `kuliner`, `favorites`, `payments`, `reviews`, dll.
- Mengisi data awal: **3 user**, **6 kategori**, **20 kuliner dummy**

#### 6. Jalankan Server
```bash
php spark serve
```
Akses di browser: `http://localhost:8080`

#### 7. Login Credentials untuk Demo

| Role | Email | Password |
|------|-------|----------|
| **Admin** | `admin@jajanmranggen.com` | `password` |
| **Kontributor 1** | `kontributor1@gmail.com` | `password` |
| **Kontributor 2** | `kontributor2@gmail.com` | `password` |

> Untuk demo Milestone 5 (geocoding) dan Milestone 7 (payment), login sebagai **contributor**.

#### 8. Menghentikan Server
Tekan `Ctrl + C` di terminal tempat `php spark serve` berjalan.

---

### Struktur Folder Penting

```
jajanmranggen/
├── app/
│   ├── Config/              # Konfigurasi (Routes, Database, Email, dll)
│   ├── Controllers/
│   │   ├── Api/             # Controller API (KulinerApi, PaymentNotification)
│   │   ├── Contributor/     # Controller halaman contributor
│   │   └── ...
│   ├── Database/
│   │   ├── Migrations/      # File migrasi database
│   │   └── Seeds/           # Seeder data dummy
│   ├── Filters/             # Filter (ApiKeyFilter)
│   ├── Libraries/           # Library (WhatsappNotification)
│   ├── Models/              # Model database
│   └── Views/               # Template view
├── public/                  # Entry point (index.php)
├── writable/                # Cache, logs, session
└── .env                     # Environment configuration
```

---

# ✅ MILESTONE 5 — Integrasi Webservice Client (Konsumsi API)
> **Kriteria:** API terintegrasi, ada error handling, data di-cache

## File yang Terlibat

| File | API yang Dikonsumsi |
|------|-------------------|
| `app/Libraries/WhatsappNotification.php` | API **Fonnte** (WhatsApp Gateway) |
| `app/Controllers/Contributor/KulinerContributor.php` → `geocode()` | API **Nominatim** (OpenStreetMap Geocoding) |
| `app/Controllers/Contributor/Payment.php` → `checkout()` | API **DOKU** (Payment Gateway) |

---

## 🎬 Demo ke Dosen

### Demo A — Geocoding + Cache (paling mudah, live di browser)

**Langkah:**
1. Login sebagai **contributor**
2. Buka `http://localhost:8080/contributor/kuliner/create`
3. Isi kolom **Alamat Lengkap** → contoh: `Jl. Malioboro, Yogyakarta`
4. Klik tombol **"📍 Cari Koordinat"**
5. Peta OpenStreetMap langsung bergerak ke lokasi → **bukti API Nominatim dipanggil**
6. Buka **DevTools (F12) → Network tab** → tunjukkan request ke `/contributor/kuliner/geocode?q=...` dan response JSON `{lat, lng, display_name}`
7. Klik "Cari Koordinat" lagi dengan alamat yang **sama** → request ke-2 lebih cepat karena dari **cache** (tidak hit API lagi)

**Yang dikatakan ke dosen:**
> *"Di sini aplikasi memanggil API Nominatim (OpenStreetMap) untuk mengkonversi alamat teks menjadi koordinat. Hasilnya di-cache selama 24 jam menggunakan CodeIgniter Cache Service, sehingga alamat yang sama tidak perlu request ke API eksternal berulang kali."*

---

### Demo B — Notifikasi WhatsApp (via alur payment)
*(Ditunjukkan bersamaan dengan Milestone 7)*

---

## 💻 Penjelasan Kode

### `app/Libraries/WhatsappNotification.php`
```
[1] $token = env('fonnte.token')
    → Token API dari .env, tidak hardcode (best practice)

[2] $client = \Config\Services::curlrequest()
    → HTTP Client bawaan CodeIgniter untuk konsumsi API

[3] $client->post('https://api.fonnte.com/send', [...])
    → POST ke endpoint API Fonnte dengan header Authorization

[4] $result = json_decode($response->getBody(), true)
    → Parse JSON response dari API

[5] catch (\Exception $e) { log_message('error', ...) }
    → ERROR HANDLING: jika API gagal, catat ke log, return false
```

### `app/Controllers/Contributor/KulinerContributor.php` → `geocode()`
```
[1] if (!$address) → return error "Alamat kosong"
    → Validasi input sebelum hit API

[2] $cacheKey = 'geocode_' . md5($address)
    → Key unik per alamat menggunakan hash MD5

[3] if ($cached = $cache->get($cacheKey)) → return $cached
    → CACHE-FIRST: cek cache dulu, kalau ada langsung return

[4] $client->get('https://nominatim.openstreetmap.org/search', [...])
    → GET ke API Nominatim dengan query params dan User-Agent header

[5] if (empty($result)) → return error "Koordinat tidak ditemukan"
    → ERROR HANDLING: API berhasil dipanggil tapi tidak ada hasil

[6] $cache->save($cacheKey, $data, 86400)
    → Simpan ke cache 86400 detik = 24 jam
```

---
---

# ✅ MILESTONE 6 — Webservice Server (Expose API Endpoint)
> **Kriteria:** Ada endpoint API yang bisa diakses pihak luar, ada autentikasi/proteksi

## File yang Terlibat

| File | Fungsi |
|------|--------|
| `app/Controllers/Api/KulinerApi.php` | Controller endpoint `GET /api/kuliner` |
| `app/Filters/ApiKeyFilter.php` | Filter proteksi API menggunakan `X-API-KEY` header |
| `app/Config/Routes.php` baris 76-78 | Mendaftarkan route API dengan filter `apikey` |

---

## 🎬 Demo ke Dosen

### Demo — Hit API endpoint dari Postman / browser

**Langkah dengan Postman:**

1. Buka **Postman** (atau aplikasi REST client apapun)
2. Buat request baru: **GET**
3. URL: `http://localhost:8080/api/kuliner?lat=-6.9917&lng=110.4897&radius=5`
4. Di tab **Headers**, tambahkan:
   ```
   Key:   X-API-KEY
   Value: JAJANMRANGGEN_SECRET_KEY_2024
   ```
5. Klik **Send** → tampil JSON response berisi daftar kuliner terdekat

**Tunjukkan error handling — tanpa API key:**
1. Hapus header `X-API-KEY`
2. Send → response `401 Unauthorized` dengan pesan `"API Key tidak valid."`

**Tunjukkan error handling — parameter salah:**
1. Hapus parameter `lat` dan `lng` dari URL
2. Send → response `400 Bad Request` dengan pesan `"Parameter lat dan lng wajib diisi."`

**Tunjukkan filter kategori:**
```
GET /api/kuliner?lat=-6.9917&lng=110.4897&radius=3&category=bakso-mie
```

**Yang dikatakan ke dosen:**
> *"Aplikasi kami juga meng-expose API endpoint sendiri di `GET /api/kuliner` yang bisa digunakan oleh aplikasi lain, misalnya aplikasi mobile. API ini dilindungi dengan API Key melalui header `X-API-KEY`. Jika key tidak valid, server menolak dengan 401. Endpoint ini mengembalikan daftar kuliner terdekat berdasarkan koordinat dengan filter radius dan kategori."*

---

## 💻 Penjelasan Kode

### `app/Filters/ApiKeyFilter.php`
```
[1] $apiKey = $request->getHeaderLine('X-API-KEY')
    → Baca API Key dari header request

[2] $validKey = env('api.secretKey', 'JAJANMRANGGEN_SECRET_KEY_2024')
    → Key valid diambil dari .env (bisa diganti tanpa ubah kode)

[3] if ($apiKey !== $validKey) → return 401
    → PROTEKSI: jika key tidak cocok, tolak request
```

### `app/Controllers/Api/KulinerApi.php`
```
[1] Validasi parameter wajib (lat, lng) → 400 jika tidak ada
[2] Validasi tipe data (harus numeric) → 400 jika bukan angka
[3] Validasi radius (0 < radius <= 50) → 400 jika di luar range
[4] $model->getNearby(lat, lng, radius, category)
    → Query DB dengan formula Haversine untuk hitung jarak
[5] array_map() → Format data response (hilangkan field sensitif)
[6] Return JSON: { status, total, radius, center, data[] }
```

### Route yang didaftarkan (`Routes.php`)
```php
// API route dengan filter apikey → semua request wajib bawa X-API-KEY
$routes->group('api', ['filter' => 'apikey'], function ($routes) {
    $routes->get('kuliner', 'Api\KulinerApi::index');
});

// Webhook DOKU — tanpa API key (DOKU yang kirim, bukan user)
$routes->post('api/payment/notification', 'Api\PaymentNotification::handle');
```

### Contoh Response JSON yang Muncul
```json
{
  "status": "success",
  "total": 5,
  "radius": 5,
  "center": { "lat": -6.9917, "lng": 110.4897 },
  "data": [
    {
      "id": 1,
      "name": "Warung Bu Mira Mranggen",
      "slug": "warung-bu-mira-mranggen",
      "address": "Jl. Raya Mranggen No.12, Mranggen, Demak",
      "latitude": -6.9917,
      "longitude": 110.4897,
      "category_name": "Makanan Berat",
      "average_rating": 4.5,
      "is_promoted": false,
      "distance_km": 0.12
    }
  ]
}
```

---
---

# ✅ MILESTONE 7 — Payment Gateway & Notifikasi
> **Kriteria:** Terintegrasi payment gateway, ada notifikasi WA/email setelah transaksi

## File yang Terlibat

| File | Fungsi |
|------|--------|
| `app/Controllers/Contributor/Payment.php` → `checkout()` | Buat payment request ke API DOKU, redirect ke halaman bayar |
| `app/Controllers/Api/PaymentNotification.php` → `handle()` | Terima webhook dari DOKU, proses status, kirim WA & email |
| `app/Libraries/WhatsappNotification.php` | Library kirim WA via Fonnte |

---

## 🎬 Demo ke Dosen

### Persiapan — Ngrok (Webhook Tunnel)

> DOKU sandbox perlu mengirim webhook ke server kita. Karena `localhost` tidak bisa diakses dari luar, kita pakai **Ngrok** untuk membuat tunnel publik.

```bash
# Buka terminal BARU (jangan matikan php spark serve)
ngrok http 8080
```
Setelah ngrok jalan, copy URL forwarding (contoh: `https://abc123.ngrok.io`), lalu:
1. Buka `.env` → cari `doku.webhookUrl`
2. Isi dengan URL ngrok + `/api/payment/notification`
   ```
   doku.webhookUrl = https://abc123.ngrok.io/api/payment/notification
   ```
3. Simpan `.env`, **restart** `php spark serve`

> **Alternatif:** Jika ngrok bermasalah, bisa install ulang ngrok di https://ngrok.com/download. Pastikan `ngrok http 8080` jalan dan tampilkan status "Online".

---

### Demo — Alur Lengkap Sponsor Kuliner & Notifikasi

**Langkah:**
1. Login sebagai **contributor**
2. Buka **Daftar Kuliner Saya** → klik tombol **"Sponsori"** pada salah satu kuliner
3. Halaman sponsor tampil → klik **"Bayar Sekarang Rp 50.000"**
4. Aplikasi membuat invoice di DB, lalu **memanggil API DOKU sandbox**
5. Browser **redirect otomatis** ke halaman pembayaran DOKU
6. Selesaikan pembayaran di halaman DOKU (gunakan akun sandbox)
7. DOKU mengirim **webhook (HTTP callback)** ke `POST /api/payment/notification`
8. Aplikasi memverifikasi **HMAC signature**, update status kuliner → `is_promoted = 1`
9. **Notifikasi WhatsApp** dikirim ke nomor HP contributor via Fonnte
10. **Email** dikirim ke email contributor via SMTP

**Bukti tambahan untuk dosen:**
- Tunjukkan tabel `payments` di database → status berubah dari `pending` ke `paid`
- Tunjukkan tabel `kuliners` → kolom `is_promoted = 1`, `promoted_until = tanggal +7 hari`
- Tunjukkan file log `writable/logs/log-YYYY-MM-DD.php` → tidak ada error
- Tunjukkan WA yang masuk di HP (jika token Fonnte aktif)

**Yang dikatakan ke dosen:**
> *"Kami mengintegrasikan DOKU sebagai payment gateway untuk fitur sponsor kuliner. Saat user memilih bayar, aplikasi memanggil API DOKU dengan HMAC-SHA256 signature untuk keamanan, lalu redirect ke halaman bayar DOKU. Setelah pembayaran sukses, DOKU otomatis mengirim webhook ke endpoint kita. Aplikasi memverifikasi signature webhook tersebut, mengupdate status kuliner menjadi promoted, lalu mengirim notifikasi WhatsApp via API Fonnte dan email via SMTP."*

---

## 💻 Penjelasan Kode

### `app/Controllers/Contributor/Payment.php` → `checkout()`
```
[1] $invoice = 'INV-' . date('YmdHis') . '-' . user_id
    → Generate nomor invoice unik berdasarkan timestamp + user ID

[2] $paymentModel->insert([...status => 'pending'])
    → Simpan record payment ke DB dengan status pending dulu

[3] $url = $isProduction ? 'api.doku.com' : 'api-sandbox.doku.com'
    → Pilih environment: sandbox untuk testing, production untuk live

[4] $digest = base64_encode(hash('sha256', $jsonPayload, true))
    → SHA256 hash dari body request untuk signing

[5] $rawSignature = "Client-Id:...\nRequest-Id:...\n..."
    → Bangun string yang akan di-sign (format sesuai docs DOKU)

[6] $finalSignature = "HMACSHA256=" . base64_encode(hash_hmac(...))
    → Tanda tangan digital request menggunakan SharedKey

[7] curl_init + curl_setopt + curl_exec
    → Kirim request ke API DOKU dengan cURL

[8] if ($httpCode == 200 && isset($result['response']['payment']['url']))
    → Cek response: jika sukses, redirect ke payment URL DOKU
    → ERROR HANDLING: jika gagal, log error, tampil flash message
```

### `app/Controllers/Api/PaymentNotification.php` → `handle()`
```
[1] Baca headers: Client-Id, Request-Id, Request-Timestamp, Signature
    → Header yang dikirim DOKU bersama webhook

[2] $calculatedSignature = "HMACSHA256=" . hash_hmac(...)
    → Hitung ulang signature dari sisi kita

[3] if (!hash_equals($calculatedSignature, $signatureHeader)) → 401
    → VERIFIKASI KEAMANAN: tolak jika signature tidak cocok
    → Mencegah webhook palsu dari pihak luar

[4] if ($transStatus === 'SUCCESS')
    → Update payment status = 'paid'
    → Update kuliner: is_promoted = 1, promoted_until = +7 hari

[5] $wa->send($user['phone'], $msg)
    → Kirim notifikasi WhatsApp via WhatsappNotification library

[6] $emailService->send()
    → Kirim email konfirmasi via CodeIgniter Email Service

[7] catch (\Exception $e) { log_message + return 500 }
    → ERROR HANDLING global: catat semua error ke log
```

---

## 📋 Checklist Sebelum Demo

Centang semua item sebelum demo ke dosen:

| Item | Milestone 5 | Milestone 6 | Milestone 7 |
|------|:-----------:|:-----------:|:-----------:|
| Server `php spark serve` jalan | ✅ | ✅ | ✅ |
| Database sudah `migrate` + `seed` | ✅ | ✅ | ✅ |
| Koneksi internet aktif | ✅ (Nominatim) | ❌ (tidak perlu) | ✅ (DOKU, Fonnte) |
| Token Fonnte valid di `.env` | ✅ | ❌ | ✅ |
| DOKU clientId & sharedKey di `.env` | ❌ | ❌ | ✅ |
| Postman terinstall | ❌ | ✅ | ❌ |
| Ngrok terinstall & tunneling jalan | ❌ | ❌ | ✅ |
| Akun sandbox DOKU (bisa test payment) | ❌ | ❌ | ✅ |
| `.env` webhookUrl diisi URL ngrok | ❌ | ❌ | ✅ |

---

## 🗣️ Script 1 Kalimat per Milestone

**Milestone 5:**
> *"Aplikasi mengkonsumsi 3 API eksternal — Nominatim untuk geocoding alamat ke koordinat (dengan caching 24 jam), Fonnte untuk notifikasi WhatsApp, dan DOKU untuk payment — semuanya dilengkapi error handling via try-catch dan logging."*

**Milestone 6:**
> *"Aplikasi menyediakan REST API endpoint `GET /api/kuliner` yang bisa diakses aplikasi lain dengan autentikasi API Key via header `X-API-KEY`, mengembalikan daftar kuliner terdekat dalam format JSON dengan validasi parameter dan error response yang terstruktur."*

**Milestone 7:**
> *"Fitur sponsor kuliner terintegrasi penuh dengan DOKU payment gateway — dari pembuatan invoice, redirect ke halaman bayar, verifikasi webhook HMAC-SHA256, hingga pengiriman notifikasi WhatsApp dan email otomatis setelah pembayaran berhasil."*
