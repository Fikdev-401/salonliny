# UAT Checklist Menu dan Akses Route per Role

Dokumen ini dipakai untuk smoke test manual semua menu utama per role.

## Prasyarat

- PHP minimal `8.2`
- `composer install`
- `.env.testing`/`.env` database siap
- Jalankan migrasi dan seed data

```bash
php artisan migrate:fresh --seed
```

## Akun Uji Minimum

- `super-admin`
- `admin`
- `kasir`
- `pimpinan`
- `karyawan`

Catatan:
- Untuk validasi `Absensi Saya`, akun sebaiknya sudah terhubung ke data `karyawan`.
- Jika belum terhubung, halaman `Scan Barcode` tetap harus tampil tanpa error (menampilkan informasi akun belum terhubung).

## Matriks Menu Sidebar

Keterangan:
- `OK` = menu wajib tampil dan dapat dibuka (HTTP 200)
- `NO` = menu tidak boleh tampil untuk role tersebut

| Menu / Route | Super Admin | Admin | Kasir | Pimpinan | Karyawan |
| --- | --- | --- | --- | --- | --- |
| Dashboard (`dashboard`) | OK | OK | OK | OK | OK |
| Pelanggan (`pelanggan.index`) | OK | OK | OK | OK | NO |
| Karyawan (`karyawan.index`) | OK | OK | NO | OK | NO |
| Layanan (`layanan.index`) | OK | OK | NO | NO | NO |
| Produk (`produk.index`) | OK | OK | OK | NO | NO |
| Pembelian (`pembelian.index`) | OK | OK | OK | NO | NO |
| Inventaris (`inventaris.index`) | OK | OK | NO | NO | NO |
| Transaksi (`transaksi.index`) | OK | OK | OK | NO | NO |
| Pengeluaran (`pengeluaran.index`) | OK | OK | OK | NO | NO |
| Absensi (`absensi.index`) | OK | OK | OK | NO | OK |
| Scan Barcode (`absensi.scan`) | OK | OK | OK | NO | OK |
| Absensi Saya (`absensi.my-attendance`) | OK | OK | OK | NO | OK |
| Monitoring Absensi (`absensi.monitoring`) | OK | OK | NO | NO | NO |
| Kelola Barcode (`absensi.barcodes.index`) | OK | OK | NO | NO | NO |
| Gaji & Insentif (`gaji.index`) | OK | OK | NO | OK | NO |
| Laporan (`laporan.index`) | OK | OK | NO | OK | NO |
| Laba Rugi (`laporan.laba-rugi`) | OK | OK | NO | OK | NO |
| Pengaturan (`pengaturan.index`) | OK | OK | NO | NO | NO |
| Kelola User (`users.index`) | OK | NO | NO | NO | NO |

## Checklist Eksekusi Per Role

Untuk setiap role:

1. Login ke aplikasi.
2. Pastikan hanya menu yang bertanda `OK` yang tampil di sidebar.
3. Klik satu per satu menu `OK`.
4. Verifikasi:
   - Halaman terbuka tanpa error 403/500.
   - Komponen utama halaman muncul (judul + tabel/form utama).
5. Coba akses URL menu bertanda `NO` langsung dari address bar.
6. Verifikasi:
   - Akses ditolak (redirect kembali + flash error "Anda tidak memiliki akses ke halaman ini.").

## Checklist Aksi Kritis Tambahan

- Laporan Transaksi:
  - Role `admin/super-admin` melihat tombol cetak struk.
  - Role `pimpinan` tidak melihat tombol cetak struk.
- Absensi Detail:
  - Role `admin/super-admin` dapat melihat form verifikasi.
  - Role non-admin tidak melihat form verifikasi.
- Gaji Detail:
  - Link detail pengeluaran hanya muncul sebagai link untuk `admin/super-admin`.
  - Role `pimpinan` melihat nomor referensi tanpa link ke halaman pengeluaran.
