<form action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="card">
        <div class="card-body">
            <div class="row g-3">
                @if(isset($pengeluaran))
                    <div class="col-md-6">
                        <label class="form-label">No. Pengeluaran</label>
                        <input type="text" class="form-control" value="{{ $pengeluaran->nomor_pengeluaran }}" readonly>
                    </div>
                @endif
                <div class="col-md-6">
                    <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', isset($pengeluaran) ? $pengeluaran->tanggal->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select name="kategori" class="form-select" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($kategoris as $kategori)
                            <option value="{{ $kategori }}" @selected(old('kategori', $pengeluaran->kategori ?? null) === $kategori)>{{ $kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Pengeluaran <span class="text-danger">*</span></label>
                    <input type="text" name="nama_pengeluaran" class="form-control" value="{{ old('nama_pengeluaran', $pengeluaran->nama_pengeluaran ?? null) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                    <input type="number" min="0" step="0.01" name="jumlah" class="form-control" value="{{ old('jumlah', $pengeluaran->jumlah ?? 0) }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $pengeluaran->keterangan ?? null) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Bukti Nota</label>
                    <input type="file" name="bukti_nota" class="form-control" accept="image/*,.pdf">
                    <small class="text-muted">Format JPG, PNG, PDF. Maksimal 10 MB.</small>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> {{ $submitLabel }}
                </button>
                <a href="{{ route('pengeluaran.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </div>
    </div>
</form>
