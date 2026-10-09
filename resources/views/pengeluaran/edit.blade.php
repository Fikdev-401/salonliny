@extends('layouts.app')

@section('title', 'Edit Pengeluaran')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Edit Pengeluaran</h4>
    <a href="{{ route('pengeluaran.show', $pengeluaran) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@include('pengeluaran._form', [
    'action' => route('pengeluaran.update', $pengeluaran),
    'method' => 'PUT',
    'submitLabel' => 'Perbarui Pengeluaran',
])
@endsection
