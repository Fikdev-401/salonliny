<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Salon & Barber Management System')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #4e73df;
            --secondary-color: #858796;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fc;
        }
        
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
            z-index: 1000;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.75rem 1.5rem;
            font-size: 0.9rem;
            border-left: 4px solid transparent;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255,255,255,0.1);
            border-left-color: #fff;
        }
        
        .sidebar .nav-link i {
            margin-right: 0.5rem;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 1.5rem;
            min-height: 100vh;
        }
        
        .topbar {
            background: white;
            padding: 1rem 1.5rem;
            margin: -1.5rem -1.5rem 1.5rem -1.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,0.15);
        }
        
        .card {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,0.1);
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #e3e6f0;
            font-weight: 600;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .page-title {
            margin-bottom: 0.25rem;
            font-weight: 700;
            color: #1f2937;
        }

        .page-subtitle {
            margin-bottom: 0;
            color: #6b7280;
            font-size: 0.95rem;
        }

        .page-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .summary-tile {
            background: #fff;
            border-radius: 0.75rem;
            padding: 1rem 1.25rem;
            box-shadow: 0 0.15rem 1.25rem 0 rgba(58,59,69,0.08);
            border: 1px solid #eef2f7;
        }

        .summary-label {
            display: block;
            color: #6b7280;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 0.35rem;
            font-weight: 600;
        }

        .summary-value {
            color: #111827;
            font-size: 1.2rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .summary-value-sm {
            color: #111827;
            font-size: 1rem;
            font-weight: 600;
        }

        .section-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .section-title {
            margin-bottom: 0.2rem;
            font-size: 1rem;
            font-weight: 700;
            color: #1f2937;
        }

        .section-caption {
            margin-bottom: 0;
            color: #6b7280;
            font-size: 0.9rem;
        }

        .soft-panel {
            background: linear-gradient(135deg, #f8fbff 0%, #f3f6fb 100%);
            border: 1px solid #e5eaf3;
            border-radius: 0.75rem;
            padding: 1rem 1.25rem;
        }

        .detail-list {
            display: grid;
            gap: 0.9rem;
        }

        .detail-row {
            display: grid;
            grid-template-columns: minmax(140px, 180px) 1fr;
            gap: 1rem;
            padding-bottom: 0.9rem;
            border-bottom: 1px solid #eef2f7;
        }

        .detail-row:last-child {
            padding-bottom: 0;
            border-bottom: none;
        }

        .detail-label {
            color: #6b7280;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .detail-value {
            color: #111827;
        }

        .table-actions {
            display: flex;
            gap: 0.35rem;
            flex-wrap: wrap;
        }

        .compact-table th,
        .compact-table td {
            vertical-align: middle;
        }

        .status-dot {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
        }

        .status-dot::before {
            content: '';
            width: 0.55rem;
            height: 0.55rem;
            border-radius: 50%;
            background: currentColor;
            opacity: 0.85;
        }
        
        .stat-card {
            border-left: 4px solid;
        }
        
        .stat-card.primary { border-left-color: #4e73df; }
        .stat-card.success { border-left-color: #1cc88a; }
        .stat-card.info { border-left-color: #36b9cc; }
        .stat-card.warning { border-left-color: #f6c23e; }
        .stat-card.danger { border-left-color: #e74a3b; }
        
        .stat-card .stat-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 0.25rem;
        }
        
        .stat-card .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .brand-logo {
            color: white;
            font-size: 1.25rem;
            font-weight: 700;
            text-align: center;
            padding: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            position: sticky;
            top: 0;
            background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
            z-index: 10;
        }
        
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding-bottom: 1rem;
        }
        
        .sidebar-nav::-webkit-scrollbar {
            width: 5px;
        }
        
        .sidebar-nav::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
        }
        
        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 3px;
        }
        
        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.5);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }

            .page-header {
                flex-direction: column;
            }

            .page-actions {
                width: 100%;
                justify-content: flex-start;
            }

            .detail-row {
                grid-template-columns: 1fr;
                gap: 0.35rem;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="brand-logo">
            <i class="bi bi-scissors"></i> SalonBarber
        </div>
        
        <div class="sidebar-nav">
            <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            
            @if(auth()->user()->hasRole(['admin', 'super-admin', 'kasir', 'pimpinan']))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('pelanggan.*') ? 'active' : '' }}" href="{{ route('pelanggan.index') }}">
                    <i class="bi bi-people"></i> Pelanggan
                </a>
            </li>
            @endif
            
            @if(auth()->user()->hasRole(['admin', 'super-admin', 'pimpinan']))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('karyawan.*') ? 'active' : '' }}" href="{{ route('karyawan.index') }}">
                    <i class="bi bi-person-badge"></i> Karyawan
                </a>
            </li>
            @endif
            
            @if(auth()->user()->hasRole(['admin', 'super-admin']))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('layanan.*') ? 'active' : '' }}" href="{{ route('layanan.index') }}">
                    <i class="bi bi-list-check"></i> Layanan
                </a>
            </li>
            @endif
            
            @if(auth()->user()->hasRole(['admin', 'super-admin']))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('produk.*') ? 'active' : '' }}" href="{{ route('produk.index') }}">
                    <i class="bi bi-box-seam"></i> Produk
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('pembelian.*') ? 'active' : '' }}" href="{{ route('pembelian.index') }}">
                    <i class="bi bi-cart-plus"></i> Pembelian
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('inventaris.*') ? 'active' : '' }}" href="{{ route('inventaris.index') }}">
                    <i class="bi bi-archive"></i> Inventaris
                </a>
            </li>
            @endif
            
            @if(auth()->user()->hasRole(['kasir', 'admin', 'super-admin']))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('transaksi.*') ? 'active' : '' }}" href="{{ route('transaksi.index') }}">
                    <i class="bi bi-cash-coin"></i> Transaksi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('pengeluaran.*') ? 'active' : '' }}" href="{{ route('pengeluaran.index') }}">
                    <i class="bi bi-wallet2"></i> Pengeluaran
                </a>
            </li>
            @endif
            
            @if(auth()->user()->hasRole(['admin', 'super-admin', 'karyawan']))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('absensi.index') || request()->routeIs('absensi.rekap') ? 'active' : '' }}" href="{{ route('absensi.index') }}">
                    <i class="bi bi-calendar-check"></i> Absensi
                </a>
            </li>
            @endif
            
            @if(auth()->user()->hasRole(['karyawan', 'kasir', 'admin', 'super-admin']))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('absensi.scan') ? 'active' : '' }}" href="{{ route('absensi.scan') }}">
                    <i class="bi bi-qr-code-scan"></i> Scan Barcode
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('absensi.my-attendance') ? 'active' : '' }}" href="{{ route('absensi.my-attendance') }}">
                    <i class="bi bi-person-check"></i> Absensi Saya
                </a>
            </li>
            @endif
            
            @if(auth()->user()->hasRole(['admin', 'super-admin']))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('absensi.monitoring') ? 'active' : '' }}" href="{{ route('absensi.monitoring') }}">
                    <i class="bi bi-broadcast"></i> Monitoring Absensi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('absensi.barcodes.*') ? 'active' : '' }}" href="{{ route('absensi.barcodes.index') }}">
                    <i class="bi bi-upc"></i> Kelola Barcode
                </a>
            </li>
            @endif
            
            @if(auth()->user()->hasRole(['admin', 'super-admin', 'pimpinan']))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('gaji.*') ? 'active' : '' }}" href="{{ route('gaji.index') }}">
                    <i class="bi bi-cash-stack"></i> Gaji & Insentif
                </a>
            </li>
            @endif
            
            @if(auth()->user()->hasRole(['pimpinan', 'admin', 'super-admin']))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}" href="{{ route('laporan.index') }}">
                    <i class="bi bi-graph-up"></i> Laporan
                </a>
            </li>
            @endif
            
            @if(auth()->user()->hasRole(['pimpinan', 'admin', 'super-admin']))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('laporan.laba-rugi') ? 'active' : '' }}" href="{{ route('laporan.laba-rugi') }}">
                    <i class="bi bi-calculator"></i> Laba Rugi
                </a>
            </li>
            @endif
            
            @if(auth()->user()->hasRole(['super-admin', 'admin']))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('pengaturan.*') || request()->routeIs('cabang.*') ? 'active' : '' }}" href="{{ route('pengaturan.index') }}">
                    <i class="bi bi-sliders"></i> Pengaturan
                </a>
            </li>
            @endif

            @if(auth()->user()->hasRole(['super-admin', 'admin']))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('landing-admin.*') ? 'active' : '' }}" href="{{ route('landing-admin.content') }}">
                    <i class="bi bi-globe"></i> Landing Page
                </a>
            </li>
            @endif
            
            @if(auth()->user()->hasRole('super-admin'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                    <i class="bi bi-person-gear"></i> Kelola User
                </a>
            </li>
            @endif
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Topbar -->
        <div class="topbar d-flex justify-content-between align-items-center">
            <div>
                <button class="btn btn-sm btn-outline-secondary d-md-none" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
            </div>
            <div class="dropdown">
                <a class="dropdown-toggle text-decoration-none text-dark" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle"></i> {{ auth()->user()->nama }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><span class="dropdown-item-text">{{ auth()->user()->role->nama }}</span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('password.change') }}"><i class="bi bi-key"></i> Ganti Password</a></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right"></i> Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Content -->
        @yield('content')
    </main>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Select2
            $('.select2').select2({
                theme: 'bootstrap-5'
            });
            
            // Sidebar toggle for mobile
            $('#sidebarToggle').on('click', function() {
                $('.sidebar').toggleClass('show');
            });
            
            // Format currency to canonical "Rp 10.000"
            window.formatRupiah = function(angka) {
                const nilai = Number(angka || 0);
                const prefix = nilai < 0 ? '-Rp ' : 'Rp ';
                return prefix + Math.abs(nilai).toLocaleString('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
            };

            window.parseRupiah = function(nilai) {
                if (typeof nilai === 'number') {
                    return nilai;
                }

                const normalized = String(nilai || '')
                    .replace(/[^0-9,-]/g, '')
                    .replace(/\./g, '')
                    .replace(',', '.');

                return Number(normalized || 0);
            };
            
            // Auto close alert
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
        });
    </script>
    
    @stack('scripts')
</body>
</html>
