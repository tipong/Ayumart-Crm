@extends('layouts.pelanggan')

@section('title', 'Wishlist Saya - AyuMart')

@push('styles')
<style>
    .wishlist-wrap { max-width: 1200px; margin: 0 auto; padding: 0 1rem; }

    /* Page hero override color for wishlist */
    .page-hero.wishlist-hero {
        background: linear-gradient(135deg, #c0392b 0%, #e7482e 100%);
    }

    /* Product card */
    .wl-card {
        background: #fff;
        border-radius: 14px;
        border: 1.5px solid var(--border);
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .wl-card:hover {
        border-color: var(--primary);
        transform: translateY(-6px);
        box-shadow: 0 10px 32px rgba(1,91,30,0.14);
    }

    .wl-img-wrap {
        position: relative;
        height: 190px;
        overflow: hidden;
        background: #f9f9f9;
    }
    .wl-img-wrap img {
        width: 100%; height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .wl-card:hover .wl-img-wrap img { transform: scale(1.06); }

    .wl-badge-discount {
        position: absolute;
        top: 0; left: 0;
        background: var(--accent);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 0 0 10px 0;
    }

    .wl-body {
        padding: 14px;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .wl-name {
        font-size: 0.88rem;
        font-weight: 700;
        color: var(--text-dark);
        line-height: 1.35;
        min-height: 36px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .wl-price {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--accent);
    }
    .wl-price-old {
        font-size: 0.8rem;
        color: var(--text-muted);
        text-decoration: line-through;
    }

    .wl-actions {
        display: flex;
        gap: 8px;
        margin-top: auto;
    }
    .wl-btn-view {
        flex: 1;
        background: var(--primary);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 9px 10px;
        font-weight: 700;
        font-size: 13px;
        transition: all 0.25s;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }
    .wl-btn-view:hover { background: var(--accent); transform: translateY(-1px); }

    .wl-btn-remove {
        width: 40px;
        background: transparent;
        border: 1.5px solid #fecaca;
        border-radius: 8px;
        color: var(--accent);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        transition: all 0.25s;
        cursor: pointer;
        flex-shrink: 0;
    }
    .wl-btn-remove:hover { background: var(--accent); color: #fff; border-color: var(--accent); }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }
    .empty-state .empty-icon {
        width: 100px; height: 100px;
        background: var(--primary-light);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 24px;
        font-size: 2.8rem;
        color: var(--primary);
    }
    .empty-state h4 { font-weight: 800; color: var(--text-dark); margin-bottom: 8px; }
    .empty-state p { color: var(--text-muted); margin-bottom: 24px; font-size: 15px; }

    @media (max-width: 576px) {
        .wl-img-wrap { height: 160px; }
    }
</style>
@endpush

@section('content')
<!-- Page Hero -->
<div class="page-hero wishlist-hero">
    <div class="container">
        <div class="d-flex align-items-center gap-3">
            <div class="hero-icon" style="background:rgba(255,255,255,0.15);">
                <i class="bi bi-heart-fill" style="color:#fff;"></i>
            </div>
            <div>
                <h1>Wishlist Saya</h1>
                <p>
                    {{ $wishlistItems->count() }} produk tersimpan
                    @if($wishlistItems->count() > 0)
                        — Jangan sampai kehabisan!
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>

<div class="container py-2 pb-4">
    <div class="wishlist-wrap">

        <!-- Breadcrumb -->
        <nav class="mb-4" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                <li class="breadcrumb-item active">Wishlist</li>
            </ol>
        </nav>

        @if($wishlistItems->count() > 0)

        <!-- Toolbar -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div style="font-size:14px;color:var(--text-muted);">
                <i class="bi bi-heart-fill text-danger me-1"></i>
                <strong style="color:var(--text-dark);">{{ $wishlistItems->count() }}</strong> produk di wishlist
            </div>
            <a href="{{ route('home') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i> Tambah Produk
            </a>
        </div>

        <!-- Grid -->
        <div class="row g-3">
            @foreach($wishlistItems as $item)
            @php
                $hasDiscount = $item->produk->hasActiveDiscount();
                $discountPct = $hasDiscount ? $item->produk->persentase_diskon : 0;
                $currentPrice = $item->produk->getCurrentPrice();
            @endphp
            <div class="col-6 col-md-4 col-lg-3">
                <div class="wl-card">
                    <a href="{{ route('product.show', $item->produk->id_produk) }}">
                        <div class="wl-img-wrap">
                            <img src="{{ $item->produk->foto_produk
                                ? \App\Helpers\ImageHelper::getProductThumbnail($item->produk->foto_produk, 220, 220)
                                : 'https://via.placeholder.com/220' }}"
                                alt="{{ $item->produk->nama_produk }}"
                                loading="lazy">
                            @if($hasDiscount)
                                <span class="wl-badge-discount">-{{ $discountPct }}%</span>
                            @endif
                        </div>
                    </a>
                    <div class="wl-body">
                        <a href="{{ route('product.show', $item->produk->id_produk) }}" style="color:inherit;">
                            <div class="wl-name">{{ $item->produk->nama_produk }}</div>
                        </a>
                        <div>
                            @if($hasDiscount)
                                <div class="wl-price-old">Rp {{ number_format($item->produk->harga_produk, 0, ',', '.') }}</div>
                                <div class="wl-price">Rp {{ number_format($currentPrice, 0, ',', '.') }}</div>
                            @else
                                <div class="wl-price" style="color:var(--primary);">Rp {{ number_format($item->produk->harga_produk, 0, ',', '.') }}</div>
                            @endif
                        </div>
                        <div class="wl-actions">
                            <a href="{{ route('product.show', $item->produk->id_produk) }}" class="wl-btn-view">
                                <i class="bi bi-eye"></i>
                                <span>Lihat</span>
                            </a>
                            <button class="wl-btn-remove btn-remove-wishlist"
                                    data-id="{{ $item->id_wishlist }}"
                                    title="Hapus dari wishlist"
                                    aria-label="Hapus dari wishlist">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @else
        <!-- Empty State -->
        <div class="ay-card">
            <div class="ay-card-body">
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="bi bi-heart"></i>
                    </div>
                    <h4>Wishlist Kosong</h4>
                    <p>Simpan produk favorit kamu di sini<br>agar mudah ditemukan nanti.</p>
                    <a href="{{ route('home') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-shop me-2"></i> Jelajahi Produk
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

    document.querySelectorAll('.btn-remove-wishlist').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const card = this.closest('.col-6, .col-md-4, .col-lg-3');

            Swal.fire({
                title: 'Hapus dari Wishlist?',
                text: 'Produk akan dihapus dari daftar wishlist Anda.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e7482e',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="bi bi-trash"></i> Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then(r => {
                if (!r.isConfirmed) return;

                fetch(`/wishlist/${id}`, {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Animasi hilang
                        if (card) {
                            card.style.transition = 'all 0.35s ease';
                            card.style.opacity = '0';
                            card.style.transform = 'scale(0.9)';
                            setTimeout(() => { card.remove(); updateCount(); }, 350);
                        }
                        Swal.fire({
                            icon: 'success',
                            title: 'Dihapus!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                        loadCounts();
                    }
                })
                .catch(() => Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan.' }));
            });
        });
    });

    function updateCount() {
        const remaining = document.querySelectorAll('.wl-card').length;
        const counter = document.querySelector('.page-hero p');
        if (counter) counter.textContent = `${remaining} produk tersimpan${remaining > 0 ? ' — Jangan sampai kehabisan!' : ''}`;
    }
});
</script>
@endpush
