# Cipta Grafika Printing Management

Aplikasi manajemen percetakan Cipta Grafika.

## Teknologi

- **Laravel:** 12.69.3 (versi terkunci pada `composer.lock`)
- **PHP:** 8.2 atau lebih baru
- **Database:** PostgreSQL
- **Frontend:** Node.js, npm, dan Vite

## Persyaratan

Sebelum menjalankan aplikasi, pasang:

- PHP 8.2+ dengan ekstensi `pdo_pgsql` dan `zip`
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

Pastikan ekstensi `pdo_pgsql` dan `zip` aktif pada PHP yang digunakan Composer. Ekstensi `zip` diperlukan untuk dependency spreadsheet.

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

## Kode master diskon

Form create dan edit diskon mewajibkan kode unik sepanjang 3-30 karakter.
Kode hanya boleh berisi huruf ASCII, angka, tanda hubung (`-`), atau garis bawah (`_`), tanpa spasi.
Spasi di awal/akhir dibuang dan kode disimpan dalam huruf besar. Duplikasi dicek tanpa membedakan huruf besar/kecil;
saat edit, kode milik diskon itu sendiri tetap diizinkan. Kode ini merupakan identitas master, bukan perubahan aturan penerapan diskon.
Halaman detail diskon menampilkan kode tersebut sebagai **Kode Promo**, di bawah nama diskon.

Detail produk web user menampilkan nama, kode, persentase, dan periode WIB promo yang berstatus Active,
sedang berlaku, serta cocok dengan pasangan mesin-kategori produk. Galeri tanpa pilihan mesin tidak menampilkan promo;
produk display tanpa konfigurasi mesin menggunakan mesin Large Format jika master tersebut tersedia.
Estimator web user menggunakan input **Kode Promo** (Terapkan/Hapus), menggantikan diskon manual user.
Server mengecek ulang status, periode WIB, dan pasangan mesin-kategori pada setiap perhitungan;
persentase hanya diambil dari master, bukan nilai kiriman client. Potongan dihitung dari subtotal
(material, laminasi, dan komponen tambahan), dibulatkan ke 2 desimal, lalu dikurangkan dari total.
Rincian estimasi dan pesan pesanan mencantumkan kode, persentase, serta nominal potongan.
Khusus estimasi yang menggunakan promo, total setelah diskon dibulatkan ke atas ke kelipatan Rp 1.000.
Selisih pembulatan ditampilkan terpisah dalam rincian dan pesan pesanan; nominal diskon tetap sesuai master.
Total yang sudah kelipatan Rp 1.000 atau nol tidak ditambah lagi. Estimasi tanpa promo tidak berubah.
Jika estimasi dasar tanpa promo sudah melakukan pembulatan, selisih antara subtotal dan total akhir
juga ditampilkan sebagai pembulatan dalam rincian dan pesan pesanan, tanpa menghitung ulang total.
Kode tidak valid menampilkan error dan menonaktifkan kelanjutan pesanan sampai diperbaiki/dihapus.
Diskon manual pada estimator admin tetap tidak berubah.
