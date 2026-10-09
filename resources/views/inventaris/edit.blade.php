@extends('layouts.app')

@section('title', 'Edit Inventaris')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Edit Inventaris</h4>
    <a href="{{ route('inventaris.show', $inventaris) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@include('inventaris._form', [
    'action' => route('inventaris.update', $inventaris),
    'method' => 'PUT',
    'submitLabel' => 'Perbarui Inventaris',
])
@endsection
