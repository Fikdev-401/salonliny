@extends('layouts.app')

@section('title', 'Edit Transaksi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Edit Transaksi</h4>
    <a href="{{ route('transaksi.show', $transaksi) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="alert alert-warning">
    Edit transaksi akan menyelaraskan ulang stok produk, detail item, insentif, dan statistik pelanggan berdasarkan data terbaru.
</div>

@include('transaksi._form', [
    'action' => route('transaksi.update', $transaksi),
    'method' => 'PUT',
    'submitLabel' => 'Perbarui Transaksi',
])
@endsection
