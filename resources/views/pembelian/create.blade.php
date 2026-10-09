@extends('layouts.app')

@section('title', 'Tambah Pembelian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Tambah Pembelian</h4>
    <a href="{{ route('pembelian.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@include('pembelian._form', [
    'action' => route('pembelian.store'),
    'method' => 'POST',
    'submitLabel' => 'Simpan Pembelian',
])
@endsection
