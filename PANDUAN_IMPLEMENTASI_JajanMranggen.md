# 🍜 Panduan Implementasi Lengkap — JajanMranggen
> CodeIgniter 4 · Docker · Leaflet.js · Midtrans · Fonnte WA
> 
> **Tim**: Mahasiswa A (Back-End Lead) · Mahasiswa B (Front-End Lead)

---

## ⚡ Ringkasan Alur Kerja

```
[Setup Docker + CI4] → [Database & Migration] → [Auth & Role] →
[CRUD Kuliner] → [Map & Geocoding] → [REST API] →
[Payment Midtrans] → [Notifikasi] → [Testing] → [Deploy/Submit]
```

---

## 📁 Struktur Folder Project (Target Akhir)

```
jajanmranggen/
├── docker-compose.yml
├── Dockerfile
├── nginx/
│   └── default.conf
├── app/
│   ├── Config/
│   ├── Controllers/
│   │   ├── Admin/
│   │   ├── Contributor/
│   │   └── Api/
│   ├── Filters/
│   ├── Libraries/
│   ├── Models/
│   ├── Views/
│   │   ├── admin/
│   │   ├── contributor/
│   │   ├── public/
│   │   └── layouts/
│   └── Database/
│       ├── Migrations/
│       └── Seeds/
├── public/
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   └── img/
│   └── uploads/
│       ├── kuliner/
│       └── thumbnails/
└── tests/
    └── app/
```

---

## 🐳 MILESTONE 1 — Setup Docker + Install CodeIgniter 4

**Penanggung Jawab**: Mahasiswa A

### Langkah 1: Buat folder project dan install CI4

Buka terminal di folder yang kamu mau, lalu jalankan:

```bash
# Buat folder
mkdir jajanmranggen && cd jajanmranggen

# Install CodeIgniter 4 via Composer
composer create-project codeigniter4/appstarter .

# Cek versi PHP (minimal 8.1)
php --version
```

### Langkah 2: Buat file `Dockerfile`

```dockerfile
# Dockerfile
FROM php:8.1-fpm

# Install ekstensi PHP yang dibutuhkan
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libcurl4-openssl-dev \
    libzip-dev \
    zip unzip git curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mysqli intl mbstring curl zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html/writable
```

### Langkah 3: Buat file `docker-compose.yml`

```yaml
# docker-compose.yml
version: '3.8'

services:
  app:
    build: .
    container_name: jajanmranggen_app
    restart: unless-stopped
    volumes:
      - .:/var/www/html
      - ./public/uploads:/var/www/html/public/uploads
    networks:
      - jajanmranggen_net
    depends_on:
      - db

  nginx:
    image: nginx:alpine
    container_name: jajanmranggen_nginx
    restart: unless-stopped
    ports:
      - "8080:80"
    volumes:
      - .:/var/www/html
      - ./nginx/default.conf:/etc/nginx/conf.d/default.conf
    networks:
      - jajanmranggen_net
    depends_on:
      - app

  db:
    image: mysql:8.0
    container_name: jajanmranggen_db
    restart: unless-stopped
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: jajanmranggen
      MYSQL_USER: ci4user
      MYSQL_PASSWORD: ci4pass
    volumes:
      - db_data:/var/lib/mysql
    ports:
      - "3307:3306"
    networks:
      - jajanmranggen_net

  phpmyadmin:
    image: phpmyadmin:latest
    container_name: jajanmranggen_pma
    restart: unless-stopped
    ports:
      - "8081:80"
    environment:
      PMA_HOST: db
      PMA_PORT: 3306
      PMA_USER: ci4user
      PMA_PASSWORD: ci4pass
    networks:
      - jajanmranggen_net
    depends_on:
      - db

networks:
  jajanmranggen_net:
    driver: bridge

volumes:
  db_data:
```

### Langkah 4: Buat file `nginx/default.conf`

```nginx
# nginx/default.conf
server {
    listen 80;
    server_name localhost;
    root /var/www/html/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

### Langkah 5: Konfigurasi `.env`

Copy `.env.example` menjadi `.env`, lalu edit:

```env
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost:8080/'
app.indexPage = ''

database.default.hostname = db
database.default.database = jajanmranggen
database.default.username = ci4user
database.default.password = ci4pass
database.default.DBDriver = MySQLi
database.default.port = 3306

# Email (Mailtrap)
email.protocol = smtp
email.SMTPHost = smtp.mailtrap.io
email.SMTPUser = ISI_DARI_MAILTRAP
email.SMTPPass = ISI_DARI_MAILTRAP
email.SMTPPort = 2525

# Midtrans Sandbox
midtrans.serverKey = ISI_SERVER_KEY_SANDBOX
midtrans.clientKey = ISI_CLIENT_KEY_SANDBOX
midtrans.isProduction = false

# Fonnte WA
fonnte.token = ISI_TOKEN_FONNTE

# API Key untuk REST API publik
api.secretKey = JAJANMRANGGEN_SECRET_KEY_2024
```

### Langkah 6: Jalankan Docker

```bash
docker-compose up -d --build

# Cek apakah container berjalan
docker-compose ps

# Akses: http://localhost:8080
# phpMyAdmin: http://localhost:8081
```

---

## 🗄️ MILESTONE 2 — Database: Migration & Seeder

**Penanggung Jawab**: Mahasiswa A

### Jalankan perintah spark untuk membuat migration

```bash
# Masuk ke container app
docker-compose exec app bash

# Buat file migration (jalankan satu per satu)
php spark make:migration CreateUsersTable
php spark make:migration CreateCategoriesTable
php spark make:migration CreateTagsTable
php spark make:migration CreateKulinerTable
php spark make:migration CreateKulinerTagsTable
php spark make:migration CreateReviewsTable
php spark make:migration CreatePhotosTable
php spark make:migration CreateFavoritesTable
php spark make:migration CreatePaymentsTable
```

### Isi masing-masing file Migration

#### `CreateUsersTable.php`
```php
<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'username'   => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'email'      => ['type' => 'VARCHAR', 'constraint' => 150, 'unique' => true],
            'password'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'role'       => ['type' => 'ENUM', 'constraint' => ['admin', 'contributor'], 'default' => 'contributor'],
            'phone'      => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
```

#### `CreateCategoriesTable.php`
```php
<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateCategoriesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'slug'        => ['type' => 'VARCHAR', 'constraint' => 120, 'unique' => true],
            'description' => ['type' => 'TEXT', 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('categories');
    }

    public function down()
    {
        $this->forge->dropTable('categories');
    }
}
```

#### `CreateKulinerTable.php`
```php
<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateKulinerTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'           => ['type' => 'VARCHAR', 'constraint' => 200],
            'slug'           => ['type' => 'VARCHAR', 'constraint' => 220, 'unique' => true],
            'description'    => ['type' => 'TEXT', 'null' => true],
            'address'        => ['type' => 'TEXT'],
            'latitude'       => ['type' => 'DECIMAL', 'constraint' => '10,8', 'null' => true],
            'longitude'      => ['type' => 'DECIMAL', 'constraint' => '11,8', 'null' => true],
            'category_id'    => ['type' => 'INT', 'unsigned' => true],
            'contributor_id' => ['type' => 'INT', 'unsigned' => true],
            'status'         => ['type' => 'ENUM', 'constraint' => ['pending', 'approved', 'rejected', 'closed_permanent'], 'default' => 'pending'],
            'average_rating' => ['type' => 'DECIMAL', 'constraint' => '3,2', 'default' => '0.00'],
            'is_promoted'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'promoted_until' => ['type' => 'DATETIME', 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('contributor_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('kuliner');
    }

    public function down()
    {
        $this->forge->dropTable('kuliner');
    }
}
```

#### `CreateReviewsTable.php`
```php
<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateReviewsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'kuliner_id' => ['type' => 'INT', 'unsigned' => true],
            'user_id'    => ['type' => 'INT', 'unsigned' => true],
            'rating'     => ['type' => 'TINYINT', 'constraint' => 1],
            'comment'    => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('kuliner_id', 'kuliner', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('reviews');
    }

    public function down()
    {
        $this->forge->dropTable('reviews');
    }
}
```

#### `CreatePhotosTable.php`
```php
<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreatePhotosTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'kuliner_id' => ['type' => 'INT', 'unsigned' => true],
            'filename'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'is_primary' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('kuliner_id', 'kuliner', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('photos');
    }

    public function down()
    {
        $this->forge->dropTable('photos');
    }
}
```

#### `CreateFavoritesTable.php`
```php
<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateFavoritesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'unsigned' => true],
            'kuliner_id' => ['type' => 'INT', 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('kuliner_id', 'kuliner', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('favorites');
    }

    public function down()
    {
        $this->forge->dropTable('favorites');
    }
}
```

#### `CreatePaymentsTable.php`
```php
<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'        => ['type' => 'INT', 'unsigned' => true],
            'kuliner_id'     => ['type' => 'INT', 'unsigned' => true],
            'invoice_number' => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'amount'         => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'status'         => ['type' => 'ENUM', 'constraint' => ['pending', 'paid', 'expired', 'failed'], 'default' => 'pending'],
            'snap_token'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'payment_method' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('kuliner_id', 'kuliner', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('payments');
    }

    public function down()
    {
        $this->forge->dropTable('payments');
    }
}
```

### Jalankan semua migration

```bash
# Di dalam container
php spark migrate
```

### Buat Seeder

```bash
php spark make:seeder MainSeeder
php spark make:seeder UserSeeder
php spark make:seeder CategorySeeder
php spark make:seeder KulinerSeeder
```

#### `UserSeeder.php`
```php
<?php
namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'username'   => 'admin',
                'email'      => 'admin@jajanmranggen.com',
                'password'   => password_hash('admin123', PASSWORD_BCRYPT),
                'role'       => 'admin',
                'phone'      => '081234567890',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username'   => 'kontributor1',
                'email'      => 'kontributor1@gmail.com',
                'password'   => password_hash('pass123', PASSWORD_BCRYPT),
                'role'       => 'contributor',
                'phone'      => '082345678901',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username'   => 'kontributor2',
                'email'      => 'kontributor2@gmail.com',
                'password'   => password_hash('pass123', PASSWORD_BCRYPT),
                'role'       => 'contributor',
                'phone'      => '083456789012',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->insertBatch($users);
    }
}
```

#### `CategorySeeder.php`
```php
<?php
namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Warung Makan', 'slug' => 'warung-makan', 'description' => 'Warung nasi dan lauk-pauk'],
            ['name' => 'Jajanan Pasar', 'slug' => 'jajanan-pasar', 'description' => 'Makanan tradisional pasar'],
            ['name' => 'Minuman & Es', 'slug' => 'minuman-es', 'description' => 'Aneka minuman dan es segar'],
            ['name' => 'Bakso & Mie', 'slug' => 'bakso-mie', 'description' => 'Bakso, mie ayam, dan sejenisnya'],
            ['name' => 'Gorengan & Snack', 'slug' => 'gorengan-snack', 'description' => 'Gorengan dan camilan'],
            ['name' => 'Seafood', 'slug' => 'seafood', 'description' => 'Masakan ikan dan seafood'],
        ];

        foreach ($categories as &$cat) {
            $cat['created_at'] = date('Y-m-d H:i:s');
            $cat['updated_at'] = date('Y-m-d H:i:s');
        }

        $this->db->table('categories')->insertBatch($categories);
    }
}
```

#### `KulinerSeeder.php` (20 data realistis area Mranggen/Demak)
```php
<?php
namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;

class KulinerSeeder extends Seeder
{
    public function run()
    {
        $kuliner = [
            ['name' => 'Warung Bu Mira Mranggen', 'slug' => 'warung-bu-mira-mranggen', 'description' => 'Warung nasi rumahan dengan masakan Jawa otentik.', 'address' => 'Jl. Raya Mranggen No.12, Mranggen, Demak', 'latitude' => -6.9917, 'longitude' => 110.4897, 'category_id' => 1, 'contributor_id' => 2, 'status' => 'approved', 'average_rating' => 4.50],
            ['name' => 'Es Dawet Pak Surip', 'slug' => 'es-dawet-pak-surip', 'description' => 'Es dawet ayu legendaris di Mranggen sejak 1990.', 'address' => 'Pasar Mranggen, Demak', 'latitude' => -6.9920, 'longitude' => 110.4870, 'category_id' => 3, 'contributor_id' => 2, 'status' => 'approved', 'average_rating' => 4.80],
            ['name' => 'Bakso Pak Harto Mranggen', 'slug' => 'bakso-pak-harto-mranggen', 'description' => 'Bakso sapi asli dengan kuah bening segar.', 'address' => 'Jl. Kauman No.5, Mranggen, Demak', 'latitude' => -6.9930, 'longitude' => 110.4905, 'category_id' => 4, 'contributor_id' => 3, 'status' => 'approved', 'average_rating' => 4.30],
            ['name' => 'Gorengan Mbak Yem', 'slug' => 'gorengan-mbak-yem', 'description' => 'Gorengan renyah: tempe, bakwan, tahu isi.', 'address' => 'Depan SD N 1 Mranggen, Demak', 'latitude' => -6.9940, 'longitude' => 110.4880, 'category_id' => 5, 'contributor_id' => 2, 'status' => 'approved', 'average_rating' => 4.20],
            ['name' => 'Warung Soto Semarang Bu Rini', 'slug' => 'warung-soto-semarang-bu-rini', 'description' => 'Soto ayam Semarang dengan bumbu kuning khas.', 'address' => 'Jl. Depok No.8, Mranggen, Demak', 'latitude' => -6.9908, 'longitude' => 110.4912, 'category_id' => 1, 'contributor_id' => 3, 'status' => 'approved', 'average_rating' => 4.60],
            ['name' => 'Jajanan Pasar Mranggen', 'slug' => 'jajanan-pasar-mranggen', 'description' => 'Aneka jajanan pasar tradisional: klepon, onde-onde, cenil.', 'address' => 'Pasar Pagi Mranggen, Demak', 'latitude' => -6.9918, 'longitude' => 110.4868, 'category_id' => 2, 'contributor_id' => 2, 'status' => 'approved', 'average_rating' => 4.10],
            ['name' => 'Seafood Pak Darto Trimulyo', 'slug' => 'seafood-pak-darto-trimulyo', 'description' => 'Ikan bakar dan goreng segar dari tambak lokal.', 'address' => 'Jl. Trimulyo Km.2, Genuk, Semarang', 'latitude' => -6.9850, 'longitude' => 110.4790, 'category_id' => 6, 'contributor_id' => 3, 'status' => 'approved', 'average_rating' => 4.40],
            ['name' => 'Mie Ayam Pak Bambang', 'slug' => 'mie-ayam-pak-bambang', 'description' => 'Mie ayam dengan topping ayam kecap gurih.', 'address' => 'Jl. Soekarno Hatta, Mranggen, Demak', 'latitude' => -6.9925, 'longitude' => 110.4920, 'category_id' => 4, 'contributor_id' => 2, 'status' => 'approved', 'average_rating' => 4.35],
            ['name' => 'Warung Pecel Mranggen', 'slug' => 'warung-pecel-mranggen', 'description' => 'Pecel sayuran dengan bumbu kacang otentik.', 'address' => 'Jl. Ahmad Yani No.20, Mranggen, Demak', 'latitude' => -6.9912, 'longitude' => 110.4895, 'category_id' => 1, 'contributor_id' => 3, 'status' => 'approved', 'average_rating' => 4.55],
            ['name' => 'Es Teh Pak Min Karangsono', 'slug' => 'es-teh-pak-min-karangsono', 'description' => 'Es teh manis jumbo paling seger di Karangsono.', 'address' => 'Karangsono, Mranggen, Demak', 'latitude' => -6.9945, 'longitude' => 110.4855, 'category_id' => 3, 'contributor_id' => 2, 'status' => 'approved', 'average_rating' => 4.00],
            ['name' => 'Nasi Goreng Bang Jon Mranggen', 'slug' => 'nasi-goreng-bang-jon', 'description' => 'Nasi goreng spesial dengan telur dan kerupuk.', 'address' => 'Jl. Branjang No.3, Mranggen, Demak', 'latitude' => -6.9935, 'longitude' => 110.4910, 'category_id' => 1, 'contributor_id' => 3, 'status' => 'approved', 'average_rating' => 4.25],
            ['name' => 'Warung Gado-Gado Bu Lastri', 'slug' => 'warung-gado-gado-bu-lastri', 'description' => 'Gado-gado segar dengan bumbu kacang pilihan.', 'address' => 'Jl. Kali Banger, Mranggen, Demak', 'latitude' => -6.9905, 'longitude' => 110.4875, 'category_id' => 1, 'contributor_id' => 2, 'status' => 'approved', 'average_rating' => 4.15],
            ['name' => 'Angkringan Mas Joko Tegalarum', 'slug' => 'angkringan-mas-joko-tegalarum', 'description' => 'Angkringan hits dengan nasi bakar dan wedang ronde.', 'address' => 'Tegalarum, Mranggen, Demak', 'latitude' => -6.9955, 'longitude' => 110.4845, 'category_id' => 1, 'contributor_id' => 3, 'status' => 'approved', 'average_rating' => 4.70],
            ['name' => 'Warung Sambel Bu Darni', 'slug' => 'warung-sambel-bu-darni', 'description' => 'Lalapan dan sambel terasi pedas merica istimewa.', 'address' => 'Jl. Mondokan, Mranggen, Demak', 'latitude' => -6.9922, 'longitude' => 110.4930, 'category_id' => 1, 'contributor_id' => 2, 'status' => 'approved', 'average_rating' => 4.45],
            ['name' => 'Tahu Gimbal Pak Tomo', 'slug' => 'tahu-gimbal-pak-tomo', 'description' => 'Tahu gimbal khas Semarang dengan bumbu kacang pedas.', 'address' => 'Jl. Mranggen Raya, Demak', 'latitude' => -6.9915, 'longitude' => 110.4888, 'category_id' => 2, 'contributor_id' => 3, 'status' => 'approved', 'average_rating' => 4.60],
            ['name' => 'Nasi Kucing Mbak Sri', 'slug' => 'nasi-kucing-mbak-sri', 'description' => 'Nasi kucing porsi kecil dengan lauk lengkap, cocok camilan malam.', 'address' => 'Depan Masjid Al-Ikhlas Mranggen', 'latitude' => -6.9928, 'longitude' => 110.4900, 'category_id' => 1, 'contributor_id' => 2, 'status' => 'approved', 'average_rating' => 4.30],
            ['name' => 'Bakwan Jagung Mbak Tini', 'slug' => 'bakwan-jagung-mbak-tini', 'description' => 'Bakwan jagung renyah dan panas, cocok untuk camilan sore.', 'address' => 'Gang Mawar No.2, Mranggen, Demak', 'latitude' => -6.9942, 'longitude' => 110.4892, 'category_id' => 5, 'contributor_id' => 3, 'status' => 'approved', 'average_rating' => 4.05],
            ['name' => 'Warung Tongseng Pak Wahyu', 'slug' => 'warung-tongseng-pak-wahyu', 'description' => 'Tongseng kambing kaya rempah yang menggugah selera.', 'address' => 'Jl. Brumbungan, Mranggen, Demak', 'latitude' => -6.9908, 'longitude' => 110.4918, 'category_id' => 1, 'contributor_id' => 2, 'status' => 'approved', 'average_rating' => 4.75],
            ['name' => 'Es Kelapa Muda Pak Slamet', 'slug' => 'es-kelapa-muda-pak-slamet', 'description' => 'Es kelapa muda langsung dari kelapa segar.', 'address' => 'Pinggir Jalan Raya Mranggen-Purwodadi', 'latitude' => -6.9950, 'longitude' => 110.4935, 'category_id' => 3, 'contributor_id' => 3, 'status' => 'approved', 'average_rating' => 4.90],
            ['name' => 'Warung Sate Bu Endah', 'slug' => 'warung-sate-bu-endah', 'description' => 'Sate ayam dan kambing dengan bumbu kecap khas Mranggen.', 'address' => 'Jl. Pangkalan, Mranggen, Demak', 'latitude' => -6.9910, 'longitude' => 110.4870, 'category_id' => 1, 'contributor_id' => 2, 'status' => 'approved', 'average_rating' => 4.65],
        ];

        foreach ($kuliner as &$k) {
            $k['created_at'] = date('Y-m-d H:i:s');
            $k['updated_at'] = date('Y-m-d H:i:s');
            $k['is_promoted'] = 0;
        }

        $this->db->table('kuliner')->insertBatch($kuliner);
    }
}
```

#### `MainSeeder.php`
```php
<?php
namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;

class MainSeeder extends Seeder
{
    public function run()
    {
        $this->call('UserSeeder');
        $this->call('CategorySeeder');
        $this->call('KulinerSeeder');
    }
}
```

### Jalankan Seeder

```bash
# Di dalam container
php spark db:seed MainSeeder
```

---

## 🔐 MILESTONE 3 — Auth: Session, Filter, Multi-Role

**Penanggung Jawab**: Mahasiswa A

### Buat Controller Auth

```bash
php spark make:controller Auth
```

#### `app/Controllers/Auth.php`
```php
<?php
namespace App\Controllers;
use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login()
    {
        if (session()->get('user_id')) {
            return $this->redirectByRole();
        }
        return view('auth/login');
    }

    public function loginPost()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $user = $this->userModel->where('email', $this->request->getPost('email'))->first();

        if (!$user || !password_verify($this->request->getPost('password'), $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Email atau password salah.');
        }

        session()->set([
            'user_id'   => $user['id'],
            'username'  => $user['username'],
            'email'     => $user['email'],
            'role'      => $user['role'],
            'logged_in' => true,
        ]);

        return $this->redirectByRole();
    }

    public function register()
    {
        return view('auth/register');
    }

    public function registerPost()
    {
        $rules = [
            'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'phone'    => 'permit_empty|max_length[20]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->userModel->save([
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'phone'    => $this->request->getPost('phone'),
            'role'     => 'contributor',
        ]);

        return redirect()->to('/login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    private function redirectByRole()
    {
        $role = session()->get('role');
        return $role === 'admin'
            ? redirect()->to('/admin/dashboard')
            : redirect()->to('/contributor/dashboard');
    }
}
```

### Buat Filter Auth & Role

```bash
php spark make:filter AuthFilter
php spark make:filter AdminFilter
php spark make:filter ContributorFilter
```

#### `app/Filters/AuthFilter.php`
```php
<?php
namespace App\Filters;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
```

#### `app/Filters/AdminFilter.php`
```php
<?php
namespace App\Filters;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/')->with('error', 'Akses ditolak. Hanya Admin.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
```

#### `app/Filters/ContributorFilter.php`
```php
<?php
namespace App\Filters;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ContributorFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
        if (!in_array(session()->get('role'), ['admin', 'contributor'])) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
```

### Daftarkan Filter di `app/Config/Filters.php`

```php
// Tambahkan di bagian $aliases
public array $aliases = [
    'csrf'        => CSRF::class,
    'toolbar'     => DebugToolbar::class,
    'honeypot'    => Honeypot::class,
    'auth'        => \App\Filters\AuthFilter::class,       // tambah
    'admin'       => \App\Filters\AdminFilter::class,      // tambah
    'contributor' => \App\Filters\ContributorFilter::class,// tambah
];
```

### Konfigurasi Routes di `app/Config/Routes.php`

```php
// Auth routes
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::loginPost');
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::registerPost');
$routes->get('/logout', 'Auth::logout');

// Public routes
$routes->get('/', 'Home::index');
$routes->get('/kuliner', 'Kuliner::index');
$routes->get('/kuliner/(:segment)', 'Kuliner::show/$1');

// Admin routes (protected)
$routes->group('admin', ['filter' => 'admin'], function ($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::index');
    $routes->get('kuliner', 'Admin\KulinerAdmin::index');
    $routes->post('kuliner/approve/(:num)', 'Admin\KulinerAdmin::approve/$1');
    $routes->post('kuliner/reject/(:num)', 'Admin\KulinerAdmin::reject/$1');
    $routes->get('kategori', 'Admin\Kategori::index');
    $routes->get('users', 'Admin\Users::index');
});

// Contributor routes (protected)
$routes->group('contributor', ['filter' => 'contributor'], function ($routes) {
    $routes->get('dashboard', 'Contributor\Dashboard::index');
    $routes->get('kuliner', 'Contributor\KulinerContributor::index');
    $routes->get('kuliner/create', 'Contributor\KulinerContributor::create');
    $routes->post('kuliner/store', 'Contributor\KulinerContributor::store');
    $routes->get('kuliner/edit/(:num)', 'Contributor\KulinerContributor::edit/$1');
    $routes->post('kuliner/update/(:num)', 'Contributor\KulinerContributor::update/$1');
    $routes->post('kuliner/delete/(:num)', 'Contributor\KulinerContributor::delete/$1');
    $routes->get('kuliner/geocode', 'Contributor\KulinerContributor::geocode');
    $routes->get('payment/sponsor/(:num)', 'Contributor\Payment::sponsor/$1');
    $routes->post('payment/checkout', 'Contributor\Payment::checkout');
});

// API routes
$routes->group('api', ['filter' => 'apikey'], function ($routes) {
    $routes->get('kuliner', 'Api\KulinerApi::index');
});
$routes->post('api/payment/notification', 'Api\PaymentNotification::handle');
```

---

## 🍽️ MILESTONE 4 — CRUD Kuliner + Upload Foto

**Penanggung Jawab**: B (UI/UX) + A (Proses/Resize)

### Model

```bash
php spark make:model KulinerModel
php spark make:model PhotoModel
php spark make:model CategoryModel
```

#### `app/Models/KulinerModel.php`
```php
<?php
namespace App\Models;
use CodeIgniter\Model;

class KulinerModel extends Model
{
    protected $table      = 'kuliner';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name', 'slug', 'description', 'address',
        'latitude', 'longitude', 'category_id', 'contributor_id',
        'status', 'average_rating', 'is_promoted', 'promoted_until',
    ];
    protected $useTimestamps = true;

    public function getWithCategory($status = 'approved', $limit = 20, $offset = 0)
    {
        return $this->select('kuliner.*, categories.name as category_name')
                    ->join('categories', 'categories.id = kuliner.category_id')
                    ->where('kuliner.status', $status)
                    ->orderBy('kuliner.is_promoted', 'DESC')
                    ->orderBy('kuliner.average_rating', 'DESC')
                    ->limit($limit, $offset)
                    ->findAll();
    }

    public function getNearby($lat, $lng, $radius = 5, $categorySlug = null)
    {
        $haversine = "(6371 * acos(cos(radians($lat)) * cos(radians(latitude))
                      * cos(radians(longitude) - radians($lng))
                      + sin(radians($lat)) * sin(radians(latitude))))";

        $builder = $this->select("kuliner.*, categories.name as category_name, $haversine AS distance")
                        ->join('categories', 'categories.id = kuliner.category_id')
                        ->where('kuliner.status', 'approved')
                        ->having("distance <=", $radius)
                        ->orderBy('distance', 'ASC');

        if ($categorySlug) {
            $builder->where('categories.slug', $categorySlug);
        }

        return $builder->findAll();
    }

    public function updateAverageRating($kuliner_id)
    {
        $avg = $this->db->table('reviews')
                        ->selectAvg('rating', 'avg_rating')
                        ->where('kuliner_id', $kuliner_id)
                        ->get()->getRow()->avg_rating;

        $this->update($kuliner_id, ['average_rating' => round($avg ?? 0, 2)]);
    }

    public function makeSlug($name)
    {
        $slug = url_title($name, '-', true);
        $count = $this->where('slug', $slug)->countAllResults();
        return $count > 0 ? $slug . '-' . time() : $slug;
    }
}
```

### Controller Contributor CRUD

#### `app/Controllers/Contributor/KulinerContributor.php`
```php
<?php
namespace App\Controllers\Contributor;
use App\Controllers\BaseController;
use App\Models\KulinerModel;
use App\Models\CategoryModel;
use App\Models\PhotoModel;

class KulinerContributor extends BaseController
{
    protected $kulinerModel;
    protected $categoryModel;
    protected $photoModel;

    public function __construct()
    {
        $this->kulinerModel  = new KulinerModel();
        $this->categoryModel = new CategoryModel();
        $this->photoModel    = new PhotoModel();
    }

    public function index()
    {
        $data['kuliner'] = $this->kulinerModel
            ->where('contributor_id', session()->get('user_id'))
            ->findAll();
        return view('contributor/kuliner/index', $data);
    }

    public function create()
    {
        $data['categories'] = $this->categoryModel->findAll();
        return view('contributor/kuliner/create', $data);
    }

    public function store()
    {
        $rules = [
            'name'        => 'required|min_length[3]|max_length[200]',
            'description' => 'permit_empty',
            'address'     => 'required',
            'category_id' => 'required|integer',
            'latitude'    => 'permit_empty|decimal',
            'longitude'   => 'permit_empty|decimal',
            'photo'       => 'permit_empty|uploaded[photo]|max_size[photo,2048]|is_image[photo]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $slug = $this->kulinerModel->makeSlug($this->request->getPost('name'));

        $kuliner_id = $this->kulinerModel->insert([
            'name'           => $this->request->getPost('name'),
            'slug'           => $slug,
            'description'    => $this->request->getPost('description'),
            'address'        => $this->request->getPost('address'),
            'latitude'       => $this->request->getPost('latitude') ?: null,
            'longitude'      => $this->request->getPost('longitude') ?: null,
            'category_id'    => $this->request->getPost('category_id'),
            'contributor_id' => session()->get('user_id'),
            'status'         => 'pending',
        ]);

        // Upload foto
        $photo = $this->request->getFile('photo');
        if ($photo && $photo->isValid() && !$photo->hasMoved()) {
            $this->processAndSavePhoto($photo, $kuliner_id, true);
        }

        return redirect()->to('/contributor/kuliner')->with('success', 'Kuliner berhasil ditambahkan, menunggu persetujuan admin.');
    }

    public function edit($id)
    {
        $kuliner = $this->kulinerModel->find($id);
        if (!$kuliner || $kuliner['contributor_id'] != session()->get('user_id')) {
            return redirect()->to('/contributor/kuliner')->with('error', 'Data tidak ditemukan.');
        }
        $data['kuliner']    = $kuliner;
        $data['categories'] = $this->categoryModel->findAll();
        $data['photos']     = $this->photoModel->where('kuliner_id', $id)->findAll();
        return view('contributor/kuliner/edit', $data);
    }

    public function update($id)
    {
        $kuliner = $this->kulinerModel->find($id);
        if (!$kuliner || $kuliner['contributor_id'] != session()->get('user_id')) {
            return redirect()->to('/contributor/kuliner')->with('error', 'Akses ditolak.');
        }

        $this->kulinerModel->update($id, [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'address'     => $this->request->getPost('address'),
            'category_id' => $this->request->getPost('category_id'),
            'latitude'    => $this->request->getPost('latitude') ?: null,
            'longitude'   => $this->request->getPost('longitude') ?: null,
            'status'      => 'pending', // Re-review setelah edit
        ]);

        $photo = $this->request->getFile('photo');
        if ($photo && $photo->isValid() && !$photo->hasMoved()) {
            $this->processAndSavePhoto($photo, $id, false);
        }

        return redirect()->to('/contributor/kuliner')->with('success', 'Data kuliner diperbarui.');
    }

    public function delete($id)
    {
        $kuliner = $this->kulinerModel->find($id);
        if (!$kuliner || $kuliner['contributor_id'] != session()->get('user_id')) {
            return redirect()->to('/contributor/kuliner')->with('error', 'Akses ditolak.');
        }
        $this->kulinerModel->delete($id);
        return redirect()->to('/contributor/kuliner')->with('success', 'Kuliner berhasil dihapus.');
    }

    // Geocoding via Nominatim (AJAX)
    public function geocode()
    {
        $address = $this->request->getGet('q');
        if (!$address) {
            return $this->response->setJSON(['error' => 'Alamat kosong']);
        }

        $cacheKey = 'geocode_' . md5($address);
        $cache    = \Config\Services::cache();

        if ($cached = $cache->get($cacheKey)) {
            return $this->response->setJSON($cached);
        }

        $client   = \Config\Services::curlrequest();
        $response = $client->get('https://nominatim.openstreetmap.org/search', [
            'query' => ['q' => $address, 'format' => 'json', 'limit' => 1],
            'headers' => ['User-Agent' => 'JajanMranggen/1.0 (jajanmranggen@gmail.com)'],
        ]);

        $result = json_decode($response->getBody(), true);

        if (empty($result)) {
            return $this->response->setJSON(['error' => 'Koordinat tidak ditemukan.']);
        }

        $data = ['lat' => $result[0]['lat'], 'lng' => $result[0]['lon'], 'display_name' => $result[0]['display_name']];
        $cache->save($cacheKey, $data, 86400); // Cache 24 jam

        return $this->response->setJSON($data);
    }

    private function processAndSavePhoto($file, $kuliner_id, $isPrimary = false)
    {
        $uploadPath = ROOTPATH . 'public/uploads/kuliner/';
        $thumbPath  = ROOTPATH . 'public/uploads/thumbnails/';

        if (!is_dir($uploadPath)) mkdir($uploadPath, 0755, true);
        if (!is_dir($thumbPath))  mkdir($thumbPath, 0755, true);

        $newName = $file->getRandomName();
        $file->move($uploadPath, $newName);

        // Resize to max 800px width
        \Config\Services::image()
            ->withFile($uploadPath . $newName)
            ->resize(800, 600, true, 'width')
            ->save($uploadPath . $newName);

        // Thumbnail 200x200
        \Config\Services::image()
            ->withFile($uploadPath . $newName)
            ->fit(200, 200, 'center')
            ->save($thumbPath . $newName);

        $this->photoModel->insert([
            'kuliner_id' => $kuliner_id,
            'filename'   => $newName,
            'is_primary' => $isPrimary ? 1 : 0,
        ]);
    }
}
```

---

## 🗺️ MILESTONE 5 — Leaflet.js Map Integration

**Penanggung Jawab**: Mahasiswa B (UI), Mahasiswa A (backend geocoding sudah di atas)

Tambahkan script berikut di view form tambah/edit kuliner:

#### `app/Views/contributor/kuliner/_map_script.php`
```html
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
            📍 Cari Koordinat
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
            this.textContent = '📍 Cari Koordinat';
        });
});
</script>
```

---

## 🌐 MILESTONE 6 — REST API Server dengan API Key

**Penanggung Jawab**: Mahasiswa A

### Buat API Key Filter

#### `app/Filters/ApiKeyFilter.php`
```php
<?php
namespace App\Filters;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ApiKeyFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $apiKey = $request->getHeaderLine('X-API-KEY');
        $validKey = env('api.secretKey', 'JAJANMRANGGEN_SECRET_KEY_2024');

        if ($apiKey !== $validKey) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['status' => 'error', 'message' => 'API Key tidak valid.']);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
```

Daftarkan di `Filters.php`:
```php
'apikey' => \App\Filters\ApiKeyFilter::class,
```

### Controller API

#### `app/Controllers/Api/KulinerApi.php`
```php
<?php
namespace App\Controllers\Api;
use App\Controllers\BaseController;
use App\Models\KulinerModel;

class KulinerApi extends BaseController
{
    public function index()
    {
        $lat      = $this->request->getGet('lat');
        $lng      = $this->request->getGet('lng');
        $radius   = $this->request->getGet('radius') ?? 5;
        $category = $this->request->getGet('category');

        if (!$lat || !$lng) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Parameter lat dan lng wajib diisi.',
            ]);
        }

        $model   = new KulinerModel();
        $kuliner = $model->getNearby($lat, $lng, $radius, $category);

        return $this->response->setJSON([
            'status' => 'success',
            'total'  => count($kuliner),
            'data'   => $kuliner,
        ]);
    }
}
```

---

## 💳 MILESTONE 7 — Payment Gateway Midtrans + Notifikasi

**Penanggung Jawab**: Mahasiswa A (backend) + B (UI checkout)

### Install Midtrans PHP Library

```bash
# Di dalam container
composer require midtrans/midtrans-php
```

### Library Notifikasi

#### `app/Libraries/WhatsappNotification.php`
```php
<?php
namespace App\Libraries;

class WhatsappNotification
{
    public function send(string $phone, string $message): bool
    {
        $token = env('fonnte.token');

        $client = \Config\Services::curlrequest();
        try {
            $response = $client->post('https://api.fonnte.com/send', [
                'headers' => ['Authorization' => $token],
                'form_params' => [
                    'target'  => $phone,
                    'message' => $message,
                ],
            ]);
            $result = json_decode($response->getBody(), true);
            return $result['status'] ?? false;
        } catch (\Exception $e) {
            log_message('error', 'WA Notification Error: ' . $e->getMessage());
            return false;
        }
    }
}
```

#### `app/Controllers/Contributor/Payment.php`
```php
<?php
namespace App\Controllers\Contributor;
use App\Controllers\BaseController;
use App\Models\KulinerModel;
use App\Models\PaymentModel;
use App\Libraries\WhatsappNotification;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;

class Payment extends BaseController
{
    protected $kulinerModel;
    protected $paymentModel;

    public function __construct()
    {
        $this->kulinerModel = new KulinerModel();
        $this->paymentModel = new PaymentModel();

        MidtransConfig::$serverKey    = env('midtrans.serverKey');
        MidtransConfig::$isProduction = env('midtrans.isProduction', false);
        MidtransConfig::$isSanitized  = true;
        MidtransConfig::$is3ds        = true;
    }

    public function sponsor($kuliner_id)
    {
        $kuliner = $this->kulinerModel->find($kuliner_id);
        if (!$kuliner || $kuliner['contributor_id'] != session()->get('user_id')) {
            return redirect()->to('/contributor/kuliner')->with('error', 'Data tidak ditemukan.');
        }
        return view('contributor/payment/sponsor', ['kuliner' => $kuliner]);
    }

    public function checkout()
    {
        $kuliner_id = $this->request->getPost('kuliner_id');
        $amount     = 50000; // Rp 50.000 untuk 7 hari promosi

        $invoice = 'INV-' . date('YmdHis') . '-' . session()->get('user_id');

        $payment_id = $this->paymentModel->insert([
            'user_id'        => session()->get('user_id'),
            'kuliner_id'     => $kuliner_id,
            'invoice_number' => $invoice,
            'amount'         => $amount,
            'status'         => 'pending',
        ]);

        $params = [
            'transaction_details' => [
                'order_id'     => $invoice,
                'gross_amount' => $amount,
            ],
            'customer_details' => [
                'first_name' => session()->get('username'),
                'email'      => session()->get('email'),
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        $this->paymentModel->update($payment_id, ['snap_token' => $snapToken]);

        return view('contributor/payment/checkout', [
            'snap_token' => $snapToken,
            'client_key' => env('midtrans.clientKey'),
            'invoice'    => $invoice,
        ]);
    }
}
```

#### `app/Controllers/Api/PaymentNotification.php`
```php
<?php
namespace App\Controllers\Api;
use App\Controllers\BaseController;
use App\Models\PaymentModel;
use App\Models\KulinerModel;
use App\Models\UserModel;
use App\Libraries\WhatsappNotification;
use Midtrans\Config as MidtransConfig;
use Midtrans\Notification;

class PaymentNotification extends BaseController
{
    public function handle()
    {
        MidtransConfig::$serverKey    = env('midtrans.serverKey');
        MidtransConfig::$isProduction = env('midtrans.isProduction', false);

        try {
            $notification   = new Notification();
            $transStatus    = $notification->transaction_status;
            $fraudStatus    = $notification->fraud_status;
            $orderId        = $notification->order_id;
            $paymentMethod  = $notification->payment_type;

            $paymentModel = new PaymentModel();
            $kulinerModel = new KulinerModel();
            $userModel    = new UserModel();
            $wa           = new WhatsappNotification();

            $payment = $paymentModel->where('invoice_number', $orderId)->first();
            if (!$payment) return $this->response->setStatusCode(404);

            if ($transStatus === 'capture' && $fraudStatus === 'accept' || $transStatus === 'settlement') {
                $paymentModel->update($payment['id'], [
                    'status'         => 'paid',
                    'payment_method' => $paymentMethod,
                ]);

                $kulinerModel->update($payment['kuliner_id'], [
                    'is_promoted'    => 1,
                    'promoted_until' => date('Y-m-d H:i:s', strtotime('+7 days')),
                ]);

                // Kirim notifikasi WA
                $user   = $userModel->find($payment['user_id']);
                $kuliner = $kulinerModel->find($payment['kuliner_id']);
                if ($user && $user['phone']) {
                    $msg = "✅ Pembayaran sponsor *{$kuliner['name']}* berhasil!\n"
                         . "Invoice: {$orderId}\n"
                         . "Kuliner Anda akan dipromosikan selama 7 hari.\nTerima kasih! 🍜";
                    $wa->send($user['phone'], $msg);
                }

                // Kirim Email
                $emailService = \Config\Services::email();
                $emailService->setTo($user['email']);
                $emailService->setSubject('Pembayaran Sponsor Berhasil - JajanMranggen');
                $emailService->setMessage(
                    "<h2>Pembayaran Berhasil!</h2>
                    <p>Halo <strong>{$user['username']}</strong>,</p>
                    <p>Kuliner <strong>{$kuliner['name']}</strong> kamu berhasil disponsori selama 7 hari.</p>
                    <p>Invoice: <code>{$orderId}</code></p>
                    <p>Terima kasih telah menggunakan JajanMranggen! 🍜</p>"
                );
                $emailService->send();

            } elseif (in_array($transStatus, ['cancel', 'deny', 'expire'])) {
                $paymentModel->update($payment['id'], ['status' => 'failed']);
            }

            return $this->response->setJSON(['status' => 'ok']);

        } catch (\Exception $e) {
            log_message('error', 'Midtrans Webhook Error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => $e->getMessage()]);
        }
    }
}
```

---

## 🧪 MILESTONE 8–9 — Testing (PHPUnit)

**Penanggung Jawab**: Mahasiswa A & B

```bash
# Jalankan test
php spark test
```

#### `tests/app/AuthTest.php`
```php
<?php
namespace Tests\App;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use App\Database\Seeds\UserSeeder;

class AuthTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $seed = UserSeeder::class;
    protected $migrate = true;
    protected $refresh = true;

    public function testLoginSuksesAsAdmin()
    {
        $result = $this->post('/login', [
            'email'    => 'admin@jajanmranggen.com',
            'password' => 'admin123',
        ]);
        $result->assertRedirectTo(site_url('/admin/dashboard'));
    }

    public function testLoginGagalPasswordSalah()
    {
        $result = $this->post('/login', [
            'email'    => 'admin@jajanmranggen.com',
            'password' => 'wrongpassword',
        ]);
        $result->assertSessionHas('error', 'Email atau password salah.');
    }

    public function testContributorTidakBisaAksesAdminRoute()
    {
        $result = $this->withSession([
            'user_id'   => 2,
            'role'      => 'contributor',
            'logged_in' => true,
        ])->get('/admin/dashboard');
        $result->assertSessionHas('error', 'Akses ditolak. Hanya Admin.');
    }

    public function testGuestTidakBisaAksesDashboard()
    {
        $result = $this->get('/admin/dashboard');
        $result->assertRedirectTo(site_url('/login'));
    }
}
```

#### `tests/app/ReviewRatingTest.php`
```php
<?php
namespace Tests\App;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\KulinerModel;
use App\Models\ReviewModel;

class ReviewRatingTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    protected $migrate = true;
    protected $refresh = true;

    public function testRataRataRatingDihitungKetikaTambahReview()
    {
        // Setup: insert kuliner dan beberapa review
        $kulinerModel = new KulinerModel();
        $kuliner_id   = $kulinerModel->insert([
            'name' => 'Test Kuliner', 'slug' => 'test-kuliner',
            'address' => 'Test Address', 'category_id' => 1,
            'contributor_id' => 1, 'status' => 'approved',
        ]);

        $db = db_connect();
        $db->table('reviews')->insertBatch([
            ['kuliner_id' => $kuliner_id, 'user_id' => 1, 'rating' => 4, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['kuliner_id' => $kuliner_id, 'user_id' => 2, 'rating' => 5, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ]);

        $kulinerModel->updateAverageRating($kuliner_id);

        $updated = $kulinerModel->find($kuliner_id);
        $this->assertEquals(4.50, $updated['average_rating']);
    }
}
```

---

## 📋 MILESTONE 10 — README & Git Setup

**Penanggung Jawab**: Mahasiswa A & B

### Inisialisasi Git Repository

```bash
# Di folder project (bukan di dalam container)
git init
git remote add origin https://github.com/USERNAME/jajanmranggen.git

# Buat .gitignore
cat > .gitignore << 'EOF'
/vendor/
.env
writable/cache/*
writable/logs/*
writable/session/*
public/uploads/*
!public/uploads/.gitkeep
EOF

# Initial commit
git checkout -b dev
git add .
git commit -m "feat: initial project setup with Docker and CI4"
git push -u origin dev
```

### Format Commit Harian

```bash
# Mahasiswa A — contoh saat selesai fitur
git checkout -b feature/auth-system dev
# ... coding ...
git add .
git commit -m "feat: add multi-role authentication with session filter"
git push origin feature/auth-system
# Buat Pull Request di GitHub: feature/auth-system → dev

# Mahasiswa B — contoh saat selesai UI
git checkout -b feature/leaflet-map dev
# ... coding ...
git add .
git commit -m "feat: add interactive Leaflet.js map with draggable marker"
git push origin feature/leaflet-map
```

---

## 🚀 Perintah Harian (Quick Reference)

```bash
# Masuk container
docker-compose exec app bash

# Migrate ulang dari nol
php spark migrate:rollback --all && php spark migrate && php spark db:seed MainSeeder

# Buat controller baru
php spark make:controller NamaController

# Buat model baru
php spark make:model NamaModel

# Lihat routes
php spark routes

# Cache clear
php spark cache:clear

# Jalankan test
php spark test

# Stop semua container
docker-compose down

# Start ulang
docker-compose up -d
```

---

## 📌 Checklist Progress

| Milestone | Tugas | PJ | Status |
|:---|:---|:---|:---|
| M1 | Setup Docker + CI4 | A | ☐ |
| M2 | Migration + Seeder | A | ☐ |
| M3 | Auth + Filter + Routes | A | ☐ |
| M4 | CRUD Kuliner + Upload | A+B | ☐ |
| M5 | Leaflet.js + Geocoding | A+B | ☐ |
| M6 | REST API + API Key | A | ☐ |
| M7 | Midtrans + WA + Email | A+B | ☐ |
| M8 | Styling Bootstrap 5 | B | ☐ |
| M9 | PHPUnit Testing | A+B | ☐ |
| M10 | README + Git History | A+B | ☐ |

---

> **Tips**: Kalian bisa centang tabel ini di README.md project sebagai progress tracker untuk dosen.
> Minta Claude jika butuh kode untuk View/UI, atau bagian tertentu yang lebih detail.
