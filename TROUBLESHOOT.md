# Troubleshooting Error 500

## Penyebab Umum Error 500

### 1. Permission Issues (Paling Umum)

Jalankan di terminal Hostinger:
```bash
cd ~/public_html/gasing.fun
chmod -R 777 storage
chmod -R 777 bootstrap/cache
chmod 644 bootstrap/*.php
```

### 2. Storage Directory Tidak Ada

Buat folder yang diperlukan:
```bash
cd ~/public_html/gasing.fun
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p storage/app/public
chmod -R 777 storage
```

### 3. APP_KEY Belum Di-generate

```bash
cd ~/public_html/gasing.fun
php artisan key:generate --force
```

### 4. Cache Error

Clear dan re-cache:
```bash
cd ~/public_html/gasing.fun
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Database Error

Cek koneksi database:
```bash
cd ~/public_html/gasing.fun
php artisan migrate:status
```

Jika error, cek file `.env`:
- Pastikan `DB_DATABASE=CHANGE_ME_DB_NAME`
- Pastikan `DB_USERNAME=CHANGE_ME_DB_USER`
- Pastikan `DB_PASSWORD=CHANGE_ME_DB_PASSWORD`

### 6. Storage Link Error

Jalankan ulang:
```bash
cd ~/public_html/gasing.fun
rm -f public/storage
php artisan storage:link
```

## Check Error Log

```bash
# Laravel log
tail -50 ~/public_html/gasing.fun/storage/logs/laravel.log

# Server error log (jika ada akses)
tail -50 /var/log/apache2/error.log
```

## Mode Debug (Untuk Melihat Error Detail)

Edit file `.env`:
```
APP_DEBUG=true
LOG_LEVEL=debug
```

⚠️ Jangan lupa matikan debug mode setelah perbaikan:
```
APP_DEBUG=false
```

## Quick Fix Script

Jalankan ini di terminal Hostinger:
```bash
cd ~/public_html/gasing.fun

# Fix permissions
chmod -R 777 storage
chmod -R 777 bootstrap/cache
chmod 644 bootstrap/*.php

# Create directories
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p storage/app/public

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Re-cache
php artisan config:cache
php artisan view:cache

echo "Fix complete!"
```

## Jika Masih Error

1. Hapus folder `bootstrap/cache/*.php`
2. Hapus folder `storage/framework/cache/data/*`
3. Hapus folder `storage/framework/views/*`
4. Refresh browser

## Contact Support

Jika masih bermasalah, cek:
1. PHP Version (harus 8.2+)
2. Extension PHP (mbstring, openssl, pdo, tokenizer, xml)
3. File `.env` sudah benar
