@php
    $isEdit = isset($transaksi);
    $initialItems = old('items');

    if ($initialItems === null && $isEdit) {
        $initialItems = $transaksi->details->map(function ($detail) {
            return [
                'tipe' => $detail->tipe,
                'item_id' => $detail->tipe === 'layanan' ? $detail->layanan_id : $detail->produk_id,
                'jumlah' => $detail->jumlah,
                'harga_satuan' => (float) $detail->harga_satuan,
                'karyawan_utama_id' => $detail->karyawan_utama_id,
                'karyawan_pendukung_1_id' => $detail->karyawan_pendukung_1_id,
                'karyawan_pendukung_2_id' => $detail->karyawan_pendukung_2_id,
                'insentif_utama' => (float) $detail->insentif_utama,
                'insentif_pendukung_1' => (float) $detail->insentif_pendukung_1,
                'insentif_pendukung_2' => (float) $detail->insentif_pendukung_2,
            ];
        })->values()->all();
    }

    $initialItems = $initialItems ?? [];
    $layananCatalog = $layanans->map(function ($layanan) {
        return [
            'id' => $layanan->id,
            'nama' => $layanan->nama,
            'harga_default' => (float) $layanan->harga_default,
            'harga_fleksibel' => (bool) $layanan->harga_fleksibel,
        ];
    })->values();
    $produkCatalog = $produks->map(function ($produk) {
        return [
            'id' => $produk->id,
            'nama' => $produk->nama,
            'jenis' => $produk->jenisProduk?->nama,
            'stok' => (int) $produk->stok,
            'satuan' => $produk->satuan,
            'harga_jual' => (float) $produk->harga_jual,
            'harga_fleksibel' => (bool) $produk->harga_fleksibel,
        ];
    })->values();
    $karyawanCatalog = $karyawans->map(function ($karyawan) {
        return [
            'id' => $karyawan->id,
            'nama' => $karyawan->nama,
            'jabatan' => $karyawan->jabatan,
        ];
    })->values();
@endphp

<form action="{{ $action }}" method="POST" id="transaksiForm">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-cart3"></i> Detail Transaksi
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Pelanggan <span class="text-danger">*</span></label>
                            <select name="pelanggan_id" class="form-select" required>
                                <option value="">Pilih Pelanggan</option>
                                @foreach($pelanggans as $pelanggan)
                                    <option value="{{ $pelanggan->id }}" @selected(old('pelanggan_id', $transaksi->pelanggan_id ?? null) == $pelanggan->id)>
                                        {{ $pelanggan->nama }}{{ $pelanggan->no_hp ? ' - ' . $pelanggan->no_hp : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                            <input
                                type="datetime-local"
                                name="tanggal_transaksi"
                                class="form-control"
                                value="{{ old('tanggal_transaksi', isset($transaksi) ? $transaksi->tanggal_transaksi->format('Y-m-d\\TH:i') : now()->format('Y-m-d\\TH:i')) }}"
                                required
                            >
                        </div>
                    </div>

                    <hr>

                    <div id="itemsContainer"></div>

                    <div class="d-flex gap-2 mb-3">
                        <button type="button" class="btn btn-outline-primary" id="addLayananButton">
                            <i class="bi bi-plus-circle"></i> Tambah Layanan
                        </button>
                        <button type="button" class="btn btn-outline-success" id="addProdukButton">
                            <i class="bi bi-plus-circle"></i> Tambah Produk
                        </button>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Catatan</label>
                        <textarea name="catatan" class="form-control" rows="3">{{ old('catatan', $transaksi->catatan ?? null) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 1rem;">
                <div class="card-header">
                    <i class="bi bi-calculator"></i> Ringkasan
                </div>
                <div class="card-body">
                    @if($isEdit)
                        <div class="mb-3">
                            <label class="form-label">Nomor Transaksi</label>
                            <input type="text" class="form-control" value="{{ $transaksi->nomor_transaksi }}" readonly>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Subtotal</label>
                        <input type="text" class="form-control" id="subtotalDisplay" value="{{ rupiah(0) }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Diskon</label>
                        <input type="number" min="0" step="0.01" name="diskon" id="diskonInput" class="form-control" value="{{ old('diskon', $transaksi->diskon ?? 0) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total</label>
                        <input type="text" class="form-control fw-bold" id="totalDisplay" value="{{ rupiah(0) }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                        <select name="metode_pembayaran" class="form-select" required>
                            @foreach(['tunai' => 'Tunai', 'transfer' => 'Transfer', 'qris' => 'QRIS', 'debit' => 'Kartu Debit', 'kredit' => 'Kartu Kredit'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('metode_pembayaran', $transaksi->metode_pembayaran ?? 'tunai') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bayar <span class="text-danger">*</span></label>
                        <input type="number" min="0" step="0.01" name="bayar" id="bayarInput" class="form-control" value="{{ old('bayar', $transaksi->bayar ?? 0) }}" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Kembalian</label>
                        <input type="text" class="form-control" id="kembalianDisplay" value="{{ rupiah(0) }}" readonly>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> {{ $submitLabel }}
                        </button>
                        <a href="{{ route('transaksi.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script type="application/json" id="layananCatalogData">@json($layananCatalog)</script>
<script type="application/json" id="produkCatalogData">@json($produkCatalog)</script>
<script type="application/json" id="karyawanCatalogData">@json($karyawanCatalog)</script>
<script type="application/json" id="initialItemsData">@json($initialItems)</script>

@push('styles')
<style>
    .item-card {
        border: 1px solid #dbe3ef;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
        background: #f8fafc;
    }

    .item-card .item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .item-card .item-type {
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .item-card .worker-block {
        padding: 0.85rem;
        border-radius: 10px;
        background: #edf4ff;
    }
</style>
@endpush

@push('scripts')
<script>
    const transactionCatalog = {
        layanan: JSON.parse(document.getElementById('layananCatalogData').textContent || '[]'),
        produk: JSON.parse(document.getElementById('produkCatalogData').textContent || '[]'),
        karyawan: JSON.parse(document.getElementById('karyawanCatalogData').textContent || '[]'),
    };

    const initialItems = JSON.parse(document.getElementById('initialItemsData').textContent || '[]');
    const itemsContainer = document.getElementById('itemsContainer');
    const diskonInput = document.getElementById('diskonInput');
    const bayarInput = document.getElementById('bayarInput');
    let itemIndex = 0;

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, (character) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;',
        }[character]));
    }

    function buildOptions(items, selectedValue, formatter, allowDisabled = false) {
        return items.map((item) => {
            const selected = String(selectedValue ?? '') === String(item.id) ? 'selected' : '';
            const disabled = allowDisabled && item.disabled ? 'disabled' : '';
            return `<option value="${escapeHtml(item.id)}" ${selected} ${disabled}>${escapeHtml(formatter(item))}</option>`;
        }).join('');
    }

    function addItem(type, preset = {}) {
        const index = itemIndex++;
        const element = document.createElement('div');
        element.className = 'item-card';
        element.dataset.index = index;
        element.dataset.type = type;

        const isLayanan = type === 'layanan';
        const catalog = isLayanan ? transactionCatalog.layanan : transactionCatalog.produk;
        const selectedItem = catalog.find((item) => String(item.id) === String(preset.item_id)) || null;
        const defaultPrice = selectedItem
            ? (isLayanan ? selectedItem.harga_default : selectedItem.harga_jual)
            : Number(preset.harga_satuan || 0);
        const readonlyPrice = selectedItem ? !selectedItem.harga_fleksibel : false;

        const productOptions = buildOptions(
            transactionCatalog.produk.map((produk) => ({
                ...produk,
                disabled: produk.stok <= 0 && String(preset.item_id ?? '') !== String(produk.id),
            })),
            preset.item_id,
            (produk) => `${produk.nama} | ${produk.jenis || '-'} | Stok: ${produk.stok} ${produk.satuan || ''}`,
            true
        );

        const typeValue = isLayanan ? 'layanan' : 'produk';
        const quantityValue = Math.max(1, Number(preset.jumlah || 1));
        const priceValue = Number(preset.harga_satuan ?? defaultPrice);

        element.innerHTML = `
            <div class="item-header">
                <div class="item-type text-${isLayanan ? 'primary' : 'success'}">
                    <i class="bi ${isLayanan ? 'bi-scissors' : 'bi-box-seam'}"></i>
                    ${isLayanan ? 'Layanan' : 'Produk'}
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(${index})">
                    <i class="bi bi-trash"></i>
                </button>
            </div>

            <input type="hidden" name="items[${index}][tipe]" value="${typeValue}">

            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label small">${isLayanan ? 'Pilih Layanan' : 'Pilih Produk'}</label>
                    <select class="form-select item-select" name="items[${index}][item_id]" data-index="${index}" required>
                        <option value="">Pilih ${isLayanan ? 'Layanan' : 'Produk'}</option>
                        ${isLayanan
                            ? buildOptions(catalog, preset.item_id, (layanan) => `${layanan.nama} | ${window.formatRupiah(layanan.harga_default)}${layanan.harga_fleksibel ? ' | Harga fleksibel' : ''}`)
                            : productOptions}
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Jumlah</label>
                    <input type="number" min="1" class="form-control jumlah-input" name="items[${index}][jumlah]" value="${quantityValue}" data-index="${index}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Harga Satuan</label>
                    <input type="hidden" class="harga-input" name="items[${index}][harga_satuan]" value="${priceValue}" data-index="${index}">
                    <input type="text" class="form-control harga-display ${readonlyPrice ? 'bg-light' : ''}" value="${escapeHtml(window.formatRupiah(priceValue))}" data-index="${index}" ${readonlyPrice ? 'readonly' : ''} required>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Total</label>
                    <input type="text" class="form-control total-display" value="${window.formatRupiah(0)}" readonly>
                </div>
            </div>

            ${isLayanan ? `
                <div class="worker-block mt-3">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small">Karyawan Utama</label>
                            <select class="form-select" name="items[${index}][karyawan_utama_id]" required>
                                <option value="">Pilih Karyawan</option>
                                ${buildOptions(transactionCatalog.karyawan, preset.karyawan_utama_id, (karyawan) => `${karyawan.nama} | ${karyawan.jabatan}`)}
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Pendukung 1</label>
                            <select class="form-select" name="items[${index}][karyawan_pendukung_1_id]">
                                <option value="">-</option>
                                ${buildOptions(transactionCatalog.karyawan, preset.karyawan_pendukung_1_id, (karyawan) => `${karyawan.nama} | ${karyawan.jabatan}`)}
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Pendukung 2</label>
                            <select class="form-select" name="items[${index}][karyawan_pendukung_2_id]">
                                <option value="">-</option>
                                ${buildOptions(transactionCatalog.karyawan, preset.karyawan_pendukung_2_id, (karyawan) => `${karyawan.nama} | ${karyawan.jabatan}`)}
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Insentif Utama</label>
                            <input type="number" min="0" step="0.01" class="form-control" name="items[${index}][insentif_utama]" value="${Number(preset.insentif_utama ?? 0)}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Insentif Pendukung 1</label>
                            <input type="number" min="0" step="0.01" class="form-control" name="items[${index}][insentif_pendukung_1]" value="${Number(preset.insentif_pendukung_1 ?? 0)}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Insentif Pendukung 2</label>
                            <input type="number" min="0" step="0.01" class="form-control" name="items[${index}][insentif_pendukung_2]" value="${Number(preset.insentif_pendukung_2 ?? 0)}">
                        </div>
                    </div>
                    <small class="text-muted">Insentif bersifat manual dan tersimpan sebagai snapshot per transaksi.</small>
                </div>
            ` : `
                <input type="hidden" name="items[${index}][insentif_utama]" value="0">
                <input type="hidden" name="items[${index}][insentif_pendukung_1]" value="0">
                <input type="hidden" name="items[${index}][insentif_pendukung_2]" value="0">
            `}
        `;

        itemsContainer.appendChild(element);
        bindRowEvents(index);
        recalculateRow(index);
    }

    function bindRowEvents(index) {
        const row = document.querySelector(`.item-card[data-index="${index}"]`);
        if (!row) {
            return;
        }

        row.querySelector('.item-select').addEventListener('change', (event) => {
            const type = row.dataset.type;
            const selectedId = event.target.value;
            const catalog = type === 'layanan' ? transactionCatalog.layanan : transactionCatalog.produk;
            const selected = catalog.find((item) => String(item.id) === String(selectedId));
            const priceInput = row.querySelector('.harga-input');
            const priceDisplay = row.querySelector('.harga-display');

            if (selected) {
                const selectedPrice = type === 'layanan' ? selected.harga_default : selected.harga_jual;
                priceInput.value = selectedPrice;
                priceDisplay.value = window.formatRupiah(selectedPrice);
                priceDisplay.readOnly = !selected.harga_fleksibel;
                priceDisplay.classList.toggle('bg-light', !selected.harga_fleksibel);
            } else {
                priceInput.value = 0;
                priceDisplay.value = window.formatRupiah(0);
                priceDisplay.readOnly = false;
                priceDisplay.classList.remove('bg-light');
            }

            recalculateRow(index);
        });

        row.querySelector('.jumlah-input').addEventListener('input', () => recalculateRow(index));
        row.querySelector('.harga-display').addEventListener('input', (event) => {
            const parsedValue = Math.max(0, window.parseRupiah(event.target.value));
            row.querySelector('.harga-input').value = parsedValue;
            event.target.value = event.target.value.trim() === '' ? '' : window.formatRupiah(parsedValue);
            recalculateRow(index);
        });
        row.querySelector('.harga-display').addEventListener('blur', (event) => {
            const parsedValue = Math.max(0, window.parseRupiah(event.target.value));
            row.querySelector('.harga-input').value = parsedValue;
            event.target.value = window.formatRupiah(parsedValue);
            recalculateRow(index);
        });
    }

    function recalculateRow(index) {
        const row = document.querySelector(`.item-card[data-index="${index}"]`);
        if (!row) {
            return;
        }

        const jumlah = Number(row.querySelector('.jumlah-input').value || 0);
        const harga = Number(row.querySelector('.harga-input').value || 0);
        const total = jumlah * harga;
        row.querySelector('.total-display').value = window.formatRupiah(total);
        recalculateSummary();
    }

    function recalculateSummary() {
        const summary = getCurrentSummary();

        document.getElementById('subtotalDisplay').value = window.formatRupiah(summary.subtotal);
        document.getElementById('totalDisplay').value = window.formatRupiah(summary.total);
        document.getElementById('kembalianDisplay').value = window.formatRupiah(summary.kembalian);
    }

    function removeItem(index) {
        document.querySelector(`.item-card[data-index="${index}"]`)?.remove();
        recalculateSummary();
    }

    function getCurrentSummary() {
        let subtotal = 0;

        document.querySelectorAll('.item-card').forEach((row) => {
            const jumlah = Number(row.querySelector('.jumlah-input').value || 0);
            const harga = Number(row.querySelector('.harga-input').value || 0);
            subtotal += jumlah * harga;
        });

        const diskon = Number(diskonInput.value || 0);
        const total = Math.max(0, subtotal - diskon);
        const bayar = Number(bayarInput.value || 0);

        return {
            subtotal,
            total,
            bayar,
            kembalian: Math.max(0, bayar - total),
        };
    }

    document.getElementById('addLayananButton').addEventListener('click', () => addItem('layanan'));
    document.getElementById('addProdukButton').addEventListener('click', () => addItem('produk'));
    diskonInput.addEventListener('input', recalculateSummary);
    bayarInput.addEventListener('input', recalculateSummary);

    document.getElementById('transaksiForm').addEventListener('submit', (event) => {
        const rows = Array.from(document.querySelectorAll('.item-card'));

        if (rows.length === 0) {
            event.preventDefault();
            alert('Tambahkan minimal satu item transaksi.');
            return;
        }

        for (const row of rows) {
            const itemLabel = row.dataset.type === 'layanan' ? 'layanan' : 'produk';
            const itemSelect = row.querySelector('.item-select');
            const jumlahInput = row.querySelector('.jumlah-input');
            const hargaInput = row.querySelector('.harga-input');

            if (!itemSelect.value) {
                event.preventDefault();
                itemSelect.focus();
                alert(`Pilih ${itemLabel} terlebih dahulu.`);
                return;
            }

            if (Number(jumlahInput.value || 0) < 1) {
                event.preventDefault();
                jumlahInput.focus();
                alert(`Jumlah ${itemLabel} minimal 1.`);
                return;
            }

            if (Number(hargaInput.value || 0) < 0) {
                event.preventDefault();
                row.querySelector('.harga-display')?.focus();
                alert('Harga satuan tidak boleh negatif.');
                return;
            }

            if (row.dataset.type === 'layanan') {
                const workerInput = row.querySelector(`select[name="items[${row.dataset.index}][karyawan_utama_id]"]`);

                if (!workerInput?.value) {
                    event.preventDefault();
                    workerInput?.focus();
                    alert('Karyawan utama wajib diisi untuk setiap layanan.');
                    return;
                }
            }
        }

        const summary = getCurrentSummary();

        if (summary.bayar < summary.total) {
            event.preventDefault();
            bayarInput.focus();
            alert('Nominal bayar wajib sama atau lebih besar dari total transaksi.');
        }
    });

    if (initialItems.length) {
        initialItems.forEach((item) => addItem(item.tipe, item));
    } else {
        addItem('layanan');
    }

    recalculateSummary();
</script>
@endpush
