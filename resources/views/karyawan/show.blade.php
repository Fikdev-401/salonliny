@extends('layouts.app')

@section('title', 'Detail Karyawan')

@section('content')
<div class="page-header">
    <div>
        <h4 class="page-title">Detail Karyawan</h4>
        <p class="page-subtitle">Profil kerja, ringkasan performa, absensi, dan histori perubahan gaji pokok.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('karyawan.edit', $karyawan) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('karyawan.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="summary-grid">
    <div class="summary-tile">
        <span class="summary-label">Status Kerja</span>
        <div class="summary-value-sm">
            @if($karyawan->status_kerja === 'aktif')
                <span class="status-dot text-success">Aktif</span>
            @elseif($karyawan->status_kerja === 'cuti')
                <span class="status-dot text-warning">Cuti</span>
            @else
                <span class="status-dot text-secondary">Nonaktif</span>
            @endif
        </div>
    </div>
    <div class="summary-tile">
        <span class="summary-label">Gaji Pokok Aktif</span>
        <div class="summary-value">{{ rupiah($karyawan->gaji_pokok) }}</div>
    </div>
    <div class="summary-tile">
        <span class="summary-label">Layanan Bulan Ini</span>
        <div class="summary-value">{{ number_format($layananBulanIni, 0, ',', '.') }}</div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="section-heading">
                    <div>
                        <h5 class="section-title">Informasi Karyawan</h5>
                        <p class="section-caption">Data identitas dan status kerja aktif saat ini.</p>
                    </div>
                </div>

                <div class="soft-panel">
                    <div class="detail-list">
                        <div class="detail-row">
                            <div class="detail-label">Nama</div>
                            <div class="detail-value fw-semibold">{{ $karyawan->nama }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Jabatan</div>
                            <div class="detail-value">{{ $karyawan->jabatan }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Tanggal Lahir</div>
                            <div class="detail-value">{{ $karyawan->tanggal_lahir?->format('d/m/Y') ?? '-' }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Tanggal Bergabung</div>
                            <div class="detail-value">{{ $karyawan->tanggal_bergabung->format('d/m/Y') }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Masa Kerja</div>
                            <div class="detail-value">{{ $karyawan->masa_kerja }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">No. HP</div>
                            <div class="detail-value">{{ $karyawan->no_hp ?? '-' }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Jenis Kelamin</div>
                            <div class="detail-value">{{ $karyawan->jenis_kelamin == 'L' ? 'Laki-laki' : ($karyawan->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Alamat</div>
                            <div class="detail-value">{{ $karyawan->alamat ?? '-' }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Akun User</div>
                            <div class="detail-value">
                                @if($karyawan->user)
                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">{{ $karyawan->user->username }}</span>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <div class="section-heading">
                    <div>
                        <h5 class="section-title">Riwayat Gaji Pokok</h5>
                        <p class="section-caption">Snapshot referensi gaji per tanggal berlaku.</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Berlaku Mulai</th>
                                <th>Gaji Pokok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($karyawan->riwayatGajis->sortByDesc('berlaku_mulai') as $riwayat)
                                <tr>
                                    <td>{{ $riwayat->berlaku_mulai->format('d/m/Y') }}</td>
                                    <td>{{ rupiah($riwayat->gaji_pokok) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center py-3 text-muted">Belum ada riwayat gaji.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="section-heading">
                    <div>
                        <h5 class="section-title">Riwayat Absensi 30 Hari Terakhir</h5>
                        <p class="section-caption">Pantau kehadiran karyawan sebagai dasar evaluasi operasional.</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle compact-table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Jam Masuk</th>
                                <th>Jam Keluar</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($karyawan->absensis as $absensi)
                            <tr>
                                <td>{{ $absensi->tanggal->format('d/m/Y') }}</td>
                                <td>{{ $absensi->jam_masuk ?? '-' }}</td>
                                <td>{{ $absensi->jam_keluar ?? '-' }}</td>
                                <td>
                                    @if($absensi->status == 'hadir')
                                        <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle">Hadir</span>
                                    @elseif($absensi->status == 'izin')
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">Izin</span>
                                    @elseif($absensi->status == 'sakit')
                                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">Sakit</span>
                                    @elseif($absensi->status == 'cuti')
                                        <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle">Cuti</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle">Alpha</span>
                                    @endif
                                </td>
                                <td>{{ $absensi->keterangan ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Belum ada data absensi pada 30 hari terakhir.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
