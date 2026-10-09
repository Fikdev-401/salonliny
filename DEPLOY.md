# Deployment Guide - Hostinger

## Informasi Subdomain

- **Domain**: `https://gasing.fun`
- **Lokasi folder**: `public_html/gasing.fun/`
- **Document root**: `public_html/gasing.fun/public/`

## Struktur Upload

```text
public_html/
`-- gasing.fun/
    |-- app/
    |-- bootstrap/
    |-- config/
    |-- database/
    |-- public/          <-- document root
    |-- resources/
    |-- routes/
    |-- storage/
    |-- vendor/
    |-- .env.hostinger
    |-- artisan
    `-- ...
```

## Persiapan PHP

Di hPanel Hostinger, buka **Advanced -> Change PHP Version** lalu aktifkan extension:

- `gd`
- `zip`
- `fileinfo`
- `openssl`
- `pdo_mysql`
- `mbstring` bila tersedia

`ext-gd` wajib untuk PhpSpreadsheet/Laravel Excel.

## Langkah Deployment

```bash
cd public_html/gasing.fun
cp .env.hostinger .env
php artisan key:generate
composer install --optimize-autoloader --no-dev
npm ci
npm run build
php artisan migrate --force
php artisan db:seed --class=ProductionSeeder --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Akun Awal

`ProductionSeeder` membuat akun `superadmin` dengan password sementara acak atau nilai `SUPER_ADMIN_PASSWORD` dari `.env`. Password ditampilkan sekali di output seed dan user wajib mengganti password saat login pertama.

## Permission

```bash
chmod -R 755 public_html/gasing.fun/storage
chmod -R 755 public_html/gasing.fun/bootstrap/cache
chmod -R 775 public_html/gasing.fun/public/storage
```

## Troubleshooting

### 403 Forbidden

Pastikan document root mengarah ke `public_html/gasing.fun/public` dan permission folder sudah benar.

### 500 Error

```bash
tail -f public_html/gasing.fun/storage/logs/laravel.log
```

### Database Connection Failed

Pastikan database sudah dibuat, kredensial `.env` benar, dan extension `pdo_mysql` aktif.

### Composer Install Gagal

Jalankan:

```bash
composer check-platform-reqs
```

Jika `ext-gd` missing, aktifkan GD di PHP Hostinger sebelum mengulang `composer install`.
