@extends('layouts.app')

@section('title', 'Edit Gaji')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Edit Gaji</h4>
    <a href="{{ route('gaji.show', $gaji) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('gaji.update', $gaji) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Karyawan</label>
                    <input type="text" class="form-control" value="{{ $gaji->karyawan->nama }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Periode</label>
                    <input type="text" class="form-control" value="{{ $gaji->periode }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Gaji Pokok Snapshot</label>
                    <input type="text" class="form-control" value="{{ rupiah($gaji->gaji_pokok) }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Insentif Snapshot</label>
                    <input type="text" class="form-control" value="{{ rupiah($gaji->total_insentif) }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Total Saat Ini</label>
                    <input type="text" class="form-control" value="{{ rupiah($gaji->total_gaji) }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Bonus</label>
                    <input type="number" min="0" step="0.01" name="bonus" class="form-control" value="{{ old('bonus', $gaji->bonus) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">THR</label>
                    <input type="number" min="0" step="0.01" name="thr" class="form-control" value="{{ old('thr', $gaji->thr) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Potongan</label>
                    <input type="number" min="0" step="0.01" name="potongan" class="form-control" value="{{ old('potongan', $gaji->potongan) }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $gaji->keterangan) }}</textarea>
                </div>
            </div>

            <div class="alert alert-info mt-4 mb-0">
                Gaji pokok dan total insentif periode ini tidak bisa diubah dari form edit agar laporan historis tetap konsisten.
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Perubahan
                </button>
                <a href="{{ route('gaji.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
