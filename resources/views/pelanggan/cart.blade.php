@extends('layouts.pelanggan')

@section('title', 'Keranjang Belanja')

@push('styles')
<style>
    :root {
        --primary-color: #3F4F44;
        --primary-hover: #2E3A31;
        --secondary-color: #556B58;
        --text-dark: #333;
        --text-muted: #666;
        --border-color: #e5e5e5;
        --bg-light: #f8f9fa;
        --success-color: #10b981;
        --danger-color: #ef4444;
        --warning-color: #f59e0b;
    }

    /* Cart Page Styles */
    .cart-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
        min-height: calc(100vh - 200px);
    }

    .page-header {
        background: linear-gradient(135deg, white, #fafafa);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border-left: 4px solid var(--primary-color);
    }

    .page-header h2 {
        color: var(--text-dark);
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .page-header h2 i {
        color: var(--primary-color);
        font-size: 2.2rem;
    }

    .page-header .btn {
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .cart-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }

    .cart-item {
        border-bottom: 2px solid var(--bg-light);
        padding: 2rem 0;
        transition: all 0.3s ease;
    }

    .cart-item:hover {
        background: linear-gradient(to right, transparent, rgba(63, 79, 68, 0.02), transparent);
        border-radius: 12px;
        padding: 2rem 1rem;
    }

    .cart-item:last-child {
        border-bottom: none;
    }

    .product-image {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 12px;
        border: 2px solid var(--border-color);
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .product-image:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .product-info h6 {
        font-weight: 700;
        color: var(--text-dark);
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
    }

    .product-info small {
        color: var(--text-muted);
        font-size: 0.85rem;
    }

    .badge {
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .qty-control {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }

    .btn-qty-decrease,
    .btn-qty-increase {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        border: 2px solid var(--border-color);
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 700;
        color: var(--text-dark);
    }

    .btn-qty-decrease:hover,
    .btn-qty-increase:hover {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
        transform: scale(1.1);
    }

    .btn-qty-decrease:active,
    .btn-qty-increase:active {
        transform: scale(0.95);
    }

    .qty-input {
        width: 70px;
        text-align: center;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        padding: 0.5rem;
        font-weight: 700;
        color: var(--text-dark);
        transition: all 0.3s ease;
    }

    .qty-input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(63, 79, 68, 0.1);
        outline: none;
    }

    .price-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .price-original {
        font-size: 0.85rem;
    }

    .price-current {
        font-size: 1.1rem;
        font-weight: 700;
    }

    .subtotal-price {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--primary-color);
    }

    .btn-remove {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: 2px solid var(--danger-color);
        color: var(--danger-color);
        background: white;
    }

    .btn-remove:hover {
        background: var(--danger-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .summary-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        position: sticky;
        top: 100px;
        border: 2px solid var(--bg-light);
    }

    .summary-card h5 {
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--bg-light);
    }

    .btn-checkout {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        border: none;
        width: 100%;
        padding: 1.25rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(63, 79, 68, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-checkout:hover:not(:disabled) {
        background: linear-gradient(135deg, var(--primary-hover), var(--primary-color));
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(63, 79, 68, 0.4);
    }

    .btn-checkout:active:not(:disabled) {
        transform: translateY(-1px);
    }

    .btn-checkout:disabled {
        background: linear-gradient(135deg, #ccc, #aaa);
        cursor: not-allowed;
        box-shadow: none;
        opacity: 0.6;
    }

    .membership-info {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 1.25rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 12px rgba(63, 79, 68, 0.3);
    }

    .membership-info small {
        opacity: 0.9;
        font-size: 0.85rem;
    }

    .membership-info .fw-bold {
        font-size: 1.2rem;
    }

    .membership-info i {
        opacity: 0.9;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1rem;
        font-size: 1rem;
    }

    .summary-item span {
        color: var(--text-dark);
    }

    .summary-item.discount {
        color: var(--success-color);
    }

    .summary-item.total {
        font-size: 1.3rem;
        font-weight: 700;
        padding-top: 1rem;
        border-top: 2px solid var(--bg-light);
    }

    .empty-cart {
        text-align: center;
        padding: 5rem 2rem;
    }

    .empty-cart i {
        font-size: 6rem;
        color: #ddd;
        margin-bottom: 1.5rem;
        display: block;
    }

    .empty-cart h4 {
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 1rem;
    }

    .empty-cart p {
        color: var(--text-muted);
        font-size: 1.1rem;
        margin-bottom: 2rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(63, 79, 68, 0.3);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, var(--primary-hover), var(--primary-color));
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(63, 79, 68, 0.4);
    }

    .btn-outline-secondary {
        border: 2px solid var(--border-color);
        color: var(--text-dark);
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        background: white;
    }

    .btn-outline-secondary:hover {
        background: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(63, 79, 68, 0.3);
    }

    .security-badge {
        margin-top: 1.5rem;
        padding: 1rem;
        background: var(--bg-light);
        border-radius: 10px;
        text-align: center;
    }

    .security-badge small {
        color: var(--text-muted);
        font-weight: 600;
    }

    .security-badge i {
        color: var(--success-color);
        margin-right: 0.5rem;
    }

    @media (max-width: 768px) {
        .cart-container {
            padding: 0 0.5rem;
        }

        .page-header {
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .page-header h2 {
            font-size: 1.5rem;
            flex-direction: column;
            gap: 0.5rem;
        }

        .page-header .d-flex {
            flex-direction: column;
            gap: 1rem;
        }

        .page-header .btn {
            width: 100%;
        }

        .cart-card {
            padding: 1rem;
        }

        .cart-item {
            padding: 1.5rem 0;
        }

        .cart-item:hover {
            padding: 1.5rem 0.5rem;
        }

        .cart-item .row {
            gap: 1rem;
        }

        .cart-item .col-md-2,
        .cart-item .col-md-4 {
            width: 100%;
            margin-bottom: 1rem;
        }

        .product-image {
            width: 100%;
            height: 200px;
        }

        .qty-control {
            justify-content: center;
        }

        .summary-card {
            position: static;
            margin-top: 1.5rem;
        }

        .btn-checkout {
            padding: 1rem;
            font-size: 1rem;
        }
    }

    @media (max-width: 576px) {
        .page-header h2 {
            font-size: 1.3rem;
        }

        .cart-item {
            padding: 1rem 0;
        }

        .product-info h6 {
            font-size: 1rem;
        }

        .qty-input {
            width: 60px;
        }

        .btn-qty-decrease,
        .btn-qty-increase {
            width: 32px;
            height: 32px;
        }
    }
</style>
@endpush

@section('content')
    <div class="cart-container">
        <!-- Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="bi bi-cart3"></i> Keranjang Belanja</h2>
                <a href="{{ route('home') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Lanjut Belanja
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Cart Items -->
            <div class="col-md-8">
                <div class="cart-card">
                    @if($cartItems->count() > 0)
                        @foreach($cartItems as $item)
                            <div class="cart-item" data-item-id="{{ $item->id_detail_cart }}">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <img src="{{ $item->produk->foto_produk ? asset('storage/' . $item->produk->foto_produk) : 'https://via.placeholder.com/100' }}"
                                             alt="{{ $item->produk->nama_produk }}"
                                             class="product-image">
                                    </div>
                                    <div class="col-md-3 product-info">
                                        <h6 class="mb-1">{{ $item->produk->nama_produk }}</h6>
                                        {{-- <small class="text-muted">Kode: {{ $item->produk->kode_produk }}</small> --}}
                                        @if($item->produk->hasActiveDiscount())
                                            <div class="mt-1">
                                                <span class="badge bg-danger">-{{ $item->produk->persentase_diskon }}%</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-2">
                                        <div class="price-info">
                                            @if($item->produk->hasActiveDiscount())
                                                <small class="text-decoration-line-through text-muted price-original">
                                                    Rp {{ number_format($item->produk->harga_produk, 0, ',', '.') }}
                                                </small>
                                                <div class="fw-bold text-success price-current">
                                                    Rp {{ number_format($item->produk->getCurrentPrice(), 0, ',', '.') }}
                                                </div>
                                            @else
                                                <div class="fw-bold price-current">
                                                    Rp {{ number_format($item->produk->harga_produk, 0, ',', '.') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="qty-control">
                                            <button type="button" class="btn-qty-decrease" data-id="{{ $item->id_detail_cart }}">
                                                <i class="bi bi-dash"></i>
                                            </button>
                                            <input type="number"
                                                   class="form-control qty-input"
                                                   value="{{ $item->qty }}"
                                                   min="1"
                                                   max="100"
                                                   data-id="{{ $item->id_detail_cart }}">
                                            <button type="button" class="btn-qty-increase" data-id="{{ $item->id_detail_cart }}">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        </div>
                                        {{-- <small class="text-muted">Stok tersedia</small> --}}
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <div class="fw-bold subtotal-price">
                                            Rp {{ number_format($item->getSubtotal(), 0, ',', '.') }}
                                        </div>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger mt-2 btn-remove"
                                                data-id="{{ $item->id_detail_cart }}">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-cart">
                            <i class="bi bi-cart-x"></i>
                            <h4 class="mt-3">Keranjang Belanja Kosong</h4>
                            <p class="text-muted">Yuk, mulai tambahkan produk ke keranjang!</p>
                            <a href="{{ route('home') }}" class="btn btn-primary mt-3">
                                <i class="bi bi-shop"></i> Mulai Belanja
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-md-4">
                <div class="summary-card">
                    <h5 class="mb-3">Ringkasan Belanja</h5>

                    @if($membership)
                        <div class="membership-info">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small>Member {{ strtoupper($membership->type_membership) }}</small>
                                    <div class="fw-bold">Diskon {{ $membership->getDiscount() }}%</div>
                                </div>
                                <i class="bi bi-award" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    @endif

                    <div class="summary-item">
                        <span>Subtotal</span>
                        <span class="fw-bold" id="subtotal-amount">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>

                    @if($discount > 0)
                        <div class="summary-item discount">
                            <span>Diskon Member</span>
                            <span class="fw-bold" id="discount-amount">- Rp {{ number_format($discount, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <div class="summary-item">
                        <span>Ongkir</span>
                        <span class="text-muted">Dihitung saat checkout</span>
                    </div>

                    <div class="summary-item total">
                        <span>Total</span>
                        <span class="text-primary" id="total-amount">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>

                    @if($cartItems->count() > 0)
                        <a href="{{ route('checkout') }}" class="btn btn-checkout">
                            <i class="bi bi-bag-check"></i> Checkout
                        </a>
                    @else
                        <button class="btn btn-checkout" disabled>
                            <i class="bi bi-bag-check"></i> Checkout
                        </button>
                    @endif

                    <div class="security-badge">
                        <small>
                            <i class="bi bi-shield-check"></i> Pembayaran aman & terpercaya
                        </small>
                    </div>
                </div>

                <div class="cart-card mt-3">
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-left"></i> Lanjutkan Belanja
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            // Quantity increase
            document.querySelectorAll('.btn-qty-increase').forEach(btn => {
                btn.addEventListener('click', function() {
                    const itemId = this.dataset.id;
                    const input = document.querySelector(`.qty-input[data-id="${itemId}"]`);
                    const max = parseInt(input.max);
                    const newValue = parseInt(input.value) + 1;

                    if (newValue <= max) {
                        input.value = newValue;
                        updateCart(itemId, newValue);
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Stok Tidak Cukup',
                            text: 'Jumlah melebihi stok yang tersedia',
                        });
                    }
                });
            });

            // Quantity decrease
            document.querySelectorAll('.btn-qty-decrease').forEach(btn => {
                btn.addEventListener('click', function() {
                    const itemId = this.dataset.id;
                    const input = document.querySelector(`.qty-input[data-id="${itemId}"]`);
                    const newValue = parseInt(input.value) - 1;

                    if (newValue >= 1) {
                        input.value = newValue;
                        updateCart(itemId, newValue);
                    }
                });
            });

            // Manual input change
            document.querySelectorAll('.qty-input').forEach(input => {
                input.addEventListener('change', function() {
                    const itemId = this.dataset.id;
                    const newValue = parseInt(this.value);
                    const max = parseInt(this.max);

                    if (newValue < 1) {
                        this.value = 1;
                        updateCart(itemId, 1);
                    } else if (newValue > max) {
                        this.value = max;
                        updateCart(itemId, max);
                        Swal.fire({
                            icon: 'warning',
                            title: 'Stok Tidak Cukup',
                            text: 'Jumlah melebihi stok yang tersedia',
                        });
                    } else {
                        updateCart(itemId, newValue);
                    }
                });
            });

            // Remove item
            document.querySelectorAll('.btn-remove').forEach(btn => {
                btn.addEventListener('click', function() {
                    const itemId = this.dataset.id;

                    Swal.fire({
                        title: 'Hapus Item?',
                        text: "Item akan dihapus dari keranjang",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            removeFromCart(itemId);
                        }
                    });
                });
            });

            function updateCart(itemId, qty) {
                fetch(`/cart/${itemId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ qty: qty })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan saat mengupdate keranjang',
                    });
                });
            }

            function removeFromCart(itemId) {
                fetch(`/cart/${itemId}`, {
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

            // Load cart and wishlist counts
            function loadCounts() {
                fetch('/api/cart/count')
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('cart-count').textContent = data.count || 0;
                    })
                    .catch(error => console.error('Error loading cart count:', error));

                fetch('/api/wishlist/count')
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('wishlist-count').textContent = data.count || 0;
                    })
                    .catch(error => console.error('Error loading wishlist count:', error));
            }

            // Load counts on page load
            loadCounts();
        });
    </script>
@endpush
