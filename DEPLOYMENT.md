# Deployment Instructions

## Pre-Deployment Setup

### 1. Database Cleanup (Before Going Live)

Sebelum website live, jalankan command untuk membersihkan semua data testing:

```bash
# Cleanup database (keep only superadmin) - USE --force
php artisan db:cleanup --force

# OR run full production setup
php artisan app:setup-production
```

### 2. Default Login

Setelah cleanup, login dengan:
- **Username:** superadmin
- **Password:** password

**⚠️ PENTING:** Ganti password default setelah login pertama!

---

## New Features

### 1. Kelola User Menu (Super Admin Only)

Super Admin sekarang memiliki menu **"Kelola User"** di sidebar untuk:
- Membuat akun Admin
- Membuat akun Pimpinan  
- Membuat akun Kasir
- Reset password user
- Edit/Delete user

### 2. Fixed Pengaturan Menu

Menu **Pengaturan** sekarang muncul dengan benar untuk Super Admin dan Admin.

---

## Hostinger Deployment Steps

### 1. Upload Files

Upload semua file ke `public_html/gasing.fun/` (tanpa folder `salon-barber/`)

### 2. Set Document Root

Pastikan document root mengarah ke folder `public`:
```
public_html/gasing.fun/public
```

### 3. Set Permissions

```bash
chmod -R 777 storage bootstrap/cache
```

### 4. Create Storage Link

```bash
php artisan storage:link
```

### 5. Run Production Setup

```bash
php artisan app:setup-production
```

### 6. Optimize

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Database Tables (After Cleanup)

Data yang tersisa setelah cleanup:
- ✅ 1 User: superadmin
- ✅ 5 Roles: super-admin, admin, pimpinan, kasir, karyawan
- ✅ 1 Cabang: Cabang Utama
- ❌ Semua data transaksi, pelanggan, karyawan, produk, layanan = TERHAPUS

---

## Commands Reference

```bash
# Cleanup database only - USE --force flag
php artisan db:cleanup --force

# Full production setup
php artisan app:setup-production

# Reset to factory defaults
php artisan migrate:fresh --seed
```
