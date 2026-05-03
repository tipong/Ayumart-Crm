@extends('layouts.admin')

@section('title', 'Manajemen Produk')

@push('styles')
<style>
    /* Custom Pagination Styling */
    .pagination {
        margin-bottom: 0;
    }

    .pagination .page-link {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 0.25rem;
        margin: 0 2px;
    }

    .pagination .page-item:first-child .page-link,
    .pagination .page-item:last-child .page-link {
        border-radius: 0.25rem;
    }

    .pagination .page-link:hover {
        background-color: #4e73df;
        color: white;
        border-color: #4e73df;
    }

    .pagination .page-item.active .page-link {
        background-color: #4e73df;
        border-color: #4e73df;
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-box text-primary"></i> Manajemen Produk
        </h1>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Tambah Produk Baru
        </a>
    </div>

    <!-- Products Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> Daftar Produk
            </h6>
        </div>
        <div class="card-body">
            @if($products->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover datatable">
                        <thead>
                            <tr>
                                <th width="5%">ID</th>
                                <th width="15%">Gambar</th>
                                <th width="20%">Nama Produk</th>
                                <th width="15%">Kategori</th>
                                <th width="12%">Harga</th>
                                <th width="8%">Stok</th>
                                <th width="15%">Status</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td><strong>#{{ $product->id }}</strong></td>
                                    <td>
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}"
                                                 alt="{{ $product->name }}"
                                                 class="img-thumbnail"
                                                 style="max-width: 80px; max-height: 80px; object-fit: cover;">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center"
                                                 style="width: 80px; height: 80px;">
                                                <i class="fas fa-image fa-2x text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $product->name }}</strong>
                                        @if($product->description)
                                            <br>
                                            <small class="text-muted">
                                                {{ Str::limit($product->description, 50) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $product->category }}</span>
                                    </td>
                                    <td>
                                        @if($product->hasActiveDiscount())
                                            <!-- Harga dengan diskon aktif -->
                                            <div>
                                                <small class="text-muted text-decoration-line-through">
                                                    Rp {{ number_format($product->harga_produk, 0, ',', '.') }}
                                                </small>
                                            </div>
                                            <div class="font-weight-bold text-success">
                                                Rp {{ number_format($product->harga_diskon, 0, ',', '.') }}
                                            </div>
                                            <span class="badge bg-danger">
                                                -{{ number_format($product->persentase_diskon, 0) }}%
                                            </span>
                                        @else
                                            <!-- Harga normal -->
                                            <div class="font-weight-bold text-success">
                                                Rp {{ number_format($product->price, 0, ',', '.') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->stock < 10)
                                            <span class="badge bg-danger">{{ $product->stock }}</span>
                                        @elseif($product->stock < 50)
                                            <span class="badge bg-warning">{{ $product->stock }}</span>
                                        @else
                                            <span class="badge bg-success">{{ $product->stock }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->stock > 0)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle"></i> Tersedia
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times-circle"></i> Habis
                                            </span>
                                        @endif
                                        <br>
                                        @if($product->hasActiveDiscount())
                                            <span class="badge bg-warning mt-1">
                                                <i class="fas fa-tag"></i> Diskon Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.products.edit', $product->id_produk) }}"
                                               class="btn btn-sm btn-warning"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="confirmDelete('delete-product-{{ $product->id_produk }}', '{{ $product->name }}')"
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        <form id="delete-product-{{ $product->id_produk }}"
                                              action="{{ route('admin.products.destroy', $product->id_produk) }}"
                                              method="POST"
                                              class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted small">
                        Menampilkan {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} dari {{ $products->total() }} produk
                    </div>
                    <div>
                        {{ $products->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada produk. Silakan tambah produk baru.</p>
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> Tambah Produk Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- SweetAlert2 for notifications -->
@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#4e73df',
                timer: 3000,
                timerProgressBar: true
            });
        });
    </script>
@endif

@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#4e73df'
            });
        });
    </script>
@endif
@endsection
