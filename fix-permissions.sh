#!/bin/bash

# Fix permissions for Hostinger
echo "Fixing permissions..."

# Set proper permissions for Laravel directories
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 644 bootstrap/*.php
chmod 755 bootstrap

# Make sure cache directory exists and is writable
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p storage/app/public

# Set writable permissions
chmod -R 777 storage/framework/cache
chmod -R 777 storage/framework/sessions
chmod -R 777 storage/framework/views
chmod -R 777 storage/logs
chmod -R 777 storage/app

echo "Permissions fixed!"
