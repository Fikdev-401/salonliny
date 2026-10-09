<form action="{{ $action }}" method="POST">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="card">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Kode Aset</label>
                    <input type="text" name="kode_aset" class="form-control" value="{{ old('kode_aset', $inventaris->kode_aset ?? null) }}" placeholder="Kosongkan untuk generate otomatis">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nama Aset <span class="text-danger">*</span></label>
                    <input type="text" name="nama_aset" class="form-control" value="{{ old('nama_aset', $inventaris->nama_aset ?? null) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jenis Aset <span class="text-danger">*</span></label>
                    <input type="text" name="jenis_aset" class="form-control" value="{{ old('jenis_aset', $inventaris->jenis_aset ?? null) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                    <input type="number" min="1" name="jumlah" class="form-control" value="{{ old('jumlah', $inventaris->jumlah ?? 1) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Satuan <span class="text-danger">*</span></label>
                    <input type="text" name="satuan" class="form-control" value="{{ old('satuan', $inventaris->satuan ?? 'unit') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Input <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_input" class="form-control" value="{{ old('tanggal_input', isset($inventaris) ? $inventaris->tanggal_input->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="bagus" @selected(old('status', $inventaris->status ?? 'bagus') === 'bagus')>Bagus</option>
                        <option value="rusak" @selected(old('status', $inventaris->status ?? null) === 'rusak')>Rusak</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $inventaris->keterangan ?? null) }}</textarea>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> {{ $submitLabel }}
                </button>
                <a href="{{ route('inventaris.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </div>
    </div>
</form>
