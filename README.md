# 🛒 E-Commerce Enterprise (Laravel + Apache Kafka + Docker +Mailpit)

Proyek ini adalah sistem e-commerce berskala enterprise yang mendemonstrasikan integrasi arsitektur asinkronus (_Event-Driven Architecture_). Aplikasi ini menggunakan **Apache Kafka** sebagai _message broker_ untuk menangani antrean pemotongan stok secara cepat, serta **Mailpit** sebagai _local SMTP server_ untuk menangani simulasi pengiriman email tagihan secara otomatis tanpa membebani performa web utama.

---

## 🏗️ Alur Arsitektur Sistem

1. **User** melakukan _checkout_ produk di halaman Web.
2. **Laravel Controller** melakukan validasi awal, membuat data order dengan status `pending`, lalu melempar _payload_ data ke Apache Kafka pada topik `order-placed` secara asinkronus (cepat).
3. **Kafka Consumer** (`ProcessOrderHandler`) yang berjalan di _background_ menangkap data tersebut, mengunci baris database untuk menghindari _race condition_, memotong stok produk, mengubah status menjadi `Waiting Payment`, dan mengirim email tagihan.
4. **Mailpit** menangkap email tersebut secara lokal sehingga dapat diuji coba tanpa internet.

---

## 🛠️ Prasyarat (Prerequisites)

Sebelum memulai, pastikan laptop Anda sudah terinstal beberapa perangkat lunak berikut:

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Wajib untuk menjalankan MySQL, Kafka, Zookeeper, dan Mailpit)
- PHP >= 8.1
- Composer

---

## 🚀 Langkah Konfigurasi & Instalasi

Ikuti langkah-langkah di bawah ini untuk merakit dan menjalankan aplikasi di komputer lokal Anda:

### 1. Clone Repositori

Buka terminal atau Git Bash Anda, lalu jalankan perintah berikut:

```bash
git clone [https://github.com/USERNAME_ANDA/NAMA_REPOSITORI_ANDA.git](https://github.com/USERNAME_ANDA/NAMA_REPOSITORI_ANDA.git)
cd NAMA_REPOSITORI_ANDA
```

### 2. Instal Dependensi PHP (Vendor)

composer install

### 3. Siapkan File Environment (.env)

APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=[http://127.0.0.1:8000](http://127.0.0.1:8000)

# ==========================================

# KONEKSI DATABASE (Menembak ke MySQL Docker)

# ==========================================

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=root

# ==========================================

# KONEKSI EMAIL (Menembak ke SMTP Mailpit Docker)

# ==========================================

MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

### 4. Generate Application Key

php artisan key:generate

### 5. Jalankan Infrastruktur Docker (Backend)

docker compose up -d

### 6. Jalankan Migrasi Database (Migration)

php artisan migrate

### 7. Jalankan aplikasi

http://localhost:8000/

### 8. Jalankan EMail

http://localhost:8025/

### 9. Running Project

docker compose up -d --build
docker compose exec laravel_app php artisan kafka:consume-orders
