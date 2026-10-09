@extends('layouts.app')

@section('title', 'Data Produk')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Data Produk</h4>
    <a href="{{ route('produk.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Produk
    </a>
</div>

<div class="card">
    <div class="card-body">
        <!-- Filter -->
        <form action="{{ route('produk.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari nama produk..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="jenis_produk_id" class="form-select">
                    <option value="">Semua Jenis</option>
                    @foreach($jenisProduks as $jenisProduk)
                        <option value="{{ $jenisProduk->id }}" @selected((string) request('jenis_produk_id') === (string) $jenisProduk->id)>{{ $jenisProduk->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="kategori" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $kategori)
                        <option value="{{ $kategori }}" {{ request('kategori') == $kategori ? 'selected' : '' }}>{{ $kategori }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="stok_status" class="form-select">
                    <option value="">Semua Stok</option>
                    <option value="menipis" {{ request('stok_status') == 'menipis' ? 'selected' : '' }}>Stok Menipis</option>
                    <option value="habis" {{ request('stok_status') == 'habis' ? 'selected' : '' }}>Stok Habis</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="bi bi-filter"></i> Filter
                </button>
            </div>
        </form>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Jenis</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Harga Jual</th>
                        <th>Status</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produks as $produk)
                    <tr>
                        <td>{{ $produk->kode ?? '-' }}</td>
                        <td>
                            {{ $produk->nama }}
                            @if($produk->harga_fleksibel)
                                <span class="badge bg-warning text-dark ms-1">Fleksibel</span>
                            @endif
                        </td>
                        <td>{{ $produk->jenisProduk->nama ?? '-' }}</td>
                        <td>{{ $produk->kategori ?? '-' }}</td>
                        <td>
                            {{ $produk->stok }} {{ $produk->satuan }}
                            @if($produk->stok_status == 'menipis')
                                <span class="badge bg-warning text-dark ms-1">!</span>
                            @elseif($produk->stok_status == 'habis')
                                <span class="badge bg-danger ms-1">Habis</span>
                            @endif
                        </td>
                        <td>{{ rupiah($produk->harga_jual) }}</td>
                        <td>
                            @if($produk->status == 'aktif')
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('produk.edit', $produk) }}" class="btn btn-sm btn-warning btn-action">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('produk.destroy', $produk) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menonaktifkan produk ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger btn-action">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">Tidak ada data produk</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $produks->links() }}
    </div>
</div>
@endsection
