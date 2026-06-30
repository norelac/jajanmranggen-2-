Ringkasan Milestone di dalam File .md:
Milestone 1: Inisialisasi framework, konfigurasi .env, dan perancangan ERD Database hingga 3NF.

Milestone 2: Implementasi penuh Database Migration & Seeder (mengisi 20+ data kuliner dummy di sekitar kampus).

Milestone 3: Pembuatan registrasi, login enkripsi, dan pengamanan rute menggunakan CodeIgniter 4 Filters berdasarkan role (Admin vs Kontributor).

Milestone 4: Dashboard admin dan CRUD master data Kategori & Tag jajanan.

Milestone 5: Pembuatan form kontribusi tempat baru dengan fitur upload multi-foto dan auto-resize otomatis (maksimal 800px).

Milestone 6: Integrasi AJAX dengan OpenStreetMap Nominatim API untuk Geocoding otomatis (mengubah teks alamat menjadi koordinat Lat/Long).

Milestone 7: Integrasi peta interaktif menggunakan Leaflet.js untuk menampilkan marker lokasi, serta implementasi filter pencarian tingkat lanjut.

Milestone 8: Sistem ulasan (review), rating dinamis (auto-calculate average rating), dan fitur simpan tempat favorit.

Milestone 9: Fitur moderasi konten oleh admin dan pembuatan Webservice Server (GET /api/kuliner) untuk membagikan data spasial kuliner terdekat dalam bentuk JSON.

Milestone 10: Pemolesan UI/UX responsif, pembersihan kode (refactoring MVC), pengelolaan repository GitHub (minimal 10+ commit bermakna), dan penulisan dokumen README.md cara instalasi.

File ini dirancang terstruktur agar Anda bisa mengidentifikasi bagian mana saja yang masih kurang dari website Anda saat ini dan menyelesaikannya satu per satu demi mengejar nilai maksimal (A) sesuai dengan rubrik penilaian kampus. Selamat mencoba!




Percakapan Universitas Dian Nuswantoro Anda tidak digunakan untuk meningkatkan kualitas model kami. Gemini adalah AI dan dapat melakukan kesalahan. Privasi Anda & GeminiTerbuka di jendela baru

# Panduan Spesifikasi & Milestone Project: Lokasi Kuliner & Review Jajanan
**Mata Kuliah: Pemrograman Web Lanjut (CodeIgniter 4)**
**Referensi: Universitas Dian Nuswantoro**

---

## 1. Deskripsi Project

Platform berbasis web ini dirancang untuk menemukan, menambahkan, dan mengulas tempat makan atau jajanan di sekitar kampus atau area tertentu. Sistem dilengkapi dengan fitur geocoding otomatis untuk mendeteksi koordinat lokasi, filter pencarian berdasarkan kategori dan tag spesifik, peta interaktif, serta sistem rating dinamis. Data lokasi diintegrasikan secara langsung menggunakan OpenStreetMap Nominatim API secara gratis dan unlimited.

### Tech Stack & API Eksternal
*   **Framework:** CodeIgniter 4 (MVC Architecture)
*   **Database:** MySQL (Dikelola penuh via Migration & Seeder)
*   **Geocoding API:** OpenStreetMap Nominatim API (`https://nominatim.openstreetmap.org`)
*   **Map Display:** Leaflet.js (Open Source Map Viewer) + Tile OpenStreetMap (OSM)
*   **Image Handling:** CodeIgniter 4 Image Manipulation Library (Auto-resize max 800px & creation of thumbnails)
*   **Authentication & Protection:** CodeIgniter 4 Filters (Route protection based on roles)

---

## 2. Struktur Hak Akses (Aktor & Role)

### A. Pengunjung (Tanpa Login / Guest)
*   Menjelajah (*browse*) daftar kuliner dengan filter (kategori, tag, rating, dan jarak).
*   Melihat detail tempat kuliner, galeri foto, dan visualisasi peta lokasi (Leaflet.js).
*   Membaca review dan rating dari kontributor lain.
*   Melakukan pencarian tempat kuliner berdasarkan nama tempat atau alamat.

### B. Kontributor (User Terautentikasi)
*   Semua kemampuan Pengunjung.
*   Mengajukan (*submit*) tempat makan baru dengan menginput nama, alamat, deskripsi, kategori, tag, dan foto.
*   Menggunakan fitur *Geocoding Otomatis*: Sistem otomatis mengambil koordinat latitude dan longitude dari Nominatim API berdasarkan string alamat yang diketik.
*   Menulis ulasan (*review*) baru dan memberikan rating skala 1-5 bintang.
*   Mengedit ulasan milik sendiri dalam batas waktu maksimal 24 jam setelah pembuatan.
*   Mengunggah foto tempat makan (maksimal 3 foto per lokasi).
*   Menyimpan tempat kuliner favorit ke dalam daftar personal (*bookmark*).
*   Menandai tempat kuliner tertentu sebagai "Tutup Permanen" (status ini akan masuk antrean validasi Admin).

### C. Admin (Superuser)
*   Mengelola master data kategori (contoh: warteg, kafe, street food, minuman, dll) via CRUD.
*   Mengelola master data tag (contoh: halal, murah, AC, WiFi, parkir, dll) via CRUD.
*   Melakukan moderasi tempat kuliner (Approve / Reject pengajuan tempat baru dari Kontributor).
*   Melakukan moderasi review (Menghapus ulasan yang mengandung unsur tidak pantas atau kasar).
*   Mengelola penuh data kuliner secara langsung melalui fungsi CRUD Administrator.
*   **Dashboard Analytics:** Menampilkan total tempat kuliner, total review, jumlah user aktif, grafik rating tertinggi, dan tren tempat favorit.

---

## 3. Rencana Milestone Pengembangan (1 - 10)

Gunakan pembagian milestone berikut untuk melengkapi komponen website Anda yang masih kurang secara bertahap dan terstruktur:

### MILESTONE 1: Perencanaan, Inisialisasi Project, & Desain ERD Database
*   **Target:** Setup environment kerja, arsitektur database, dan konfigurasi awal.
*   **Tugas Detail:**
    1.  Inisialisasi project CodeIgniter 4 melalui Composer.
    2.  Konfigurasi file `.env` (database credentials, `CI_ENVIRONMENT = development`, `app.baseURL`).
    3.  Membuat rancangan ERD yang mencakup minimal 6 tabel utama: `users`, `kuliner`, `categories`, `tags`, `kuliner_tags` (pivot), `reviews`, dan `favorites`. Ensure normalisasi mencapai **3NF**.
    4.  Membuat database MySQL kosong sesuai konfigurasi.

### MILESTONE 2: Implementasi Database Migration & Seeder
*   **Target:** Seluruh struktur database dibuat secara programatis tanpa manipulasi manual di phpMyAdmin.
*   **Tugas Detail:**
    1.  Membuat file migrasi (`php spark make:migration`) untuk setiap tabel dengan tipe data yang presisi, foreign key constraint, dan indeks yang optimal.
    2.  Menjalankan perintah `php spark migrate` untuk menguji pembentukan tabel.
    3.  Membuat file Seeder (`php spark make:seeder`) untuk mengisi data awal, termasuk minimal: 1 akun admin, 3 akun kontributor, 5 kategori master, 10 tag master, serta **20+ data kuliner simulasi sekitar kampus**.
    4.  Memastikan fungsi `php spark migrate:refresh --seed` berjalan mulus tanpa error (rollback test).

### MILESTONE 3: Sistem Autentikasi, Registrasi, dan Pembatasan Hak Akses (Filters)
*   **Target:** Manajemen user yang aman dan pembatasan halaman berdasarkan role.
*   **Tugas Detail:**
    1.  Membangun fitur Registrasi Kontributor lengkap dengan validasi input (email unik, konfirmasi password).
    2.  Membangun fitur Login dengan enkripsi password menggunakan `password_hash()` dan `password_verify()`.
    3.  Membuat Custom Filter di `app/Filters/` (misalnya `AdminFilter` dan `AuthFilter`) untuk memproteksi routes.
    4.  Menerapkan proteksi ketat pada `app/Config/Routes.php`: halaman submit tempat & tulis review hanya bisa diakses user login; halaman dashboard admin hanya bisa diakses role admin.

### MILESTONE 4: Dashboard Admin & CRUD Master Data (Kategori & Tag)
*   **Target:** Backend fungsional untuk administrator dalam mengelola parameter sistem.
*   **Tugas Detail:**
    1.  Membuat layout dashboard Admin menggunakan template responsif (seperti AdminLTE atau Bootstrap clean layout).
    2.  Implementasi fungsi CRUD penuh untuk entitas `Categories` lengkap dengan form validation dan session *flash message* sebagai feedback sukses/gagal.
    3.  Implementasi fungsi CRUD penuh untuk entitas `Tags`.
    4.  Menampilkan widget statistik sederhana pada beranda dashboard (Total User, Total Kuliner, Total Ulasan).

### MILESTONE 5: Modul Kontributor - Form Submit Kuliner & Auto-Resize Upload Foto
*   **Target:** Fitur kontribusi tempat makan baru oleh user login yang disertai manajemen aset gambar yang efisien.
*   **Tugas Detail:**
    1.  Membuat form input data kuliner bagi kontributor (Nama, Kategori, Checkbox Tags, Alamat, Deskripsi).
    2.  Menyediakan field upload gambar yang mendukung input hingga **3 foto**.
    3.  Mengintegrasikan CodeIgniter 4 Image Manipulation Library pada controller saat proses upload terjadi.
    4.  Melakukan *auto-resize* gambar ke resolusi maksimal lebar 800px dan otomatis men-generate thumbnail berukuran kecil untuk efisiensi performa loading halaman gallery.

### MILESTONE 6: Integrasi OpenStreetMap Nominatim API (Geocoding Otomatis via AJAX)
*   **Target:** Mengubah string alamat tekstual menjadi data koordinat spasial secara otomatis.
*   **Tugas Detail:**
    1.  Menambahkan tombol "Cari Koordinat via Alamat" di samping atau di bawah field alamat pada Form Submit Kuliner.
    2.  Membuat script JavaScript (menggunakan `fetch` API atau jQuery AJAX) untuk menangkap teks alamat saat tombol diklik.
    3.  Mengirimkan request AJAX ke controller lokal CodeIgniter 4, yang kemudian meneruskan request (via `CURL` atau `HTTP Client` bawaan CI4) ke endpoint pihak ketiga: `https://nominatim.openstreetmap.org/search?q={ALAMAT}&format=json`.
    4.  Mengekstrak data koordinat dari response array JSON pada indeks pertama: `[0].lat` dan `[0].lon`.
    5.  Memasukkan secara otomatis (*auto-fill*) nilai koordinat tersebut ke dalam field input hidden/readonly `latitude` dan `longitude` di dalam form, lalu menyimpannya ke database saat form di-submit.

### MILESTONE 7: Integrasi Peta Interaktif Leaflet.js & Fitur Pencarian / Filter Publik
*   **Target:** Menyajikan antarmuka visual berbasis peta untuk pengunjung dan mempermudah pencarian lokasi kuliner.
*   **Tugas Detail:**
    1.  Membuat halaman detail kuliner yang memuat library CSS & JS dari Leaflet.js secara lokal atau CDN.
    2.  Membuat container peta `<div id="map"></div>` dan menulis skrip inisialisasi Leaflet dengan koordinat `lat` dan `lng` yang diambil dari database tempat tersebut.
    3.  Menampilkan marker interaktif tepat di atas titik lokasi kuliner di atas peta OpenStreetMap Open-Source Tiles.
    4.  Membangun query pencarian tingkat lanjut pada model publik untuk menyaring kuliner berdasarkan kata kunci nama, filter kategori, atau multi-selection tag.
    5.  Menerapkan pagination teratur (`$model->paginate()`) pada list hasil pencarian untuk menghindari overload data.

### MILESTONE 8: Sistem Review, Rating Dinamis, & Fitur Favorit
*   **Target:** Mengaktifkan interaksi sosial user melalui ulasan berbobot dan tracking reputasi tempat kuliner.
*   **Tugas Detail:**
    1.  Membuat form ulasan di halaman detail kuliner yang hanya muncul jika user sudah login. Form berisi input teks review dan input rating (angka/bintang 1 sampai 5).
    2.  Mengimplementasikan logic *trigger database* atau perhitungan langsung di level Model/Service untuk mengalkulasi ulang nilai rata-rata rating (*average rating*) dari suatu tempat makan setiap kali ulasan baru berhasil disimpan.
    3.  Menambahkan validasi di Controller agar user hanya bisa mengedit teks review miliknya sendiri dalam jangka waktu maksimal 24 jam sejak ulasan dikirimkan.
    4.  Membangun modul AJAX sederhana untuk fitur "Simpan ke Favorit" (Bookmark) bagi kontributor beserta halaman daftarnya di profil user.

### MILESTONE 9: Moderasi Admin & Pembuatan Webservice Server (API Endpoint)
*   **Target:** Fitur kontrol kualitas data oleh admin serta membuka akses data bagi integrasi eksternal (display kampus).
*   **Tugas Detail:**
    1.  Membangun halaman daftar moderasi tempat kuliner di sisi admin untuk mengubah status pengajuan dari "Pending" menjadi "Approved" atau "Rejected".
    2.  Membangun fitur bagi admin untuk menghapus ulasan yang dinilai melanggar kebijakan.
    3.  Membuat API Controller khusus yang mengekspos data kuliner dalam format JSON terstruktur dengan standarisasi RESTful.
    4.  Menyediakan endpoint publik: `GET /api/kuliner?lat={x}&lng={y}&radius={km}` yang menerima parameter koordinat sentral dan radius jangkauan untuk memfilter daftar kuliner terdekat (Berguna untuk integrasi poster/display info kampus).

### MILESTONE 10: Finetuning UI/UX, Refactoring Kode, Pengelolaan GitHub & Dokumentasi
*   **Target:** Memoles visual, memastikan kebersihan kode, mengamankan repository, dan mempersiapkan demonstrasi.
*   **Tugas Detail:**
    1.  Melakukan audit tampilan untuk menjamin layout konsisten di seluruh halaman, responsif di perangkat mobile, dan navigasi menu jelas bagi user.
    2.  Refactoring kode: Pastikan business logic berada di dalam Model atau Service layer, sedangkan Controller hanya mengatur alur request/response. Tambahkan komentar penjelasan pada fungsi yang kompleks.
    3.  Memastikan file `.env` sudah masuk ke dalam `.gitignore`, dan membuat file `.env.example` sebagai panduan setup bagi pengembang lain.
    4.  Melakukan push final ke GitHub dengan riwayat commit yang terstruktur dan bermakna (minimal terdapat 10+ commit teratur).
    5.  Menulis file `README.md` secara komprehensif yang berisi: langkah instalasi detail, konfigurasi environment, kredensial akun demo (admin & kontributor), diagram ERD, serta screenshot visual fitur utama website.

---

## 4. Strategi Memaksimalkan Penilaian (Rubrik Panduan)

Untuk memastikan project Anda mendapatkan nilai maksimal (Skor Sempurna / Sangat Baik), pastikan kriteria berikut terpenuhi sebelum pengumpulan:

1.  **Database (Bobot 10%):** Jangan membuat tabel manual di database manager. Gunakan rancangan relasi yang kokoh, capai normalisasi tingkat 3NF, dan pastikan data dummy dari seeder terlihat realistis mencerminkan lokasi kuliner nyata di sekitar area target.
2.  **Migration & Seeder (Bobot 5%):** Pastikan skema database dapat dibangun ulang kapan saja hanya dengan menjalankan satu baris perintah: `php spark migrate:refresh --seed`. Fitur rollback wajib berfungsi tanpa kegagalan constraint.
3.  **Autentikasi & Proteksi (Bobot 10%):** Terapkan pembatasan hak akses berlapis memanfaatkan CI4 Filters. Lakukan pengujian manual: pastikan jika guest mencoba mengetik langsung URL admin di browser address bar, sistem otomatis menolaknya dan mengarahkan kembali (*redirect*) ke halaman login dengan pesan peringatan yang aman.
4.  **Fitur CRUD (Bobot 15%):** Semua form wajib dilengkapi dengan library Form Validation bawaan CI4. Tampilkan pesan error yang informatif di bawah masing-masing field jika input tidak valid, serta gunakan session flash message (misal: Bootstrap Alert) untuk mengonfirmasi operasi sukses.
5.  **Konsumsi API Eksternal (Bobot 15%):** Integrasi OpenStreetMap Nominatim API wajib memiliki *error handling* yang kuat (misal: jika koneksi internet terputus atau API down, berikan fallback alert ke user dan pastikan aplikasi tidak mengalami crash/blank page).
6.  **Expose API Endpoint (Bobot 15%):** Pastikan struktur response JSON dari endpoint `/api/kuliner` tersusun dengan rapi, memiliki HTTP status code yang tepat (200 OK, 400 Bad Request, dll), serta dilengkapi dokumentasi skema response pada dokumentasi utama.
7.  **Kualitas Kode (Bobot 10%):** Patuhi arsitektur MVC secara disiplin. Jangan menulis query SQL mentah (*raw query*) di dalam file View atau Controller. Manfaatkan Query Builder CI4 di dalam model untuk menjaga keamanan dari celah SQL Injection.
8.  **GitHub & Dokumen (Bobot 5%):** Kelola repository Anda secara bertahap selama masa pengerjaan, hindari melakukan satu single commit besar di akhir waktu pengumpulan karena hal tersebut mereduksi poin transparansi riwayat pengerjaan.

---
*Selamat menyempurnakan project website kuliner Anda! Ikuti panduan milestone ini langkah demi langkah untuk hasil yang optimal.*
Panduan_Spesifikasi_Project_Kuliner_Review.md
Menampilkan Panduan_Spesifikasi_Project_Kuliner_Review.md.