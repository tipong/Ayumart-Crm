@extends('layouts.pelanggan')

@section('title', 'Wishlist Saya')

@push('styles')
<style>
    /* Wishlist Page Styles */
    .wishlist-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .page-header {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .page-header h2 {
        color: var(--text-dark);
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0;
    }

    .wishlist-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .product-card {
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        transition: all 0.3s;
        height: 100%;
        background: white;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        border-color: var(--primary-color);
    }

    .product-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 1rem;
        border: 1px solid var(--border-color);
    }

    .product-name {
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 0.3rem;
        font-size: 0.95rem;
        line-height: 1.4;
    }

    .product-price {
        color: var(--primary-color);
        font-weight: 700;
        font-size: 1.1rem;
    }

    .btn-add-to-cart {
        background: var(--primary-color);
        color: white;
        border: none;
        width: 100%;
        padding: 0.7rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-add-to-cart:hover {
        background: var(--primary-hover);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(63, 79, 68, 0.3);
    }

    .btn-add-to-cart:disabled {
        background: #ccc;
        cursor: not-allowed;
    }

    .empty-wishlist {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-wishlist i {
        font-size: 5rem;
        color: var(--border-color);
        margin-bottom: 1rem;
    }

    .empty-wishlist h4 {
        font-weight: 700;
        color: var(--text-dark);
    }

    @media (max-width: 768px) {
        .product-card {
            margin-bottom: 1rem;
        }

        .wishlist-container {
            padding: 0 0.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="wishlist-container">
    <!-- Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h2><i class="bi bi-heart-fill text-danger"></i> Wishlist Saya</h2>
            <a href="{{ route('home') }}" class="btn btn-primary">
                <i class="bi bi-arrow-left"></i> Lanjut Belanja
            </a>
        </div>
    </div>

    <div class="wishlist-card">
        @if($wishlistItems->count() > 0)
            <div class="row">
                @foreach($wishlistItems as $item)
                    <div class="col-md-3 col-sm-6">
                        <div class="product-card">
                            <div class="position-relative">
                                <img src="{{ $item->produk->foto_produk ? asset('storage/' . $item->produk->foto_produk) : 'https://via.placeholder.com/200' }}"
                                     alt="{{ $item->produk->nama_produk }}"
                                     class="product-image">
                                @if($item->produk->hasActiveDiscount())
                                    <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                                        -{{ $item->produk->persentase_diskon }}%
                                    </span>
                                @endif
                            </div>

                            <h6 class="product-name">{{ Str::limit($item->produk->nama_produk, 50) }}</h6>
                            <small class="text-muted d-block mb-2">{{ $item->produk->kode_produk }}</small>

                            <div class="mb-3">
                                @if($item->produk->hasActiveDiscount())
                                    <div>
                                        <small class="text-decoration-line-through text-muted">
                                            Rp {{ number_format($item->produk->harga_produk, 0, ',', '.') }}
                                        </small>
                                    </div>
                                    <h5 class="product-price mb-0 text-success">
                                        Rp {{ number_format($item->produk->getCurrentPrice(), 0, ',', '.') }}
                                        <span class="badge bg-danger ms-2">-{{ $item->produk->persentase_diskon }}%</span>
                                    </h5>
                                @else
                                    <h5 class="product-price mb-0">
                                        Rp {{ number_format($item->produk->harga_produk, 0, ',', '.') }}
                                    </h5>
                                @endif
                            </div>

                            <div class="d-grid gap-2">
                                <a href="{{ route('product.show', $item->produk->id_produk) }}"
                                   class="btn btn-add-to-cart">
                                    <i class="bi bi-eye"></i> Lihat Produk
                                </a>
                                <button class="btn btn-outline-danger btn-sm btn-remove"
                                        data-id="{{ $item->id_wishlist }}">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-wishlist">
                <i class="bi bi-heart"></i>
                <h4 class="mt-3">Wishlist Kosong</h4>
                <p class="text-muted">Belum ada produk yang ditambahkan ke wishlist</p>
                <a href="{{ route('home') }}" class="btn btn-primary mt-3">
                    <i class="bi bi-shop"></i> Mulai Belanja
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Remove from wishlist
        document.querySelectorAll('.btn-remove').forEach(btn => {
            btn.addEventListener('click', function() {
                const wishlistId = this.dataset.id;

                Swal.fire({
                    title: 'Hapus dari Wishlist?',
                    text: "Produk akan dihapus dari wishlist",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        removeFromWishlist(wishlistId);
                    }
                });
            });
        });

        function removeFromWishlist(wishlistId) {
            fetch(`/wishlist/${wishlistId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan saat menghapus item',
                });
            });
        }
    });
</script>
@endpush
