<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $product->nama_produk }} - AyuMart</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;600;700;800&family=Barlow+Condensed:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        :root {
            --primary:       #015b1e;
            --primary-dark:  #013d14;
            --primary-light: #e8f5e9;
            --accent:        #e7482e;
            --accent-dark:   #c43520;
            --text-dark:     #1a1a1a;
            --text-mid:      #444;
            --text-muted:    #777;
            --border:        #e0e0e0;
            --white:         #ffffff;
            --body-bg:       #e8f5e9;
        }

        body {
            font-family: 'Source Sans Pro', sans-serif;
            background-color: var(--body-bg);
            font-size: 15px;
        }

        a { text-decoration: none !important; }

        /* NAVBAR */
        .navbar {
            background: #fff !important;
            border-bottom: 2px solid var(--primary);
            padding: 0.5rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .navbar-brand {
            display: flex; align-items: center; gap: 10px;
            font-family: 'Barlow Condensed', sans-serif;
            font-weight: 700; font-size: 1.6rem;
            color: var(--primary) !important; letter-spacing: 0.5px;
        }
        .navbar-brand img { height: 45px; width: auto; }
        .navbar .nav-link {
            color: var(--text-dark) !important;
            font-weight: 600; font-size: 14px;
            padding: 0.55rem 1rem !important;
            transition: all 0.25s; border-radius: 5px;
        }
        .navbar .nav-link:hover { color: var(--white) !important; background-color: var(--primary); }
        /* Search */
        .navbar-search { max-width: 480px; flex-grow: 1; }
        .navbar-search input {
            border: 2px solid var(--border); border-right: none;
            border-radius: 100px 0 0 100px; padding: 8px 18px;
            font-family: 'Source Sans Pro', sans-serif; font-size: 14px;
        }
        .navbar-search input:focus { border-color: var(--primary); box-shadow: none; }
        .navbar-search button {
            background: var(--primary); border: 2px solid var(--primary);
            color: white; padding: 8px 20px;
            border-radius: 0 100px 100px 0; font-size: 15px;
        }
        .navbar-search button:hover { background: var(--primary-dark); }
        /* Nav icons */
        .nav-icon {
            position: relative; display: inline-flex;
            align-items: center; justify-content: center;
            width: 40px; height: 40px;
            color: var(--text-dark) !important;
            border-radius: 50%; transition: all 0.25s; font-size: 1.25rem;
        }
        .nav-icon:hover { background-color: var(--primary-light); color: var(--primary) !important; }
        .nav-icon i { font-size: 1.3rem; }
        .nav-icon .badge {
            position: absolute; top: 2px; right: 2px;
            font-size: 0.6rem; padding: 2px 5px; min-width: 17px;
            border-radius: 10px; background: var(--accent) !important;
        }
        /* User btn */
        .user-dropdown {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--primary); color: #fff !important;
            border-radius: 100px; padding: 6px 16px;
            font-weight: 600; font-size: 14px; transition: all 0.25s;
        }
        .user-dropdown:hover { background: var(--primary-dark); }
        .user-avatar {
            width: 26px; height: 26px; border-radius: 50%;
            background: rgba(255,255,255,0.3);
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.75rem; margin-right: 0;
        }
        /* Dropdown */
        .dropdown-menu {
            border: 1px solid var(--border); border-radius: 10px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.12); padding: 8px 0;
        }
        .dropdown-item {
            padding: 10px 20px; font-size: 14px;
            font-weight: 600; color: var(--text-mid); transition: all 0.2s;
        }
        .dropdown-item:hover { background: var(--primary-light); color: var(--primary); }
        .dropdown-item i { margin-right: 8px; width: 20px; color: var(--primary); }
        .navbar-toggler { border: 2px solid var(--primary); padding: 4px 8px; }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%23015b1e' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* BRANCH BAR */
        .branch-location-section {
            background: var(--primary-light);
            border-bottom: 1.5px solid rgba(1,91,30,0.1);
            padding: 10px 0;
            font-size: 13px;
            font-weight: 600;
            color: var(--primary);
        }
        #branchSelector {
            border: 1.5px solid var(--primary);
            font-size: 13px;
            padding: 5px 12px;
            font-weight: 600;
            min-width: 180px;
            border-radius: 8px;
            color: var(--primary);
            background-color: #fff;
        }
        #branchSelector:focus {
            box-shadow: 0 0 0 0.2rem rgba(1,91,30,0.15);
            border-color: var(--primary);
        }
        #detectLocationBtn {
            font-size: 13px; 
            padding: 5px 14px; 
            border-radius: 8px; 
            border: 1.5px solid var(--primary);
            color: var(--primary);
            font-weight: 600;
            transition: all 0.2s;
        }
        #detectLocationBtn:hover { 
            background: var(--primary); 
            color: #fff !important; 
        }
        .notification-item { transition: background-color 0.2s; }
        .notification-item:hover { background-color: var(--primary-light) !important; }
        .unread-notification { background-color: #f0fdf4; border-left: 3px solid var(--primary); }

        /* PRODUCT DETAIL */
        .product-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .product-card {
            background: white;
            border-radius: 14px;
            padding: 2rem;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
            border: 1px solid var(--border);
        }
        .product-image-container {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            border: 1px solid var(--border);
            cursor: zoom-in;
        }
        .product-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            transition: transform 0.4s ease, opacity 0.15s ease-in-out;
        }
        .product-image-container:hover .product-image {
            transform: scale(1.06);
        }
        .product-gallery {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }
        .gallery-thumb {
            width: 80px; height: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid var(--border);
            cursor: pointer;
            transition: all 0.25s;
        }
        .gallery-thumb:hover, .gallery-thumb.active { border-color: var(--primary); }
        .product-title {
            font-size: 1.7rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 1rem;
            font-family: 'Source Sans Pro', sans-serif;
        }
        .product-price {
            font-size: 2rem;
            font-weight: 700;
            color: var(--accent);
            margin-bottom: 0.5rem;
        }
        .product-price-old {
            font-size: 1.1rem;
            color: var(--text-muted);
            text-decoration: line-through;
        }
        .discount-badge {
            display: inline-block;
            background: var(--accent);
            color: white;
            padding: 0.3rem 0.9rem;
            border-radius: 100px;
            font-weight: 700;
            font-size: 0.85rem;
            margin-left: 0.5rem;
        }
        .stock-info {
            padding: 1rem;
            background: var(--primary-light);
            border-radius: 10px;
            margin: 1rem 0;
            border: 1px solid rgba(1,91,30,0.15);
        }
        .btn-primary {
            background: var(--primary);
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 100px;
            font-weight: 700;
            transition: all 0.3s;
            font-family: 'Source Sans Pro', sans-serif;
        }
        .btn-primary:hover {
            background: var(--accent);
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(231,72,46,0.35);
        }
        .btn-outline-primary {
            border: 2px solid var(--primary);
            color: var(--primary);
            border-radius: 100px;
            font-weight: 700;
        }
        .btn-outline-primary:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }
        .quantity-control {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 1.5rem 0;
            background: #f8f9fa;
            padding: 8px 16px;
            border-radius: 12px;
            width: fit-content;
            border: 1px solid var(--border);
        }
        .quantity-control label {
            font-weight: 700;
            color: var(--text-dark);
            margin-right: 1rem;
            margin-bottom: 0;
        }
        .quantity-control button {
            width: 36px; height: 36px;
            border-radius: 50%;
            border: 1px solid var(--border);
            background: white;
            color: var(--primary);
            cursor: pointer;
            font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.2s;
        }
        .quantity-control button:hover:not(:disabled) {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            transform: scale(1.05);
        }
        .quantity-control button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .quantity-control input {
            width: 50px; text-align: center;
            border: none;
            background: transparent;
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--text-dark);
        }
        /* Reviews */
        .review-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border: 1.5px solid var(--border);
            transition: all 0.25s;
        }
        .review-card:hover { border-color: var(--primary); box-shadow: 0 4px 14px rgba(1,91,30,0.1); }
        .review-author { font-weight: 700; color: var(--text-dark); font-size: 1rem; }
        .review-rating { color: #f59e0b; margin-top: 0.2rem; }
        .review-date { color: var(--text-muted); font-size: 0.82rem; margin-top: 0.2rem; }
        .review-content { color: var(--text-mid); line-height: 1.8; font-style: italic; }
        .user-avatar-review img { border: 2px solid var(--primary); }
        /* Footer */
        footer {
            background: var(--primary);
            color: rgba(255,255,255,0.85);
            padding: 40px 0 0;
            margin-top: 50px;
        }
        footer h5, footer h6 {
            color: #fff; font-weight: 700;
            font-size: 0.95rem; text-transform: uppercase;
            letter-spacing: 1px; margin-bottom: 14px;
        }
        footer p, footer li, footer a {
            color: rgba(255,255,255,0.78);
            font-size: 0.88rem; line-height: 1.9;
        }
        footer a:hover { color: #fff; }
        footer ul { list-style: none; padding: 0; }
        .footer-social a {
            display: inline-flex; align-items: center; justify-content: center;
            width: 34px; height: 34px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%; color: #fff !important;
            margin-right: 7px; transition: all 0.25s;
        }
        .footer-social a:hover { background: var(--accent); transform: translateY(-2px); }
        .footer-bottom {
            background: var(--primary-dark); padding: 11px 0;
            text-align: center; font-size: 0.82rem;
            color: rgba(255,255,255,0.6); margin-top: 28px;
        }
        /* ============================================
           MICRO-INTERACTIONS & USER-FRIENDLY CONTROLS
        ============================================ */
        @keyframes badgePulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.4); }
            100% { transform: scale(1); }
        }
        .badge-pulse {
            animation: badgePulse 0.4s ease-out;
        }

        /* Scroll to Top Button */
        #scrollToTopBtn {
            position: fixed;
            bottom: 85px;
            right: 20px;
            z-index: 99;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            border: none;
            outline: none;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        #scrollToTopBtn.show {
            opacity: 1;
            visibility: visible;
        }
        #scrollToTopBtn:hover {
            background: var(--accent);
            transform: translateY(-3px);
        }

        /* Floating Cart Button */
        #floatingCartBtn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 99;
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background: var(--accent);
            color: white;
            border: none;
            outline: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(231,72,46,0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        #floatingCartBtn i {
            font-size: 1.5rem;
        }
        #floatingCartBtn .badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: var(--primary);
            color: white;
            border: 2px solid white;
            border-radius: 50%;
            padding: 3px 6px;
            font-size: 0.7rem;
            font-weight: 700;
        }
        #floatingCartBtn:hover {
            background: var(--accent-dark);
            transform: scale(1.1);
        }

        @media (max-width: 768px) {
            .product-image { height: 280px; }
            .product-title { font-size: 1.3rem; }
            .product-price { font-size: 1.5rem; }
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div class="d-none d-md-block" style="background:var(--primary);color:rgba(255,255,255,0.9);font-size:13px;padding:5px 0;">
        <div class="container d-flex justify-content-between align-items-center">
            <span><i class="bi bi-telephone-fill me-1"></i> +62 85 955 202 267 &nbsp;|&nbsp; <i class="bi bi-envelope-fill me-1"></i> tigaayumart@gmail.com</span>
            <span>
                <a href="https://www.facebook.com/3aayumart" class="me-2" style="color:rgba(255,255,255,0.9);"><i class="bi bi-facebook"></i></a>
                <a href="https://www.instagram.com/3aayumart12/" style="color:rgba(255,255,255,0.9);"><i class="bi bi-instagram"></i></a>
            </span>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('images/logo.png') }}" alt="AyuMart">
                <span>Ayu Mart</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Search -->
                <div class="navbar-search mx-lg-3 my-2 my-lg-0">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Cari produk di sini..." id="searchInput"
                               onkeydown="if(event.key==='Enter'){window.location.href='{{ route('home') }}?search='+encodeURIComponent(this.value);}">
                        <button class="btn" type="button"
                                onclick="window.location.href='{{ route('home') }}?search='+encodeURIComponent(document.getElementById('searchInput').value);">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
                <!-- Right Menu -->
                <ul class="navbar-nav ms-auto align-items-center gap-1">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="bi bi-person-plus me-1"></i> Daftar
                            </a>
                        </li>
                    @else
                        <!-- Ticket -->
                        <li class="nav-item">
                            <a class="nav-icon" href="{{ route('pelanggan.tickets.index') }}" title="Bantuan">
                                <i class="bi bi-headset"></i>
                                <span class="badge" id="ticket-count" style="display:none;">0</span>
                            </a>
                        </li>
                        <!-- Wishlist -->
                        <li class="nav-item">
                            <a class="nav-icon" href="{{ route('pelanggan.wishlist') }}" title="Wishlist">
                                <i class="bi bi-heart"></i>
                                <span class="badge" id="wishlist-count" style="display:none;">0</span>
                            </a>
                        </li>
                        <!-- Cart -->
                        <li class="nav-item">
                            <a class="nav-icon" href="{{ route('pelanggan.cart') }}" title="Keranjang">
                                <i class="bi bi-cart3"></i>
                                <span class="badge" id="cart-count" style="display:none;">0</span>
                            </a>
                        </li>
                        <!-- User -->
                        <li class="nav-item dropdown ms-1">
                            <a class="user-dropdown dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <span class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                <span class="d-none d-lg-inline">{{ Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('pelanggan.profile') }}"><i class="bi bi-person-circle"></i> Profil Saya</a></li>
                                <li><a class="dropdown-item" href="{{ route('pelanggan.orders') }}"><i class="bi bi-bag-check"></i> Pesanan Saya</a></li>
                                <li><a class="dropdown-item" href="{{ route('pelanggan.reviews.index') }}"><i class="bi bi-star-fill" style="color:#f59e0b;"></i> Review Saya</a></li>
                                <li><a class="dropdown-item" href="{{ route('membership') }}"><i class="bi bi-award"></i> Membership</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}" style="color:var(--accent);"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right" style="color:var(--accent);"></i> Keluar
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>


    <!-- Branch Bar -->
    <section class="branch-location-section">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-geo-alt-fill text-danger"></i>
                    <span id="branch-info">
                        @if(session('nearest_branch'))
                            <strong>{{ session('nearest_branch')['nama_cabang'] }}</strong>
                            @if(session('nearest_branch')['distance'])
                                <span class="text-muted ms-1" style="font-weight:400;">
                                    <i class="bi bi-pin-map"></i> {{ number_format(session('nearest_branch')['distance'], 1) }} km
                                </span>
                            @endif
                        @else
                            <strong>Pilih Cabang Terdekat</strong>
                        @endif
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <select id="branchSelector" class="form-select form-select-sm">
                        <option value="">Pilih Cabang</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id_cabang }}"
                                {{ session('nearest_branch') && session('nearest_branch')['id_cabang'] == $branch->id_cabang ? 'selected' : '' }}>
                                {{ $branch->nama_cabang }}
                            </option>
                        @endforeach
                    </select>
                    <button id="detectLocationBtn" class="btn btn-sm btn-outline-primary">
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
                    <div class="product-image-container">
                        <img src="{{ $product->foto_produk ? \App\Helpers\ImageHelper::getProductImage($product->foto_produk) : 'https://via.placeholder.com/400' }}"
                             alt="{{ $product->nama_produk }}"
                             class="product-image"
                             id="mainImage">
                    </div>

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
                        @php
                            $isTierProduk = ($product->discount_target === 'tier');
                            $tierDiskonProduk = null;
                            $hargaAkhirProduk = $product->harga_produk;
                            $pctDiskonProduk = 0;
                            $adaDiskonProduk = false;

                            if ($isTierProduk && isset($customerTier) && $customerTier) {
                                $tierDiskonProduk = \App\Models\ProductMemberDiscount::findByProductAndTier($product->id_produk, $customerTier);
                                if ($tierDiskonProduk && $product->hasActiveDiscount()) {
                                    $pctDiskonProduk = $tierDiskonProduk->discount_percentage;
                                    $hargaAkhirProduk = $product->harga_produk - ($product->harga_produk * ($pctDiskonProduk / 100));
                                    $adaDiskonProduk = true;
                                }
                            } elseif (!$isTierProduk && $product->hasActiveDiscount()) {
                                $hargaAkhirProduk = $product->harga_diskon ?? $product->harga_produk;
                                $pctDiskonProduk = $product->persentase_diskon;
                                $adaDiskonProduk = true;
                            }
                        @endphp

                        @if($adaDiskonProduk)
                            <div class="product-price-old">Rp {{ number_format($product->harga_produk, 0, ',', '.') }}</div>
                            <div class="product-price">
                                Rp {{ number_format($hargaAkhirProduk, 0, ',', '.') }}
                                <span class="discount-badge">-{{ number_format($pctDiskonProduk, 0) }}%</span>
                                @if($isTierProduk && isset($customerTier) && $customerTier)
                                    <small class="d-block text-muted" style="font-size:0.75rem; font-weight:400;">
                                        Harga khusus Tier {{ ucfirst($customerTier) }} Anda
                                    </small>
                                @endif
                            </div>
                            <div class="text-success small mt-2">
                                <i class="bi bi-clock-fill"></i>
                                Promo hingga {{ $product->tanggal_akhir_diskon ? $product->tanggal_akhir_diskon->format('d M Y') : '' }}
                            </div>
                        @else
                            <div class="product-price">Rp {{ number_format($product->harga_produk, 0, ',', '.') }}</div>
                            @if($isTierProduk && $product->hasActiveDiscount() && (!isset($customerTier) || !$customerTier))
                                <small class="text-info d-block mt-1">
                                    <i class="bi bi-crown"></i> Produk ini memiliki harga khusus Member. <a href="{{ route('login') }}">Login</a> untuk melihat harga Anda.
                                </small>
                            @endif
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
                    <!-- @if(!empty($memberDiscounts) && $memberDiscounts->count() > 0)
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
                    @endif -->

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
                        <button id="btnAddToCartDetail" class="btn btn-primary flex-fill" onclick="addToCart({{ $product->id_produk }})">
                            <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                        </button>
                        <button id="btnWishlistDetail" class="btn btn-outline-primary" onclick="addToWishlist({{ $product->id_produk }})" title="Tambah ke Wishlist">
                            <i class="bi bi-heart"></i>
                        </button>
                        <button class="btn btn-outline-primary" onclick="shareProduct()" title="Bagikan Produk">
                            <i class="bi bi-share"></i>
                        </button>
                    </div>
                    @else
                    <div class="alert alert-warning d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <i class="bi bi-exclamation-triangle"></i> Produk ini sedang tidak tersedia di cabang yang dipilih. Silakan pilih cabang lain.
                        </div>
                        <button class="btn btn-sm btn-outline-secondary ms-2" onclick="shareProduct()" title="Bagikan Produk">
                            <i class="bi bi-share text-dark"></i>
                        </button>
                    </div>
                    @endif
                    @else
                    <div class="alert alert-info d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <i class="bi bi-info-circle"></i>
                            <a href="{{ route('login') }}" class="fw-bold">Login</a> untuk membeli produk ini
                        </div>
                        <button class="btn btn-sm btn-outline-secondary ms-2" onclick="shareProduct()" title="Bagikan Produk">
                            <i class="bi bi-share text-dark"></i>
                        </button>
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
                        <img src="{{ str_starts_with($review->foto_review, 'http') ? $review->foto_review : asset('storage/' . $review->foto_review) }}"
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

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="{{ asset('images/logo.png') }}" alt="AyuMart" height="50" style="background:white;border-radius:50%;padding:2px;">
                        <div>
                            <h5 class="mb-0" style="font-family:'Barlow Condensed',sans-serif;font-size:1.4rem;">AYU MART</h5>
                            <small style="color:rgba(255,255,255,0.65);font-style:italic;">Always Fresh, Be Healthy</small>
                        </div>
                    </div>
                    <p>Supermarket online terpercaya pilihan keluarga Indonesia.</p>
                </div>
                <div class="col-md-3 mb-4">
                    <h6>Ikuti Kami</h6>
                    <div class="footer-social mb-3">
                        <a href="https://www.facebook.com/3aayumart"><i class="bi bi-facebook"></i></a>
                        <a href="https://www.instagram.com/3aayumart12/"><i class="bi bi-instagram"></i></a>
                        <a href="https://wa.me/6285955202267"><i class="bi bi-whatsapp"></i></a>
                    </div>
                    <p class="mb-1"><i class="bi bi-telephone-fill me-2"></i>+62 85 955 202 267</p>
                    <p><i class="bi bi-envelope-fill me-2"></i>tigaayumart@gmail.com</p>
                </div>
                <div class="col-md-2 mb-4">
                    <h6>Layanan</h6>
                    <ul>
                        <li><a href="{{ route('pelanggan.tickets.index') }}">Pusat Bantuan</a></li>
                        <li><a href="{{ route('pelanggan.orders') }}">Pesanan Saya</a></li>
                        <li><a href="{{ route('home') }}">Belanja</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; {{ date('Y') }} AyuMart Supermarket. Hak Cipta Dilindungi.
        </div>
    </footer>

    @auth
    <!-- Floating Cart Button -->
    <a href="{{ route('pelanggan.cart') }}" id="floatingCartBtn" title="Keranjang Belanja">
        <i class="bi bi-cart3"></i>
        <span class="badge" id="floating-cart-count" style="display:none;">0</span>
    </a>
    @endauth

    <!-- Scroll to Top Button -->
    <button id="scrollToTopBtn" onclick="window.scrollTo({top: 0, behavior: 'smooth'})" title="Kembali ke Atas">
        <i class="bi bi-arrow-up-short" style="font-size: 1.8rem;"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        @php
            $stok = $product->stok_cabang ?? $product->stok_produk ?? 0;
        @endphp
        const maxQty = {{ $stok }};

        // Change main image
        // Change main image
        function changeImage(src, element) {
            const mainImg = document.getElementById('mainImage');
            if (mainImg) {
                mainImg.style.opacity = 0;
                setTimeout(() => {
                    mainImg.src = src;
                    mainImg.style.opacity = 1;
                }, 150);
            }
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

        // Share product function
        function shareProduct() {
            const tempInput = document.createElement('input');
            tempInput.value = window.location.href;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);

            // Toast feedback
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
            Toast.fire({
                icon: 'success',
                title: 'Tautan disalin ke papan klip!'
            });
        }

        // Add to cart
        function addToCart(productId) {
            const qtyInput = document.getElementById('quantity');
            const qty = qtyInput ? parseInt(qtyInput.value) : 1;

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1800,
                timerProgressBar: true
            });

            const btn = document.getElementById('btnAddToCartDetail');
            let originalContent = '';
            if (btn) {
                originalContent = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';
            }

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
                    Toast.fire({
                        icon: 'success',
                        title: 'Produk ditambahkan ke keranjang'
                    });
                    loadCounts();

                    if (btn) {
                        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Berhasil!';
                        btn.className = 'btn btn-success flex-fill text-white';
                        setTimeout(() => {
                            btn.disabled = false;
                            btn.innerHTML = originalContent;
                            btn.className = 'btn btn-primary flex-fill';
                        }, 1500);
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Terjadi kesalahan',
                        confirmButtonColor: '#015b1e'
                    });
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = originalContent;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan',
                    confirmButtonColor: '#015b1e'
                });
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalContent;
                }
            });
        }

        // Add to wishlist
        function addToWishlist(productId) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1800,
                timerProgressBar: true
            });

            const btn = document.getElementById('btnWishlistDetail');
            let originalContent = '';
            if (btn) {
                originalContent = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            }

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
                    Toast.fire({
                        icon: 'success',
                        title: 'Produk ditambahkan ke wishlist'
                    });
                    loadCounts();

                    if (btn) {
                        btn.innerHTML = '<i class="bi bi-heart-fill"></i>';
                        btn.className = 'btn btn-success text-white';
                        setTimeout(() => {
                            btn.disabled = false;
                            btn.innerHTML = originalContent;
                            btn.className = 'btn btn-outline-primary';
                        }, 1500);
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Terjadi kesalahan',
                        confirmButtonColor: '#015b1e'
                    });
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = originalContent;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan',
                    confirmButtonColor: '#015b1e'
                });
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalContent;
                }
            });
        }

        // Load cart and wishlist counts
        @auth
        function loadCounts() {
            const triggerBadgeAnimation = (el, newVal) => {
                if (!el) return;
                const prevVal = el.textContent;
                el.textContent = newVal;
                el.style.display = newVal > 0 ? 'inline-block' : 'none';
                if (prevVal !== String(newVal)) {
                    el.classList.remove('badge-pulse');
                    void el.offsetWidth; // trigger reflow
                    el.classList.add('badge-pulse');
                }
            };

            fetch('/api/cart/count')
                .then(response => response.json())
                .then(data => {
                    const count = data.count || 0;
                    const c = document.getElementById('cart-count');
                    triggerBadgeAnimation(c, count);
                    
                    const fc = document.getElementById('floating-cart-count');
                    triggerBadgeAnimation(fc, count);
                })
                .catch(error => console.error('Error loading cart count:', error));

            fetch('/api/wishlist/count')
                .then(response => response.json())
                .then(data => {
                    const count = data.count || 0;
                    const w = document.getElementById('wishlist-count');
                    triggerBadgeAnimation(w, count);
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
            if (!navigator.geolocation) {
                Swal.fire({
                    icon: 'error',
                    title: 'Browser Tidak Mendukung',
                    text: 'Browser Anda tidak mendukung fitur deteksi lokasi',
                    confirmButtonColor: '#015b1e'
                });
                return;
            }

            const btn = this;
            const originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mencari...';

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
                            confirmButtonColor: '#015b1e',
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
                            confirmButtonColor: '#015b1e'
                        });
                        btn.disabled = false;
                        btn.innerHTML = originalHTML;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat mendeteksi lokasi',
                        confirmButtonColor: '#015b1e'
                    });
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                });
            }, function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perijinan Diperlukan',
                    text: 'Izinkan akses lokasi untuk mendeteksi cabang terdekat',
                    confirmButtonColor: '#015b1e'
                });
                btn.disabled = false;
                btn.innerHTML = originalHTML;
            });
        });

        // Branch Selector - Change Branch
        document.getElementById('branchSelector').addEventListener('change', function() {
            const branchId = this.value;
            if (!branchId) return;

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
                        title: 'Cabang Diubah',
                        text: `Sekarang memilih cabang: ${data.branch.nama_cabang}`,
                        confirmButtonColor: '#015b1e',
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
                        confirmButtonColor: '#015b1e'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan saat mengubah cabang',
                    confirmButtonColor: '#015b1e'
                });
            });
        });

        // ===================== SCROLL TO TOP VISIBILITY =====================
        window.addEventListener('scroll', function() {
            const btn = document.getElementById('scrollToTopBtn');
            if (btn) {
                if (window.scrollY > 300) {
                    btn.classList.add('show');
                } else {
                    btn.classList.remove('show');
                }
            }
        });
    </script>

</body>
</html>
