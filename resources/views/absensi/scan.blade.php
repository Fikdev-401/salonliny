@extends('layouts.app')

@section('title', 'Scan Barcode Absensi')

@section('content')
@php
    $isAttendanceComplete = $todayAttendance && $todayAttendance->jam_keluar;
    $isCheckedInOnly = $todayAttendance && !$todayAttendance->jam_keluar;
    $scanActionLabel = $isCheckedInOnly ? 'Check-out' : 'Check-in';
    $heroStateClass = $isAttendanceComplete ? 'is-complete' : ($isCheckedInOnly ? 'is-checkout' : 'is-checkin');
@endphp
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        
        @if(!$karyawan)
        <!-- Error: Karyawan tidak ditemukan -->
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-exclamation-triangle-fill fs-1 text-warning"></i>
                <h4 class="mt-3">Akun Belum Terhubung</h4>
                <p class="text-muted">
                    Akun Anda ({{ auth()->user()->nama }}) belum terhubung dengan data karyawan.<br>
                    Silakan hubungi Admin untuk menghubungkan akun Anda.
                </p>
                
                <div class="alert alert-light border mt-3">
                    <small class="text-muted">
                        <strong>Informasi Akun:</strong><br>
                        Nama: {{ auth()->user()->nama }}<br>
                        Username: {{ auth()->user()->username }}<br>
                        Role: {{ auth()->user()->role->nama }}
                    </small>
                </div>

                @if(auth()->user()->hasRole(['admin', 'super-admin']))
                <div class="mt-4">
                    <a href="{{ route('karyawan.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Buat Data Karyawan
                    </a>
                </div>
                @endif
            </div>
        </div>
        @else
        <div class="scan-page">
            <div class="scan-hero {{ $heroStateClass }} mb-4">
                <div class="scan-hero__content">
                    <div class="scan-hero__eyebrow">Absensi Mandiri</div>
                    <h1 class="scan-hero__title">Scan barcode lalu ambil foto verifikasi.</h1>
                    <p class="scan-hero__subtitle mb-0">
                        Halo, {{ $karyawan->nama }}. Semua data check-in/check-out langsung tersimpan ke sistem absensi.
                    </p>
                </div>
                <div class="scan-hero__meta">
                    <div class="scan-pill">
                        <i class="bi bi-calendar-event"></i>
                        <span>{{ now()->format('l, d F Y') }}</span>
                    </div>
                    <div class="scan-pill">
                        <i class="bi bi-person-badge"></i>
                        <span>{{ $karyawan->jabatan }}</span>
                    </div>
                </div>
            </div>

            <div class="scan-steps mb-4">
                <div class="scan-step scan-step--active">
                    <div class="scan-step__number">1</div>
                    <div>
                        <div class="scan-step__title">Scan Barcode</div>
                        <div class="scan-step__text">Arahkan kamera ke barcode check-in/check-out.</div>
                    </div>
                </div>
                <div class="scan-step">
                    <div class="scan-step__number">2</div>
                    <div>
                        <div class="scan-step__title">Foto Verifikasi</div>
                        <div class="scan-step__text">Ambil selfie atau pakai kamera belakang bila diperlukan.</div>
                    </div>
                </div>
                <div class="scan-step">
                    <div class="scan-step__number">3</div>
                    <div>
                        <div class="scan-step__title">Simpan Otomatis</div>
                        <div class="scan-step__text">Lokasi, foto, dan jam absensi masuk ke database.</div>
                    </div>
                </div>
            </div>

            <div class="card scan-status-card mb-4">
                <div class="card-body">
                    <div class="scan-status-card__header">
                        <div>
                            <div class="scan-status-card__label">Status Hari Ini</div>
                            @if($todayAttendance)
                                @if($todayAttendance->jam_keluar)
                                    <h5 class="mb-1 text-success">Absensi selesai</h5>
                                    <p class="text-muted mb-0">Anda sudah check-in dan check-out hari ini.</p>
                                @else
                                    <h5 class="mb-1 text-warning-emphasis">Sudah check-in</h5>
                                    <p class="text-muted mb-0">Lanjutkan scan untuk proses check-out.</p>
                                @endif
                            @else
                                <h5 class="mb-1 text-info-emphasis">Belum check-in</h5>
                                <p class="text-muted mb-0">Mulai dari scan barcode untuk absensi masuk.</p>
                            @endif
                        </div>
                        <div class="scan-status-badge {{ $heroStateClass }}">
                            @if($isAttendanceComplete)
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Selesai</span>
                            @elseif($isCheckedInOnly)
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Menunggu Check-out</span>
                            @else
                                <i class="bi bi-box-arrow-in-right"></i>
                                <span>Siap Check-in</span>
                            @endif
                        </div>
                    </div>

                    <div class="scan-status-grid">
                        <div class="scan-status-item">
                            <span class="scan-status-item__label">Jam Masuk</span>
                            <span class="scan-status-item__value">{{ $todayAttendance?->jam_masuk ?? '--:--' }}</span>
                        </div>
                        <div class="scan-status-item">
                            <span class="scan-status-item__label">Jam Pulang</span>
                            <span class="scan-status-item__value">{{ $todayAttendance?->jam_keluar ?? '--:--' }}</span>
                        </div>
                        <div class="scan-status-item">
                            <span class="scan-status-item__label">Verifikasi</span>
                            <span class="scan-status-item__value">
                                {{ $todayAttendance?->status_verifikasi ? ucfirst($todayAttendance->status_verifikasi) : 'Belum ada' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scanner Card -->
            @if(!$todayAttendance || !$todayAttendance->jam_keluar)
            <div class="card scan-panel">
                <div class="card-body">
                    <div class="scan-panel__header">
                        <div>
                            <div class="scan-panel__eyebrow">Tahap 1</div>
                            <h5 class="mb-1">
                                <i class="bi bi-upc-scan"></i>
                                Scan untuk {{ $scanActionLabel }}
                            </h5>
                            <p class="text-muted mb-0">Setelah barcode valid, kamera foto akan terbuka otomatis.</p>
                        </div>
                        <div class="scan-panel__actions">
                            <button type="button" id="btn-toggle-camera" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-camera-switch"></i> Kamera Belakang
                            </button>
                            <span class="scan-inline-hint" id="scanner-camera-caption">Mode scanner aktif</span>
                        </div>
                    </div>

                    <div id="scanner-container" class="mb-3">
                        <div id="reader" class="scan-reader"></div>
                    </div>

                    <div id="scan-status" class="alert alert-info text-center mb-3">
                        <i class="bi bi-camera"></i> Arahkan kamera ke barcode...
                    </div>

                    <div class="scan-manual-entry">
                        <label class="form-label">Atau input kode barcode manual</label>
                        <div class="scan-manual-entry__controls">
                            <input type="text" id="manual-barcode" class="form-control" placeholder="Masukkan kode barcode">
                            <button class="btn btn-primary" type="button" id="btn-manual">
                                <i class="bi bi-check"></i>
                                <span>Gunakan Kode</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Photo Capture Modal -->
            <div class="modal fade" id="photoModal" data-bs-backdrop="static" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content scan-photo-modal">
                        <div class="modal-header border-0 pb-0">
                            <div>
                                <div class="scan-panel__eyebrow">Tahap 2</div>
                                <h5 class="modal-title mb-1">
                                    <i class="bi bi-camera"></i> Ambil Foto Verifikasi
                                </h5>
                                <p class="text-muted mb-0 small">Pilih kamera depan atau belakang sesuai kondisi lapangan.</p>
                            </div>
                            <div class="d-flex flex-column align-items-end gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-toggle-photo-camera">
                                    <i class="bi bi-camera-switch"></i> Kamera Depan
                                </button>
                                <span class="scan-inline-hint" id="photo-camera-caption">Siap mengambil foto</span>
                            </div>
                        </div>
                        <div class="modal-body pt-3">
                            <div class="text-center mb-3">
                                <div id="photo-container" class="scan-photo-frame">
                                    <video id="camera-preview" autoplay playsinline class="scan-photo-frame__media"></video>
                                    <canvas id="photo-canvas" style="display: none;"></canvas>
                                    <img id="photo-preview" class="scan-photo-frame__media" style="display: none;">
                                </div>
                                <p class="text-muted mt-3 mb-0">
                                    <i class="bi bi-info-circle"></i> Pastikan wajah atau bukti kehadiran terlihat jelas sebelum konfirmasi.
                                </p>
                            </div>

                            <div class="alert alert-light border scan-location-card mb-0">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-geo-alt fs-4 me-2 text-primary"></i>
                                    <div>
                                        <small class="text-muted d-block">Lokasi Verifikasi</small>
                                        <span id="location-text">Mendeteksi lokasi...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-center border-0 pt-0">
                            <button type="button" class="btn btn-outline-secondary" id="btn-cancel-photo">
                                <i class="bi bi-x-lg"></i> Batal
                            </button>
                            <button type="button" class="btn btn-secondary" id="btn-retake" style="display: none;">
                                <i class="bi bi-arrow-counterclockwise"></i> Ulangi
                            </button>
                            <button type="button" class="btn btn-primary btn-lg" id="btn-capture">
                                <i class="bi bi-camera-fill"></i> Ambil Foto
                            </button>
                            <button type="button" class="btn btn-success btn-lg" id="btn-confirm" style="display: none;">
                                <i class="bi bi-check-lg"></i> Konfirmasi Absensi
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading Modal -->
            <div class="modal fade" id="loadingModal" data-bs-backdrop="static" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body text-center py-5">
                            <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <h5>Memproses Absensi...</h5>
                            <p class="text-muted mb-0">Mohon tunggu sebentar</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <div class="text-center mt-4">
                <a href="{{ route('absensi.my-attendance') }}" class="btn btn-outline-primary">
                    <i class="bi bi-calendar-check"></i> Lihat Riwayat Absensi
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .scan-page {
        --scan-primary: #0f5ae0;
        --scan-primary-dark: #0a3ca3;
        --scan-surface: #f4f8ff;
        --scan-border: #d9e3f3;
        --scan-text-soft: #5c6f91;
    }

    .scan-hero {
        border-radius: 24px;
        padding: 1.5rem;
        color: #fff;
        background:
            radial-gradient(circle at top right, rgba(255,255,255,.22), transparent 32%),
            linear-gradient(135deg, #0e4fd1 0%, #0c6ef2 55%, #2aa8ff 100%);
        box-shadow: 0 18px 48px rgba(12, 67, 166, 0.18);
    }

    .scan-hero.is-checkout {
        background:
            radial-gradient(circle at top right, rgba(255,255,255,.18), transparent 32%),
            linear-gradient(135deg, #a86600 0%, #d98900 55%, #f5b942 100%);
    }

    .scan-hero.is-complete {
        background:
            radial-gradient(circle at top right, rgba(255,255,255,.18), transparent 32%),
            linear-gradient(135deg, #087b52 0%, #0b9d63 55%, #38c98a 100%);
    }

    .scan-hero__eyebrow,
    .scan-panel__eyebrow {
        text-transform: uppercase;
        letter-spacing: .08em;
        font-size: .74rem;
        font-weight: 700;
        opacity: .86;
        margin-bottom: .45rem;
    }

    .scan-hero__title {
        font-size: clamp(1.55rem, 4vw, 2.25rem);
        line-height: 1.1;
        margin-bottom: .65rem;
    }

    .scan-hero__subtitle {
        max-width: 44rem;
        opacity: .92;
    }

    .scan-hero__meta {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
        margin-top: 1rem;
    }

    .scan-pill {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .6rem .85rem;
        border-radius: 999px;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.18);
        backdrop-filter: blur(10px);
    }

    .scan-steps {
        display: grid;
        gap: .85rem;
        grid-template-columns: 1fr;
    }

    .scan-step {
        display: flex;
        gap: .85rem;
        padding: 1rem;
        border-radius: 18px;
        background: #fff;
        border: 1px solid var(--scan-border);
        box-shadow: 0 10px 28px rgba(15, 43, 92, 0.06);
    }

    .scan-step--active {
        background: linear-gradient(180deg, #ffffff 0%, #f5f9ff 100%);
        border-color: #bfd2f0;
    }

    .scan-step__number {
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        background: #e7f0ff;
        color: var(--scan-primary-dark);
        flex-shrink: 0;
    }

    .scan-step__title {
        font-weight: 700;
        margin-bottom: .1rem;
    }

    .scan-step__text {
        color: var(--scan-text-soft);
        font-size: .92rem;
    }

    .scan-status-card,
    .scan-panel {
        border: 1px solid var(--scan-border);
        border-radius: 22px;
        box-shadow: 0 12px 32px rgba(12, 52, 118, 0.06);
    }

    .scan-status-card__header {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .scan-status-card__label {
        text-transform: uppercase;
        letter-spacing: .08em;
        font-size: .75rem;
        font-weight: 700;
        color: var(--scan-text-soft);
        margin-bottom: .35rem;
    }

    .scan-status-badge {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        width: fit-content;
        padding: .7rem .95rem;
        border-radius: 999px;
        font-weight: 700;
        border: 1px solid transparent;
    }

    .scan-status-badge.is-checkin {
        background: #e8f4ff;
        color: #0e64b5;
        border-color: #c8e3ff;
    }

    .scan-status-badge.is-checkout {
        background: #fff5da;
        color: #9a6b00;
        border-color: #f3de9f;
    }

    .scan-status-badge.is-complete {
        background: #e7fbf1;
        color: #12734e;
        border-color: #bfead3;
    }

    .scan-status-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: .75rem;
    }

    .scan-status-item {
        padding: .95rem;
        border-radius: 16px;
        background: var(--scan-surface);
        border: 1px solid var(--scan-border);
    }

    .scan-status-item__label {
        display: block;
        color: var(--scan-text-soft);
        font-size: .82rem;
        margin-bottom: .3rem;
    }

    .scan-status-item__value {
        display: block;
        font-weight: 700;
        color: #17315d;
    }

    .scan-panel__header {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .scan-panel__actions {
        display: flex;
        flex-direction: column;
        gap: .5rem;
        align-items: stretch;
    }

    .scan-inline-hint {
        color: var(--scan-text-soft);
        font-size: .84rem;
    }

    #reader {
        border: 2px dashed #c9d9f0;
        border-radius: 18px;
        overflow: hidden;
        background:
            linear-gradient(180deg, rgba(14,79,209,.04), rgba(14,79,209,.01)),
            #f8fbff;
        min-height: 300px;
    }

    #reader video {
        border-radius: 18px;
    }

    .scan-reader {
        width: 100%;
        min-height: 300px;
    }

    .camera-mirror {
        transform: scaleX(-1);
    }

    #reader__dashboard_section_csr span {
        display: none;
    }

    .scan-manual-entry {
        border-top: 1px solid #e8eef7;
        padding-top: 1rem;
    }

    .scan-manual-entry__controls {
        display: grid;
        grid-template-columns: 1fr;
        gap: .75rem;
    }

    .scan-photo-modal {
        border-radius: 24px;
        overflow: hidden;
    }

    .scan-photo-frame {
        position: relative;
        max-width: 420px;
        margin: 0 auto;
        padding: .7rem;
        border-radius: 24px;
        background: linear-gradient(180deg, #f9fbff 0%, #edf4ff 100%);
        border: 1px solid #dbe7f8;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.8);
    }

    .scan-photo-frame__media {
        width: 100%;
        border-radius: 18px;
        display: block;
        background: #d9e4f2;
    }

    .scan-location-card {
        border-radius: 18px;
    }

    .scan-success {
        animation: pulse 0.5s ease-in-out;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    @media (min-width: 768px) {
        .scan-steps {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .scan-status-card__header,
        .scan-panel__header {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }

        .scan-panel__actions {
            align-items: flex-end;
        }

        .scan-manual-entry__controls {
            grid-template-columns: 1fr auto;
        }
    }

    @media (max-width: 767.98px) {
        .scan-hero {
            padding: 1.2rem;
            border-radius: 20px;
        }

        .scan-status-grid {
            grid-template-columns: 1fr;
        }

        .scan-photo-modal .modal-header,
        .scan-photo-modal .modal-footer {
            flex-direction: column;
            align-items: stretch;
        }

        .scan-photo-modal .modal-footer .btn {
            width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
@if($karyawan)
<!-- HTML5 QR Code Scanner -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
$(document).ready(function() {
    let html5QrCode;
    let scannerCameras = [];
    let scannerCameraIndex = 0;
    let scannerStarted = false;
    let scannedBarcode = null;
    let photoData = null;
    let latitude = null;
    let longitude = null;
    let photoStream = null;
    let photoFacingMode = 'user';
    let photoDeviceId = null;
    let photoDevices = [];

    const photoModal = new bootstrap.Modal(document.getElementById('photoModal'));
    const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
    const cameraPreview = document.getElementById('camera-preview');
    const photoPreview = document.getElementById('photo-preview');
    const photoCanvas = document.getElementById('photo-canvas');

    // Initialize scanner
    async function initScanner() {
        html5QrCode = new Html5Qrcode("reader");

        try {
            scannerCameras = await Html5Qrcode.getCameras();

            if (!scannerCameras.length) {
                $('#scan-status').html('<i class="bi bi-exclamation-triangle"></i> Kamera tidak ditemukan');
                $('#scan-status').removeClass('alert-info').addClass('alert-warning');
                return;
            }

            scannerCameraIndex = findPreferredCameraIndex(scannerCameras, 'environment');
            await startScannerByIndex(scannerCameraIndex);
        } catch (err) {
            console.error('Error getting cameras:', err);
            $('#scan-status').html('<i class="bi bi-exclamation-triangle"></i> Tidak dapat mengakses kamera');
            $('#scan-status').removeClass('alert-info').addClass('alert-danger');
        }
    }

    function findPreferredCameraIndex(cameras, facingMode) {
        const normalizedMode = facingMode === 'environment' ? ['back', 'rear', 'environment'] : ['front', 'user', 'selfie'];
        const matchedIndex = cameras.findIndex(camera => {
            const label = `${camera.label || ''} ${camera.id || ''}`.toLowerCase();
            return normalizedMode.some(keyword => label.includes(keyword));
        });

        return matchedIndex >= 0 ? matchedIndex : 0;
    }

    async function safeStopScanner() {
        if (!html5QrCode || !scannerStarted) {
            return;
        }

        try {
            await html5QrCode.stop();
        } catch (error) {
            console.warn('Scanner stop warning:', error);
        } finally {
            scannerStarted = false;
        }
    }

    async function startScannerByIndex(index) {
        if (!scannerCameras.length) {
            return;
        }

        const selectedCamera = scannerCameras[index] || scannerCameras[0];
        scannerCameraIndex = Math.max(0, scannerCameras.findIndex(camera => camera.id === selectedCamera.id));

        await safeStopScanner();

        await html5QrCode.start(
            selectedCamera.id,
            {
                fps: 10,
                qrbox: { width: 250, height: 250 }
            },
            onScanSuccess,
            onScanFailure
        );

        scannerStarted = true;
        updateScannerToggleLabel();
    }

    function updateScannerToggleLabel() {
        if (!scannerCameras.length) {
            return;
        }

        const activeCamera = scannerCameras[scannerCameraIndex];
        const label = `${activeCamera?.label || activeCamera?.id || ''}`.toLowerCase();
        const modeLabel = ['back', 'rear', 'environment'].some(keyword => label.includes(keyword))
            ? 'Kamera Belakang'
            : 'Kamera Depan';

        $('#btn-toggle-camera').html(`<i class="bi bi-camera-switch"></i> ${modeLabel}`);
        $('#scanner-camera-caption').text(`Scanner aktif: ${modeLabel.toLowerCase()}`);
    }

    async function onScanSuccess(decodedText, decodedResult) {
        // Stop scanner
        await safeStopScanner();
        scannedBarcode = decodedText;
        
        $('#scan-status').html('<i class="bi bi-check-circle"></i> Barcode terdeteksi: ' + decodedText);
        $('#scan-status').removeClass('alert-info').addClass('alert-success');
        
        // Show photo modal after short delay
        setTimeout(() => {
            openPhotoModal();
        }, 500);
    }

    function onScanFailure(error) {
        // Silently fail - this runs continuously while scanning
    }

    // Manual barcode input
    $('#btn-manual').on('click', function() {
        const barcode = $('#manual-barcode').val().trim();
        if (barcode) {
            scannedBarcode = barcode;
            openPhotoModal();
        } else {
            alert('Masukkan kode barcode terlebih dahulu');
        }
    });

    // Toggle camera
    $('#btn-toggle-camera').on('click', async function() {
        if (scannerCameras.length < 2) {
            Swal.fire({
                icon: 'info',
                title: 'Kamera tunggal',
                text: 'Perangkat ini hanya mendeteksi satu kamera.',
            });
            return;
        }

        scannerCameraIndex = (scannerCameraIndex + 1) % scannerCameras.length;

        try {
            await startScannerByIndex(scannerCameraIndex);
        } catch (error) {
            console.error('Error toggling scanner camera:', error);
            $('#scan-status').html('<i class="bi bi-exclamation-triangle"></i> Gagal mengganti kamera scanner');
            $('#scan-status').removeClass('alert-info').addClass('alert-danger');
        }
    });

    // Photo Modal Functions
    async function openPhotoModal() {
        await safeStopScanner();
        resetPhotoCaptureState();
        photoModal.show();
        await initPhotoDevices();
        await initCamera();
        getLocation();
    }

    async function initPhotoDevices() {
        try {
            const devices = await navigator.mediaDevices.enumerateDevices();
            photoDevices = devices.filter(device => device.kind === 'videoinput');
        } catch (error) {
            photoDevices = [];
        }

        updatePhotoToggleLabel();
    }

    function updatePhotoToggleLabel() {
        const label = photoFacingMode === 'environment' ? 'Kamera Belakang' : 'Kamera Depan';
        $('#btn-toggle-photo-camera')
            .prop('disabled', photoDevices.length < 2)
            .html(`<i class="bi bi-camera-switch"></i> ${label}`);
        $('#photo-camera-caption').text(`Mode foto aktif: ${label.toLowerCase()}`);
    }

    async function stopPhotoCamera() {
        if (photoStream) {
            photoStream.getTracks().forEach(track => track.stop());
            photoStream = null;
        }
    }

    async function initCamera(preferredMode = photoFacingMode, preferredDeviceId = photoDeviceId) {
        await stopPhotoCamera();

        const videoConstraints = preferredDeviceId
            ? {
                deviceId: { exact: preferredDeviceId },
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
            : {
                facingMode: { ideal: preferredMode },
                width: { ideal: 1280 },
                height: { ideal: 720 }
            };

        return navigator.mediaDevices.getUserMedia({
            video: videoConstraints,
            audio: false
        })
        .then(async stream => {
            photoStream = stream;
            cameraPreview.srcObject = stream;

            const activeTrack = stream.getVideoTracks()[0];
            const settings = activeTrack?.getSettings?.() || {};
            photoDeviceId = settings.deviceId || preferredDeviceId || null;
            photoFacingMode = settings.facingMode || preferredMode;
            cameraPreview.classList.toggle('camera-mirror', photoFacingMode !== 'environment');

            await initPhotoDevices();
        })
        .catch(err => {
            console.error('Error accessing camera:', err);
            Swal.fire({
                icon: 'error',
                title: 'Kamera tidak tersedia',
                text: 'Tidak dapat mengakses kamera untuk foto absensi.',
            });
            throw err;
        });
    }

    function resetPhotoCaptureState() {
        photoData = null;
        cameraPreview.style.display = 'block';
        photoPreview.style.display = 'none';
        photoPreview.removeAttribute('src');
        $('#btn-capture').show();
        $('#btn-retake').hide();
        $('#btn-confirm').hide();
    }

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    latitude = position.coords.latitude;
                    longitude = position.coords.longitude;
                    $('#location-text').html(
                        '<i class="bi bi-check-circle-fill text-success"></i> Lokasi berhasil dideteksi'
                    );
                },
                (error) => {
                    console.error('Error getting location:', error);
                    $('#location-text').html(
                        '<i class="bi bi-x-circle-fill text-warning"></i> Lokasi tidak dapat dideteksi'
                    );
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        } else {
            $('#location-text').html(
                '<i class="bi bi-x-circle-fill text-warning"></i> Browser tidak mendukung geolokasi'
            );
        }
    }

    // Capture photo
    $('#btn-capture').on('click', function() {
        // Set canvas size to match video
        photoCanvas.width = cameraPreview.videoWidth;
        photoCanvas.height = cameraPreview.videoHeight;
        
        // Draw video frame to canvas
        const ctx = photoCanvas.getContext('2d');
        ctx.save();

        if (photoFacingMode !== 'environment') {
            ctx.translate(photoCanvas.width, 0);
            ctx.scale(-1, 1);
        }

        ctx.drawImage(cameraPreview, 0, 0, photoCanvas.width, photoCanvas.height);
        ctx.restore();
        
        // Get base64 data
        photoData = photoCanvas.toDataURL('image/jpeg', 0.8);
        
        // Show preview
        photoPreview.src = photoData;
        photoPreview.style.display = 'block';
        cameraPreview.style.display = 'none';
        
        // Stop camera
        stopPhotoCamera();
        
        // Toggle buttons
        $('#btn-capture').hide();
        $('#btn-retake').show();
        $('#btn-confirm').show();
    });

    // Retake photo
    $('#btn-retake').on('click', async function() {
        resetPhotoCaptureState();
        await initCamera();
    });

    $('#btn-toggle-photo-camera').on('click', async function() {
        photoFacingMode = photoFacingMode === 'environment' ? 'user' : 'environment';
        photoDeviceId = null;
        resetPhotoCaptureState();

        try {
            await initCamera(photoFacingMode, null);
        } catch (error) {
            // handled in initCamera
        }
    });

    $('#btn-cancel-photo').on('click', function() {
        photoModal.hide();
        resetScanner();
    });

    // Confirm and submit
    $('#btn-confirm').on('click', function() {
        if (!photoData) {
            alert('Ambil foto terlebih dahulu');
            return;
        }

        photoModal.hide();
        loadingModal.show();

        // Send data to server
        $.ajax({
            url: '{{ route("absensi.process-scan") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                barcode: scannedBarcode,
                foto: photoData,
                latitude: latitude,
                longitude: longitude,
            },
            success: function(response) {
                loadingModal.hide();
                
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message,
                    });
                    resetScanner();
                }
            },
            error: function(xhr) {
                loadingModal.hide();
                
                const response = xhr.responseJSON || {};
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: response.message || 'Terjadi kesalahan saat memproses absensi',
                });
                resetScanner();
            }
        });
    });

    function resetScanner() {
        scannedBarcode = null;
        latitude = null;
        longitude = null;
        resetPhotoCaptureState();
        
        $('#manual-barcode').val('');
        $('#scan-status').html('<i class="bi bi-camera"></i> Arahkan kamera ke barcode...');
        $('#scan-status').removeClass('alert-success alert-danger').addClass('alert-info');
        
        // Restart scanner
        if (scannerCameras.length) {
            startScannerByIndex(scannerCameraIndex).catch(error => {
                console.error('Error restarting scanner:', error);
            });
        }
    }

    document.getElementById('photoModal').addEventListener('hidden.bs.modal', function() {
        stopPhotoCamera();
        resetPhotoCaptureState();
    });

    // Initialize
    initScanner();
});
</script>

<!-- SweetAlert2 for better alerts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endif
@endpush
