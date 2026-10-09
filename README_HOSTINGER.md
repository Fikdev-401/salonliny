# Deploy ke Hostinger - gasing.fun (Subdomain)

## Informasi Penting
- **Domain**: https://gasing.fun
- **Lokasi Upload**: `public_html/gasing.fun/`
- **Document Root**: `public_html/gasing.fun/public/`

## Langkah Deploy

### 1. Upload File
Upload semua file ke folder `public_html/gasing.fun/` di Hostinger.

### 2. Setting Document Root
Di Hostinger hPanel:
1. Pergi ke menu **Domains** → **Subdomains**
2. Pilih subdomain `gasing.fun`
3. Set **Document Root** ke: `public_html/gasing.fun/public`
4. Save

### 3. Jalankan Command via Terminal Hostinger

```bash
cd ~/public_html/gasing.fun

# Copy environment
cp .env.hostinger .env

# Generate key
php artisan key:generate

# Install dependencies (jika composer tersedia)
composer install --optimize-autoloader --no-dev

# Atau upload folder vendor dari lokal jika composer tidak tersedia

# Run migrations
php artisan migrate --force

# Seed database
php artisan db:seed --class=HostingerDatabaseSeeder --force

# Storage link
php artisan storage:link

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4. Set Permission
```bash
chmod -R 755 ~/public_html/gasing.fun/storage
chmod -R 755 ~/public_html/gasing.fun/bootstrap/cache
chmod -R 775 ~/public_html/gasing.fun/public/storage
```

## Struktur Folder yang Benar

```
public_html/
└── gasing.fun/                 <-- Upload ke sini
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── public/                 <-- Document root
    │   ├── index.php
    │   └── .htaccess
    ├── resources/
    ├── routes/
    ├── storage/
    ├── vendor/
    ├── .env
    ├── .env.hostinger
    ├── artisan
    └── ...
```

## Jika Document Root Tidak Bisa Diubah

Jika Hostinger tidak mengizinkan mengubah document root:

1. Pastikan file `.htaccess` ada di `public_html/gasing.fun/.htaccess`
2. File `.htaccess` akan otomatis redirect ke folder `public/`

## Login Default

| Role | Username | Password |
|------|----------|----------|
| Super Admin | superadmin | password |
| Pimpinan | pimpinan | password |
| Admin | admin | password |
| Kasir | kasir | password |
| Karyawan | karyawan | password |

## Membersihkan Data

```bash
cd ~/public_html/gasing.fun
php artisan db:seed --class=CleanDataSeeder --force
```

## Troubleshooting

### Error 500
Check log: `tail -f ~/public_html/gasing.fun/storage/logs/laravel.log`

### Error 403
Set permission:
```bash
chmod -R 755 ~/public_html/gasing.fun
chmod -R 777 ~/public_html/gasing.fun/storage
chmod -R 777 ~/public_html/gasing.fun/bootstrap/cache
```

### Database Error
Pastikan database sudah dibuat dan credentials benar di file `.env`:
```
DB_HOST=localhost
DB_DATABASE=CHANGE_ME_DB_NAME
DB_USERNAME=CHANGE_ME_DB_USER
DB_PASSWORD=CHANGE_ME_DB_PASSWORD
```
