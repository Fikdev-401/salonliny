@extends('layouts.app')

@section('title', 'Gaji & Insentif')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Gaji & Insentif</h4>
    @if(auth()->user()->hasRole(['admin', 'super-admin']))
        <a href="{{ route('gaji.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Hitung Gaji
        </a>
    @endif
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('gaji.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label">Periode</label>
                <input type="month" name="periode" class="form-control" value="{{ request('periode', now()->format('Y-m')) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    @foreach(['draft', 'disetujui', 'dibayar'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="{{ route('gaji.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Karyawan</th>
                        <th>Periode</th>
                        <th>Gaji Pokok</th>
                        <th>Insentif</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th width="280">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gajis as $gaji)
                        <tr>
                            <td>{{ $gaji->karyawan->nama ?? '-' }}</td>
                            <td>{{ $gaji->periode }}</td>
                            <td>{{ rupiah($gaji->gaji_pokok) }}</td>
                            <td>{{ rupiah($gaji->total_insentif) }}</td>
                            <td class="fw-semibold">{{ rupiah($gaji->total_gaji) }}</td>
                            <td>
                                <span class="badge
                                    @if($gaji->status === 'draft') bg-secondary
                                    @elseif($gaji->status === 'disetujui') bg-info
                                    @elseif($gaji->status === 'dibayar') bg-success
                                    @else bg-warning text-dark @endif">
                                    {{ ucfirst($gaji->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <a href="{{ route('gaji.show', $gaji) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($gaji->status === 'draft')
                                        @if(auth()->user()->hasRole(['admin', 'super-admin']))
                                            <a href="{{ route('gaji.edit', $gaji) }}" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endif
                                        @if(auth()->user()->hasRole(['pimpinan', 'super-admin']))
                                            <form action="{{ route('gaji.approve', $gaji) }}" method="POST" onsubmit="return confirm('Setujui gaji ini?')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="bi bi-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                    @if($gaji->status === 'disetujui' && auth()->user()->hasRole(['admin', 'super-admin']))
                                        <form action="{{ route('gaji.bayar', $gaji) }}" method="POST" onsubmit="return confirm('Bayarkan gaji ini? Pengeluaran otomatis akan dibuat.')">
                                            @csrf
                                            <input type="hidden" name="tanggal_bayar" value="{{ now()->format('Y-m-d') }}">
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                <i class="bi bi-cash-stack"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('gaji.slip', $gaji) }}" class="btn btn-sm btn-secondary" target="_blank">
                                        <i class="bi bi-file-pdf"></i>
                                    </a>
                                </div>
                                @if($gaji->pengeluaran)
                                    <div class="small text-muted mt-1">Pengeluaran: {{ $gaji->pengeluaran->nomor_pengeluaran }}</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">Belum ada data gaji.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $gajis->links() }}
    </div>
</div>
@endsection
