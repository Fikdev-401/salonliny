@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Laporan</h4>
</div>

<div class="row g-4">
    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="bi bi-graph-up-arrow text-primary" style="font-size: 3rem;"></i>
                <h5 class="mt-3">Laporan Pendapatan</h5>
                <p class="text-muted">Lihat ringkasan pendapatan harian dan rentang waktu tertentu.</p>
                <a href="{{ route('laporan.pendapatan') }}" class="btn btn-primary">Lihat Laporan</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="bi bi-graph-down-arrow text-danger" style="font-size: 3rem;"></i>
                <h5 class="mt-3">Laporan Pengeluaran</h5>
                <p class="text-muted">Lihat semua pengeluaran operasional per periode.</p>
                <a href="{{ route('laporan.pengeluaran') }}" class="btn btn-danger">Lihat Laporan</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="bi bi-receipt text-success" style="font-size: 3rem;"></i>
                <h5 class="mt-3">Laporan Transaksi</h5>
                <p class="text-muted">Daftar lengkap transaksi dengan detail pelanggan.</p>
                <a href="{{ route('laporan.transaksi') }}" class="btn btn-success">Lihat Laporan</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="bi bi-calendar-check text-info" style="font-size: 3rem;"></i>
                <h5 class="mt-3">Laporan Absensi</h5>
                <p class="text-muted">Rekap kehadiran karyawan per bulan.</p>
                <a href="{{ route('laporan.absensi') }}" class="btn btn-info text-white">Lihat Laporan</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="bi bi-cash-stack text-warning" style="font-size: 3rem;"></i>
                <h5 class="mt-3">Laporan Gaji</h5>
                <p class="text-muted">Rekap gaji dan insentif karyawan per periode.</p>
                <a href="{{ route('laporan.gaji') }}" class="btn btn-warning text-dark">Lihat Laporan</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="bi bi-scissors text-secondary" style="font-size: 3rem;"></i>
                <h5 class="mt-3">Laporan Layanan</h5>
                <p class="text-muted">Statistik layanan yang paling sering dipesan.</p>
                <a href="{{ route('laporan.layanan') }}" class="btn btn-secondary">Lihat Laporan</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="bi bi-person-badge text-dark" style="font-size: 3rem;"></i>
                <h5 class="mt-3">Performa Karyawan</h5>
                <p class="text-muted">Rekap layanan, insentif bulanan, dan detail transaksi sumber insentif.</p>
                <a href="{{ route('laporan.performa') }}" class="btn btn-dark">Lihat Laporan</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="bi bi-archive text-primary" style="font-size: 3rem;"></i>
                <h5 class="mt-3">Laporan Inventaris</h5>
                <p class="text-muted">Audit inventaris salon per periode, status aset, dan jumlah unit.</p>
                <a href="{{ route('laporan.inventaris') }}" class="btn btn-primary">Lihat Laporan</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="bi bi-calculator text-success" style="font-size: 3rem;"></i>
                <h5 class="mt-3">Laporan Laba Rugi</h5>
                <p class="text-muted">Perhitungan laba/rugi dari pendapatan transaksi dan pengeluaran yang sudah tersentralisasi.</p>
                <a href="{{ route('laporan.laba-rugi') }}" class="btn btn-success">Lihat Laporan</a>
            </div>
        </div>
    </div>
</div>
@endsection
