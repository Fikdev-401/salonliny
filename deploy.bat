@echo off
echo ===========================================
echo    DEPLOY SALON BARBER TO HOSTINGER
echo ===========================================
echo.

:: Configuration
set HOST=147.79.79.148
set USER=u1351709
set LOCAL_PATH=D:\Proyek\salon-barber\salon-barber
set REMOTE_PATH=/home/u1351709/public_html/gasing.fun

:: Run migrations first
echo [1/4] Running migrations...
cd %LOCAL_PATH%
php artisan migrate --force

:: Clear caches
echo [2/4] Clearing caches...
php artisan config:clear
php artisan cache:clear
php artisan view:clear

:: Optimize
echo [3/4] Optimizing...
php artisan config:cache
php artisan route:cache

:: Upload files via rsync or SCP
echo [4/4] Uploading files to Hostinger...
echo Please manually upload files using FileZilla or cPanel File Manager
echo.
echo Source: %LOCAL_PATH%
echo Destination: %REMOTE_PATH%
echo.
echo Don't forget to:
echo 1. Upload all files except .git, node_modules, vendor
echo 2. Run 'php artisan storage:link' on server
echo 3. Set permissions: chmod -R 777 storage bootstrap/cache
echo.

pause
