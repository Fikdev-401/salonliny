@extends('layouts.app')

@section('title', 'Detail Absensi')

@section('content')
@php
    $tanggalLabel = $absensi->tanggal->translatedFormat('l, d F Y');
    $statusConfig = match ($absensi->status) {
        'hadir' => ['label' => 'Hadir', 'class' => 'success'],
        'izin' => ['label' => 'Izin', 'class' => 'warning text-dark'],
        'sakit' => ['label' => 'Sakit', 'class' => 'info text-dark'],
        'cuti' => ['label' => 'Cuti', 'class' => 'primary'],
        default => ['label' => 'Alpha', 'class' => 'danger'],
    };
    $verificationConfig = match ($absensi->status_verifikasi) {
        'terverifikasi' => ['label' => 'Terverifikasi', 'class' => 'success', 'hint' => 'Bukti absensi sudah disetujui admin.'],
        'ditolak' => ['label' => 'Ditolak', 'class' => 'danger', 'hint' => 'Bukti absensi ditandai bermasalah atau tidak valid.'],
        default => ['label' => 'Pending', 'class' => 'warning text-dark', 'hint' => 'Menunggu review admin.'],
    };
    $hasLocation = (bool) ($absensi->latitude_masuk || $absensi->latitude_keluar);
@endphp

<div class="attendance-detail-page">
    <div class="attendance-detail-hero mb-4">
        <div>
            <div class="attendance-detail-hero__eyebrow">Detail Absensi</div>
            <h1 class="attendance-detail-hero__title">{{ $absensi->karyawan->nama }}</h1>
            <p class="attendance-detail-hero__subtitle mb-0">
                {{ $tanggalLabel }} · {{ $absensi->karyawan->jabatan }} · Durasi kerja {{ $absensi->durasi_kerja }}
            </p>
        </div>
        <div class="attendance-detail-hero__meta">
            <div class="attendance-detail-pill">
                <i class="bi bi-calendar-event"></i>
                <span>{{ $absensi->tanggal->format('d/m/Y') }}</span>
            </div>
            <div class="attendance-detail-pill">
                <i class="bi bi-patch-check"></i>
                <span>{{ $verificationConfig['label'] }}</span>
            </div>
            <span class="badge bg-{{ $statusConfig['class'] }} attendance-detail-status">{{ $statusConfig['label'] }}</span>
            <a href="{{ route('absensi.monitoring', ['tanggal' => $absensi->tanggal->format('Y-m-d')]) }}" class="btn btn-light attendance-detail-hero__cta">
                <i class="bi bi-arrow-left"></i> Kembali ke Monitoring
            </a>
        </div>
    </div>

    <div class="attendance-detail-summary-grid mb-4">
        <div class="attendance-detail-summary-card primary">
            <span class="attendance-detail-summary-card__label">Status Hari Ini</span>
            <strong class="attendance-detail-summary-card__value">{{ $statusConfig['label'] }}</strong>
            <span class="attendance-detail-summary-card__hint">Status kehadiran yang dicatat sistem</span>
        </div>
        <div class="attendance-detail-summary-card success">
            <span class="attendance-detail-summary-card__label">Jam Masuk</span>
            <strong class="attendance-detail-summary-card__value">{{ $absensi->jam_masuk ?? '--:--' }}</strong>
            <span class="attendance-detail-summary-card__hint">Waktu check-in karyawan</span>
        </div>
        <div class="attendance-detail-summary-card warning">
            <span class="attendance-detail-summary-card__label">Jam Keluar</span>
            <strong class="attendance-detail-summary-card__value">{{ $absensi->jam_keluar ?? '--:--' }}</strong>
            <span class="attendance-detail-summary-card__hint">Waktu check-out karyawan</span>
        </div>
        <div class="attendance-detail-summary-card info">
            <span class="attendance-detail-summary-card__label">Durasi Kerja</span>
            <strong class="attendance-detail-summary-card__value">{{ $absensi->durasi_kerja }}</strong>
            <span class="attendance-detail-summary-card__hint">Selisih check-in dan check-out</span>
        </div>
        <div class="attendance-detail-summary-card neutral">
            <span class="attendance-detail-summary-card__label">Verifikasi</span>
            <strong class="attendance-detail-summary-card__value">{{ $verificationConfig['label'] }}</strong>
            <span class="attendance-detail-summary-card__hint">{{ $verificationConfig['hint'] }}</span>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card attendance-detail-panel mb-4">
                <div class="card-body">
                    <div class="attendance-detail-panel__header mb-3">
                        <div>
                            <div class="attendance-detail-panel__eyebrow">Ringkasan Kehadiran</div>
                            <h5 class="mb-1">Informasi absensi harian</h5>
                            <p class="text-muted mb-0">Status utama, catatan, lokasi, dan metadata verifikasi.</p>
                        </div>
                        <span class="attendance-detail-chip">{{ $absensi->barcode ? 'Barcode tercatat' : 'Tanpa barcode' }}</span>
                    </div>

                    <div class="attendance-detail-info-grid">
                        <div class="attendance-detail-info">
                            <span class="attendance-detail-info__label">Karyawan</span>
                            <strong>{{ $absensi->karyawan->nama }}</strong>
                            <small>{{ $absensi->karyawan->jabatan }}</small>
                        </div>
                        <div class="attendance-detail-info">
                            <span class="attendance-detail-info__label">Tanggal</span>
                            <strong>{{ $tanggalLabel }}</strong>
                            <small>Dicatat pada {{ $absensi->created_at->format('d/m/Y H:i') }} WITA</small>
                        </div>
                        <div class="attendance-detail-info">
                            <span class="attendance-detail-info__label">Lokasi Check-in</span>
                            <strong>{{ $absensi->lokasi_masuk ?? 'Tidak tercatat' }}</strong>
                            <small>{{ $absensi->latitude_masuk && $absensi->longitude_masuk ? 'Koordinat tersedia' : 'Tidak ada koordinat' }}</small>
                        </div>
                        <div class="attendance-detail-info">
                            <span class="attendance-detail-info__label">Lokasi Check-out</span>
                            <strong>{{ $absensi->lokasi_keluar ?? 'Tidak tercatat' }}</strong>
                            <small>{{ $absensi->latitude_keluar && $absensi->longitude_keluar ? 'Koordinat tersedia' : 'Tidak ada koordinat' }}</small>
                        </div>
                    </div>

                    @if($absensi->keterangan || $absensi->catatan_verifikasi)
                        <div class="attendance-detail-note-grid mt-4">
                            @if($absensi->keterangan)
                                <div class="attendance-detail-note">
                                    <span class="attendance-detail-note__label">Keterangan Karyawan</span>
                                    <p class="mb-0">{{ $absensi->keterangan }}</p>
                                </div>
                            @endif
                            @if($absensi->catatan_verifikasi)
                                <div class="attendance-detail-note">
                                    <span class="attendance-detail-note__label">Catatan Verifikasi</span>
                                    <p class="mb-0">{{ $absensi->catatan_verifikasi }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="card attendance-detail-panel mb-4">
                <div class="card-body">
                    <div class="attendance-detail-panel__header mb-3">
                        <div>
                            <div class="attendance-detail-panel__eyebrow">Bukti Foto</div>
                            <h5 class="mb-1">Dokumentasi check-in dan check-out</h5>
                            <p class="text-muted mb-0">Foto selfie dan koordinat yang dikirim dari perangkat karyawan.</p>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="attendance-detail-photo-card">
                                <div class="attendance-detail-photo-card__top">
                                    <span class="attendance-detail-photo-card__label">Foto Check-in</span>
                                    <span class="badge bg-success">{{ $absensi->jam_masuk ?? '--:--' }}</span>
                                </div>
                                @if($absensi->foto_masuk)
                                    <img src="{{ $absensi->foto_masuk_url }}" class="attendance-detail-photo-card__image" alt="Foto Check-in">
                                    <div class="attendance-detail-photo-card__meta">
                                        @if($absensi->latitude_masuk && $absensi->longitude_masuk)
                                            <a href="https://www.google.com/maps?q={{ $absensi->latitude_masuk }},{{ $absensi->longitude_masuk }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-geo-alt"></i> Lihat Lokasi
                                            </a>
                                        @else
                                            <span class="text-muted">Lokasi check-in tidak tersedia.</span>
                                        @endif
                                    </div>
                                @else
                                    <div class="attendance-detail-empty">
                                        <i class="bi bi-camera"></i>
                                        <p class="mb-0">Tidak ada foto check-in.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="attendance-detail-photo-card">
                                <div class="attendance-detail-photo-card__top">
                                    <span class="attendance-detail-photo-card__label">Foto Check-out</span>
                                    <span class="badge bg-warning text-dark">{{ $absensi->jam_keluar ?? '--:--' }}</span>
                                </div>
                                @if($absensi->foto_keluar)
                                    <img src="{{ $absensi->foto_keluar_url }}" class="attendance-detail-photo-card__image" alt="Foto Check-out">
                                    <div class="attendance-detail-photo-card__meta">
                                        @if($absensi->latitude_keluar && $absensi->longitude_keluar)
                                            <a href="https://www.google.com/maps?q={{ $absensi->latitude_keluar }},{{ $absensi->longitude_keluar }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-geo-alt"></i> Lihat Lokasi
                                            </a>
                                        @else
                                            <span class="text-muted">Lokasi check-out tidak tersedia.</span>
                                        @endif
                                    </div>
                                @else
                                    <div class="attendance-detail-empty">
                                        <i class="bi bi-camera-video-off"></i>
                                        <p class="mb-0">Tidak ada foto check-out.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($hasLocation)
                <div class="card attendance-detail-panel">
                    <div class="card-body p-0">
                        <div class="attendance-detail-map__header">
                            <div>
                                <div class="attendance-detail-panel__eyebrow">Lokasi Absensi</div>
                                <h5 class="mb-1">Peta koordinat check-in dan check-out</h5>
                            </div>
                        </div>
                        <div id="map" class="attendance-detail-map"></div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-xl-4">
            <div class="card attendance-detail-panel mb-4">
                <div class="card-body">
                    <div class="attendance-detail-panel__header mb-3">
                        <div>
                            <div class="attendance-detail-panel__eyebrow">Review Admin</div>
                            <h5 class="mb-1">Verifikasi bukti absensi</h5>
                            <p class="text-muted mb-0">Status pending dibuat otomatis setelah scan dan perlu ditindaklanjuti admin.</p>
                        </div>
                    </div>

                    <div class="attendance-detail-current-status">
                        <span class="attendance-detail-current-status__label">Status saat ini</span>
                        <span class="badge bg-{{ $verificationConfig['class'] }}">{{ $verificationConfig['label'] }}</span>
                    </div>

                    <form action="{{ route('absensi.verify', $absensi) }}" method="POST" class="attendance-detail-verify-form">
                        @csrf
                        <div class="attendance-detail-choice-list">
                            <label class="attendance-detail-choice">
                                <input type="radio" name="status_verifikasi" value="terverifikasi" {{ $absensi->status_verifikasi === 'terverifikasi' ? 'checked' : '' }} required>
                                <div>
                                    <strong>Terverifikasi</strong>
                                    <small>Foto, waktu, dan lokasi dianggap valid.</small>
                                </div>
                            </label>
                            <label class="attendance-detail-choice">
                                <input type="radio" name="status_verifikasi" value="ditolak" {{ $absensi->status_verifikasi === 'ditolak' ? 'checked' : '' }} required>
                                <div>
                                    <strong>Ditolak</strong>
                                    <small>Gunakan jika bukti absensi tidak valid atau perlu koreksi.</small>
                                </div>
                            </label>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan Verifikasi</label>
                            <textarea name="catatan_verifikasi" class="form-control" rows="4" placeholder="Tambahkan catatan jika perlu tindak lanjut...">{{ old('catatan_verifikasi', $absensi->catatan_verifikasi) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-lg"></i> Simpan Verifikasi
                        </button>
                    </form>
                </div>
            </div>

            <div class="card attendance-detail-panel">
                <div class="card-body">
                    <div class="attendance-detail-panel__header mb-3">
                        <div>
                            <div class="attendance-detail-panel__eyebrow">Metadata Sistem</div>
                            <h5 class="mb-1">Jejak perangkat dan audit</h5>
                        </div>
                    </div>

                    <div class="attendance-detail-device-list">
                        <div class="attendance-detail-device-item">
                            <span>IP Address</span>
                            <code>{{ $absensi->ip_address ?? '-' }}</code>
                        </div>
                        <div class="attendance-detail-device-item">
                            <span>Barcode</span>
                            <code>{{ $absensi->barcode ?? '-' }}</code>
                        </div>
                        <div class="attendance-detail-device-item">
                            <span>Dibuat</span>
                            <strong>{{ $absensi->created_at->format('d/m/Y H:i') }} WITA</strong>
                        </div>
                        <div class="attendance-detail-device-item">
                            <span>Diupdate</span>
                            <strong>{{ $absensi->updated_at->format('d/m/Y H:i') }} WITA</strong>
                        </div>
                        <div class="attendance-detail-device-item">
                            <span>Diverifikasi Oleh</span>
                            <strong>{{ $absensi->diverifikasiOleh?->name ?? 'Belum diverifikasi' }}</strong>
                        </div>
                        <div class="attendance-detail-device-item">
                            <span>Waktu Verifikasi</span>
                            <strong>{{ $absensi->diverifikasi_at ? $absensi->diverifikasi_at->format('d/m/Y H:i') . ' WITA' : '-' }}</strong>
                        </div>
                    </div>

                    @if($absensi->user_agent)
                        <div class="attendance-detail-user-agent mt-3">
                            <span class="attendance-detail-user-agent__label">User Agent</span>
                            <p class="mb-0">{{ $absensi->user_agent }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
@if($hasLocation)
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endif
<style>
    .attendance-detail-page {
        --attendance-detail-primary: #0f5ae0;
        --attendance-detail-surface: #f4f8ff;
        --attendance-detail-border: #d9e3f3;
        --attendance-detail-soft-text: #617494;
    }

    .attendance-detail-hero {
        border-radius: 24px;
        padding: 1.5rem;
        color: #fff;
        background:
            radial-gradient(circle at top right, rgba(255,255,255,.18), transparent 35%),
            linear-gradient(135deg, #112e72 0%, #0f5ae0 55%, #2aa8ff 100%);
        box-shadow: 0 18px 48px rgba(12, 67, 166, 0.18);
    }

    .attendance-detail-hero__eyebrow,
    .attendance-detail-panel__eyebrow {
        text-transform: uppercase;
        letter-spacing: .08em;
        font-size: .74rem;
        font-weight: 700;
        opacity: .86;
        margin-bottom: .45rem;
    }

    .attendance-detail-hero__title {
        font-size: clamp(1.55rem, 4vw, 2.2rem);
        line-height: 1.1;
        margin-bottom: .65rem;
    }

    .attendance-detail-hero__subtitle {
        max-width: 44rem;
        opacity: .92;
    }

    .attendance-detail-hero__meta {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
        margin-top: 1rem;
        align-items: center;
    }

    .attendance-detail-pill {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .65rem .9rem;
        border-radius: 999px;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.18);
        backdrop-filter: blur(10px);
    }

    .attendance-detail-status,
    .attendance-detail-hero__cta {
        border-radius: 999px;
    }

    .attendance-detail-summary-grid {
        display: grid;
        gap: .85rem;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .attendance-detail-summary-card {
        display: flex;
        flex-direction: column;
        gap: .15rem;
        padding: 1rem;
        border-radius: 20px;
        border: 1px solid var(--attendance-detail-border);
        background: #fff;
        box-shadow: 0 12px 30px rgba(12, 52, 118, 0.06);
    }

    .attendance-detail-summary-card.primary { border-color: #c7d8f7; background: #f3f8ff; }
    .attendance-detail-summary-card.success { border-color: #c9ead8; background: #f2fcf6; }
    .attendance-detail-summary-card.warning { border-color: #f2e0a1; background: #fff9e8; }
    .attendance-detail-summary-card.info { border-color: #cae8f0; background: #f2fbff; }
    .attendance-detail-summary-card.neutral { border-color: #dfe6f2; background: #f7f9fd; }

    .attendance-detail-summary-card__label {
        color: var(--attendance-detail-soft-text);
        font-size: .82rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        font-weight: 700;
    }

    .attendance-detail-summary-card__value {
        font-size: 1.55rem;
        line-height: 1.1;
        color: #18345f;
    }

    .attendance-detail-summary-card__hint {
        color: var(--attendance-detail-soft-text);
        font-size: .88rem;
    }

    .attendance-detail-panel {
        border-radius: 22px;
        border: 1px solid var(--attendance-detail-border);
        box-shadow: 0 12px 32px rgba(12, 52, 118, 0.06);
    }

    .attendance-detail-panel__header {
        display: flex;
        flex-direction: column;
        gap: .85rem;
    }

    .attendance-detail-chip {
        display: inline-flex;
        width: fit-content;
        padding: .55rem .85rem;
        border-radius: 999px;
        background: var(--attendance-detail-surface);
        border: 1px solid var(--attendance-detail-border);
        color: var(--attendance-detail-soft-text);
        font-weight: 600;
    }

    .attendance-detail-info-grid,
    .attendance-detail-note-grid {
        display: grid;
        gap: 1rem;
    }

    .attendance-detail-info,
    .attendance-detail-note,
    .attendance-detail-device-item,
    .attendance-detail-current-status,
    .attendance-detail-user-agent {
        padding: .95rem 1rem;
        border-radius: 18px;
        background: var(--attendance-detail-surface);
        border: 1px solid var(--attendance-detail-border);
    }

    .attendance-detail-info__label,
    .attendance-detail-note__label,
    .attendance-detail-user-agent__label {
        display: block;
        margin-bottom: .3rem;
        color: var(--attendance-detail-soft-text);
        font-size: .82rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        font-weight: 700;
    }

    .attendance-detail-info strong,
    .attendance-detail-note p,
    .attendance-detail-device-item strong,
    .attendance-detail-device-item code {
        color: #18345f;
    }

    .attendance-detail-info small {
        display: block;
        margin-top: .25rem;
        color: var(--attendance-detail-soft-text);
    }

    .attendance-detail-photo-card {
        height: 100%;
        padding: 1rem;
        border-radius: 20px;
        background: linear-gradient(180deg, #ffffff 0%, #f4f8ff 100%);
        border: 1px solid var(--attendance-detail-border);
    }

    .attendance-detail-photo-card__top,
    .attendance-detail-photo-card__meta {
        display: flex;
        flex-wrap: wrap;
        gap: .65rem;
        justify-content: space-between;
        align-items: center;
    }

    .attendance-detail-photo-card__label {
        color: var(--attendance-detail-soft-text);
        font-size: .84rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        font-weight: 700;
    }

    .attendance-detail-photo-card__image {
        width: 100%;
        margin: 1rem 0;
        max-height: 420px;
        object-fit: contain;
        border-radius: 18px;
        border: 1px solid var(--attendance-detail-border);
        background: #fff;
    }

    .attendance-detail-empty {
        text-align: center;
        padding: 2.5rem 1rem;
        margin-top: 1rem;
        border-radius: 18px;
        background: #f9fbfe;
        border: 1px dashed var(--attendance-detail-border);
        color: var(--attendance-detail-soft-text);
    }

    .attendance-detail-empty i {
        display: block;
        font-size: 2rem;
        margin-bottom: .6rem;
    }

    .attendance-detail-verify-form {
        display: grid;
        gap: 1rem;
    }

    .attendance-detail-choice-list {
        display: grid;
        gap: .85rem;
    }

    .attendance-detail-choice {
        display: flex;
        gap: .8rem;
        align-items: start;
        padding: .95rem 1rem;
        border-radius: 18px;
        border: 1px solid var(--attendance-detail-border);
        background: #fff;
        cursor: pointer;
    }

    .attendance-detail-choice input {
        margin-top: .25rem;
    }

    .attendance-detail-choice small {
        display: block;
        margin-top: .2rem;
        color: var(--attendance-detail-soft-text);
    }

    .attendance-detail-current-status {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: .75rem;
    }

    .attendance-detail-current-status__label {
        color: var(--attendance-detail-soft-text);
        font-size: .9rem;
    }

    .attendance-detail-device-list {
        display: grid;
        gap: .85rem;
    }

    .attendance-detail-device-item {
        display: flex;
        flex-direction: column;
        gap: .2rem;
    }

    .attendance-detail-device-item span {
        color: var(--attendance-detail-soft-text);
        font-size: .82rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        font-weight: 700;
    }

    .attendance-detail-user-agent {
        color: var(--attendance-detail-soft-text);
    }

    .attendance-detail-map__header {
        padding: 1.25rem 1.25rem 0;
    }

    .attendance-detail-map {
        height: 320px;
        margin-top: 1rem;
        border-radius: 0 0 22px 22px;
    }

    @media (min-width: 768px) {
        .attendance-detail-summary-grid {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }

        .attendance-detail-info-grid,
        .attendance-detail-note-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .attendance-detail-device-list {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .attendance-detail-panel__header {
            flex-direction: row;
            justify-content: space-between;
            align-items: start;
        }
    }

    @media (max-width: 767.98px) {
        .attendance-detail-hero {
            padding: 1.2rem;
            border-radius: 20px;
        }

        .attendance-detail-summary-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
@if($hasLocation)
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const mapElement = document.getElementById('map');

    if (!mapElement) {
        return;
    }

    const map = L.map('map');

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map);

    const bounds = L.latLngBounds();
    let markerCount = 0;

    @if($absensi->latitude_masuk && $absensi->longitude_masuk)
        L.marker([{{ $absensi->latitude_masuk }}, {{ $absensi->longitude_masuk }}])
            .addTo(map)
            .bindPopup('<strong>Check-in</strong><br>{{ $absensi->jam_masuk ?? '--:--' }}');
        bounds.extend([{{ $absensi->latitude_masuk }}, {{ $absensi->longitude_masuk }}]);
        markerCount++;
    @endif

    @if($absensi->latitude_keluar && $absensi->longitude_keluar)
        L.marker([{{ $absensi->latitude_keluar }}, {{ $absensi->longitude_keluar }}])
            .addTo(map)
            .bindPopup('<strong>Check-out</strong><br>{{ $absensi->jam_keluar ?? '--:--' }}');
        bounds.extend([{{ $absensi->latitude_keluar }}, {{ $absensi->longitude_keluar }}]);
        markerCount++;
    @endif

    if (markerCount > 0) {
        map.fitBounds(bounds, { padding: [32, 32] });
    } else {
        map.setView([-5.147665, 119.432732], 12);
    }
});
</script>
@endif
@endpush
