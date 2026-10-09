#!/bin/bash

# Script untuk membersihkan semua data kecuali akun user
echo "Membersihkan data..."
php artisan db:seed --class=CleanDataSeeder --force
echo "Data berhasil dibersihkan!"
