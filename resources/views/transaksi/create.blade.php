@extends('layouts.app')

@section('title', 'Transaksi Baru')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Transaksi Baru</h4>
    <a href="{{ route('transaksi.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@include('transaksi._form', [
    'action' => route('transaksi.store'),
    'method' => 'POST',
    'submitLabel' => 'Simpan Transaksi',
])
@endsection
