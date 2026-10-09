<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Barcode Absensi</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- JsBarcode -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    
    <style>
        @media print {
            @page {
                size: A4;
                margin: 10mm;
            }
            .no-print {
                display: none !important;
            }
            .barcode-card {
                page-break-inside: avoid;
            }
        }
        
        body {
            background: #f8f9fa;
            padding: 20px;
        }
        
        .barcode-card {
            background: white;
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .barcode-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #495057;
        }
        
        .barcode-text {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }
        
        .karyawan-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .tanggal-info {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 15px;
        }
        
        .tipe-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .tipe-checkin {
            background: #d4edda;
            color: #155724;
        }
        
        .tipe-checkout {
            background: #fff3cd;
            color: #856404;
        }
        
        .instructions {
            background: #e9ecef;
            padding: 10px;
            border-radius: 5px;
            margin-top: 15px;
            font-size: 11px;
            color: #495057;
        }
        
        .barcode-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .barcode-card {
                box-shadow: none;
                border: 1px solid #dee2e6;
            }
        }
    </style>
</head>
<body>
    <!-- Print Controls -->
    <div class="no-print mb-4">
        <div class="card">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Preview Print Barcode</h5>
                    <small class="text-muted">{{ $barcodes->count() }} barcode akan diprint</small>
                </div>
                <button type="button" class="btn btn-primary btn-lg" onclick="window.print()">
                    <i class="bi bi-printer"></i> Print Sekarang
                </button>
            </div>
        </div>
    </div>

    <!-- Barcode Cards -->
    <div class="barcode-container">
        @foreach($barcodes as $barcode)
        <div class="barcode-card">
            <div class="barcode-title">SISTEM ABSENSI</div>
            
            <div class="karyawan-name">{{ $barcode->karyawan->nama }}</div>
            <div class="tanggal-info">
                {{ $barcode->tanggal->format('d F Y') }}
            </div>
            
            <div class="tipe-badge {{ $barcode->tipe == 'checkin' ? 'tipe-checkin' : 'tipe-checkout' }}">
                {{ $barcode->tipe == 'checkin' ? 'CHECK-IN' : 'CHECK-OUT' }}
            </div>
            
            <div class="my-3">
                <svg class="barcode-svg" 
                     data-barcode="{{ $barcode->barcode }}"
                     data-type="{{ $barcode->tipe == 'checkin' ? 'CHECK-IN' : 'CHECK-OUT' }}">
                </svg>
            </div>
            
            <div class="barcode-text">{{ $barcode->barcode }}</div>
            
            <div class="instructions">
                <strong>Cara Penggunaan:</strong><br>
                1. Buka halaman Scan Barcode<br>
                2. Arahkan kamera ke barcode ini<br>
                3. Ambil foto selfie<br>
                4. Konfirmasi absensi
            </div>
        </div>
        @endforeach
    </div>

    <script>
        // Generate barcodes using JsBarcode
        document.querySelectorAll('.barcode-svg').forEach(function(svg) {
            const barcode = svg.getAttribute('data-barcode');
            const type = svg.getAttribute('data-type');
            
            JsBarcode(svg, barcode, {
                format: "CODE128",
                width: 2,
                height: 60,
                displayValue: false,
                margin: 0,
            });
        });

        // Auto print after load (optional)
        // window.onload = function() {
        //     setTimeout(function() {
        //         window.print();
        //     }, 1000);
        // };
    </script>
</body>
</html>
