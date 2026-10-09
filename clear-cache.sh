#!/bin/bash
cd /home/u1351709/public_html/gasing.fun
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan config:clear
echo "Cache cleared successfully!"
