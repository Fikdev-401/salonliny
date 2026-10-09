@extends('layouts.app')

@section('title', 'Tambah Pengeluaran')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Tambah Pengeluaran Manual</h4>
    <a href="{{ route('pengeluaran.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@include('pengeluaran._form', [
    'action' => route('pengeluaran.store'),
    'method' => 'POST',
    'submitLabel' => 'Simpan Pengeluaran',
])
@endsection
