# Fix Error 500 Setelah Login

## Langkah 1: Cek Error Log
Jalankan di Terminal Hostinger:
```bash
cd ~/public_html/gasing.fun
tail -30 storage/logs/laravel.log
```

## Langkah 2: Fix Permissions (PENTING!)
Jalankan di Terminal Hostinger:
```bash
cd ~/public_html/gasing.fun

# Set permission untuk storage dan cache
chmod -R 777 storage
chmod -R 777 bootstrap/cache
chmod 644 bootstrap/*.php
chmod 755 bootstrap

# Buat folder yang diperlukan jika belum ada
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p storage/app/public

# Set permission lagi
chmod -R 777 storage
```

## Langkah 3: Clear Cache
```bash
cd ~/public_html/gasing.fun
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Langkah 4: Fix Storage Link
```bash
cd ~/public_html/gasing.fun
rm -rf public/storage
php artisan storage:link
```

## Langkah 5: Check .env File
Pastikan file `.env` ada dan isinya benar:
```
APP_KEY=base64:xxxxxxxxxx
APP_DEBUG=false
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=CHANGE_ME_DB_NAME
DB_USERNAME=CHANGE_ME_DB_USER
DB_PASSWORD=CHANGE_ME_DB_PASSWORD
```

Jika `APP_KEY` kosong, generate dengan:
```bash
cd ~/public_html/gasing.fun
php artisan key:generate --force
```

## Langkah 6: Re-cache
```bash
cd ~/public_html/gasing.fun
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Script Auto-Fix
Copy dan paste semua command di bawah ini sekaligus:

```bash
cd ~/public_html/gasing.fun
chmod -R 777 storage
chmod -R 777 bootstrap/cache
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p storage/app/public
chmod -R 777 storage
php artisan cache:clear
php artisan config:clear
php artisan view:clear
rm -rf public/storage
php artisan storage:link
php artisan config:cache
php artisan view:cache
echo "FIX COMPLETE!"
```

## Mode Debug (Lihat Error Detail)

Jika ingin melihat error detail, edit file `.env`:
```
APP_DEBUG=true
```

Lalu refresh halaman dashboard. Setelah melihat error, ubah kembali:
```
APP_DEBUG=false
```

## Penyebab Paling Umum:

1. **Permission denied** pada folder `storage/` atau `bootstrap/cache/`
2. **Folder storage/framework/cache/data** tidak ada
3. **Storage symlink** tidak berfungsi
4. **APP_KEY** belum di-generate

Setelah menjalankan langkah di atas, refresh browser (Ctrl+F5).
