# JajanMranggen - Kuliner Review Platform 🍔🗺️

JajanMranggen adalah platform berbasis web untuk menemukan, menambahkan, dan mengulas tempat makan atau jajanan di sekitar. Sistem ini dibangun dengan arsitektur MVC menggunakan **CodeIgniter 4**, dilengkapi integrasi geocoding otomatis (**OpenStreetMap Nominatim API**), peta interaktif (**Leaflet.js**), dan pembayaran sponsor (**DOKU Payment Gateway**).

---

## 🌟 Fitur Utama

- **🌍 Peta Interaktif & Geocoding**: Otomatis mendeteksi koordinat (Latitude/Longitude) dari teks alamat, ditampilkan dengan Leaflet.js.
- **🔐 Multi-Role Auth (Admin & Kontributor)**: Pembatasan akses menggunakan filter CI4.
- **⭐ Sistem Review & Rating Dinamis**: Auto-calculate nilai rata-rata dari seluruh ulasan pelanggan.
- **❤️ Favorit (Bookmark)**: Simpan tempat kuliner favorit menggunakan sistem AJAX.
- **🖼️ Auto-Resize Upload**: Otomatis memperkecil resolusi foto unggahan menggunakan CI4 Image Manipulation.
- **🚀 API Endpoint**: Expose data spasial kuliner melalui `GET /api/kuliner` (dilindungi API Key).
- **💳 Integrasi DOKU Payment Gateway**: Fitur sponsor bagi kontributor untuk mempromosikan tempat kulinernya.

---

## 💻 Tech Stack
- **Framework:** CodeIgniter 4
- **Database:** MySQL
- **Frontend:** Bootstrap 5, Custom Vanilla CSS, Bootstrap Icons
- **Maps:** Leaflet.js
- **Geocoding API:** OpenStreetMap Nominatim API
- **Payment Gateway:** DOKU (Sandbox)

---

## ⚙️ Persyaratan Sistem (Requirements)
- PHP >= 8.1
- Composer
- Ekstensi PHP: `intl`, `mbstring`, `json`, `mysqlnd`, `curl`, `gd` (untuk auto-resize image)
- MySQL / MariaDB (via XAMPP/Laragon)

---

## 🚀 Panduan Instalasi (Langkah demi Langkah)

1. **Clone Repository**
   ```bash
   git clone https://github.com/username/jajanmranggen.git
   cd jajanmranggen
   ```

2. **Install Dependensi Composer**
   ```bash
   composer install
   ```

3. **Konfigurasi Environment (`.env`)**
   Duplikat file `env` menjadi `.env` lalu buka file tersebut dan ubah konfigurasi berikut:
   ```env
   CI_ENVIRONMENT = development

   app.baseURL = 'http://localhost:8080/'

   database.default.hostname = localhost
   database.default.database = jajanmranggen_db
   database.default.username = root
   database.default.password = 
   database.default.DBDriver = MySQLi

   # Konfigurasi DOKU Payment Gateway
   doku.clientId = 'BRN-0254-XXXXXXXXXXXXX'
   doku.sharedKey = 'SK-XXXXXXXXXXXXXXXXXXXX'
   doku.isProduction = false
   ```

4. **Buat Database**
   Buka phpMyAdmin / GUI Database Anda, buat database baru dengan nama `jajanmranggen_db`.

5. **Jalankan Migration & Seeder (Otomatis)**
   Perintah ini akan membuat semua struktur tabel dan mengisi puluhan data dummy kuliner otomatis.
   ```bash
   php spark migrate:refresh --seed
   ```

6. **Jalankan Local Development Server**
   ```bash
   php spark serve
   ```
   Akses aplikasi melalui browser di: **http://localhost:8080**

---

## 🔑 Kredensial Akun Demo

Seeder otomatis membuatkan beberapa akun untuk testing:

| Role | Username | Email | Password |
| :--- | :--- | :--- | :--- |
| **Admin** | admin | admin@jajanmranggen.test | `password123` |
| **Kontributor** | bobby | bobby@example.com | `password123` |
| **Kontributor** | andi | andi@example.com | `password123` |
| **Kontributor** | siti | siti@example.com | `password123` |

---

## 📊 Struktur Database (ERD Singkat)

Proyek ini telah menempuh normalisasi 3NF dengan relasi antar tabel sebagai berikut:
1. `users` (id, username, email, password, role)
2. `categories` (id, name, slug, description)
3. `tags` (id, name, slug)
4. `kuliner` (id, name, slug, address, latitude, longitude, category_id, contributor_id, average_rating, status, dll.)
5. `kuliner_tags` (kuliner_id, tag_id) -> Tabel pivot M:M
6. `reviews` (id, kuliner_id, user_id, rating, comment)
7. `favorites` (id, user_id, kuliner_id)
8. `payments` (id, user_id, kuliner_id, invoice_number, amount, status, snap_token)

---

## 📡 API Endpoint (Webservice)
Tersedia API bagi developer pihak ketiga (misal: display info kampus / mobile app).

### Autentikasi
Semua request wajib menyertakan API Key melalui header:

| Header | Nilai |
| :--- | :--- |
| `X-API-KEY` | `JAJANMRANGGEN_SECRET_KEY_2024` |

### Endpoint: Cari Kuliner Terdekat

```
GET /api/kuliner
```

**Query Parameters:**

| Parameter | Tipe | Required | Default | Deskripsi |
| :--- | :--- | :---: | :---: | :--- |
| `lat` | float | ✅ | - | Latitude titik pusat pencarian |
| `lng` | float | ✅ | - | Longitude titik pusat pencarian |
| `radius` | float | ❌ | `5` | Radius pencarian dalam km (max 50) |
| `category` | string | ❌ | - | Slug kategori untuk filter (contoh: `bakso-mie`) |

**Contoh Request:**
```http
GET /api/kuliner?lat=-6.983&lng=110.409&radius=3&category=bakso-mie
X-API-KEY: JAJANMRANGGEN_SECRET_KEY_2024
```

**Contoh Response (200 OK):**
```json
{
    "status": "success",
    "total": 2,
    "radius": 3,
    "center": {
        "lat": -6.983,
        "lng": 110.409
    },
    "data": [
        {
            "id": 5,
            "name": "Bakso Pak Edi",
            "slug": "bakso-pak-edi",
            "description": "Bakso sapi ukuran besar dengan kuah kaldu gurih...",
            "address": "Jl. Veteran No.15, Semarang",
            "latitude": -6.985,
            "longitude": 110.412,
            "category_name": "Bakso & Mie",
            "average_rating": 4.5,
            "is_promoted": true,
            "distance_km": 0.42
        }
    ]
}
```

**Error Response (401 — API Key tidak valid):**
```json
{
    "status": "error",
    "message": "API Key tidak valid."
}
```

**Error Response (400 — Parameter tidak lengkap):**
```json
{
    "status": "error",
    "message": "Parameter lat dan lng wajib diisi."
}
```

### Catatan
- Perhitungan jarak menggunakan **rumus Haversine** untuk akurasi spasial.
- Data yang dikembalikan hanya kuliner dengan status **approved**.

---

## 💳 Payment Gateway (DOKU)

Kontributor dapat mensponsori tempat kuliner untuk dipromosikan selama 7 hari.

### Alur Pembayaran
1. Kontributor memilih kuliner untuk disponsori (Rp50.000 / 7 hari)
2. Sistem generate invoice & redirect ke halaman pembayaran **DOKU** (sandbox)
3. Pelanggan membayar melalui channel yang tersedia di DOKU
4. DOKU mengirim notifikasi ke endpoint `/api/payment/notification`
5. Sistem verifikasi **HMAC Signature**, update status menjadi `paid`
6. Kuliner otomatis di-set `is_promoted = 1` selama 7 hari
7. Notifikasi dikirim ke kontributor melalui **WhatsApp** dan **Email**

### Teknologi Notifikasi
| Channel | Tools |
| :--- | :--- |
| **WhatsApp** | Fonnte API (via `WhatsappNotification` library) |
| **Email** | SMTP Mailtrap (sandbox) |

---

> Dibuat untuk memenuhi kualifikasi Tugas Akhir Pemrograman Web Lanjut (CodeIgniter 4) - Universitas Dian Nuswantoro.
