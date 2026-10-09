@extends('layouts.app')

@section('title', 'Absensi Karyawan')

@section('content')
@php
    $tanggalLabel = $selectedDate->translatedFormat('l, d F Y');
    $activeFilters = collect([
        request('tanggal'),
        request('karyawan_id'),
        request('status'),
        request('status_verifikasi'),
    ])->filter(fn ($value) => filled($value))->count();
@endphp

<div class="attendance-admin-page">
    <div class="attendance-admin-hero mb-4">
        <div>
            <div class="attendance-admin-hero__eyebrow">Daftar Absensi</div>
            <h1 class="attendance-admin-hero__title">Kelola absensi harian karyawan dari satu tempat.</h1>
            <p class="attendance-admin-hero__subtitle mb-0">
                Gunakan filter untuk memeriksa status hadir, verifikasi foto, serta kebutuhan edit dan tindak lanjut admin.
            </p>
        </div>
        <div class="attendance-admin-hero__meta">
            <div class="attendance-admin-pill">
                <i class="bi bi-calendar-event"></i>
                <span>{{ $tanggalLabel }}</span>
            </div>
            <div class="attendance-admin-pill">
                <i class="bi bi-funnel"></i>
                <span>{{ $activeFilters }} filter aktif</span>
            </div>
            <a href="{{ route('absensi.monitoring') }}" class="btn btn-light attendance-admin-hero__cta">
                <i class="bi bi-broadcast"></i> Monitoring
            </a>
            <a href="{{ route('absensi.rekap') }}" class="btn btn-outline-light attendance-admin-hero__cta">
                <i class="bi bi-calendar-month"></i> Rekap
            </a>
        </div>
    </div>

    <div class="card attendance-admin-filter-card mb-4">
        <div class="card-body">
            <form action="{{ route('absensi.index') }}" method="GET" class="attendance-admin-filter-form">
                <div>
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal', now()->toDateString()) }}">
                </div>
                <div>
                    <label class="form-label">Karyawan</label>
                    <select name="karyawan_id" class="form-select">
                        <option value="">Semua</option>
                        @foreach($karyawans as $karyawan)
                            <option value="{{ $karyawan->id }}" @selected(request('karyawan_id') == $karyawan->id)>{{ $karyawan->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua</option>
                        <option value="hadir" @selected(request('status') === 'hadir')>Hadir</option>
                        <option value="izin" @selected(request('status') === 'izin')>Izin</option>
                        <option value="sakit" @selected(request('status') === 'sakit')>Sakit</option>
                        <option value="alpha" @selected(request('status') === 'alpha')>Alpha</option>
                        <option value="cuti" @selected(request('status') === 'cuti')>Cuti</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Verifikasi</label>
                    <select name="status_verifikasi" class="form-select">
                        <option value="">Semua</option>
                        <option value="pending" @selected(request('status_verifikasi') === 'pending')>Pending</option>
                        <option value="terverifikasi" @selected(request('status_verifikasi') === 'terverifikasi')>Terverifikasi</option>
                        <option value="ditolak" @selected(request('status_verifikasi') === 'ditolak')>Ditolak</option>
                    </select>
                </div>
                <div class="attendance-admin-filter-form__actions">
                    <button type="submit" class="btn btn-secondary">
                        <i class="bi bi-filter"></i> Terapkan Filter
                    </button>
                    <a href="{{ route('absensi.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
            <div class="attendance-admin-filter-note">
                <i class="bi bi-info-circle"></i>
                <span>Semua statistik di bawah mengikuti filter yang sedang aktif.</span>
            </div>
        </div>
    </div>

    <div class="attendance-admin-summary-grid mb-4">
        <div class="attendance-admin-summary-card primary">
            <span class="attendance-admin-summary-card__label">Total Hasil</span>
            <strong class="attendance-admin-summary-card__value">{{ $stats['total'] }}</strong>
            <span class="attendance-admin-summary-card__hint">Record sesuai filter</span>
        </div>
        <div class="attendance-admin-summary-card warning">
            <span class="attendance-admin-summary-card__label">Pending Verifikasi</span>
            <strong class="attendance-admin-summary-card__value">{{ $stats['pending_verifikasi'] }}</strong>
            <span class="attendance-admin-summary-card__hint">Perlu review admin</span>
        </div>
        <div class="attendance-admin-summary-card success">
            <span class="attendance-admin-summary-card__label">Terverifikasi</span>
            <strong class="attendance-admin-summary-card__value">{{ $stats['terverifikasi'] }}</strong>
            <span class="attendance-admin-summary-card__hint">Sudah lolos review</span>
        </div>
        <div class="attendance-admin-summary-card info">
            <span class="attendance-admin-summary-card__label">Sudah Check-out</span>
            <strong class="attendance-admin-summary-card__value">{{ $stats['sudah_checkout'] }}</strong>
            <span class="attendance-admin-summary-card__hint">Hari kerja lengkap</span>
        </div>
        <div class="attendance-admin-summary-card neutral">
            <span class="attendance-admin-summary-card__label">Dengan Foto</span>
            <strong class="attendance-admin-summary-card__value">{{ $stats['dengan_foto'] }}</strong>
            <span class="attendance-admin-summary-card__hint">Bukti selfie tersedia</span>
        </div>
    </div>

    <div class="card attendance-admin-panel">
        <div class="card-body">
            <div class="attendance-admin-panel__header mb-3">
                <div>
                    <div class="attendance-admin-panel__eyebrow">Data Harian</div>
                    <h5 class="mb-1">Absensi karyawan yang difilter</h5>
                    <p class="text-muted mb-0">Lihat detail, edit data manual, atau hapus record yang tidak valid.</p>
                </div>
                <span class="attendance-admin-record-count">{{ $absensis->total() }} record</span>
            </div>

            <div class="attendance-admin-list">
                @forelse($absensis as $absensi)
                    @php
                        $statusConfig = match ($absensi->status) {
                            'hadir' => ['label' => 'Hadir', 'class' => 'success'],
                            'izin' => ['label' => 'Izin', 'class' => 'warning text-dark'],
                            'sakit' => ['label' => 'Sakit', 'class' => 'info text-dark'],
                            'cuti' => ['label' => 'Cuti', 'class' => 'primary'],
                            default => ['label' => 'Alpha', 'class' => 'danger'],
                        };
                    @endphp
                    <article class="attendance-admin-entry">
                        <div class="attendance-admin-entry__top">
                            <div class="attendance-admin-entry__identity">
                                @if($absensi->foto_masuk)
                                    <img src="{{ $absensi->foto_masuk_url }}" class="attendance-admin-avatar" alt="Foto {{ $absensi->karyawan->nama }}">
                                @else
                                    <div class="attendance-admin-avatar attendance-admin-avatar--placeholder">
                                        <i class="bi bi-person"></i>
                                    </div>
                                @endif
                                <div>
                                    <h6 class="mb-1">{{ $absensi->karyawan->nama }}</h6>
                                    <div class="attendance-admin-entry__meta">
                                        <span><i class="bi bi-calendar-event"></i> {{ $absensi->tanggal->translatedFormat('d M Y') }}</span>
                                        <span>{{ $absensi->karyawan->jabatan }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="attendance-admin-entry__actions">
                                <a href="{{ route('absensi.show', $absensi) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                                @if(auth()->user()->hasRole(['admin', 'super-admin']))
                                    <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $absensi->id }}">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <form action="{{ route('absensi.destroy', $absensi) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus absensi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <div class="attendance-admin-entry__badges">
                            <span class="badge bg-{{ $statusConfig['class'] }}">{{ $statusConfig['label'] }}</span>
                            @include('absensi.partials.status-verifikasi-badge', ['status' => $absensi->status_verifikasi])
                            @if($absensi->foto_masuk || $absensi->foto_keluar)
                                <span class="badge bg-success">Foto ada</span>
                            @else
                                <span class="badge bg-secondary">Tanpa foto</span>
                            @endif
                        </div>

                        <div class="attendance-admin-entry__grid">
                            <div class="attendance-admin-info">
                                <span class="attendance-admin-info__label">Jam Masuk</span>
                                <strong>{{ $absensi->jam_masuk ?? '--:--' }}</strong>
                                <small>Check-in tercatat</small>
                            </div>
                            <div class="attendance-admin-info">
                                <span class="attendance-admin-info__label">Jam Keluar</span>
                                <strong>{{ $absensi->jam_keluar ?? '--:--' }}</strong>
                                <small>{{ $absensi->jam_keluar ? 'Check-out selesai' : 'Belum check-out' }}</small>
                            </div>
                            <div class="attendance-admin-info">
                                <span class="attendance-admin-info__label">Durasi Kerja</span>
                                <strong>{{ $absensi->durasi_kerja }}</strong>
                                <small>Hitung otomatis dari waktu kerja</small>
                            </div>
                            <div class="attendance-admin-info">
                                <span class="attendance-admin-info__label">Barcode</span>
                                <strong>{{ $absensi->barcode ?: '-' }}</strong>
                                <small>{{ $absensi->barcode ? 'Scan tersedia' : 'Input manual / lama' }}</small>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="attendance-admin-empty">
                        <i class="bi bi-inbox"></i>
                        <p class="mb-0">Tidak ada data absensi untuk filter yang dipilih.</p>
                    </div>
                @endforelse
            </div>

            @if($absensis->hasPages())
                <div class="mt-4">
                    {{ $absensis->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@foreach($absensis as $absensi)
<div class="modal fade" id="editModal{{ $absensi->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content attendance-admin-modal">
            <form action="{{ route('absensi.update', $absensi) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Absensi - {{ $absensi->karyawan->nama }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Jam Masuk</label>
                        <input type="time" name="jam_masuk" class="form-control" value="{{ $absensi->jam_masuk ? \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i') : '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jam Keluar</label>
                        <input type="time" name="jam_keluar" class="form-control" value="{{ $absensi->jam_keluar ? \Carbon\Carbon::parse($absensi->jam_keluar)->format('H:i') : '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="hadir" @selected($absensi->status === 'hadir')>Hadir</option>
                            <option value="izin" @selected($absensi->status === 'izin')>Izin</option>
                            <option value="sakit" @selected($absensi->status === 'sakit')>Sakit</option>
                            <option value="alpha" @selected($absensi->status === 'alpha')>Alpha</option>
                            <option value="cuti" @selected($absensi->status === 'cuti')>Cuti</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3">{{ $absensi->keterangan }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('styles')
<style>
    .attendance-admin-page {
        --attendance-admin-primary: #0f5ae0;
        --attendance-admin-surface: #f4f8ff;
        --attendance-admin-border: #d9e3f3;
        --attendance-admin-soft-text: #617494;
    }

    .attendance-admin-hero {
        border-radius: 24px;
        padding: 1.5rem;
        color: #fff;
        background:
            radial-gradient(circle at top right, rgba(255,255,255,.18), transparent 35%),
            linear-gradient(135deg, #112e72 0%, #0f5ae0 55%, #2aa8ff 100%);
        box-shadow: 0 18px 48px rgba(12, 67, 166, 0.18);
    }

    .attendance-admin-hero__eyebrow,
    .attendance-admin-panel__eyebrow {
        text-transform: uppercase;
        letter-spacing: .08em;
        font-size: .74rem;
        font-weight: 700;
        opacity: .86;
        margin-bottom: .45rem;
    }

    .attendance-admin-hero__title {
        font-size: clamp(1.55rem, 4vw, 2.2rem);
        line-height: 1.1;
        margin-bottom: .65rem;
    }

    .attendance-admin-hero__subtitle {
        max-width: 44rem;
        opacity: .92;
    }

    .attendance-admin-hero__meta {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
        margin-top: 1rem;
        align-items: center;
    }

    .attendance-admin-pill {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .65rem .9rem;
        border-radius: 999px;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.18);
        backdrop-filter: blur(10px);
    }

    .attendance-admin-hero__cta {
        border-radius: 999px;
    }

    .attendance-admin-filter-card,
    .attendance-admin-panel {
        border-radius: 22px;
        border: 1px solid var(--attendance-admin-border);
        box-shadow: 0 12px 32px rgba(12, 52, 118, 0.06);
    }

    .attendance-admin-filter-form {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .attendance-admin-filter-form__actions {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
        align-items: end;
    }

    .attendance-admin-filter-note {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        margin-top: 1rem;
        padding: .75rem 1rem;
        border-radius: 14px;
        background: var(--attendance-admin-surface);
        border: 1px solid var(--attendance-admin-border);
        color: var(--attendance-admin-soft-text);
        font-size: .9rem;
    }

    .attendance-admin-summary-grid {
        display: grid;
        gap: .85rem;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .attendance-admin-summary-card {
        display: flex;
        flex-direction: column;
        gap: .15rem;
        padding: 1rem;
        border-radius: 20px;
        border: 1px solid var(--attendance-admin-border);
        background: #fff;
        box-shadow: 0 12px 30px rgba(12, 52, 118, 0.06);
    }

    .attendance-admin-summary-card.primary { border-color: #c7d8f7; background: #f3f8ff; }
    .attendance-admin-summary-card.warning { border-color: #f2e0a1; background: #fff9e8; }
    .attendance-admin-summary-card.success { border-color: #c9ead8; background: #f2fcf6; }
    .attendance-admin-summary-card.info { border-color: #cae8f0; background: #f2fbff; }
    .attendance-admin-summary-card.neutral { border-color: #dfe6f2; background: #f7f9fd; }

    .attendance-admin-summary-card__label {
        color: var(--attendance-admin-soft-text);
        font-size: .82rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        font-weight: 700;
    }

    .attendance-admin-summary-card__value {
        font-size: 1.85rem;
        line-height: 1;
        color: #18345f;
    }

    .attendance-admin-summary-card__hint {
        color: var(--attendance-admin-soft-text);
        font-size: .88rem;
    }

    .attendance-admin-panel__header {
        display: flex;
        flex-direction: column;
        gap: .85rem;
    }

    .attendance-admin-record-count {
        display: inline-flex;
        width: fit-content;
        padding: .55rem .85rem;
        border-radius: 999px;
        background: var(--attendance-admin-surface);
        border: 1px solid var(--attendance-admin-border);
        color: var(--attendance-admin-soft-text);
        font-weight: 600;
    }

    .attendance-admin-list {
        display: grid;
        gap: 1rem;
    }

    .attendance-admin-entry {
        padding: 1rem;
        border-radius: 18px;
        border: 1px solid var(--attendance-admin-border);
        background: #fff;
    }

    .attendance-admin-entry__top,
    .attendance-admin-entry__actions,
    .attendance-admin-entry__badges {
        display: flex;
        flex-wrap: wrap;
        gap: .65rem;
        align-items: center;
    }

    .attendance-admin-entry__top {
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: .9rem;
    }

    .attendance-admin-entry__identity {
        display: flex;
        align-items: center;
        gap: .85rem;
    }

    .attendance-admin-avatar {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }

    .attendance-admin-avatar--placeholder {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #dbe6f5;
        color: #4c648d;
        font-size: 1.5rem;
    }

    .attendance-admin-entry__meta {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
        color: var(--attendance-admin-soft-text);
        font-size: .88rem;
    }

    .attendance-admin-entry__grid {
        display: grid;
        gap: .85rem;
        grid-template-columns: 1fr;
        margin-top: .95rem;
    }

    .attendance-admin-info {
        padding: .9rem;
        border-radius: 16px;
        background: var(--attendance-admin-surface);
        border: 1px solid var(--attendance-admin-border);
    }

    .attendance-admin-info__label {
        display: block;
        margin-bottom: .25rem;
        color: var(--attendance-admin-soft-text);
        font-size: .8rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        font-weight: 700;
    }

    .attendance-admin-info strong {
        display: block;
        color: #18345f;
    }

    .attendance-admin-info small {
        color: var(--attendance-admin-soft-text);
    }

    .attendance-admin-empty {
        text-align: center;
        padding: 2rem 1rem;
        border-radius: 18px;
        background: #f9fbfe;
        border: 1px dashed var(--attendance-admin-border);
        color: var(--attendance-admin-soft-text);
    }

    .attendance-admin-empty i {
        display: block;
        font-size: 2rem;
        margin-bottom: .6rem;
    }

    .attendance-admin-modal {
        border-radius: 20px;
        overflow: hidden;
    }

    @media (min-width: 768px) {
        .attendance-admin-filter-form {
            grid-template-columns: repeat(4, minmax(0, 1fr)) auto;
            align-items: end;
        }

        .attendance-admin-summary-grid {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }

        .attendance-admin-entry__grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .attendance-admin-panel__header {
            flex-direction: row;
            justify-content: space-between;
            align-items: start;
        }
    }

    @media (max-width: 767.98px) {
        .attendance-admin-hero {
            padding: 1.2rem;
            border-radius: 20px;
        }

        .attendance-admin-summary-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush
