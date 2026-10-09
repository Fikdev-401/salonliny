@extends('layouts.app')

@section('title', 'Edit Pembelian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Edit Pembelian</h4>
    <a href="{{ route('pembelian.show', $pembelian) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@include('pembelian._form', [
    'action' => route('pembelian.update', $pembelian),
    'method' => 'PUT',
    'submitLabel' => 'Perbarui Pembelian',
])
@endsection
