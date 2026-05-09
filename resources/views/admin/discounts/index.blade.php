@extends('layouts.admin')

@section('title', 'Manajemen Diskon')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tags text-warning"></i> Manajemen Diskon
        </h1>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <!-- Discount Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Diskon Aktif
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $totalActiveDiscounts }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Diskon Nonaktif
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $totalInactiveDiscounts }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-pause-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Tanpa Diskon
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $totalNoDiscounts }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Discount Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-warning">
                <i class="fas fa-list"></i> Daftar Produk & Diskon
            </h6>
            <div class="text-muted small">
                Total Produk: <strong>{{ $products->total() }}</strong>
            </div>
        </div>
        <div class="card-body">
            @if($products->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                {{-- <th width="5%">ID</th> --}}
                                <th width="10%">Gambar</th>
                                <th width="18%">Nama Produk</th>
                                <th width="10%">Kategori</th>
                                <th width="11%">Harga Normal</th>
                                <th width="11%">Harga Diskon</th>
                                <th width="12%">Tanggal Diskon</th>
                                <th width="9%">Target</th>
                                <th width="10%">Status</th>
                                <th width="12%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr>
                                    {{-- <td><strong>#{{ $product->id_produk }}</strong></td> --}}
                                    <td>
                                        @if($product->foto_produk)
                                            <img src="{{ \App\Helpers\ImageHelper::getProductThumbnail($product->foto_produk, 60, 60) }}"
                                                 alt="{{ $product->nama_produk }}"
                                                 class="img-thumbnail"
                                                 style="max-width: 60px; max-height: 60px; object-fit: cover;">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center"
                                                 style="width: 60px; height: 60px;">
                                                <i class="fas fa-image fa-2x text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $product->nama_produk }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $product->kode_produk }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-info text-black">{{ $product->jenis->nama_jenis ?? 'Tanpa Kategori' }}</span>
                                    </td>
                                    <td>
                                        <strong>Rp {{ number_format($product->harga_produk, 0, ',', '.') }}</strong>
                                    </td>
                                    <td>
                                        @if($product->harga_diskon)
                                            <span class="text-success font-weight-bold">
                                                Rp {{ number_format($product->harga_diskon, 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->hasActiveDiscount() || $product->discount_target === 'tier')
                                            <span class="text-dark">
                                                {{ $product->tanggal_mulai_diskon ? $product->tanggal_mulai_diskon->format('d/m/Y') : '-' }} -
                                                {{ $product->tanggal_akhir_diskon ? $product->tanggal_akhir_diskon->format('d/m/Y') : '-' }}
                                            </span>
                                            @if($product->isDiscountExpired())
                                                <br>
                                                <small class="text-danger">
                                                    <i class="fas fa-exclamation-triangle"></i> Sudah Lewat
                                                </small>
                                            @endif
                                        @elseif($product->harga_diskon)
                                            <span class="badge badge-secondary text-black">
                                                Tidak Aktif
                                            </span>
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
                                                    'bronze'   => ['bg' => 'warning',   'text' => 'dark',  'icon' => '🥉'],
                                                    'silver'   => ['bg' => 'secondary', 'text' => 'dark', 'icon' => '🥈'],
                                                    'gold'     => ['bg' => 'info',      'text' => 'dark', 'icon' => '🥇'],
                                                    'platinum' => ['bg' => 'danger',    'text' => 'dark', 'icon' => '💎'],
                                                ];
                                            @endphp
                                            @if($tierDiscounts->count() > 0)
                                                @foreach(['bronze','silver','gold','platinum'] as $t)
                                                    @if(isset($tierDiscounts[$t]))
                                                        @php $tb = $tierBadges[$t]; @endphp
                                                        <span class="badge badge-{{ $tb['bg'] }} text-{{ $tb['text'] }} mb-1 d-inline-block" style="font-size:0.7rem;">
                                                            {{ $tb['icon'] }} {{ ucfirst($t) }}: -{{ number_format($tierDiscounts[$t]->discount_percentage, 0) }}%
                                                        </span><br>
                                                    @endif
                                                @endforeach
                                            @else
                                                <span class="badge badge-info text-white">
                                                    <i class="fas fa-crown"></i> Tier
                                                </span>
                                            @endif
                                        @elseif($product->discount_target === 'general' || ($product->harga_diskon && !$product->discount_target))
                                            <span class="badge badge-success text-dark">
                                                <i class="fas fa-globe"></i> Umum
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
                                                    <span class="badge badge-secondary text-black">
                                                        Sudah Lewat
                                                    </span>
                                                @else
                                                    <span class="badge badge-success text-success">
                                                        <i class="fas fa-check-circle"></i> Aktif
                                                    </span>
                                                @endif
                                            @else
                                                <span class="badge badge-warning text-warning">
                                                    <i class="fas fa-pause-circle"></i> Nonaktif
                                                </span>
                                            @endif
                                        @else
                                            <span class="badge badge-secondary text-secondary">
                                                <i class="fas fa-times-circle"></i> Belum Ada
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group-vertical btn-group-sm" role="group">
                                            @php
                                                $hasAnyDiscount = $product->harga_diskon || ($product->discount_target === 'tier' && $product->is_diskon_active);
                                                $isActiveAndValid = ($product->hasActiveDiscount() || ($product->discount_target === 'tier' && $product->is_diskon_active)) && !$product->isDiscountExpired();
                                            @endphp
                                            @if($hasAnyDiscount)
                                                @if($isActiveAndValid)
                                                    <!-- Edit Discount -->
                                                    <a href="{{ route('admin.discounts.edit', $product->id_produk) }}"
                                                       class="btn btn-warning btn-sm mb-1"
                                                       title="Edit Diskon">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>

                                                    <!-- Delete Discount -->
                                                    <button type="button"
                                                            class="btn btn-danger btn-sm"
                                                            onclick="confirmDelete('delete-discount-{{ $product->id_produk }}', '{{ $product->nama_produk }}')"
                                                            title="Hapus Diskon">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </button>

                                                    <form id="delete-discount-{{ $product->id_produk }}"
                                                          action="{{ route('admin.discounts.destroy', $product->id_produk) }}"
                                                          method="POST"
                                                          class="d-none">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                @else
                                                    <!-- Diskon sudah lewat - Tambah Diskon Baru -->
                                                    <a href="{{ route('admin.discounts.create', $product->id_produk) }}"
                                                       class="btn btn-success btn-sm mb-1"
                                                       title="Tambah Diskon Baru">
                                                        <i class="fas fa-plus"></i> Tambah Diskon
                                                    </a>

                                                    <!-- Hapus Diskon Lama -->
                                                    <button type="button"
                                                            class="btn btn-danger btn-sm"
                                                            onclick="confirmDelete('delete-discount-{{ $product->id_produk }}', '{{ $product->nama_produk }}')"
                                                            title="Hapus Diskon Lama">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </button>

                                                    <form id="delete-discount-{{ $product->id_produk }}"
                                                          action="{{ route('admin.discounts.destroy', $product->id_produk) }}"
                                                          method="POST"
                                                          class="d-none">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                @endif
                                            @else
                                                <!-- Tambah Diskon -->
                                                <a href="{{ route('admin.discounts.create', $product->id_produk) }}"
                                                   class="btn btn-success btn-sm"
                                                   title="Tambah Diskon">
                                                    <i class="fas fa-plus"></i> Tambah Diskon
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted small">
                            Menampilkan {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }}
                            dari {{ $products->total() }} produk
                        </div>
                        <div>
                            {{ $products->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum ada produk tersedia</h5>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
    /* Pagination Styling */
    .pagination {
        margin-bottom: 0;
    }

    .pagination .page-link {
        color: #f6c23e;
        border-color: #dee2e6;
    }

    .pagination .page-link:hover {
        color: #d4a419;
        background-color: #f8f9fc;
        border-color: #dee2e6;
    }

    .pagination .page-item.active .page-link {
        background-color: #f6c23e;
        border-color: #f6c23e;
        color: white;
    }

    .pagination .page-item.disabled .page-link {
        color: #858796;
        background-color: #fff;
        border-color: #dee2e6;
    }

    /* Table responsive improvements */
    .table-hover tbody tr:hover {
        background-color: #f8f9fc;
    }

    /* Badge improvements */
    .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }

    /* Button group vertical spacing */
    .btn-group-vertical .btn {
        margin-bottom: 0;
    }

    .btn-group-vertical .form-group {
        margin-bottom: 0;
    }
</style>
@endpush

@push('scripts')
<script>
    function confirmDelete(formId, productName) {
        if (confirm('Apakah Anda yakin ingin menghapus diskon dari produk "' + productName + '"?\n\nDiskon akan dihapus dan harga kembali normal.')) {
            document.getElementById(formId).submit();
        }
    }

    function navigateToMemberTab() {
        // This function is called on member tier link, the tab will be activated by the hash
        // No additional logic needed as Bootstrap handles tab activation with hash
    }
</script>
@endpush
