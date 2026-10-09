@extends('layouts.app')

@section('title', 'Riwayat Absensi Saya')

@section('content')
@php
    $bulanLabel = \Carbon\Carbon::parse($bulan . '-01')->translatedFormat('F Y');
    $latestAbsensi = $absensis->sortByDesc('tanggal')->first();
@endphp

<div class="attendance-page">
    <div class="attendance-hero mb-4">
        <div>
            <div class="attendance-hero__eyebrow">Riwayat Absensi</div>
            <h1 class="attendance-hero__title">Pantau kehadiran Anda dengan ringkas.</h1>
            <p class="attendance-hero__subtitle mb-0">
                Semua check-in, check-out, foto verifikasi, dan status approval tersusun per bulan agar mudah dicek dari HP.
            </p>
        </div>
        <div class="attendance-hero__meta">
            <div class="attendance-pill">
                <i class="bi bi-person-badge"></i>
                <span>{{ $karyawan->nama }}</span>
            </div>
            <div class="attendance-pill">
                <i class="bi bi-calendar-event"></i>
                <span>{{ $bulanLabel }}</span>
            </div>
            <a href="{{ route('absensi.scan') }}" class="btn btn-light attendance-hero__cta">
                <i class="bi bi-qr-code-scan"></i> Scan Barcode
            </a>
        </div>
    </div>

    <div class="attendance-summary-grid mb-4">
        <div class="attendance-summary-card success">
            <span class="attendance-summary-card__label">Hadir</span>
            <strong class="attendance-summary-card__value">{{ $stats['hadir'] }}</strong>
            <span class="attendance-summary-card__hint">Hari efektif tercatat</span>
        </div>
        <div class="attendance-summary-card warning">
            <span class="attendance-summary-card__label">Izin</span>
            <strong class="attendance-summary-card__value">{{ $stats['izin'] }}</strong>
            <span class="attendance-summary-card__hint">Persetujuan izin</span>
        </div>
        <div class="attendance-summary-card info">
            <span class="attendance-summary-card__label">Sakit</span>
            <strong class="attendance-summary-card__value">{{ $stats['sakit'] }}</strong>
            <span class="attendance-summary-card__hint">Tidak masuk karena sakit</span>
        </div>
        <div class="attendance-summary-card danger">
            <span class="attendance-summary-card__label">Alpha</span>
            <strong class="attendance-summary-card__value">{{ $stats['alpha'] }}</strong>
            <span class="attendance-summary-card__hint">Belum ada absensi</span>
        </div>
        <div class="attendance-summary-card neutral">
            <span class="attendance-summary-card__label">Total Catatan</span>
            <strong class="attendance-summary-card__value">{{ $stats['total'] }}</strong>
            <span class="attendance-summary-card__hint">Semua status bulan ini</span>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="card attendance-panel h-100">
                <div class="card-body">
                    <div class="attendance-panel__eyebrow">Snapshot Terkini</div>
                    <h5 class="mb-3">Status absensi terakhir</h5>

                    @if($latestAbsensi)
                        <div class="attendance-status-card">
                            <div class="attendance-status-card__top">
                                <div>
                                    <div class="attendance-status-card__date">{{ $latestAbsensi->tanggal->translatedFormat('l, d M Y') }}</div>
                                    <div class="attendance-status-card__meta">{{ $latestAbsensi->durasi_kerja }}</div>
                                </div>
                                <div>
                                    @if($latestAbsensi->status === 'hadir')
                                        <span class="badge bg-success">Hadir</span>
                                    @elseif($latestAbsensi->status === 'izin')
                                        <span class="badge bg-warning text-dark">Izin</span>
                                    @elseif($latestAbsensi->status === 'sakit')
                                        <span class="badge bg-info text-dark">Sakit</span>
                                    @elseif($latestAbsensi->status === 'cuti')
                                        <span class="badge bg-primary">Cuti</span>
                                    @else
                                        <span class="badge bg-danger">Alpha</span>
                                    @endif
                                </div>
                            </div>
                            <div class="attendance-status-card__grid">
                                <div>
                                    <span class="attendance-status-card__label">Masuk</span>
                                    <strong>{{ $latestAbsensi->jam_masuk ?? '--:--' }}</strong>
                                </div>
                                <div>
                                    <span class="attendance-status-card__label">Pulang</span>
                                    <strong>{{ $latestAbsensi->jam_keluar ?? '--:--' }}</strong>
                                </div>
                                <div>
                                    <span class="attendance-status-card__label">Verifikasi</span>
                                    <strong>{{ ucfirst($latestAbsensi->status_verifikasi ?? 'pending') }}</strong>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="attendance-empty">
                            <i class="bi bi-calendar-x"></i>
                            <p class="mb-0">Belum ada absensi di bulan ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card attendance-panel h-100">
                <div class="card-body">
                    <div class="attendance-panel__eyebrow">Filter Data</div>
                    <h5 class="mb-3">Pilih bulan yang ingin ditinjau</h5>

                    <form action="{{ route('absensi.my-attendance') }}" method="GET" class="attendance-filter-form">
                        <div>
                            <label class="form-label">Bulan</label>
                            <input type="month" name="bulan" class="form-control" value="{{ $bulan }}">
                        </div>
                        <div class="attendance-filter-form__actions">
                            <button type="submit" class="btn btn-secondary">
                                <i class="bi bi-filter"></i> Tampilkan
                            </button>
                            <a href="{{ route('absensi.my-attendance') }}" class="btn btn-outline-secondary">Bulan Ini</a>
                        </div>
                    </form>

                    <div class="attendance-filter-note">
                        <i class="bi bi-shield-check"></i>
                        <span>Foto dan lokasi hanya muncul jika absensi dibuat lewat scan barcode.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card attendance-panel">
        <div class="card-body">
            <div class="attendance-panel__header mb-3">
                <div>
                    <div class="attendance-panel__eyebrow">Detail Harian</div>
                    <h5 class="mb-1">Absensi {{ $bulanLabel }}</h5>
                    <p class="text-muted mb-0">Riwayat check-in/check-out, verifikasi, lokasi, dan bukti foto.</p>
                </div>
                <span class="attendance-record-count">{{ $absensis->count() }} catatan</span>
            </div>

            <div class="attendance-timeline">
                @forelse($absensis as $absensi)
                    <article class="attendance-entry">
                        <div class="attendance-entry__header">
                            <div>
                                <h6 class="mb-1">{{ $absensi->tanggal->translatedFormat('l, d F Y') }}</h6>
                                <div class="attendance-entry__meta">
                                    <span><i class="bi bi-box-arrow-in-right text-success"></i> {{ $absensi->jam_masuk ?? '--:--' }}</span>
                                    <span><i class="bi bi-box-arrow-left text-warning"></i> {{ $absensi->jam_keluar ?? '--:--' }}</span>
                                    <span><i class="bi bi-hourglass-split text-primary"></i> {{ $absensi->durasi_kerja }}</span>
                                </div>
                            </div>
                            <div class="attendance-entry__badges">
                                @if($absensi->status === 'hadir')
                                    <span class="badge bg-success">Hadir</span>
                                @elseif($absensi->status === 'izin')
                                    <span class="badge bg-warning text-dark">Izin</span>
                                @elseif($absensi->status === 'sakit')
                                    <span class="badge bg-info text-dark">Sakit</span>
                                @elseif($absensi->status === 'cuti')
                                    <span class="badge bg-primary">Cuti</span>
                                @else
                                    <span class="badge bg-danger">Alpha</span>
                                @endif
                                @include('absensi.partials.status-verifikasi-badge', ['status' => $absensi->status_verifikasi])
                            </div>
                        </div>

                        <div class="attendance-entry__grid">
                            <div class="attendance-entry__info">
                                <span class="attendance-entry__label">Lokasi Check-in</span>
                                <strong>{{ $absensi->lokasi_masuk ?? 'Tidak tercatat' }}</strong>
                            </div>
                            <div class="attendance-entry__info">
                                <span class="attendance-entry__label">Lokasi Check-out</span>
                                <strong>{{ $absensi->lokasi_keluar ?? 'Tidak tercatat' }}</strong>
                            </div>
                            <div class="attendance-entry__info">
                                <span class="attendance-entry__label">Catatan</span>
                                <strong>{{ $absensi->keterangan ?: 'Tidak ada catatan' }}</strong>
                            </div>
                        </div>

                        @if($absensi->foto_masuk || $absensi->foto_keluar)
                            <div class="attendance-entry__actions">
                                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#fotoModal{{ $absensi->id }}">
                                    <i class="bi bi-images"></i> Lihat Foto Absensi
                                </button>
                            </div>

                            <div class="modal fade" id="fotoModal{{ $absensi->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content attendance-photo-modal">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Foto Absensi - {{ $absensi->tanggal->translatedFormat('d F Y') }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                @if($absensi->foto_masuk)
                                                    <div class="col-md-6">
                                                        <div class="attendance-photo-card">
                                                            <span class="attendance-photo-card__label">Foto Check-in</span>
                                                            <img src="{{ $absensi->foto_masuk_url }}" class="img-fluid rounded" alt="Foto Masuk">
                                                            <small class="text-muted mt-2 d-block">{{ $absensi->jam_masuk ?? '--:--' }}</small>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if($absensi->foto_keluar)
                                                    <div class="col-md-6">
                                                        <div class="attendance-photo-card">
                                                            <span class="attendance-photo-card__label">Foto Check-out</span>
                                                            <img src="{{ $absensi->foto_keluar_url }}" class="img-fluid rounded" alt="Foto Keluar">
                                                            <small class="text-muted mt-2 d-block">{{ $absensi->jam_keluar ?? '--:--' }}</small>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </article>
                @empty
                    <div class="attendance-empty">
                        <i class="bi bi-inbox"></i>
                        <p class="mb-0">Belum ada data absensi untuk bulan {{ $bulanLabel }}.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .attendance-page {
        --attendance-primary: #0f5ae0;
        --attendance-primary-dark: #083b9a;
        --attendance-surface: #f4f8ff;
        --attendance-border: #d9e3f3;
        --attendance-soft-text: #617494;
    }

    .attendance-hero {
        border-radius: 24px;
        padding: 1.5rem;
        color: #fff;
        background:
            radial-gradient(circle at top right, rgba(255,255,255,.18), transparent 35%),
            linear-gradient(135deg, #0e4fd1 0%, #0b6bf0 55%, #2aa8ff 100%);
        box-shadow: 0 18px 48px rgba(12, 67, 166, 0.18);
    }

    .attendance-hero__eyebrow,
    .attendance-panel__eyebrow {
        text-transform: uppercase;
        letter-spacing: .08em;
        font-size: .74rem;
        font-weight: 700;
        opacity: .85;
        margin-bottom: .45rem;
    }

    .attendance-hero__title {
        font-size: clamp(1.55rem, 4vw, 2.2rem);
        line-height: 1.1;
        margin-bottom: .65rem;
    }

    .attendance-hero__subtitle {
        max-width: 44rem;
        opacity: .92;
    }

    .attendance-hero__meta {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
        margin-top: 1rem;
        align-items: center;
    }

    .attendance-pill {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .65rem .9rem;
        border-radius: 999px;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.18);
        backdrop-filter: blur(10px);
    }

    .attendance-hero__cta {
        border-radius: 999px;
        font-weight: 600;
    }

    .attendance-summary-grid {
        display: grid;
        gap: .85rem;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .attendance-summary-card {
        display: flex;
        flex-direction: column;
        gap: .15rem;
        padding: 1rem;
        border-radius: 20px;
        border: 1px solid var(--attendance-border);
        background: #fff;
        box-shadow: 0 12px 30px rgba(12, 52, 118, 0.06);
    }

    .attendance-summary-card.success { border-color: #c9ead8; background: #f2fcf6; }
    .attendance-summary-card.warning { border-color: #f2e0a1; background: #fff9e8; }
    .attendance-summary-card.info { border-color: #cae8f0; background: #f2fbff; }
    .attendance-summary-card.danger { border-color: #f2cfd3; background: #fff4f5; }
    .attendance-summary-card.neutral { border-color: #dfe6f2; background: #f7f9fd; }

    .attendance-summary-card__label {
        color: var(--attendance-soft-text);
        font-size: .82rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        font-weight: 700;
    }

    .attendance-summary-card__value {
        font-size: 1.9rem;
        line-height: 1;
        color: #18345f;
    }

    .attendance-summary-card__hint {
        color: var(--attendance-soft-text);
        font-size: .88rem;
    }

    .attendance-panel {
        border-radius: 22px;
        border: 1px solid var(--attendance-border);
        box-shadow: 0 12px 32px rgba(12, 52, 118, 0.06);
    }

    .attendance-status-card {
        padding: 1rem;
        border-radius: 18px;
        background: linear-gradient(180deg, #ffffff 0%, #f4f8ff 100%);
        border: 1px solid var(--attendance-border);
    }

    .attendance-status-card__top {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .attendance-status-card__date {
        font-weight: 700;
        color: #17315d;
    }

    .attendance-status-card__meta,
    .attendance-record-count,
    .attendance-filter-note,
    .attendance-entry__label {
        color: var(--attendance-soft-text);
        font-size: .88rem;
    }

    .attendance-status-card__grid,
    .attendance-entry__grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: .75rem;
    }

    .attendance-status-card__label {
        display: block;
        font-size: .8rem;
        color: var(--attendance-soft-text);
        margin-bottom: .2rem;
    }

    .attendance-filter-form {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .attendance-filter-form__actions {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
        align-items: end;
    }

    .attendance-filter-note {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        margin-top: 1rem;
        padding: .75rem 1rem;
        border-radius: 14px;
        background: var(--attendance-surface);
        border: 1px solid var(--attendance-border);
    }

    .attendance-panel__header {
        display: flex;
        flex-direction: column;
        gap: .85rem;
    }

    .attendance-record-count {
        display: inline-flex;
        width: fit-content;
        padding: .55rem .85rem;
        border-radius: 999px;
        background: var(--attendance-surface);
        border: 1px solid var(--attendance-border);
        font-weight: 600;
    }

    .attendance-timeline {
        display: grid;
        gap: 1rem;
    }

    .attendance-entry {
        padding: 1rem;
        border-radius: 18px;
        border: 1px solid var(--attendance-border);
        background: #fff;
    }

    .attendance-entry__header {
        display: flex;
        flex-direction: column;
        gap: .85rem;
        margin-bottom: .9rem;
    }

    .attendance-entry__meta {
        display: flex;
        flex-wrap: wrap;
        gap: .85rem;
        color: var(--attendance-soft-text);
        font-size: .9rem;
    }

    .attendance-entry__badges {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
    }

    .attendance-entry__info {
        padding: .85rem;
        border-radius: 14px;
        background: var(--attendance-surface);
        border: 1px solid var(--attendance-border);
    }

    .attendance-entry__label {
        display: block;
        margin-bottom: .2rem;
    }

    .attendance-entry__actions {
        margin-top: .9rem;
    }

    .attendance-empty {
        text-align: center;
        padding: 2rem 1rem;
        border-radius: 18px;
        background: #f9fbfe;
        border: 1px dashed var(--attendance-border);
        color: var(--attendance-soft-text);
    }

    .attendance-empty i {
        display: block;
        font-size: 2rem;
        margin-bottom: .6rem;
    }

    .attendance-photo-modal {
        border-radius: 20px;
        overflow: hidden;
    }

    .attendance-photo-card {
        padding: .9rem;
        border-radius: 16px;
        background: var(--attendance-surface);
        border: 1px solid var(--attendance-border);
    }

    .attendance-photo-card__label {
        display: inline-block;
        margin-bottom: .65rem;
        font-size: .84rem;
        color: var(--attendance-soft-text);
        text-transform: uppercase;
        letter-spacing: .05em;
        font-weight: 700;
    }

    @media (min-width: 768px) {
        .attendance-summary-grid {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }

        .attendance-status-card__grid,
        .attendance-entry__grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .attendance-filter-form {
            grid-template-columns: minmax(0, 260px) auto;
            align-items: end;
        }

        .attendance-panel__header,
        .attendance-entry__header {
            flex-direction: row;
            justify-content: space-between;
            align-items: start;
        }
    }
</style>
@endpush
