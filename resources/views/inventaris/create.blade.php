@extends('layouts.app')

@section('title', 'Tambah Inventaris')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Tambah Inventaris</h4>
    <a href="{{ route('inventaris.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@include('inventaris._form', [
    'action' => route('inventaris.store'),
    'method' => 'POST',
    'submitLabel' => 'Simpan Inventaris',
])
@endsection
