@extends('layouts.app')

@section('title', 'Monitoring Absensi')

@section('content')
@php
    $tanggalLabel = $tanggal->translatedFormat('l, d F Y');
    $pendingVerifikasi = $hadirList->where('status_verifikasi', 'pending')->count();
@endphp

<div class="monitoring-page">
    <div class="monitoring-hero mb-4">
        <div>
            <div class="monitoring-hero__eyebrow">Monitoring Real-Time</div>
            <h1 class="monitoring-hero__title">Pantau kehadiran karyawan per hari secara cepat.</h1>
            <p class="monitoring-hero__subtitle mb-0">
                Dashboard ini merangkum siapa yang sudah hadir, siapa yang belum absen, siapa yang terlambat, dan aktivitas check-in/check-out terbaru.
            </p>
        </div>
        <div class="monitoring-hero__meta">
            <div class="monitoring-pill">
                <i class="bi bi-calendar-event"></i>
                <span>{{ $tanggalLabel }}</span>
            </div>
            <div class="monitoring-pill">
                <i class="bi bi-patch-check"></i>
                <span>{{ $pendingVerifikasi }} pending verifikasi</span>
            </div>
        </div>
    </div>

    <div class="card monitoring-filter-card mb-4">
        <div class="card-body">
            <form action="{{ route('absensi.monitoring') }}" method="GET" class="monitoring-filter-form">
                <div>
                    <label class="form-label">Tanggal Monitoring</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ $tanggal->format('Y-m-d') }}">
                </div>
                <div class="monitoring-filter-form__actions">
                    <button type="submit" class="btn btn-secondary">
                        <i class="bi bi-filter"></i> Tampilkan
                    </button>
                    <a href="{{ route('absensi.monitoring') }}" class="btn btn-outline-secondary">Hari Ini</a>
                </div>
            </form>
            <div class="monitoring-refresh-note">
                <i class="bi bi-arrow-clockwise"></i>
                <span>Halaman ini refresh otomatis setiap 30 detik.</span>
            </div>
        </div>
    </div>

    <div class="monitoring-summary-grid mb-4">
        <div class="monitoring-summary-card primary">
            <span class="monitoring-summary-card__label">Total Karyawan</span>
            <strong class="monitoring-summary-card__value">{{ $stats['total_karyawan'] }}</strong>
            <span class="monitoring-summary-card__hint">Seluruh karyawan aktif</span>
        </div>
        <div class="monitoring-summary-card success">
            <span class="monitoring-summary-card__label">Sudah Hadir</span>
            <strong class="monitoring-summary-card__value">{{ $stats['sudah_hadir'] }}</strong>
            <span class="monitoring-summary-card__hint">Check-in tercatat</span>
        </div>
        <div class="monitoring-summary-card warning">
            <span class="monitoring-summary-card__label">Belum Hadir</span>
            <strong class="monitoring-summary-card__value">{{ $stats['belum_hadir'] }}</strong>
            <span class="monitoring-summary-card__hint">Belum ada check-in</span>
        </div>
        <div class="monitoring-summary-card info">
            <span class="monitoring-summary-card__label">Sudah Pulang</span>
            <strong class="monitoring-summary-card__value">{{ $stats['sudah_pulang'] }}</strong>
            <span class="monitoring-summary-card__hint">Check-out selesai</span>
        </div>
        <div class="monitoring-summary-card danger">
            <span class="monitoring-summary-card__label">Terlambat</span>
            <strong class="monitoring-summary-card__value">{{ $stats['terlambat'] }}</strong>
            <span class="monitoring-summary-card__hint">Masuk setelah 08:00</span>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card monitoring-panel h-100">
                <div class="card-body">
                    <div class="monitoring-panel__header mb-3">
                        <div>
                            <div class="monitoring-panel__eyebrow">Karyawan Hadir</div>
                            <h5 class="mb-1">{{ $hadirList->count() }} orang sudah absen</h5>
                            <p class="text-muted mb-0">Termasuk yang masih bekerja dan yang sudah check-out.</p>
                        </div>
                        <span class="monitoring-badge success">{{ $pendingVerifikasi }} pending</span>
                    </div>

                    <div class="monitoring-list">
                        @forelse($hadirList as $absensi)
                            <article class="monitoring-entry">
                                <div class="monitoring-entry__identity">
                                    @if($absensi->foto_masuk)
                                        <img src="{{ $absensi->foto_masuk_url }}" class="monitoring-avatar" alt="Foto {{ $absensi->karyawan->nama }}">
                                    @else
                                        <div class="monitoring-avatar monitoring-avatar--placeholder">
                                            <i class="bi bi-person"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h6 class="mb-1">{{ $absensi->karyawan->nama }}</h6>
                                        <div class="monitoring-entry__meta">
                                            <span><i class="bi bi-box-arrow-in-right text-success"></i> {{ $absensi->jam_masuk ?? '--:--' }}</span>
                                            <span><i class="bi bi-box-arrow-left text-warning"></i> {{ $absensi->jam_keluar ?? '--:--' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="monitoring-entry__side">
                                    <div class="monitoring-entry__badges">
                                        @if($absensi->jam_masuk && \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i') > '08:00')
                                            <span class="badge bg-warning text-dark">Terlambat</span>
                                        @endif
                                        @include('absensi.partials.status-verifikasi-badge', ['status' => $absensi->status_verifikasi])
                                    </div>
                                    <div class="monitoring-entry__detail">
                                        <span>{{ $absensi->durasi_kerja }}</span>
                                        <a href="{{ route('absensi.show', $absensi) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="monitoring-empty">
                                <i class="bi bi-inbox"></i>
                                <p class="mb-0">Belum ada karyawan yang hadir pada tanggal ini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card monitoring-panel h-100">
                <div class="card-body">
                    <div class="monitoring-panel__header mb-3">
                        <div>
                            <div class="monitoring-panel__eyebrow">Belum Hadir</div>
                            <h5 class="mb-1">{{ $belumHadirList->count() }} orang belum absen</h5>
                            <p class="text-muted mb-0">Gunakan daftar ini untuk follow-up cepat ke tim.</p>
                        </div>
                        <span class="monitoring-badge warning">Perlu ditindaklanjuti</span>
                    </div>

                    <div class="monitoring-list">
                        @forelse($belumHadirList as $karyawan)
                            <article class="monitoring-entry compact">
                                <div class="monitoring-entry__identity">
                                    <div class="monitoring-avatar monitoring-avatar--placeholder">
                                        <i class="bi bi-person"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">{{ $karyawan->nama }}</h6>
                                        <div class="text-muted">{{ $karyawan->jabatan }}</div>
                                    </div>
                                </div>
                                <div class="monitoring-entry__side">
                                    <span class="badge bg-secondary">Belum Absen</span>
                                </div>
                            </article>
                        @empty
                            <div class="monitoring-empty success">
                                <i class="bi bi-check-circle"></i>
                                <p class="mb-0">Semua karyawan sudah hadir hari ini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card monitoring-panel mt-4">
        <div class="card-body">
            <div class="monitoring-panel__header mb-3">
                <div>
                    <div class="monitoring-panel__eyebrow">Aktivitas Terbaru</div>
                    <h5 class="mb-1">Feed absensi terbaru</h5>
                    <p class="text-muted mb-0">Urutan terbaru untuk memudahkan verifikasi cepat.</p>
                </div>
                <span class="monitoring-badge primary">{{ $recentAbsensis->count() }} aktivitas</span>
            </div>

            <div class="monitoring-activity-feed">
                @forelse($recentAbsensis as $absensi)
                    <article class="monitoring-activity">
                        <div class="monitoring-activity__time">{{ $absensi->updated_at->format('H:i:s') }}</div>
                        <div class="monitoring-activity__body">
                            <div class="monitoring-activity__top">
                                <div>
                                    <h6 class="mb-1">{{ $absensi->karyawan->nama }}</h6>
                                    <div class="monitoring-activity__meta">
                                        @if($absensi->jam_keluar && $absensi->updated_at->diffInMinutes(\Carbon\Carbon::parse($absensi->jam_keluar)) < 5)
                                            <span class="badge bg-warning text-dark">Check-out</span>
                                        @else
                                            <span class="badge bg-success">Check-in</span>
                                        @endif
                                        @if($absensi->foto_masuk || $absensi->foto_keluar)
                                            <span class="monitoring-mini-flag"><i class="bi bi-camera-fill"></i> Foto ada</span>
                                        @else
                                            <span class="monitoring-mini-flag muted"><i class="bi bi-camera"></i> Tanpa foto</span>
                                        @endif
                                        @if($absensi->lokasi_masuk || $absensi->lokasi_keluar)
                                            <span class="monitoring-mini-flag"><i class="bi bi-geo-alt-fill"></i> Lokasi tercatat</span>
                                        @else
                                            <span class="monitoring-mini-flag muted"><i class="bi bi-geo-alt"></i> Lokasi kosong</span>
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ route('absensi.show', $absensi) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="monitoring-empty">
                        <i class="bi bi-clock-history"></i>
                        <p class="mb-0">Belum ada aktivitas absensi pada tanggal ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .monitoring-page {
        --monitoring-primary: #0f5ae0;
        --monitoring-primary-dark: #083b9a;
        --monitoring-surface: #f4f8ff;
        --monitoring-border: #d9e3f3;
        --monitoring-soft-text: #617494;
    }

    .monitoring-hero {
        border-radius: 24px;
        padding: 1.5rem;
        color: #fff;
        background:
            radial-gradient(circle at top right, rgba(255,255,255,.18), transparent 35%),
            linear-gradient(135deg, #112e72 0%, #0f5ae0 55%, #2aa8ff 100%);
        box-shadow: 0 18px 48px rgba(12, 67, 166, 0.18);
    }

    .monitoring-hero__eyebrow,
    .monitoring-panel__eyebrow {
        text-transform: uppercase;
        letter-spacing: .08em;
        font-size: .74rem;
        font-weight: 700;
        opacity: .86;
        margin-bottom: .45rem;
    }

    .monitoring-hero__title {
        font-size: clamp(1.55rem, 4vw, 2.2rem);
        line-height: 1.1;
        margin-bottom: .65rem;
    }

    .monitoring-hero__subtitle {
        max-width: 44rem;
        opacity: .92;
    }

    .monitoring-hero__meta {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
        margin-top: 1rem;
    }

    .monitoring-pill {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .65rem .9rem;
        border-radius: 999px;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.18);
        backdrop-filter: blur(10px);
    }

    .monitoring-filter-card,
    .monitoring-panel {
        border-radius: 22px;
        border: 1px solid var(--monitoring-border);
        box-shadow: 0 12px 32px rgba(12, 52, 118, 0.06);
    }

    .monitoring-filter-form {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .monitoring-filter-form__actions {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
        align-items: end;
    }

    .monitoring-refresh-note {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        margin-top: 1rem;
        padding: .75rem 1rem;
        border-radius: 14px;
        background: var(--monitoring-surface);
        border: 1px solid var(--monitoring-border);
        color: var(--monitoring-soft-text);
        font-size: .9rem;
    }

    .monitoring-summary-grid {
        display: grid;
        gap: .85rem;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .monitoring-summary-card {
        display: flex;
        flex-direction: column;
        gap: .15rem;
        padding: 1rem;
        border-radius: 20px;
        border: 1px solid var(--monitoring-border);
        background: #fff;
        box-shadow: 0 12px 30px rgba(12, 52, 118, 0.06);
    }

    .monitoring-summary-card.primary { border-color: #c7d8f7; background: #f3f8ff; }
    .monitoring-summary-card.success { border-color: #c9ead8; background: #f2fcf6; }
    .monitoring-summary-card.warning { border-color: #f2e0a1; background: #fff9e8; }
    .monitoring-summary-card.info { border-color: #cae8f0; background: #f2fbff; }
    .monitoring-summary-card.danger { border-color: #f2cfd3; background: #fff4f5; }

    .monitoring-summary-card__label {
        color: var(--monitoring-soft-text);
        font-size: .82rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        font-weight: 700;
    }

    .monitoring-summary-card__value {
        font-size: 1.9rem;
        line-height: 1;
        color: #18345f;
    }

    .monitoring-summary-card__hint {
        color: var(--monitoring-soft-text);
        font-size: .88rem;
    }

    .monitoring-panel__header {
        display: flex;
        flex-direction: column;
        gap: .85rem;
    }

    .monitoring-badge {
        display: inline-flex;
        width: fit-content;
        align-items: center;
        padding: .55rem .85rem;
        border-radius: 999px;
        font-size: .86rem;
        font-weight: 700;
        border: 1px solid transparent;
    }

    .monitoring-badge.primary { background: #edf4ff; border-color: #c7d8f7; color: #0f5ae0; }
    .monitoring-badge.success { background: #eefbf4; border-color: #c9ead8; color: #0a8752; }
    .monitoring-badge.warning { background: #fff8e4; border-color: #f2e0a1; color: #9a6b00; }

    .monitoring-list,
    .monitoring-activity-feed {
        display: grid;
        gap: 1rem;
    }

    .monitoring-entry,
    .monitoring-activity {
        border: 1px solid var(--monitoring-border);
        border-radius: 18px;
        padding: 1rem;
        background: #fff;
    }

    .monitoring-entry {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .monitoring-entry__identity {
        display: flex;
        align-items: center;
        gap: .85rem;
    }

    .monitoring-entry__meta,
    .monitoring-mini-flag,
    .monitoring-entry__detail,
    .monitoring-activity__meta {
        color: var(--monitoring-soft-text);
        font-size: .88rem;
    }

    .monitoring-entry__meta,
    .monitoring-activity__meta {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
    }

    .monitoring-entry__side,
    .monitoring-activity__top {
        display: flex;
        flex-direction: column;
        gap: .75rem;
    }

    .monitoring-entry__badges,
    .monitoring-entry__detail {
        display: flex;
        flex-wrap: wrap;
        gap: .65rem;
        align-items: center;
    }

    .monitoring-avatar {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }

    .monitoring-avatar--placeholder {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #dbe6f5;
        color: #4c648d;
        font-size: 1.5rem;
    }

    .monitoring-activity {
        display: grid;
        grid-template-columns: 86px minmax(0, 1fr);
        gap: 1rem;
        align-items: start;
    }

    .monitoring-activity__time {
        font-weight: 700;
        color: #17315d;
        padding: .6rem .75rem;
        border-radius: 14px;
        background: var(--monitoring-surface);
        border: 1px solid var(--monitoring-border);
        text-align: center;
    }

    .monitoring-mini-flag {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
    }

    .monitoring-mini-flag.muted {
        opacity: .8;
    }

    .monitoring-empty {
        text-align: center;
        padding: 2rem 1rem;
        border-radius: 18px;
        background: #f9fbfe;
        border: 1px dashed var(--monitoring-border);
        color: var(--monitoring-soft-text);
    }

    .monitoring-empty.success {
        background: #f2fcf6;
        color: #0b7a4e;
        border-color: #c9ead8;
    }

    .monitoring-empty i {
        display: block;
        font-size: 2rem;
        margin-bottom: .6rem;
    }

    @media (min-width: 768px) {
        .monitoring-filter-form {
            grid-template-columns: minmax(0, 240px) auto;
            align-items: end;
        }

        .monitoring-summary-grid {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }

        .monitoring-panel__header,
        .monitoring-entry,
        .monitoring-entry__side,
        .monitoring-activity__top {
            flex-direction: row;
            justify-content: space-between;
            align-items: start;
        }
    }

    @media (max-width: 767.98px) {
        .monitoring-hero {
            padding: 1.2rem;
            border-radius: 20px;
        }

        .monitoring-activity {
            grid-template-columns: 1fr;
        }

        .monitoring-activity__time {
            width: fit-content;
        }
    }
</style>
@endpush

@push('scripts')
<script>
setInterval(function() {
    location.reload();
}, 30000);
</script>
@endpush
