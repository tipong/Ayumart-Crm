@extends('layouts.admin')

@section('title', 'Manajemen Diskon')

@push('styles')
<style>
    /* Custom Pagination Styling */
    .pagination {
        margin-bottom: 0;
    }

    .pagination .page-link {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 6px;
        margin: 0 2px;
        cursor: pointer;
        border: 1px solid #e5e7eb;
        color: #0f766e;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .pagination .page-link:hover:not(.disabled) {
        background-color: #10b981;
        color: white;
        border-color: #10b981;
    }

    .pagination .page-item.active .page-link {
        background-color: #10b981;
        border-color: #10b981;
        color: white;
    }

    .pagination .page-item.disabled .page-link {
        color: #9ca3af;
        cursor: not-allowed;
        background-color: #f3f4f6;
        border-color: #e5e7eb;
    }

    /* Glow Stat Cards */
    .stat-card-glow {
        border-radius: 12px;
        padding: 1.5rem;
        border: none;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    }
    
    .stat-card-glow:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    .stat-card-body {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .stat-card-glow .label {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        opacity: 0.85;
        letter-spacing: 0.5px;
        display: block;
        margin-bottom: 0.5rem;
    }

    .stat-card-glow .value {
        font-size: 1.85rem;
        font-weight: 800;
        margin: 0;
    }

    .stat-card-glow .icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .card-active {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .card-paused {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .card-none {
        background: linear-gradient(135deg, #9ca3af 0%, #4b5563 100%);
    }

    .form-control:focus, .form-select:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.25);
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-2">
    <!-- Page Heading -->
    <div class="page-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4">
        <div>
            <h1 class="fw-extrabold text-success-emphasis mb-1">
                <i class="bi bi-tags-fill text-success"></i> Manajemen Diskon Produk
            </h1>
            <p class="text-muted mb-0">Kelola persentase diskon umum maupun tier membership pelanggan.</p>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Discount Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card-glow card-active h-100">
                <div class="stat-card-body">
                    <div>
                        <span class="label">Diskon Aktif</span>
                        <h3 class="value">{{ number_format($totalActiveDiscounts) }}</h3>
                    </div>
                    <div class="icon-wrapper">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card-glow card-paused h-100">
                <div class="stat-card-body">
                    <div>
                        <span class="label">Diskon Nonaktif</span>
                        <h3 class="value">{{ number_format($totalInactiveDiscounts) }}</h3>
                    </div>
                    <div class="icon-wrapper">
                        <i class="bi bi-pause-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card-glow card-none h-100">
                <div class="stat-card-body">
                    <div>
                        <span class="label">Tanpa Diskon</span>
                        <h3 class="value">{{ number_format($totalNoDiscounts) }}</h3>
                    </div>
                    <div class="icon-wrapper">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Discount Table Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-3 ps-4 d-flex justify-content-between align-items-center">
            <h5 class="m-0 fw-bold text-success-emphasis">
                <i class="bi bi-list-ul text-success me-1"></i> Daftar Produk & Diskon
            </h5>
            <span class="text-muted small">Total Produk: <strong>{{ $products->total() }}</strong></span>
        </div>

        <!-- Filter and Search Bar -->
        <div class="p-4 border-bottom bg-light bg-opacity-25">
            <form action="{{ route('admin.discounts.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label for="search" class="form-label small fw-bold text-secondary mb-1">Cari Produk</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               id="search" 
                               class="form-control border-start-0" 
                               placeholder="Nama atau kode produk..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                
                <div class="col-6 col-md-2">
                    <label for="category" class="form-label small fw-bold text-secondary mb-1">Kategori</label>
                    <select name="category" id="category" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id_jenis }}" {{ request('category') == $cat->id_jenis ? 'selected' : '' }}>
                                {{ $cat->nama_jenis }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label for="target" class="form-label small fw-bold text-secondary mb-1">Sasaran</label>
                    <select name="target" id="target" class="form-select">
                        <option value="">Semua Sasaran</option>
                        <option value="general" {{ request('target') == 'general' ? 'selected' : '' }}>Umum</option>
                        <option value="tier" {{ request('target') == 'tier' ? 'selected' : '' }}>Tier Member</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label for="status" class="form-label small fw-bold text-secondary mb-1">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Kedaluwarsa</option>
                        <option value="none" {{ request('status') == 'none' ? 'selected' : '' }}>Tanpa Diskon</option>
                    </select>
                </div>

                <div class="col-6 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-success flex-grow-1 fw-bold text-white">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                    @if(request()->anyFilled(['search', 'category', 'target', 'status']))
                        <a href="{{ route('admin.discounts.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            @if($products->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-secondary">
                            <tr>
                                <th scope="col" class="ps-4 py-3" width="10%">Gambar</th>
                                <th scope="col" class="py-3" width="22%">Nama Produk</th>
                                <th scope="col" class="py-3" width="12%">Kategori</th>
                                <th scope="col" class="py-3 text-end" width="12%">Harga Normal</th>
                                <th scope="col" class="py-3 text-end" width="12%">Harga Diskon</th>
                                <th scope="col" class="py-3" width="14%">Masa Berlaku</th>
                                <th scope="col" class="py-3" width="10%">Sasaran</th>
                                <th scope="col" class="py-3" width="8%">Status</th>
                                <th scope="col" class="pe-4 py-3 text-center" width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td class="ps-4">
                                        @if($product->foto_produk)
                                            <img src="{{ \App\Helpers\ImageHelper::getProductThumbnail($product->foto_produk, 50, 50) }}"
                                                 alt="{{ $product->nama_produk }}"
                                                 class="rounded border"
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded border d-flex align-items-center justify-content-center"
                                                 style="width: 50px; height: 50px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark d-block">{{ $product->nama_produk }}</span>
                                        <small class="text-muted" style="font-size: 0.8rem;">{{ $product->kode_produk }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 px-2.5 py-1 fw-bold">
                                            {{ $product->jenis->nama_jenis ?? 'Tanpa Kategori' }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-semibold text-dark">
                                        Rp {{ number_format($product->harga_produk, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end">
                                        @if($product->harga_diskon)
                                            <span class="fw-bold text-success-emphasis">
                                                Rp {{ number_format($product->harga_diskon, 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->hasActiveDiscount() || $product->discount_target === 'tier')
                                            <div class="d-flex flex-column" style="font-size: 0.85rem;">
                                                <span class="text-dark fw-medium">
                                                    {{ $product->tanggal_mulai_diskon ? $product->tanggal_mulai_diskon->format('d/m/Y') : '-' }} s/d
                                                    {{ $product->tanggal_akhir_diskon ? $product->tanggal_akhir_diskon->format('d/m/Y') : '-' }}
                                                </span>
                                                @if($product->isDiscountExpired())
                                                    <small class="text-danger mt-1 fw-semibold">
                                                        <i class="bi bi-exclamation-triangle"></i> Masa Berlaku Habis
                                                    </small>
                                                @endif
                                            </div>
                                        @elseif($product->harga_diskon)
                                            <span class="badge bg-secondary">Tidak Aktif</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->discount_target === 'tier')
                                            @php
                                                $tierDiscounts = \App\Models\ProductMemberDiscount::where('product_id', $product->id_produk)
                                                    ->where('is_active', true)
                                                    ->get()
                                                    ->keyBy('tier');
                                                $tierBadges = [
                                                    'bronze'   => ['bg' => 'warning text-dark', 'icon' => '🥉'],
                                                    'silver'   => ['bg' => 'secondary', 'icon' => '🥈'],
                                                    'gold'     => ['bg' => 'info text-dark', 'icon' => '🥇'],
                                                    'platinum' => ['bg' => 'danger', 'icon' => '💎'],
                                                ];
                                            @endphp
                                            @if($tierDiscounts->count() > 0)
                                                @foreach(['bronze','silver','gold','platinum'] as $t)
                                                    @if(isset($tierDiscounts[$t]))
                                                        @php $tb = $tierBadges[$t]; @endphp
                                                        <span class="badge bg-{{ $tb['bg'] }} border mb-1" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                                            {{ $tb['icon'] }} {{ ucfirst($t) }}: -{{ number_format($tierDiscounts[$t]->discount_percentage, 0) }}%
                                                        </span><br>
                                                    @endif
                                                @endforeach
                                            @else
                                                <span class="badge bg-dark">
                                                    <i class="bi bi-award-fill"></i> Tier
                                                </span>
                                            @endif
                                        @elseif($product->discount_target === 'general' || ($product->harga_diskon && !$product->discount_target))
                                            <span class="badge bg-teal" style="background-color: #14b8a6;">
                                                <i class="bi bi-people-fill"></i> Umum
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $hasDiscount = $product->harga_diskon || ($product->discount_target === 'tier' && $product->is_diskon_active);
                                        @endphp
                                        @if($hasDiscount)
                                            @if($product->is_diskon_active)
                                                @if($product->isDiscountExpired())
                                                    <span class="badge bg-secondary">Kedaluwarsa</span>
                                                @else
                                                    <span class="badge bg-success">Aktif</span>
                                                @endif
                                            @else
                                                <span class="badge bg-warning text-dark">Nonaktif</span>
                                            @endif
                                        @else
                                            <span class="badge bg-light text-dark border">Kosong</span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-center">
                                        <div class="d-flex flex-column gap-1 align-items-center">
                                            @php
                                                $hasAnyDiscount = $product->harga_diskon || ($product->discount_target === 'tier' && $product->is_diskon_active);
                                                $isActiveAndValid = ($product->hasActiveDiscount() || ($product->discount_target === 'tier' && $product->is_diskon_active)) && !$product->isDiscountExpired();
                                            @endphp
                                            @if($hasAnyDiscount)
                                                @if($isActiveAndValid)
                                                    <a href="{{ route('admin.discounts.edit', $product->id_produk) }}"
                                                       class="btn btn-sm btn-outline-warning w-100 rounded-pill py-1 px-3">
                                                        <i class="bi bi-pencil-square"></i> Edit
                                                    </a>
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-danger w-100 rounded-pill py-1 px-3"
                                                            onclick="confirmDelete('delete-discount-{{ $product->id_produk }}', '{{ $product->nama_produk }}')">
                                                        <i class="bi bi-trash"></i> Hapus
                                                    </button>
                                                @else
                                                    <a href="{{ route('admin.discounts.create', $product->id_produk) }}"
                                                       class="btn btn-sm btn-outline-success w-100 rounded-pill py-1 px-3">
                                                        <i class="bi bi-plus-circle"></i> Tambah
                                                    </a>
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-danger w-100 rounded-pill py-1 px-3"
                                                            onclick="confirmDelete('delete-discount-{{ $product->id_produk }}', '{{ $product->nama_produk }}')">
                                                        <i class="bi bi-trash"></i> Hapus
                                                    </button>
                                                @endif

                                                <form id="delete-discount-{{ $product->id_produk }}"
                                                      action="{{ route('admin.discounts.destroy', $product->id_produk) }}"
                                                      method="POST"
                                                      class="d-none">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            @else
                                                <a href="{{ route('admin.discounts.create', $product->id_produk) }}"
                                                   class="btn btn-sm btn-success text-white w-100 rounded-pill py-1 px-3">
                                                    <i class="bi bi-plus-circle"></i> Diskon
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Info & Links -->
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center p-4 border-top">
                    <div class="text-muted small mb-3 mb-md-0">
                        Menampilkan <strong>{{ $products->firstItem() }}</strong> sampai <strong>{{ $products->lastItem() }}</strong> dari <strong>{{ $products->total() }}</strong> produk
                    </div>
                    @if($products->hasPages())
                        <div>
                            {{ $products->links('pagination.bootstrap-4') }}
                        </div>
                    @endif
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                    Belum ada produk tersedia.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(formId, productName) {
        Swal.fire({
            title: 'Hapus Diskon?',
            text: `Apakah Anda yakin ingin menghapus program diskon dari produk "${productName}"? Harga akan dikembalikan normal.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }
</script>
@endpush
