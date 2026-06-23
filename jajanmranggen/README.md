# JajanMranggen - Kuliner Review Platform 🍔🗺️

JajanMranggen adalah platform berbasis web untuk menemukan, menambahkan, dan mengulas tempat makan atau jajanan di sekitar. Sistem ini dibangun dengan arsitektur MVC menggunakan **CodeIgniter 4**, dilengkapi integrasi geocoding otomatis (**OpenStreetMap Nominatim API**), peta interaktif (**Leaflet.js**), dan pembayaran sponsor (**Midtrans**).

---

## 🌟 Fitur Utama

- **🌍 Peta Interaktif & Geocoding**: Otomatis mendeteksi koordinat (Latitude/Longitude) dari teks alamat, ditampilkan dengan Leaflet.js.
- **🔐 Multi-Role Auth (Admin & Kontributor)**: Pembatasan akses menggunakan filter CI4.
- **⭐ Sistem Review & Rating Dinamis**: Auto-calculate nilai rata-rata dari seluruh ulasan pelanggan.
- **❤️ Favorit (Bookmark)**: Simpan tempat kuliner favorit menggunakan sistem AJAX.
- **🖼️ Auto-Resize Upload**: Otomatis memperkecil resolusi foto unggahan menggunakan CI4 Image Manipulation.
- **🚀 API Endpoint**: Expose data spasial kuliner melalui `GET /api/kuliner` (dilindungi API Key).
- **💳 Integrasi Midtrans**: Fitur bagi kontributor untuk mensponsori tempat kulinernya (promosi).

---

## 💻 Tech Stack
- **Framework:** CodeIgniter 4
- **Database:** MySQL
- **Frontend:** Bootstrap 5, Custom Vanilla CSS, Bootstrap Icons
- **Maps:** Leaflet.js
- **Geocoding API:** OpenStreetMap Nominatim API
- **Payment Gateway:** Midtrans (Sandbox)

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

   # Konfigurasi Midtrans
   midtrans.serverKey = 'SB-Mid-server-XXXXX'
   midtrans.clientKey = 'SB-Mid-client-XXXXX'
   midtrans.isProduction = false
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
Tersedia API bagi developer pihak ketiga (misal: display info kampus/mobile app).

**Endpoint:** `GET /api/kuliner`  
**Headers:** `X-API-KEY : JAJANMRANGGEN_SECRET_KEY_2024`  
**Query Params:**
- `lat` (required) : Garis lintang titik pencarian.
- `lng` (required) : Garis bujur titik pencarian.
- `radius` (optional) : Radius pencarian dalam satuan KM (default 5).

---

> Dibuat untuk memenuhi kualifikasi Tugas Akhir Pemrograman Web Lanjut (CodeIgniter 4) - Universitas Dian Nuswantoro.
