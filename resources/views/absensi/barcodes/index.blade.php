@extends('layouts.app')

@section('title', 'Manajemen Barcode Absensi')

@section('content')
<div class="page-header">
    <div>
        <h4 class="page-title">Manajemen Barcode</h4>
        <p class="page-subtitle">Generate dan kelola barcode untuk absensi karyawan</p>
    </div>
    <div class="page-actions">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#bulkGenerateModal">
            <i class="bi bi-plus-circle"></i> Generate Massal
        </button>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateModal">
            <i class="bi bi-plus"></i> Generate Barcode
        </button>
    </div>
</div>

<!-- Statistics -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card primary h-100">
            <div class="card-body text-center">
                <div class="stat-value text-primary">{{ $statistics['total'] }}</div>
                <div class="stat-label text-primary">Total Barcode</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card success h-100">
            <div class="card-body text-center">
                <div class="stat-value text-success">{{ $statistics['active'] }}</div>
                <div class="stat-label text-success">Aktif</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card secondary h-100">
            <div class="card-body text-center">
                <div class="stat-value text-secondary">{{ $statistics['used'] }}</div>
                <div class="stat-label text-secondary">Digunakan</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card danger h-100">
            <div class="card-body text-center">
                <div class="stat-value text-danger">{{ $statistics['expired'] }}</div>
                <div class="stat-label text-danger">Expired</div>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('absensi.barcodes.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Tanggal</label>
                <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal', now()->toDateString()) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Karyawan</label>
                <select name="karyawan_id" class="form-select">
                    <option value="">Semua</option>
                    @foreach($karyawans as $k)
                        <option value="{{ $k->id }}" {{ request('karyawan_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Tipe</label>
                <select name="tipe" class="form-select">
                    <option value="">Semua</option>
                    <option value="checkin" {{ request('tipe') == 'checkin' ? 'selected' : '' }}>Check-in</option>
                    <option value="checkout" {{ request('tipe') == 'checkout' ? 'selected' : '' }}>Check-out</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="used" {{ request('status') == 'used' ? 'selected' : '' }}>Digunakan</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-secondary flex-fill">
                    <i class="bi bi-filter"></i> Filter
                </button>
                <form action="{{ route('absensi.barcodes.cleanup') }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin membersihkan barcode expired?')">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger" title="Bersihkan barcode kedaluwarsa" aria-label="Bersihkan barcode kedaluwarsa">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </form>
    </div>
</div>

<!-- Barcode List -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Barcode</h5>
        <div>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="printSelected()">
                <i class="bi bi-printer"></i> Print Terpilih
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <form id="printForm" action="{{ route('absensi.barcodes.print') }}" method="POST" target="_blank">
            @csrf
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th>Barcode</th>
                            <th>Karyawan</th>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Status</th>
                            <th>Dibuat Oleh</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barcodes as $barcode)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input barcode-checkbox" name="barcode_ids[]" value="{{ $barcode->id }}" {{ $barcode->is_used ? 'disabled' : '' }}>
                            </td>
                            <td>
                                <code class="fs-6">{{ $barcode->barcode }}</code>
                                <button type="button" class="btn btn-sm btn-link p-0 ms-1" onclick="copyBarcode('{{ $barcode->barcode }}')">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </td>
                            <td>{{ $barcode->karyawan->nama }}</td>
                            <td>{{ $barcode->tanggal->format('d/m/Y') }}</td>
                            <td>
                                @if($barcode->tipe == 'checkin')
                                    <span class="badge bg-success">Check-in</span>
                                @else
                                    <span class="badge bg-warning">Check-out</span>
                                @endif
                            </td>
                            <td>@include('absensi.partials.barcode-status-badge', ['barcode' => $barcode])</td>
                            <td>{{ $barcode->createdBy->nama }}</td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('absensi.barcodes.print.single', $barcode) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                    @if(!$barcode->is_used)
                                    <form action="{{ route('absensi.barcodes.destroy', $barcode) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus barcode ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="bi bi-inbox fs-1 text-muted"></i>
                                <p class="text-muted mb-0">Tidak ada data barcode</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    </div>
    @if($barcodes->hasPages())
    <div class="card-footer">
        {{ $barcodes->links() }}
    </div>
    @endif
</div>

<!-- Generate Single Modal -->
<div class="modal fade" id="generateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('absensi.barcodes.generate') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Generate Barcode Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Karyawan <span class="text-danger">*</span></label>
                        <select name="karyawan_id" class="form-select select2" required>
                            <option value="">Pilih Karyawan</option>
                            @foreach($karyawans as $k)
                                <option value="{{ $k->id }}">{{ $k->nama }} - {{ $k->jabatan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipe Barcode <span class="text-danger">*</span></label>
                        <select name="tipe" class="form-select" required>
                            <option value="both">Check-in & Check-out</option>
                            <option value="checkin">Check-in Only</option>
                            <option value="checkout">Check-out Only</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Generate Modal -->
<div class="modal fade" id="bulkGenerateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('absensi.barcodes.bulk-generate') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Generate Barcode Massal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control" value="{{ now()->toDateString() }}" required>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="generateForAll" name="generate_for_all" value="1">
                        <label class="form-check-label" for="generateForAll">
                            Generate untuk semua karyawan aktif
                        </label>
                    </div>

                    <div class="mb-3" id="karyawanSelectContainer">
                        <label class="form-label">Pilih Karyawan <span class="text-danger">*</span></label>
                        <select name="karyawan_ids[]" class="form-select select2" multiple style="width: 100%;">
                            @foreach($karyawans as $k)
                                <option value="{{ $k->id }}">{{ $k->nama }} - {{ $k->jabatan }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Tahan Ctrl/Cmd untuk memilih banyak karyawan</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Generate Massal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Select all checkbox
$('#selectAll').on('change', function() {
    $('.barcode-checkbox:not(:disabled)').prop('checked', $(this).prop('checked'));
});

// Print selected
function printSelected() {
    const selected = $('.barcode-checkbox:checked');
    if (selected.length === 0) {
        alert('Pilih minimal 1 barcode untuk diprint');
        return;
    }
    $('#printForm').submit();
}

// Copy barcode to clipboard
function copyBarcode(barcode) {
    navigator.clipboard.writeText(barcode).then(() => {
        alert('Barcode disalin: ' + barcode);
    });
}

// Toggle karyawan select based on "generate for all" checkbox
$('#generateForAll').on('change', function() {
    if ($(this).prop('checked')) {
        $('#karyawanSelectContainer').hide();
        $('select[name="karyawan_ids[]"]').prop('required', false);
    } else {
        $('#karyawanSelectContainer').show();
        $('select[name="karyawan_ids[]"]').prop('required', true);
    }
});
</script>
@endpush
