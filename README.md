# Cipta Grafika Printing Management

Aplikasi manajemen percetakan Cipta Grafika.

## Teknologi

- **Laravel:** 13.26.1 (versi terkunci pada `composer.lock`)
- **PHP:** 8.3 atau lebih baru
- **Database:** PostgreSQL
- **Frontend:** Node.js, npm, dan Vite

## Persyaratan

Sebelum menjalankan aplikasi, pasang:

- PHP 8.3+ dengan ekstensi `pdo_pgsql`
- Composer
- PostgreSQL
- Node.js dan npm

## Instalasi dan menjalankan aplikasi

Clone repository dan masuk ke folder project:

```bash
git clone <URL_REPOSITORY>
cd cipta-grafika-printing-management
```

Pasang dependency PHP:

```bash
composer install
```

### Inisialisasi file `.env`

File `.env` tidak disimpan di GitHub karena berisi konfigurasi lokal dan bisa memuat kredensial. Setelah clone, buat salinannya dari file contoh:

```bash
cp .env.example .env
```

Buka `.env` di editor, lalu sesuaikan konfigurasi aplikasi dan koneksi PostgreSQL. Contoh konfigurasi database:

```dotenv
APP_NAME="Cipta Grafika Printing Management"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=cipta_grafika
DB_USERNAME=postgres
DB_PASSWORD=password_postgresql_anda
```

Sesuaikan `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` dengan PostgreSQL yang Anda gunakan. Buat database dengan nama yang sama seperti nilai `DB_DATABASE` sebelum menjalankan migrasi. Contoh menggunakan `psql`:

```sql
CREATE DATABASE cipta_grafika;
```

Setelah konfigurasi database siap, buat application key dan jalankan migrasi:

```bash
php artisan key:generate
php artisan migrate
```

Pasang dependency frontend dan buat aset:

```bash
npm install
npm run build
```

Jalankan aplikasi dalam mode development:

```bash
composer run dev
```

Ikuti alamat lokal yang ditampilkan di terminal.

## Menjalankan test

```bash
composer test
```

## Catatan

- Jangan unggah file `.env` ke GitHub. File tersebut dibuat lokal dari `.env.example`, yang memang disimpan di repository sebagai template.
- `composer run setup` menjalankan migrasi secara otomatis. Untuk instalasi PostgreSQL baru, ikuti langkah di atas agar `.env` dan database PostgreSQL sudah dikonfigurasi sebelum migrasi dijalankan.
