<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $product->nama_produk }} Ayu Mart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        :root {
            --primary-color: #3F4F44;
            --secondary-color: #64748b;
            --success-color: #3F4F44;
            --danger-color: #ef4444;
            --green: #3F4F44;
            --light-green: #f6ffed;
            --dark-green: #556B58;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
        }

        /* Navbar Style */
        .navbar {
            background: linear-gradient(135deg, #3F4F44 0%, #2E3A31 100%);
            box-shadow: 0 2px 8px 0 rgba(82,196,26,.15);
            padding: 0.8rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.8rem;
            color: white !important;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
        }

        .nav-link {
            color: rgba(255,255,255,0.95) !important;
            font-weight: 500;
            transition: all 0.3s;
            padding: 0.5rem 1rem !important;
        }

        .nav-link:hover {
            color: white !important;
            opacity: 0.8;
        }

        .btn-outline-light {
            border: 2px solid rgba(255,255,255,0.8);
            font-weight: 600;
        }

        .btn-outline-light:hover {
            background-color: white;
            color: #3F4F44;
        }

        /* Search Bar in Navbar */
        .navbar-search {
            max-width: 500px;
            flex-grow: 1;
        }

        .navbar-search input {
            border: none;
            border-radius: 2px;
            padding: 8px 15px;
            background: rgba(255,255,255,0.9);
        }

        .navbar-search input:focus {
            background: white;
            outline: none;
            box-shadow: 0 0 4px rgba(0,0,0,0.15);
        }

        .navbar-search button {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            padding: 8px 20px;
            border-radius: 2px;
            transition: all 0.3s;
        }

        .navbar-search button:hover {
            background: rgba(255,255,255,0.3);
        }

        /* User Menu Icons */
        .nav-icon {
            position: relative;
            padding: 8px 12px;
            color: rgba(255,255,255,0.95) !important;
            transition: all 0.2s;
        }

        .nav-icon:hover {
            color: white !important;
            background: rgba(255,255,255,0.1);
            border-radius: 4px;
        }

        .nav-icon i {
            font-size: 1.3rem;
        }

        .nav-icon .badge {
            position: absolute;
            top: 2px;
            right: 2px;
            font-size: 0.65rem;
            padding: 2px 5px;
            min-width: 18px;
        }

        .user-dropdown {
            background: rgba(255,255,255,0.1);
            padding: 8px 15px;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .user-dropdown:hover {
            background: rgba(255,255,255,0.2);
        }

        .user-avatar {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: white;
            color: #3F4F44;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.75rem;
            margin-right: 8px;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            border-radius: 4px;
            margin-top: 8px;
        }

        .dropdown-item {
            padding: 10px 20px;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .dropdown-item:hover {
            background: #fff5f3;
            color: #3F4F44;
        }

        .dropdown-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* Branch Location Section */
        .branch-location-section {
            background: #fff3cd;
            border-bottom: 1px solid #ffd966;
            padding: 10px 0;
            margin-bottom: 0;
        }

        .branch-info-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        .branch-current {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
        }

        .branch-current i.bi-geo-alt-fill {
            font-size: 1.2rem;
        }

        .branch-selector {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        #branchSelector {
            min-width: 200px;
            border: 1px solid #ffc107;
            background: white;
        }

        #detectLocationBtn {
            white-space: nowrap;
        }

        /* Notification Styles */
        .notification-item {
            transition: background-color 0.2s;
        }

        .notification-item:hover {
            background-color: #f8f9fa !important;
        }

        .unread-notification {
            background-color: #fff5f3;
            border-left: 3px solid #3F4F44;
        }

        .unread-notification:hover {
            background-color: #ffe8e3 !important;
        }

        #notificationDropdownMenu {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        #notificationDropdownMenu .dropdown-header {
            background-color: #f8f9fa;
            padding: 12px 20px;
            font-size: 0.95rem;
        }

        #markAllReadBtn {
            color: #3F4F44;
            font-size: 0.8rem;
        }

        #markAllReadBtn:hover {
            color: #d73211;
        }

        /* Product Detail */
        .product-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .product-card {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            margin-bottom: 1.5rem;
        }

        .product-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e5e5e5;
        }

        .product-gallery {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .gallery-thumb {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
            border: 2px solid #e5e5e5;
            cursor: pointer;
            transition: all 0.3s;
        }

        .gallery-thumb:hover,
        .gallery-thumb.active {
            border-color: #3F4F44;
        }

        .product-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1rem;
        }

        .product-price {
            font-size: 2rem;
            font-weight: 700;
            color: #3F4F44;
            margin-bottom: 0.5rem;
        }

        .product-price-old {
            font-size: 1.2rem;
            color: #666;
            text-decoration: line-through;
        }

        .discount-badge {
            display: inline-block;
            background: #3F4F44;
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.9rem;
            margin-left: 0.5rem;
        }

        .stock-info {
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 6px;
            margin: 1rem 0;
        }

        .btn-primary {
            background: #3F4F44;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background: #556B58;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(238, 77, 45, 0.3);
        }

        .btn-outline-primary {
            border-color: #3F4F44;
            color: #3F4F44;
        }

        .btn-outline-primary:hover {
            background: #3F4F44;
            border-color: #3F4F44;
            color: white;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1rem 0;
        }

        .quantity-control button {
            width: 40px;
            height: 40px;
            border-radius: 6px;
            border: 1px solid #e5e5e5;
            background: white;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .quantity-control button:hover {
            background: #3F4F44;
            color: white;
            border-color: #3F4F44;
        }

        .quantity-control input {
            width: 80px;
            text-align: center;
            border: 1px solid #e5e5e5;
            border-radius: 6px;
            padding: 0.5rem;
            font-weight: 600;
        }

        /* Reviews */
        .rating-summary {
            border: 1px solid #e5e5e5;
        }

        .review-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border: 1px solid #e5e5e5;
            transition: all 0.3s;
        }

        .review-card:hover {
            border-color: #3F4F44;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .review-header {
            margin-bottom: 1rem;
        }

        .review-author {
            font-weight: 600;
            color: #333;
            font-size: 1.1rem;
        }

        .review-rating {
            color: #fbbf24;
            margin-top: 0.25rem;
        }

        .review-rating i {
            font-size: 1rem;
        }

        .review-date {
            color: #666;
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }

        .review-content {
            color: #333;
            line-height: 1.8;
            font-style: italic;
        }

        .review-photo img {
            transition: all 0.3s;
        }

        .review-photo img:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .user-avatar-review img {
            border: 2px solid #3F4F44;
        }

        @media (max-width: 768px) {
            .product-image {
                height: 300px;
            }

            .product-title {
                font-size: 1.4rem;
            }

            .product-price {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('images/logo.png') }}" alt="AyuMart" height="36" class="d-inline-block align-text-top me-2">
                Ayu Mart
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Search Bar -->
                <div class="navbar-search mx-lg-3 my-2 my-lg-0">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Cari Barang disini..." id="searchInput">
                        <button class="btn" type="button">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>

                <!-- Right Menu -->
                <ul class="navbar-nav ms-auto align-items-center">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right"></i> Masuk
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="bi bi-person-plus"></i> Daftar
                            </a>
                        </li>
                    @else
                        <!-- Notifikasi -->
                        <li class="nav-item dropdown">
                            <a class="nav-link nav-icon dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" title="Notifikasi" id="notificationDropdown">
                                <i class="bi bi-bell"></i>
                                <span class="badge bg-danger" id="notification-count" style="display: none;">0</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" style="width: 350px; max-height: 500px; overflow-y: auto;" id="notificationDropdownMenu">
                                <li class="dropdown-header d-flex justify-content-between align-items-center">
                                    <span><strong>Notifikasi</strong></span>
                                    <button class="btn btn-sm btn-link text-decoration-none p-0" onclick="markAllAsRead()" id="markAllReadBtn">
                                        <small>Tandai Semua Dibaca</small>
                                    </button>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li id="notificationList">
                                    <div class="text-center py-3">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mb-0 mt-2 small text-muted">Memuat notifikasi...</p>
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li class="text-center">
                                    <a class="dropdown-item small" href="{{ route('pelanggan.orders') }}">
                                        <i class="bi bi-eye"></i> Lihat Semua Pesanan
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Ticketing -->
                        <li class="nav-item">
                            <a class="nav-link nav-icon" href="{{ route('pelanggan.tickets.index') }}" title="Ticketing">
                                <i class="bi bi-headset"></i>
                                <span class="badge bg-info" id="ticket-count">0</span>
                            </a>
                        </li>

                        <!-- Wishlist -->
                        <li class="nav-item">
                            <a class="nav-link nav-icon" href="{{ route('pelanggan.wishlist') }}" title="Wishlist">
                                <i class="bi bi-heart"></i>
                                <span class="badge bg-danger" id="wishlist-count">0</span>
                            </a>
                        </li>

                        <!-- Keranjang -->
                        <li class="nav-item">
                            <a class="nav-link nav-icon" href="{{ route('pelanggan.cart') }}" title="Keranjang">
                                <i class="bi bi-cart3"></i>
                                <span class="badge bg-danger" id="cart-count">0</span>
                            </a>
                        </li>

                        <!-- User Menu Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle user-dropdown" href="#" role="button" data-bs-toggle="dropdown">
                                <span class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                <span class="d-none d-lg-inline">{{ Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('pelanggan.profile') }}">
                                        <i class="bi bi-person-circle"></i> Profil Saya
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('pelanggan.orders') }}">
                                        <i class="bi bi-bag-check"></i> Pesanan Saya
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('pelanggan.reviews.index') }}">
                                        <i class="bi bi-star-fill text-warning"></i> Review Saya
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right"></i> Keluar
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>


    <!-- Branch Location Section -->
    <section class="branch-location-section" style="background: #fff3cd; border-bottom: 1px solid #ffd966; padding: 10px 0; margin-bottom: 0;">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
                <div class="d-flex align-items-center" style="gap: 8px; font-size: 0.95rem;">
                    <i class="bi bi-geo-alt-fill text-danger" style="font-size: 1.2rem;"></i>
                    <span id="branch-info">
                        @if(session('nearest_branch'))
                            <strong>{{ session('nearest_branch')['nama_cabang'] }}</strong>
                            @if(session('nearest_branch')['distance'])
                                <small class="text-muted ms-2">
                                    <i class="bi bi-pin-map"></i>
                                    {{ number_format(session('nearest_branch')['distance'], 1) }} km
                                </small>
                            @endif
                        @else
                            <strong>Pilih Cabang Terdekat</strong>
                        @endif
                    </span>
                </div>
                <div class="d-flex align-items-center" style="gap: 8px;">
                    <select id="branchSelector" class="form-select form-select-sm" style="min-width: 200px; border: 1px solid #ffc107; background: white;">
                        <option value="">Ganti Cabang</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id_cabang }}"
                                {{ session('nearest_branch') && session('nearest_branch')['id_cabang'] == $branch->id_cabang ? 'selected' : '' }}>
                                {{ $branch->nama_cabang }}
                            </option>
                        @endforeach
                    </select>
                    <button id="detectLocationBtn" class="btn btn-sm btn-outline-primary" style="white-space: nowrap;" title="Deteksi Lokasi Saya">
                        <i class="bi bi-crosshair"></i> Deteksi Lokasi
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Detail -->
    <div class="product-container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $product->nama_produk }}</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Product Images -->
            <div class="col-md-5">
                <div class="product-card">
                    <img src="{{ $product->foto_produk ? \App\Helpers\ImageHelper::getProductImage($product->foto_produk) : 'https://via.placeholder.com/400' }}"
                         alt="{{ $product->nama_produk }}"
                         class="product-image"
                         id="mainImage">

                    @if($images->count() > 0)
                    <div class="product-gallery">
                        <img src="{{ \App\Helpers\ImageHelper::getProductThumbnail($product->foto_produk, 100, 100) }}"
                             class="gallery-thumb active"
                             onclick="changeImage('{{ \App\Helpers\ImageHelper::getProductImage($product->foto_produk) }}', this)">
                        @foreach($images as $image)
                        <img src="{{ asset('storage/' . $image->path_gambar) }}"
                             class="gallery-thumb"
                             onclick="changeImage('{{ asset('storage/' . $image->path_gambar) }}', this)">
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-md-7">
                <div class="product-card">
                    <h1 class="product-title">{{ $product->nama_produk }}</h1>

                    <!-- Price -->
                    <div class="mb-3">
                        @if($product->hasActiveDiscount())
                            <div class="product-price-old">Rp {{ number_format($product->harga_produk, 0, ',', '.') }}</div>
                            <div class="product-price">
                                Rp {{ number_format($product->getCurrentPrice(), 0, ',', '.') }}
                                <span class="discount-badge">-{{ number_format($product->getDiscountPercentage(), 0) }}%</span>
                            </div>
                            <div class="text-success small mt-2">
                                <i class="bi bi-clock-fill"></i>
                                Promo hingga {{ $product->tanggal_akhir_diskon ? $product->tanggal_akhir_diskon->format('d M Y') : '' }}
                            </div>
                        @else
                            <div class="product-price">Rp {{ number_format($product->harga_produk, 0, ',', '.') }}</div>
                        @endif
                    </div>

                    <!-- Stock -->
                    <div class="stock-info">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Stok di Cabang Terpilih:</strong>
                                @php
                                    $stok = $product->stok_cabang ?? $product->stok_produk ?? 0;
                                @endphp
                                @if($stok > 10)
                                    <span class="text-success"><strong>{{ $stok }}</strong> tersedia</span>
                                @elseif($stok > 0)
                                    <span class="text-warning"><strong>{{ $stok }}</strong> tersisa (Terbatas)</span>
                                @else
                                    <span class="text-danger">Stok habis di cabang ini</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <h5>Deskripsi Produk</h5>
                        <p>{{ $product->deskripsi_produk ?? 'Tidak ada deskripsi' }}</p>
                    </div>

                    <!-- Member Discount Pricing Table -->
                    @php
                        $memberDiscounts = $product->getAllMemberDiscounts();
                    @endphp
                    @if(!empty($memberDiscounts) && $memberDiscounts->count() > 0)
                    <div class="mb-4">
                        <h5 class="mb-3">
                            <i class="bi bi-crown"></i> Harga Khusus Member
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0" style="font-size: 0.9rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tier Membership</th>
                                        <th class="text-right">Diskon</th>
                                        <th class="text-right">Harga</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($memberDiscounts as $discount)
                                    <tr>
                                        <td>
                                            <span class="badge badge-{{
                                                $discount['tier'] === 'platinum' ? 'warning' :
                                                ($discount['tier'] === 'gold' ? 'info' :
                                                ($discount['tier'] === 'silver' ? 'secondary' : 'success'))
                                            }}">
                                                {{ $discount['tier_name'] }}
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <span class="text-danger font-weight-bold">
                                                -{{ number_format($discount['discount_percentage'], 0) }}%
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <span class="text-success font-weight-bold">
                                                Rp {{ number_format($discount['price_with_discount'], 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle"></i>
                            Harga akan berubah sesuai dengan tier membership Anda
                        </small>
                    </div>
                    @endif

                    @auth
                    @php
                        $stok = $product->stok_cabang ?? $product->stok_produk ?? 0;
                    @endphp
                    @if($stok > 0)
                    <!-- Quantity -->
                    <div class="quantity-control">
                        <label>Jumlah:</label>
                        <button type="button" onclick="decreaseQty()">
                            <i class="bi bi-dash"></i>
                        </button>
                        <input type="number" id="quantity" value="1" min="1" max="{{ $stok }}" readonly>
                        <button type="button" onclick="increaseQty()">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2 mb-3">
                        <button class="btn btn-primary flex-fill" onclick="addToCart({{ $product->id_produk }})">
                            <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                        </button>
                        <button class="btn btn-outline-primary" onclick="addToWishlist({{ $product->id_produk }})">
                            <i class="bi bi-heart"></i>
                        </button>
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> Produk ini sedang tidak tersedia di cabang yang dipilih. Silakan pilih cabang lain.
                    </div>
                    @endif
                    @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <a href="{{ route('login') }}">Login</a> untuk membeli produk ini
                    </div>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="product-card mt-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">
                    <i class="bi bi-star-fill text-warning"></i> Ulasan Produk
                </h4>
                <div class="text-muted">
                    <span class="badge bg-warning text-dark">
                        <i class="bi bi-star-fill"></i> {{ number_format($averageRating, 1) }}
                    </span>
                    <span class="ms-2">{{ $totalReviews }} ulasan</span>
                </div>
            </div>

            <!-- Rating Summary -->
            @if($totalReviews > 0)
            <div class="rating-summary mb-4 p-4" style="background: #f8f9fa; border-radius: 8px;">
                <div class="row align-items-center">
                    <div class="col-md-3 text-center border-end">
                        <div class="display-4 fw-bold text-warning">{{ number_format($averageRating, 1) }}</div>
                        <div class="text-warning mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($averageRating))
                                    <i class="bi bi-star-fill"></i>
                                @elseif($i - 0.5 <= $averageRating)
                                    <i class="bi bi-star-half"></i>
                                @else
                                    <i class="bi bi-star"></i>
                                @endif
                            @endfor
                        </div>
                        <div class="text-muted small">Dari {{ $totalReviews }} ulasan</div>
                    </div>
                    <div class="col-md-9">
                        <div class="px-3">
                            @foreach($ratingDistribution as $stars => $data)
                            <div class="d-flex align-items-center mb-2">
                                <div class="me-2" style="width: 50px;">{{ $stars }} <i class="bi bi-star-fill text-warning"></i></div>
                                <div class="progress flex-fill" style="height: 8px;">
                                    <div class="progress-bar bg-warning" role="progressbar"
                                         style="width: {{ $data['percentage'] }}%"
                                         aria-valuenow="{{ $data['percentage'] }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100"></div>
                                </div>
                                <div class="ms-2 text-muted" style="width: 50px;">{{ $data['count'] }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Reviews List -->
            @if($reviews->count() > 0)
                @foreach($reviews as $review)
                <div class="review-card">
                    <div class="review-header">
                        <div class="d-flex align-items-center gap-3">
                            <div class="user-avatar-review">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($review->customer_name) }}&background=667eea&color=fff"
                                     alt="{{ $review->customer_name }}"
                                     style="width: 50px; height: 50px; border-radius: 50%;">
                            </div>
                            <div>
                                <div class="review-author">{{ $review->customer_name }}</div>
                                <div class="review-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <i class="bi bi-star-fill"></i>
                                        @else
                                            <i class="bi bi-star"></i>
                                        @endif
                                    @endfor
                                    <span class="ms-2 text-muted small">({{ $review->rating }}.0)</span>
                                </div>
                                <div class="review-date">
                                    <i class="bi bi-calendar"></i>
                                    {{ \Carbon\Carbon::parse($review->created_at)->format('d M Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="review-content mt-3">
                        "{{ $review->review }}"
                    </div>
                    @if($review->foto_review)
                    <div class="review-photo mt-3">
                        <img src="{{ asset('storage/' . $review->foto_review) }}"
                             alt="Review Photo"
                             style="max-width: 200px; max-height: 200px; border-radius: 8px; cursor: pointer; border: 1px solid var(--border-color);"
                             onclick="window.open(this.src, '_blank')">
                    </div>
                    @endif
                </div>
                @endforeach
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-chat-dots" style="font-size: 3rem; opacity: 0.3;"></i>
                    <p class="mt-3 mb-1">Belum ada ulasan untuk produk ini</p>
                    <small>Jadilah yang pertama memberikan ulasan!</small>
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        @php
            $stok = $product->stok_cabang ?? $product->stok_produk ?? 0;
        @endphp
        const maxQty = {{ $stok }};

        // Change main image
        function changeImage(src, element) {
            document.getElementById('mainImage').src = src;
            document.querySelectorAll('.gallery-thumb').forEach(thumb => {
                thumb.classList.remove('active');
            });
            element.classList.add('active');
        }

        // Quantity controls
        function increaseQty() {
            const input = document.getElementById('quantity');
            const currentQty = parseInt(input.value);
            if (currentQty < maxQty) {
                input.value = currentQty + 1;
            }
        }

        function decreaseQty() {
            const input = document.getElementById('quantity');
            const currentQty = parseInt(input.value);
            if (currentQty > 1) {
                input.value = currentQty - 1;
            }
        }

        // Add to cart
        function addToCart(productId) {
            const qty = parseInt(document.getElementById('quantity').value);

            fetch(`/cart/add/${productId}`, {
                method: 'POST',
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
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    loadCounts();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.message,
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan',
                });
            });
        }

        // Add to wishlist
        function addToWishlist(productId) {
            fetch(`/wishlist/add/${productId}`, {
                method: 'POST',
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
                    });
                    loadCounts();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.message,
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan',
                });
            });
        }

        // Load cart and wishlist counts
        @auth
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

        function loadTicketCount() {
            fetch('/api/ticket/count')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('ticket-count').textContent = data.count || 0;
                })
                .catch(error => console.error('Error loading ticket count:', error));
        }

        function loadNotificationCount() {
            fetch('/api/notification/count')
                .then(response => response.json())
                .then(data => {
                    const countElement = document.getElementById('notification-count');
                    if (data.count > 0) {
                        countElement.textContent = data.count;
                        countElement.style.display = 'inline-block';
                    } else {
                        countElement.style.display = 'none';
                    }
                })
                .catch(error => console.error('Error loading notification count:', error));
        }

        function loadNotifications() {
            fetch('/api/notifications')
                .then(response => response.json())
                .then(data => {
                    const notificationList = document.getElementById('notificationList');
                    if (data.notifications && data.notifications.length > 0) {
                        notificationList.innerHTML = data.notifications.map(notif => `
                            <li class="notification-item ${!notif.is_read ? 'unread-notification' : ''}">
                                <a href="${notif.link || '#'}" class="dropdown-item" onclick="markNotificationAsRead(${notif.id})">
                                    <div class="d-flex justify-content-between">
                                        <strong>${notif.title}</strong>
                                        <small class="text-muted">${new Date(notif.created_at).toLocaleDateString()}</small>
                                    </div>
                                    <small class="text-muted d-block">${notif.message}</small>
                                </a>
                            </li>
                        `).join('');
                    } else {
                        notificationList.innerHTML = '<li><p class="text-center text-muted my-3">Tidak ada notifikasi</p></li>';
                    }
                })
                .catch(error => console.error('Error loading notifications:', error));
        }

        function markNotificationAsRead(notificationId) {
            fetch(`/api/notification/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function markAllAsRead() {
            fetch('/api/notifications/read-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(() => {
                loadNotificationCount();
                loadNotifications();
            })
            .catch(error => console.error('Error:', error));
        }

        // Load counts on page load
        loadCounts();
        loadTicketCount();
        loadNotificationCount();

        // Refresh counts every 30 seconds
        setInterval(() => {
            loadCounts();
            loadTicketCount();
            loadNotificationCount();
        }, 30000);

        // Load notifications when dropdown is opened
        document.getElementById('notificationDropdown')?.addEventListener('click', function(e) {
            loadNotifications();
        });
        @endauth

        // Branch Selector - Detect Location
        document.getElementById('detectLocationBtn').addEventListener('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;

                    fetch('/api/set-user-location', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ latitude: lat, longitude: lon })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.branch) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Lokasi Ditemukan',
                                text: `Cabang terdekat: ${data.branch.nama_cabang} (${data.branch.distance} km)`,
                                confirmButtonColor: '#3F4F44',
                                timer: 2000,
                                timerProgressBar: true,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'info',
                                title: 'Informasi',
                                text: 'Cabang terdekat tidak ditemukan',
                                confirmButtonColor: '#3F4F44'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat mendeteksi lokasi',
                            confirmButtonColor: '#3F4F44'
                        });
                    });
                }, function() {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perijinan Diperlukan',
                        text: 'Izinkan akses lokasi untuk mendeteksi cabang terdekat',
                        confirmButtonColor: '#3F4F44'
                    });
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Browser Tidak Mendukung',
                    text: 'Browser Anda tidak mendukung fitur deteksi lokasi',
                    confirmButtonColor: '#3F4F44'
                });
            }
        });

        // Branch Selector - Change Branch
        document.getElementById('branchSelector').addEventListener('change', function() {
            const branchId = this.value;

            if (branchId) {
                fetch('/api/change-branch', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ id_cabang: branchId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Cabang Berhasil Diubah',
                            text: `Anda sekarang memilih cabang: ${data.branch.nama_cabang}`,
                            confirmButtonColor: '#3F4F44',
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat mengubah cabang',
                            confirmButtonColor: '#3F4F44'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat mengubah cabang',
                        confirmButtonColor: '#3F4F44'
                    });
                });
            }
        });
    </script>

    @auth
    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    @endauth
</body>
</html>
