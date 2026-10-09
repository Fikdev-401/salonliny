#!/bin/bash

# Deployment Script for Hostinger Subdomain
# Run this on Hostinger server in directory: public_html/gasing.fun

echo "=== Deployment Salon Barber ke Hostinger ==="
echo "Subdomain: gasing.fun"
echo "Location: public_html/gasing.fun"
echo ""

# Check if in correct directory
if [ ! -f "artisan" ]; then
    echo "Error: artisan file not found!"
    echo "Please run this script from public_html/gasing.fun directory"
    exit 1
fi

# Step 1: Copy environment
echo "[1/7] Setup environment..."
cp .env.hostinger .env

# Step 2: Generate key
echo "[2/7] Generate application key..."
php artisan key:generate

# Step 3: Install dependencies
echo "[3/7] Install dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction

# Step 4: Run migrations
echo "[4/7] Run database migrations..."
php artisan migrate --force

# Step 5: Seed database
echo "[5/7] Seed database..."
php artisan db:seed --class=HostingerDatabaseSeeder --force

# Step 6: Storage link
echo "[6/7] Create storage link..."
php artisan storage:link

# Step 7: Cache
echo "[7/7] Cache configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "=== Deployment Selesai! ==="
echo "Domain: https://gasing.fun"
echo ""
echo "Default Login:"
echo "  Super Admin: superadmin / password"
echo "  Pimpinan: pimpinan / password"
echo "  Admin: admin / password"
echo "  Kasir: kasir / password"
echo "  Karyawan: karyawan / password"
