# Sistem Manajemen Salon & Barber

Sistem manajemen lengkap untuk bisnis salon dan barber dengan fitur transaksi multi-pekerja, harga fleksibel, multi-cabang, absensi barcode/foto, gaji, dan laporan.

## Fitur Utama

### Role Pengguna
- **Super Admin**: akses penuh seluruh cabang dan konfigurasi.
- **Pimpinan**: melihat laporan dan menyetujui gaji.
- **Admin**: mengelola operasional cabang, master data, absensi, pembelian, dan pembayaran gaji.
- **Kasir**: mencatat transaksi dan pembayaran sesuai cabang.
- **Karyawan**: melihat absensi pribadi dan melakukan scan barcode.

### Modul
- Pelanggan, karyawan, layanan, produk, jenis produk, cabang, inventaris.
- Transaksi multi-pekerja dengan produk/layanan, harga fleksibel, dan insentif snapshot.
- Absensi barcode dengan foto wajib, GPS opsional, dan flag verifikasi saat lokasi kosong.
- Gaji dengan alur eksplisit: `draft -> disetujui -> dibayar`.
- Laporan pendapatan, pengeluaran, transaksi, absensi, gaji, layanan, performa karyawan, inventaris, dan laba rugi.

## Teknologi

- Laravel 12
- MySQL atau SQLite
- Bootstrap 5, Select2, Chart.js, Vite
- DomPDF dan Maatwebsite Excel

## Instalasi Lokal

```bash
cd salon-barber
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
npm run build
php artisan serve
```

Pastikan PHP CLI/server mengaktifkan extension yang dibutuhkan, terutama `gd`, `zip`, `fileinfo`, `openssl`, dan driver database. `phpoffice/phpspreadsheet` mensyaratkan `ext-gd`.

## Login Seed Lokal

Seeder development dapat membuat akun demo dengan password `password`. Untuk seed produksi (`ProductionSeeder`), superadmin dibuat dengan password sementara acak atau nilai `SUPER_ADMIN_PASSWORD`, lalu wajib mengganti password saat login pertama.

## Struktur Folder Penting

```text
salon-barber/
|-- app/
|   |-- Http/Controllers/
|   |-- Models/
|   `-- Services/
|-- database/
|   |-- migrations/
|   `-- seeders/
|-- resources/
|   `-- views/
|-- routes/
|   `-- web.php
`-- storage/
    `-- app/public/
```

## Verifikasi

```bash
php artisan test
composer audit
npm audit --audit-level=moderate
npm run build
```

## Lisensi

Private - Untuk kebutuhan internal perusahaan.
