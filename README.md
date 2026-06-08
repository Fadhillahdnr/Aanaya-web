# 🎵 Aanaya Website

<p align="center">
  <img src="public/images/about-image.png" width="400" alt="Aanaya Logo">
</p>

<p align="center">
  <strong>A dreamy, emotional, and cinematic indie music experience</strong>
</p>

---

## 📖 Daftar Isi

- [Tentang Aanaya](#tentang-aanaya)
- [Fitur](#fitur)
- [Tech Stack](#tech-stack)
- [Prasyarat Sistem](#prasyarat-sistem)
- [Panduan Instalasi Lengkap](#panduan-instalasi-lengkap)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Perintah Berguna](#perintah-berguna)
- [Troubleshooting](#troubleshooting)
- [Struktur Project](#struktur-project)
- [Kontribusi](#kontribusi)
- [Lisensi](#lisensi)

---

## 📝 Tentang Aanaya

Aanaya adalah band indie dengan nuansa **dreamy, cinematic, dan emosional**. Menggabungkan visual aesthetic dengan musik intimate yang membawa pendengar masuk ke dalam suasana hangat, nostalgic, dan penuh perasaan.

Setiap lagu dibuat untuk menghadirkan rasa tentang **nostalgia, kehilangan, harapan, dan perjalanan emosional** yang relatable bagi banyak orang.

Dengan sentuhan **aesthetic soft pink, ambience cinematic, dan vibe modern indie**, Aanaya ingin menciptakan pengalaman yang bukan hanya didengar, tetapi juga **dirasakan**.

---

## ✨ Fitur

- 🎵 **Music Gallery** - Galeri musik yang indah dengan visual premium
- 🛒 **Online Store** - Toko merchandise untuk penggemar setia
- 📝 **Articles & Blog** - Artikel, cerita, dan konten eksklusif
- 👤 **User Profiles** - Profil pengguna dan sistem autentikasi
- 🎨 **Aesthetic Design** - Design modern dengan Tailwind CSS
- 📊 **Admin Dashboard** - Dashboard untuk mengelola konten
- 🚀 **Production Ready** - Siap untuk deployment ke production

---

## 🛠 Tech Stack

| Layer | Teknologi |
|-------|-----------|
| **Backend** | Laravel 12, PHP 8.2+ |
| **Frontend** | Blade Templates, Tailwind CSS, Alpine.js |
| **Database** | MySQL 8.0+ |
| **Build Tool** | Vite 7+ |
| **Package Manager** | Composer, NPM |
| **Storage** | Cloudinary (untuk upload media) |
| **Testing** | PHPUnit |
| **Email** | Resend |

---

## ⚙️ Prasyarat Sistem

Sebelum memulai, pastikan Anda sudah menginstal:

### **1. Git**
- **Windows/Mac/Linux**: Download dari https://git-scm.com/
- Verifikasi: `git --version`

### **2. PHP 8.2 atau lebih tinggi**
- **Windows**: 
  - Download dari https://windows.php.net/download/
  - Atau gunakan Laravel Sail (Docker)
  - Atau gunakan XAMPP/WAMP/Laragon
- **Mac**: 
  - `brew install php@8.2`
- **Linux**: 
  - `sudo apt-get install php8.2 php8.2-{curl,dom,mbstring,mysql,pdo,sqlite,tokenizer,xml}`

- Verifikasi: `php -v` (harus PHP 8.2+)

### **3. Composer** (PHP Package Manager)
- Download dari https://getcomposer.org/download/
- Verifikasi: `composer --version`

### **4. Node.js dan NPM** (untuk frontend assets)
- Download dari https://nodejs.org/ (pilih LTS version)
- Verifikasi: 
  ```bash
  node --version
  npm --version
  ```

### **5. MySQL 8.0+** (Database)
- **Windows**: Download MySQL Community Server dari https://dev.mysql.com/downloads/mysql/
- **Mac**: `brew install mysql`
- **Linux**: `sudo apt-get install mysql-server`
- **Alternatif**: Docker atau Laravel Sail

- Verifikasi: `mysql --version` atau `mysql -u root -p` (untuk login)

### **6. Text Editor/IDE (Optional)**
- VSCode (https://code.visualstudio.com/) - Recommended
- PHPStorm
- Sublime Text
- Atau editor favorit Anda

---

## 🚀 Panduan Instalasi Lengkap

### **Step 1: Clone Repository dari GitHub**

```bash
# Buka terminal/command prompt
# Navigate ke folder tempat ingin menyimpan project
cd C:\xampp\htdocs    # Windows XAMPP example
# atau
cd ~/Projects          # macOS/Linux example

# Clone repository
git clone https://github.com/username/aanaya-web.git

# Masuk ke folder project
cd aanaya-web
```

### **Step 2: Install PHP Dependencies (Backend)**

```bash
# Pastikan Anda sudah di folder project
# Jalankan composer untuk install semua PHP dependencies

composer install

# Jika ada error, coba:
composer install --no-interaction
```

**Apa yang dilakukan?**
- Download semua library PHP yang diperlukan (Laravel, Cloudinary, dll)
- File akan tersimpan di folder `vendor/`
- Proses ini memakan waktu 2-5 menit

### **Step 3: Setup Environment File (.env)**

```bash
# Copy file .env.example menjadi .env
# Windows:
copy .env.example .env

# macOS/Linux:
cp .env.example .env
```

**Edit file `.env` dengan konfigurasi Anda:**

Buka file `.env` dengan text editor dan konfigurasi sebagai berikut:

```env
APP_NAME="Aanaya"
APP_ENV=local                    # 'local' untuk development, 'production' untuk production
APP_KEY=                         # Akan di-generate otomatis di step berikutnya
APP_DEBUG=true                   # 'true' saat development, 'false' saat production
APP_URL=http://localhost:8000    # URL aplikasi Anda

# Database Configuration
DB_CONNECTION=mysql              # Jenis database (mysql, sqlite, pgsql, sqlsrv)
DB_HOST=127.0.0.1               # Host database (localhost atau 127.0.0.1)
DB_PORT=3306                     # Port MySQL (default 3306)
DB_DATABASE=aanaya_db            # Nama database (buat terlebih dahulu atau auto-create)
DB_USERNAME=root                 # Username database
DB_PASSWORD=                     # Password database (kosongkan jika tidak ada password)

# Mail Configuration (Optional)
MAIL_MAILER=resend               # Email service provider
MAIL_FROM_ADDRESS=noreply@aanaya.music
MAIL_FROM_NAME="Aanaya"

# Cloudinary Configuration (Optional, untuk upload gambar/video)
CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret

# Session Configuration
SESSION_DRIVER=database          # Session driver (database, cookie, file, memcached)
CACHE_DRIVER=database            # Cache driver

# Queue Configuration (Optional)
QUEUE_CONNECTION=database        # Queue connection (database, redis, sync)
```

**Contoh untuk development lokal dengan MySQL default:**
```env
APP_NAME="Aanaya"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aanaya_db
DB_USERNAME=root
DB_PASSWORD=
```

### **Step 4: Generate Application Key**

```bash
# Generate unique application key untuk enkripsi
php artisan key:generate

# Output yang berhasil akan menunjukkan:
# Application key set successfully.
```

### **Step 5: Setup Database**

#### **Opsi A: Jika database sudah terbuat**
```bash
# Jalankan migration untuk membuat tabel-tabel
php artisan migrate

# Jika ingin reset (hapus semua data dan buat ulang):
php artisan migrate:reset
php artisan migrate

# Atau:
php artisan migrate:refresh
```

#### **Opsi B: Jika database belum terbuat**

```bash
# Login ke MySQL terlebih dahulu
mysql -u root -p

# Atau gunakan MySQL client lainnya

# Buat database
CREATE DATABASE aanaya_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# Kemudian jalankan migration
php artisan migrate
```

**Apa yang dilakukan?**
- Membuat tabel-tabel: users, articles, products, orders, galleries, music, dll
- Membuat kolom-kolom yang diperlukan
- Setup relasi antar tabel

### **Step 6: Seed Database (Optional, untuk data testing)**

```bash
# Jalankan seeder untuk membuat data dummy/testing
php artisan db:seed

# Atau jalankan migration dengan seed sekaligus:
php artisan migrate --seed
```

### **Step 7: Install Frontend Dependencies (NPM)**

```bash
# Install semua JavaScript dependencies untuk frontend
npm install

# Proses ini akan membuat folder node_modules/
# Memakan waktu 1-3 menit
```

### **Step 8: Build Frontend Assets**

```bash
# Build CSS (Tailwind) dan JavaScript (Alpine.js)
npm run build

# Output akan disimpan di public/build/
```

### **Step 9: (Optional) Set Correct Directory Permissions**

Jika menggunakan Linux/Mac:

```bash
# Berikan permission untuk storage dan bootstrap cache
chmod -R 775 storage bootstrap/cache

# Atau:
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

---

## ▶️ Menjalankan Aplikasi

### **Option 1: Mode Development (Recommended)**

```bash
# Jalankan semua service sekaligus: server, queue, logs, Vite hot reload
composer run dev

# Ini akan menjalankan:
# ✓ Laravel server (port 8000)
# ✓ Queue listener
# ✓ Pail logs (real-time logging)
# ✓ Vite dev server (hot reload untuk assets)

# Buka browser dan akses: http://localhost:8000
```

**Stop server**: Tekan `Ctrl+C`

### **Option 2: Jalankan Manual Terpisah (Untuk Development Detail)**

**Terminal 1 - Laravel Server:**
```bash
php artisan serve
# Server akan berjalan di http://localhost:8000
```

**Terminal 2 - Frontend Hot Reload (Opsional):**
```bash
npm run dev
# Vite akan watch perubahan CSS dan JavaScript
```

**Terminal 3 - Queue Listener (Opsional):**
```bash
php artisan queue:listen
# Untuk memproses background jobs
```

**Terminal 4 - Tail Logs Real-time (Opsional):**
```bash
php artisan pail
# Untuk melihat log real-time
```

### **Option 3: Production Server**

```bash
# Build optimized assets
npm run build

# Jalankan dengan production web server
php artisan serve --host 0.0.0.0 --port 8000

# Atau gunakan web server seperti Apache/Nginx (lihat dokumentasi)
```

---

## 📋 Perintah Berguna

### **Database & Migration**
```bash
# Jalankan semua migration yang pending
php artisan migrate

# Buat table baru melalui migration file baru
php artisan make:migration create_table_name

# Rollback migration (undo) yang terakhir
php artisan migrate:rollback

# Rollback semua migration
php artisan migrate:reset

# Refresh: rollback + re-run semua migration
php artisan migrate:refresh

# Refresh + seed dengan data
php artisan migrate:refresh --seed
```

### **Data & Seeding**
```bash
# Run semua seeder
php artisan db:seed

# Run seeder spesifik
php artisan db:seed --class=UserSeeder

# Membuat seeder baru
php artisan make:seeder NameSeeder
```

### **Artisan Command Lainnya**
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Membuat migration baru
php artisan make:migration migration_name

# Membuat model baru
php artisan make:model ModelName

# Membuat controller baru
php artisan make:controller ControllerName

# Membuat request form baru
php artisan make:request RequestName

# Membuat seeders
php artisan make:seeder SeederName

# List semua route yang terdaftar
php artisan route:list

# Tinker - Interactive shell untuk testing
php artisan tinker
```

### **Testing**
```bash
# Jalankan semua test
composer test

# Jalankan test file spesifik
php artisan test tests/Feature/UserTest.php

# Jalankan test dengan verbose output
php artisan test --verbose

# Jalankan test dengan code coverage report
php artisan test --coverage
```

### **Code Quality**
```bash
# Format code sesuai Laravel style guide (Pint)
./vendor/bin/pint

# Check code tanpa auto-fix
./vendor/bin/pint --test
```

### **Asset Management**
```bash
# Build production-optimized assets
npm run build

# Watch untuk development dengan hot reload
npm run dev

# Publikasikan vendor assets
php artisan vendor:publish
```

---

## 🔧 Troubleshooting

### **Error: `composer: command not found`**
```bash
# Install Composer terlebih dahulu
# Download dari https://getcomposer.org/download/

# Atau jika sudah install tapi masih error:
# Windows: Tambahkan path Composer ke environment variables
# Mac/Linux: Gunakan full path
/usr/local/bin/composer install
```

### **Error: `PHP version does not satisfy requirement ^8.2`**
```bash
# Update PHP ke versi 8.2 atau lebih tinggi
php -v  # Cek versi PHP Anda

# Mac:
brew install php@8.2
brew link php@8.2

# Linux:
sudo apt-get install php8.2

# Windows: Download dari https://windows.php.net/
```

### **Error: `PDOException - SQLSTATE[HY000] - could not find driver`**
```bash
# MySQL driver tidak terinstall di PHP

# Mac:
brew install php@8.2-mysql

# Linux:
sudo apt-get install php8.2-mysql

# Windows: Edit php.ini dan uncomment extension=pdo_mysql
```

### **Error: `Database does not exist`**
```bash
# Buat database terlebih dahulu
mysql -u root -p -e "CREATE DATABASE aanaya_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Atau update DB_DATABASE di .env
# Kemudian jalankan:
php artisan migrate
```

### **Error: `No application encryption key has been generated`**
```bash
# Generate application key
php artisan key:generate
```

### **Error: `npm: command not found`**
```bash
# Install Node.js terlebih dahulu
# Download dari https://nodejs.org/ (pilih LTS)

# Verifikasi:
node --version
npm --version
```

### **Error: `npm install` sangat lambat**
```bash
# Gunakan npm cache clean
npm cache clean --force

# Atau gunakan mirror Indonesia (lebih cepat):
npm config set registry https://registry.npmmirror.com

# Kemudian jalankan lagi:
npm install
```

### **Port 8000 sudah digunakan**
```bash
# Gunakan port berbeda
php artisan serve --port 8001

# Atau cari proses yang menggunakan port 8000
# Windows:
netstat -ano | findstr :8000
taskkill /PID <PID> /F

# Mac/Linux:
lsof -i :8000
kill -9 <PID>
```

### **Error: Permission denied di storage/ atau bootstrap/cache/**
```bash
# Linux/Mac:
sudo chmod -R 775 storage bootstrap/cache

# Atau:
sudo chown -R $(whoami):$(whoami) storage bootstrap/cache
sudo chmod -R 755 storage bootstrap/cache

# Windows: Biasanya tidak perlu, tapi jika ada error:
# Klik kanan folder → Properties → Security → Edit Permissions
```

### **Aplikasi berjalan lambat**
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize untuk production
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### **Assets CSS/JS tidak muncul (404)**
```bash
# Rebuild assets
npm run build

# Atau untuk development dengan hot reload:
npm run dev

# Publish vendor assets jika perlu:
php artisan vendor:publish
```

---

## 📂 Struktur Project

```
aanaya-web/
├── app/
│   ├── Http/
│   │   ├── Controllers/      # Controller untuk handle request
│   │   ├── Middleware/       # Middleware untuk filter request
│   │   └── Requests/         # Form request validation
│   ├── Models/               # Eloquent models (User, Article, Product, dll)
│   ├── Providers/            # Service providers
│   └── Traits/               # Reusable code traits
├── bootstrap/
│   ├── app.php              # Application bootstrap
│   └── cache/               # Cache directory
├── config/                  # Configuration files (app, database, mail, dll)
├── database/
│   ├── factories/           # Model factories untuk testing
│   ├── migrations/          # Database migration files
│   └── seeders/             # Database seeder files
├── public/
│   ├── index.php            # Entry point aplikasi
│   ├── build/               # Compiled assets (CSS, JS)
│   ├── images/              # Static images
│   └── uploads/             # User uploaded files
├── resources/
│   ├── css/                 # Tailwind CSS files
│   ├── js/                  # JavaScript files (Alpine.js)
│   └── views/               # Blade template files
├── routes/
│   ├── web.php              # Web routes
│   ├── auth.php             # Auth routes (login, register)
│   └── console.php          # Console commands
├── storage/                 # Storage files (logs, sessions, dll)
├── tests/                   # Test files (Feature, Unit)
├── vendor/                  # Composer dependencies (jangan edit)
├── .env                     # Environment variables (di-generate dari .env.example)
├── artisan                  # Laravel CLI
├── composer.json            # Composer configuration
├── package.json             # NPM configuration
├── vite.config.js           # Vite build configuration
├── tailwind.config.js       # Tailwind CSS configuration
├── postcss.config.js        # PostCSS configuration
└── README.md                # Dokumentasi project
```

---

## 🎯 Fitur & Capabilities

### **User Management**
- ✓ User registration & authentication
- ✓ User roles dan permissions
- ✓ Profile management
- ✓ Social authentication (via Socialite)

### **Music Management**
- ✓ Upload & manage musik
- ✓ Music gallery dengan preview
- ✓ Music metadata (artist, album, genre)
- ✓ Music videos
- ✓ Music comments & interactions

### **Content Management**
- ✓ Articles & blog posts
- ✓ Article categories & tags
- ✓ Article blocks (flexible content structure)
- ✓ Comments system

### **E-Commerce**
- ✓ Product catalog
- ✓ Shopping cart
- ✓ Order management
- ✓ Order items tracking
- ✓ Payment integration

### **Media Management**
- ✓ Image uploads (via Cloudinary)
- ✓ Gallery management
- ✓ Comic images
- ✓ Video hosting

### **Admin Features**
- ✓ Dashboard (untuk admin)
- ✓ Content management
- ✓ User management
- ✓ Order tracking

---

## 🤝 Kontribusi

Kami sangat menerima kontribusi dari komunitas! Untuk berkontribusi:

1. **Fork** repository ini
2. **Buat branch** untuk fitur baru:
   ```bash
   git checkout -b feature/nama-fitur
   ```
3. **Commit** perubahan Anda:
   ```bash
   git commit -m "Menambahkan fitur: nama fitur"
   ```
4. **Push** ke branch:
   ```bash
   git push origin feature/nama-fitur
   ```
5. **Buat Pull Request** di GitHub

### **Coding Standards**
- Ikuti Laravel best practices
- Gunakan PSR-12 coding standard
- Tambahkan tests untuk fitur baru
- Update dokumentasi jika diperlukan

---

## 📄 Lisensi

Project ini dilisensikan di bawah [MIT License](LICENSE).

---

## 📞 Support & Contact

Jika ada pertanyaan atau butuh bantuan:

- **Issues**: Buat issue di GitHub untuk bug reports atau feature requests
- **Email**: contact@aanaya.music (ganti dengan email nyata)
- **Social**: Follow Aanaya di Instagram @aanayamusic

---

## 🚀 Quick Start Cheatsheet

```bash
# 1. Clone
git clone https://github.com/username/aanaya-web.git && cd aanaya-web

# 2. Install dependencies
composer install
npm install

# 3. Setup .env
cp .env.example .env
# Edit .env dengan konfigurasi database Anda

# 4. Generate key
php artisan key:generate

# 5. Setup database
php artisan migrate

# 6. Build assets
npm run build

# 7. Run application
composer run dev

# Buka http://localhost:8000 di browser
```

---

**Happy coding! 🎵✨**
