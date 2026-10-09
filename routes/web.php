<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\GajiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\CabangController;
use App\Http\Controllers\InventarisController;
use App\Http\Controllers\JenisProdukController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\AbsensiBarcodeController;

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    // Change Password
    Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('password.change');
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    Route::middleware(['password.changed'])->group(function () {
    // Dashboard
    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Pelanggan (Admin, Kasir)
    Route::middleware(['role:admin,kasir,super-admin,pimpinan'])->group(function () {
        Route::get('/pelanggan/search', [PelangganController::class, 'search'])->name('pelanggan.search');
        Route::resource('pelanggan', PelangganController::class);
    });

    // Karyawan (Admin, Pimpinan)
    Route::middleware(['role:admin,pimpinan,super-admin'])->group(function () {
        Route::get('/karyawan/search', [KaryawanController::class, 'search'])->name('karyawan.search');
        Route::resource('karyawan', KaryawanController::class);
    });

    // Layanan (Admin)
    Route::middleware(['role:admin,super-admin'])->group(function () {
        Route::get('/layanan/search', [LayananController::class, 'search'])->name('layanan.search');
        Route::resource('layanan', LayananController::class);
    });

    // Produk (Admin, Kasir)
    Route::middleware(['role:admin,kasir,super-admin'])->group(function () {
        Route::get('/produk/search', [ProdukController::class, 'search'])->name('produk.search');
        Route::resource('produk', ProdukController::class);
    });

    // Pembelian (Admin, Kasir)
    Route::middleware(['role:admin,kasir,super-admin'])->group(function () {
        Route::resource('pembelian', PembelianController::class);
    });

    // Transaksi (Kasir, Admin)
    Route::middleware(['role:kasir,admin,super-admin'])->group(function () {
        Route::get('/transaksi/{transaksi}/cetak', [TransaksiController::class, 'cetakStruk'])->name('transaksi.cetak');
        Route::get('/transaksi/{transaksi}/struk', [TransaksiController::class, 'cetakStruk'])->name('transaksi.struk');
        Route::get('/transaksi/layanan/{id}', [TransaksiController::class, 'getLayananDetail'])->name('transaksi.layanan.detail');
        Route::get('/transaksi/produk/{id}', [TransaksiController::class, 'getProdukDetail'])->name('transaksi.produk.detail');
        Route::resource('transaksi', TransaksiController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    });

    // Pengeluaran (Kasir, Admin)
    Route::middleware(['role:kasir,admin,super-admin'])->group(function () {
        Route::resource('pengeluaran', PengeluaranController::class);
        Route::get('/pengeluaran/{pengeluaran}/bukti', [PengeluaranController::class, 'downloadBukti'])->name('pengeluaran.bukti');
    });

    // Absensi Barcode Management (Admin Only) - keep static routes before /absensi/{absensi}
    Route::middleware(['role:admin,super-admin'])->group(function () {
        Route::get('/absensi/barcodes', [AbsensiBarcodeController::class, 'index'])->name('absensi.barcodes.index');
        Route::post('/absensi/barcodes/generate', [AbsensiBarcodeController::class, 'generate'])->name('absensi.barcodes.generate');
        Route::post('/absensi/barcodes/bulk-generate', [AbsensiBarcodeController::class, 'bulkGenerate'])->name('absensi.barcodes.bulk-generate');
        Route::post('/absensi/barcodes/print', [AbsensiBarcodeController::class, 'print'])->name('absensi.barcodes.print');
        Route::post('/absensi/barcodes/cleanup', [AbsensiBarcodeController::class, 'cleanup'])->name('absensi.barcodes.cleanup');
        Route::get('/absensi/barcodes/{barcode}/print', [AbsensiBarcodeController::class, 'printSingle'])->name('absensi.barcodes.print.single');
        Route::get('/absensi/barcodes/{barcode}', [AbsensiBarcodeController::class, 'show'])->name('absensi.barcodes.show');
        Route::delete('/absensi/barcodes/{barcode}', [AbsensiBarcodeController::class, 'destroy'])->name('absensi.barcodes.destroy');
    });

    // Absensi Monitoring and manual fallback (Admin Only)
    Route::middleware(['role:admin,super-admin'])->group(function () {
        Route::get('/absensi-monitoring', [AbsensiController::class, 'monitoring'])->name('absensi.monitoring');
        Route::post('/absensi/check-in', [AbsensiController::class, 'checkIn'])->name('absensi.checkin');
        Route::post('/absensi/check-out', [AbsensiController::class, 'checkOut'])->name('absensi.checkout');
        Route::post('/absensi/{absensi}/verify', [AbsensiController::class, 'verify'])->name('absensi.verify');
    });

    // Absensi (Admin, Karyawan, Kasir)
    Route::middleware(['role:admin,karyawan,kasir,super-admin'])->group(function () {
        Route::get('/absensi/rekap', [AbsensiController::class, 'rekap'])->name('absensi.rekap');
        
        // Barcode Scan Routes
        Route::get('/absensi/scan', [AbsensiController::class, 'scan'])->name('absensi.scan');
        Route::post('/absensi/scan', [AbsensiController::class, 'processScan'])->name('absensi.process-scan');
        Route::get('/absensi/my-attendance', [AbsensiController::class, 'myAttendance'])->name('absensi.my-attendance');
        Route::get('/absensi/today-barcode', [AbsensiController::class, 'getTodayBarcode'])->name('absensi.today-barcode');
        
        Route::resource('absensi', AbsensiController::class)->except(['create', 'show', 'edit']);
        Route::get('/absensi/{absensi}', [AbsensiController::class, 'show'])->name('absensi.show');
    });

    // Gaji (Admin, Pimpinan)
    Route::middleware(['role:admin,pimpinan,super-admin'])->group(function () {
        Route::get('/gaji/preview', [GajiController::class, 'preview'])->name('gaji.preview');
        Route::get('/gaji/{gaji}/slip', [GajiController::class, 'slipGaji'])->name('gaji.slip');
        Route::post('/gaji/{gaji}/approve', [GajiController::class, 'approve'])->name('gaji.approve');
        Route::post('/gaji/{gaji}/bayar', [GajiController::class, 'bayar'])->name('gaji.bayar');
        Route::resource('gaji', GajiController::class);
    });

    // Laporan (Pimpinan, Admin)
    Route::middleware(['role:pimpinan,admin,super-admin'])->group(function () {
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/pendapatan', [LaporanController::class, 'pendapatan'])->name('laporan.pendapatan');
        Route::get('/laporan/pengeluaran', [LaporanController::class, 'pengeluaran'])->name('laporan.pengeluaran');
        Route::get('/laporan/transaksi', [LaporanController::class, 'transaksi'])->name('laporan.transaksi');
        Route::get('/laporan/absensi', [LaporanController::class, 'absensi'])->name('laporan.absensi');
        Route::get('/laporan/gaji', [LaporanController::class, 'gaji'])->name('laporan.gaji');
        Route::get('/laporan/layanan', [LaporanController::class, 'layanan'])->name('laporan.layanan');
        Route::get('/laporan/performa-karyawan', [LaporanController::class, 'performaKaryawan'])->name('laporan.performa');
        Route::get('/laporan/performa-karyawan/{karyawan}/detail', [LaporanController::class, 'performaKaryawanDetail'])->name('laporan.performa.detail');
        Route::get('/laporan/performa/{karyawan}/detail', [LaporanController::class, 'performaKaryawanDetail'])->name('laporan.performa.detail.alt');
        Route::get('/laporan/inventaris', [LaporanController::class, 'inventaris'])->name('laporan.inventaris');
        Route::get('/laporan/laba-rugi', [LaporanController::class, 'labaRugi'])->name('laporan.laba-rugi');
    });

    // Pengaturan (Super Admin, Admin)
    Route::middleware(['role:super-admin,admin'])->group(function () {
        Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
        Route::post('/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
        Route::put('/pengaturan/jenis-produk/{jenisProduk}', [JenisProdukController::class, 'update'])->name('pengaturan.jenis-produk.update');
        Route::resource('inventaris', InventarisController::class)->parameters(['inventaris' => 'inventaris']);
    });
    
    // Cabang Management (Super Admin Only)
    Route::middleware(['role:super-admin'])->group(function () {
        Route::resource('cabang', CabangController::class)->except(['show']);
    });
    
    // User Management (Super Admin only)
    Route::middleware(['role:super-admin'])->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.reset-password');
    });
    });
});
