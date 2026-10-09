@extends('layouts.app')

@section('title', 'Hitung Gaji')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Hitung Gaji</h4>
    <a href="{{ route('gaji.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('gaji.store') }}" method="POST" id="gajiForm">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Karyawan <span class="text-danger">*</span></label>
                    <select name="karyawan_id" id="gajiKaryawanInput" class="form-select" required>
                        <option value="">Pilih Karyawan</option>
                        @foreach($karyawans as $karyawan)
                            <option value="{{ $karyawan->id }}" @selected(old('karyawan_id') == $karyawan->id)>
                                {{ $karyawan->nama }} | {{ $karyawan->jabatan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Periode <span class="text-danger">*</span></label>
                    <input type="month" name="periode" id="gajiPeriodeInput" class="form-control" value="{{ old('periode', now()->format('Y-m')) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Gaji Pokok Snapshot</label>
                    <input type="text" id="gajiPokokPreview" class="form-control" value="{{ rupiah(0) }}" readonly>
                    <small class="text-muted">Nilai diambil dari riwayat gaji pokok karyawan untuk periode terpilih.</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Insentif Periode</label>
                    <input type="text" id="totalInsentifPreview" class="form-control" value="{{ rupiah(0) }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Total Dasar</label>
                    <input type="text" id="baseTotalPreview" class="form-control fw-semibold" value="{{ rupiah(0) }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Bonus</label>
                    <input type="number" min="0" step="0.01" name="bonus" id="bonusInput" class="form-control" value="{{ old('bonus', 0) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">THR</label>
                    <input type="number" min="0" step="0.01" name="thr" id="thrInput" class="form-control" value="{{ old('thr', 0) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Potongan</label>
                    <input type="number" min="0" step="0.01" name="potongan" id="potonganInput" class="form-control" value="{{ old('potongan', 0) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Hadir</label>
                    <input type="text" id="jumlahHadirPreview" class="form-control" value="0 hari" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Izin</label>
                    <input type="text" id="jumlahIzinPreview" class="form-control" value="0 hari" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sakit</label>
                    <input type="text" id="jumlahSakitPreview" class="form-control" value="0 hari" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Alpha</label>
                    <input type="text" id="jumlahAlphaPreview" class="form-control" value="0 hari" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Estimasi Total Gaji</label>
                    <input type="text" id="estimasiTotalPreview" class="form-control fw-semibold" value="{{ rupiah(0) }}" readonly>
                </div>
                <div class="col-12">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan') }}</textarea>
                </div>
            </div>

            <div class="alert alert-danger mt-4 mb-0 d-none" id="gajiPreviewError"></div>
            <div class="alert alert-info mt-4 mb-0">
                Gaji pokok dan total insentif akan dibekukan sebagai snapshot periode ini, sehingga perubahan gaji pokok di masa depan tidak mengubah laporan periode yang sudah lewat.
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary" id="gajiSubmitButton">
                    <i class="bi bi-calculator"></i> Hitung Gaji
                </button>
                <a href="{{ route('gaji.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const gajiKaryawanInput = document.getElementById('gajiKaryawanInput');
    const gajiPeriodeInput = document.getElementById('gajiPeriodeInput');
    const gajiPokokPreview = document.getElementById('gajiPokokPreview');
    const totalInsentifPreview = document.getElementById('totalInsentifPreview');
    const baseTotalPreview = document.getElementById('baseTotalPreview');
    const jumlahHadirPreview = document.getElementById('jumlahHadirPreview');
    const jumlahIzinPreview = document.getElementById('jumlahIzinPreview');
    const jumlahSakitPreview = document.getElementById('jumlahSakitPreview');
    const jumlahAlphaPreview = document.getElementById('jumlahAlphaPreview');
    const estimasiTotalPreview = document.getElementById('estimasiTotalPreview');
    const bonusInput = document.getElementById('bonusInput');
    const thrInput = document.getElementById('thrInput');
    const potonganInput = document.getElementById('potonganInput');
    const gajiSubmitButton = document.getElementById('gajiSubmitButton');
    const gajiPreviewError = document.getElementById('gajiPreviewError');
    const gajiForm = document.getElementById('gajiForm');
    let currentSnapshot = null;

    function resetPreview() {
        currentSnapshot = null;
        gajiPokokPreview.value = window.formatRupiah(0);
        totalInsentifPreview.value = window.formatRupiah(0);
        baseTotalPreview.value = window.formatRupiah(0);
        jumlahHadirPreview.value = '0 hari';
        jumlahIzinPreview.value = '0 hari';
        jumlahSakitPreview.value = '0 hari';
        jumlahAlphaPreview.value = '0 hari';
        estimasiTotalPreview.value = window.formatRupiah(0);
    }

    function setPreviewError(message = '') {
        const hasError = message.trim() !== '';
        gajiPreviewError.textContent = message;
        gajiPreviewError.classList.toggle('d-none', !hasError);
        gajiSubmitButton.disabled = hasError || !currentSnapshot;
    }

    function updateEstimatedTotal() {
        const baseTotal = Number(currentSnapshot?.base_total || 0);
        const bonus = Number(bonusInput.value || 0);
        const thr = Number(thrInput.value || 0);
        const potongan = Number(potonganInput.value || 0);
        estimasiTotalPreview.value = window.formatRupiah(Math.max(0, baseTotal + bonus + thr - potongan));
    }

    async function fetchGajiPreview() {
        const karyawanId = gajiKaryawanInput.value;
        const periode = gajiPeriodeInput.value;

        if (!karyawanId || !periode) {
            resetPreview();
            setPreviewError('Pilih karyawan dan periode untuk menghitung snapshot gaji.');
            return;
        }

        setPreviewError('');
        gajiSubmitButton.disabled = true;

        try {
            const response = await fetch(`{{ route('gaji.preview') }}?${new URLSearchParams({
                karyawan_id: karyawanId,
                periode,
            })}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            const payload = await response.json();

            if (!response.ok) {
                resetPreview();
                setPreviewError(payload.message || 'Preview gaji tidak dapat dihitung.');
                return;
            }

            currentSnapshot = payload;
            gajiPokokPreview.value = window.formatRupiah(payload.gaji_pokok);
            totalInsentifPreview.value = window.formatRupiah(payload.total_insentif);
            baseTotalPreview.value = window.formatRupiah(payload.base_total);
            jumlahHadirPreview.value = `${payload.jumlah_hadir} hari`;
            jumlahIzinPreview.value = `${payload.jumlah_izin} hari`;
            jumlahSakitPreview.value = `${payload.jumlah_sakit} hari`;
            jumlahAlphaPreview.value = `${payload.jumlah_alpha} hari`;
            setPreviewError('');
            updateEstimatedTotal();
            gajiSubmitButton.disabled = false;
        } catch (error) {
            resetPreview();
            setPreviewError('Preview gaji gagal dimuat. Periksa koneksi atau data karyawan.');
        }
    }

    gajiForm.addEventListener('submit', (event) => {
        if (!currentSnapshot) {
            event.preventDefault();
            setPreviewError('Preview gaji wajib berhasil dimuat sebelum data disimpan.');
        }
    });

    gajiKaryawanInput.addEventListener('change', fetchGajiPreview);
    gajiPeriodeInput.addEventListener('change', fetchGajiPreview);
    bonusInput.addEventListener('input', updateEstimatedTotal);
    thrInput.addEventListener('input', updateEstimatedTotal);
    potonganInput.addEventListener('input', updateEstimatedTotal);

    resetPreview();
    fetchGajiPreview();
</script>
@endpush
@endsection
