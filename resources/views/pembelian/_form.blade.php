@php
    $isEdit = isset($pembelian);
    $selectedJenis = old('jenis_pembelian', $pembelian->jenis_pembelian ?? 'produk_jual');
@endphp

<form action="{{ $action }}" method="POST" id="pembelianForm">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="card">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', isset($pembelian) ? $pembelian->tanggal->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jenis Pembelian <span class="text-danger">*</span></label>
                    <select name="jenis_pembelian" id="jenisPembelianInput" class="form-select" required>
                        @foreach($jenisPembelians as $value => $label)
                            <option value="{{ $value }}" @selected($selectedJenis === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Supplier</label>
                    <input type="text" name="supplier" class="form-control" value="{{ old('supplier', $pembelian->supplier ?? null) }}">
                </div>

                <div class="col-md-6 product-fields">
                    <label class="form-label">Produk</label>
                    <select name="produk_id" id="produkPembelianInput" class="form-select">
                        <option value="">Pilih Produk</option>
                        @foreach($produks as $produk)
                            <option
                                value="{{ $produk->id }}"
                                data-slug="{{ $produk->jenisProduk?->slug }}"
                                data-harga="{{ $produk->harga_beli }}"
                                @selected(old('produk_id', $pembelian->produk_id ?? null) == $produk->id)
                            >
                                {{ $produk->nama }} | {{ $produk->jenisProduk?->nama ?? '-' }} | Stok {{ $produk->stok }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Pilihan produk otomatis difilter sesuai jenis pembelian.</small>
                </div>

                <div class="col-md-6 inventory-fields">
                    <label class="form-label">Kode Aset</label>
                    <input type="text" name="kode_aset" class="form-control" value="{{ old('kode_aset', $pembelian->inventaris->kode_aset ?? null) }}" placeholder="Kosongkan untuk generate otomatis">
                </div>
                <div class="col-md-6 inventory-fields">
                    <label class="form-label">Nama Aset</label>
                    <input type="text" name="nama_aset" class="form-control" value="{{ old('nama_aset', $pembelian->inventaris->nama_aset ?? null) }}">
                </div>
                <div class="col-md-3 inventory-fields">
                    <label class="form-label">Jenis Aset</label>
                    <input type="text" name="jenis_aset" class="form-control" value="{{ old('jenis_aset', $pembelian->inventaris->jenis_aset ?? null) }}">
                </div>
                <div class="col-md-3 inventory-fields">
                    <label class="form-label">Satuan Aset</label>
                    <input type="text" name="satuan_aset" class="form-control" value="{{ old('satuan_aset', $pembelian->inventaris->satuan ?? 'unit') }}">
                </div>
                <div class="col-md-3 inventory-fields">
                    <label class="form-label">Status Aset</label>
                    <select name="status_aset" class="form-select">
                        @foreach($statusInventaris as $value => $label)
                            <option value="{{ $value }}" @selected(old('status_aset', $pembelian->inventaris->status ?? 'bagus') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12 inventory-fields">
                    <label class="form-label">Keterangan Aset</label>
                    <textarea name="keterangan_aset" class="form-control" rows="2">{{ old('keterangan_aset', $pembelian->inventaris->keterangan ?? null) }}</textarea>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                    <input type="number" min="1" name="jumlah" id="jumlahPembelianInput" class="form-control" value="{{ old('jumlah', $pembelian->jumlah ?? 1) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Harga Satuan <span class="text-danger">*</span></label>
                    <input type="number" min="0" step="0.01" name="harga_satuan" id="hargaPembelianInput" class="form-control" value="{{ old('harga_satuan', $pembelian->harga_satuan ?? 0) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Total</label>
                    <input type="text" id="totalPembelianDisplay" class="form-control" value="{{ rupiah(old('total', $pembelian->total ?? 0)) }}" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label">No. HP Supplier</label>
                    <input type="text" name="no_hp_supplier" class="form-control" value="{{ old('no_hp_supplier', $pembelian->no_hp_supplier ?? null) }}">
                </div>

                <div class="col-12">
                    <label class="form-label">Keterangan Umum</label>
                    <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $pembelian->keterangan ?? null) }}</textarea>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> {{ $submitLabel }}
                </button>
                <a href="{{ route('pembelian.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    const jenisToSlug = {
        produk_jual: 'produk-jual',
        bahan_pakai: 'bahan-pakai',
        barang_sewa: 'barang-sewa',
    };

    const jenisInput = document.getElementById('jenisPembelianInput');
    const produkInput = document.getElementById('produkPembelianInput');
    const hargaInput = document.getElementById('hargaPembelianInput');
    const jumlahInput = document.getElementById('jumlahPembelianInput');
    const totalDisplay = document.getElementById('totalPembelianDisplay');

    function recalculatePembelianTotal() {
        const total = Number(hargaInput.value || 0) * Number(jumlahInput.value || 0);
        totalDisplay.value = window.formatRupiah(total);
    }

    function togglePembelianFields() {
        const jenis = jenisInput.value;
        const isInventaris = jenis === 'alat_inventaris';

        document.querySelectorAll('.inventory-fields').forEach((element) => {
            element.style.display = isInventaris ? '' : 'none';
        });
        document.querySelectorAll('.product-fields').forEach((element) => {
            element.style.display = isInventaris ? 'none' : '';
        });

        Array.from(produkInput.options).forEach((option) => {
            if (!option.value) {
                option.hidden = false;
                return;
            }

            if (isInventaris) {
                option.hidden = true;
                return;
            }

            option.hidden = option.dataset.slug !== jenisToSlug[jenis];
        });

        if (!isInventaris && produkInput.selectedOptions[0] && produkInput.selectedOptions[0].hidden) {
            produkInput.value = '';
        }
    }

    jenisInput.addEventListener('change', togglePembelianFields);
    produkInput.addEventListener('change', () => {
        const selected = produkInput.selectedOptions[0];
        if (selected?.dataset.harga) {
            hargaInput.value = selected.dataset.harga;
            recalculatePembelianTotal();
        }
    });
    hargaInput.addEventListener('input', recalculatePembelianTotal);
    jumlahInput.addEventListener('input', recalculatePembelianTotal);

    togglePembelianFields();
    recalculatePembelianTotal();
</script>
@endpush
