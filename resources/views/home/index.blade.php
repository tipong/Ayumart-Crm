<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AyuMart - Supermarket Online | Always Fresh, Be Healthy</title>
    <meta name="description" content="Belanja kebutuhan supermarket online di AyuMart. Produk segar, harga terjangkau, pengiriman cepat.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;600;700;800&family=Barlow+Condensed:wght@400;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        /* ============================================
           VARIABLES & BASE
        ============================================ */
        :root {
            --primary:        #015b1e;
            --primary-dark:   #013d14;
            --primary-light:  #e8f5e9;
            --accent:         #e7482e;
            --accent-dark:    #c43520;
            --text-dark:      #1a1a1a;
            --text-mid:       #444;
            --text-muted:     #777;
            --border:         #e0e0e0;
            --white:          #ffffff;
            --body-bg:        #e8f5e9;
            --card-shadow:    0 3px 12px rgba(0,0,0,0.08);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Source Sans Pro', sans-serif;
            background-color: var(--body-bg);
            color: var(--text-dark);
            font-size: 15px;
        }

        a { text-decoration: none !important; color: inherit; }

        /* ============================================
           TOP BAR
        ============================================ */
        .top-bar {
            background: var(--primary);
            color: rgba(255,255,255,0.9);
            font-size: 13px;
            padding: 5px 0;
        }
        .top-bar a { color: rgba(255,255,255,0.9); }
        .top-bar a:hover { color: #fff; }

        /* ============================================
           NAVBAR
        ============================================ */
        .navbar {
            background: #fff !important;
            border-bottom: 2px solid var(--primary);
            padding: 0.5rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: 'Barlow Condensed', sans-serif;
            font-weight: 700;
            font-size: 1.6rem;
            color: var(--primary) !important;
            letter-spacing: 0.5px;
        }
        .navbar-brand img { height: 45px; width: auto; }

        .navbar .nav-link {
            color: var(--text-dark) !important;
            font-weight: 600;
            font-size: 14px;
            padding: 0.55rem 1rem !important;
            transition: all 0.25s;
            border-radius: 5px;
        }
        .navbar .nav-link:hover {
            color: var(--white) !important;
            background-color: var(--primary);
        }

        /* Search bar */
        .navbar-search { max-width: 480px; flex-grow: 1; }
        .navbar-search input {
            border: 2px solid var(--border);
            border-right: none;
            border-radius: 100px 0 0 100px;
            padding: 8px 18px;
            background: #fff;
            font-family: 'Source Sans Pro', sans-serif;
            font-size: 14px;
        }
        .navbar-search input:focus {
            border-color: var(--primary);
            box-shadow: none;
            outline: none;
        }
        .navbar-search button {
            background: var(--primary);
            border: 2px solid var(--primary);
            color: white;
            padding: 8px 20px;
            border-radius: 0 100px 100px 0;
            transition: all 0.25s;
            font-size: 15px;
        }
        .navbar-search button:hover { background: var(--primary-dark); border-color: var(--primary-dark); }

        /* Nav icon buttons */
        .nav-icon-btn {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            color: var(--text-dark) !important;
            border-radius: 50%;
            transition: all 0.25s;
            font-size: 1.25rem;
        }
        .nav-icon-btn:hover {
            background-color: var(--primary-light);
            color: var(--primary) !important;
        }
        .nav-icon-btn .badge {
            position: absolute;
            top: 2px;
            right: 2px;
            font-size: 0.6rem;
            padding: 2px 5px;
            min-width: 17px;
            border-radius: 10px;
            background: var(--accent) !important;
        }

        /* User button */
        .user-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--primary);
            color: #fff !important;
            border-radius: 100px;
            padding: 6px 16px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.25s;
        }
        .user-btn:hover { background: var(--primary-dark); color: #fff !important; }
        .user-avatar {
            width: 26px; height: 26px; border-radius: 50%;
            background: rgba(255,255,255,0.3);
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.75rem;
        }

        /* Dropdown */
        .dropdown-menu {
            border: 1px solid var(--border);
            border-radius: 10px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.12);
            padding: 8px 0;
        }
        .dropdown-item {
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-mid);
            transition: all 0.2s;
        }
        .dropdown-item:hover { background: var(--primary-light); color: var(--primary); }
        .dropdown-item i { width: 20px; margin-right: 8px; color: var(--primary); }

        .navbar-toggler { border: 2px solid var(--primary); padding: 4px 8px; }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%23015b1e' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* ============================================
           BRANCH BAR
        ============================================ */
        .branch-bar {
            background: var(--primary-light);
            border-bottom: 1.5px solid rgba(1,91,30,0.1);
            padding: 10px 0;
            font-size: 13px;
            font-weight: 600;
            color: var(--primary);
        }
        .branch-bar .form-select {
            border: 1.5px solid var(--primary);
            font-size: 13px;
            padding: 5px 12px;
            font-weight: 600;
            min-width: 180px;
            border-radius: 8px;
            color: var(--primary);
            background-color: #fff;
        }
        .branch-bar .form-select:focus {
            box-shadow: 0 0 0 0.2rem rgba(1,91,30,0.15);
            border-color: var(--primary);
        }
        .branch-bar .btn { 
            font-size: 13px; 
            padding: 5px 14px; 
            border-radius: 8px; 
            border: 1.5px solid var(--primary);
            color: var(--primary);
            font-weight: 600;
            transition: all 0.2s;
        }
        .branch-bar .btn:hover {
            background: var(--primary);
            color: #fff !important;
        }

        /* ============================================
           HERO SECTION
        ============================================ */
        .hero-section {
            position: relative;
            width: 100%;
            height: 420px;
            overflow: hidden;
            margin-bottom: 0;
        }
        .hero-section img.hero-bg {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
        }
        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to right, rgba(1,91,30,0.72) 0%, rgba(1,91,30,0.25) 60%, transparent 100%);
            display: flex;
            align-items: center;
        }
        .hero-text {
            padding: 40px;
            max-width: 500px;
        }
        .hero-text h1 {
            font-family: 'Barlow Condensed', sans-serif;
            font-weight: 800;
            font-size: 3rem;
            color: #fff;
            line-height: 1.15;
            letter-spacing: 1px;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        .hero-text p {
            color: rgba(255,255,255,0.9);
            font-size: 1.1rem;
            margin-bottom: 25px;
            font-weight: 400;
        }
        .btn-hero {
            background: var(--accent);
            color: #fff;
            border: none;
            padding: 12px 32px;
            border-radius: 100px;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s;
            display: inline-block;
            font-family: 'Source Sans Pro', sans-serif;
        }
        .btn-hero:hover {
            background: var(--accent-dark);
            color: #fff;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(231,72,46,0.45);
        }

        @media (max-width: 768px) {
            .hero-section { height: 280px; }
            .hero-text { padding: 20px; }
            .hero-text h1 { font-size: 1.8rem; }
            .hero-text p { font-size: 0.9rem; margin-bottom: 15px; }
            .btn-hero { padding: 9px 22px; font-size: 0.9rem; }
        }

        /* ============================================
           SECTION TITLES
        ============================================ */
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--primary-light);
        }
        .section-header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section-header-left .section-icon {
            width: 38px; height: 38px;
            background: var(--primary);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: #fff;
            font-size: 1.1rem;
        }
        .section-title-text {
            font-family: 'Barlow Condensed', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary);
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .btn-see-all {
            font-size: 13px;
            font-weight: 600;
            color: var(--primary);
            border: 1.5px solid var(--primary);
            padding: 4px 14px;
            border-radius: 100px;
            transition: all 0.25s;
        }
        .btn-see-all:hover { background: var(--primary); color: #fff; }

        /* ============================================
           CONTENT SECTIONS
        ============================================ */
        .content-section {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 22px;
            box-shadow: var(--card-shadow);
        }

        /* ============================================
           CATEGORY CARDS
        ============================================ */
        .category-card {
            text-align: center;
            padding: 15px 8px;
            border-radius: 16px;
            background: #fff;
            border: 1.5px solid var(--border);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            cursor: pointer;
            display: block;
            height: 100%;
        }
        .category-card:hover {
            border-color: var(--primary);
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(1,91,30,0.12);
        }
        .category-card.active-category {
            border-color: var(--primary);
            background: var(--primary-light);
            box-shadow: 0 4px 12px rgba(1,91,30,0.08);
        }
        .category-card .cat-img-wrap {
            width: 56px; height: 56px;
            border-radius: 50%;
            overflow: hidden;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 10px;
            border: 2px solid var(--border);
            transition: all 0.3s;
            background: #fff;
        }
        .category-card:hover .cat-img-wrap,
        .category-card.active-category .cat-img-wrap {
            border-color: var(--primary);
            transform: scale(1.08);
        }
        .category-card .cat-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .category-card h6 {
            font-size: 0.82rem;
            color: var(--text-mid);
            margin: 0;
            font-weight: 700;
            line-height: 1.3;
        }
        .category-card.active-category h6 { color: var(--primary); }

        /* ============================================
           PRODUCT CARDS
        ============================================ */
        .wrap-prodi {
            border: 1.5px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            position: relative;
            background: white;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .wrap-prodi:hover {
            transform: translateY(-6px);
            box-shadow: 0 8px 24px rgba(1,91,30,0.15);
            border-color: var(--primary);
        }

        .product-img-wrap {
            position: relative;
            height: 180px;
            background: #f9f9f9;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .product-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        .wrap-prodi:hover .product-img-wrap img { transform: scale(1.07); }
        .product-img-wrap .bi { font-size: 3.5rem; color: #ccc; }

        /* Discount badge */
        .badge-diskon {
            position: absolute;
            top: 0;
            left: 0;
            background: var(--accent);
            color: white;
            font-size: 12px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 0 0 10px 0;
            z-index: 1;
        }

        /* Stock badge */
        .badge-stock-out {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .badge-stock-out span {
            background: #fff;
            color: var(--accent);
            font-weight: 700;
            font-size: 13px;
            padding: 6px 16px;
            border-radius: 100px;
        }

        /* Product body */
        .prodi-body {
            padding: 12px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .prodi-name {
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 6px;
            min-height: 38px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            line-height: 1.35;
        }
        .prodi-price {
            font-family: 'Source Sans Pro', sans-serif;
            font-weight: 700;
            color: var(--accent);
            font-size: 1.1rem;
            margin-bottom: 2px;
        }
        .prodi-price-old {
            font-size: 0.8rem;
            color: var(--text-muted);
            text-decoration: line-through;
            margin-bottom: 8px;
        }
        .prodi-stock-info {
            font-size: 12px;
            margin-bottom: 10px;
        }
        .prodi-stock-info .badge { font-size: 11px; padding: 3px 8px; border-radius: 100px; font-weight: 600; }
        .badge-available { background: rgba(1,91,30,0.12); color: var(--primary); }
        .badge-low { background: rgba(255,152,0,0.12); color: #e65100; }
        .badge-out { background: rgba(231,72,46,0.12); color: var(--accent); }

        .btn-tambah {
            width: 100%;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 12px;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.25s;
            margin-top: auto;
            font-family: 'Source Sans Pro', sans-serif;
        }
        .btn-tambah:hover {
            background: var(--accent);
            transform: translateY(-1px);
        }
        .btn-tambah:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        .btn-login-to-buy {
            width: 100%;
            background: transparent;
            color: var(--primary);
            border: 1.5px solid var(--primary);
            border-radius: 8px;
            padding: 7px 12px;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.25s;
            margin-top: auto;
        }
        .btn-login-to-buy:hover { background: var(--primary); color: #fff; }

        /* ============================================
           PROMO SECTION SPECIFIC
        ============================================ */
        .promo-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, var(--accent), #ff6b4a);
            color: #fff;
            padding: 4px 14px;
            border-radius: 100px;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        /* ============================================
           FEATURES / WHY CHOOSE
        ============================================ */
        .feature-card {
            background: #fff;
            border-radius: 12px;
            padding: 25px 20px;
            text-align: center;
            border: 1.5px solid var(--border);
            transition: all 0.3s;
            height: 100%;
        }
        .feature-card:hover {
            border-color: var(--primary);
            transform: translateY(-4px);
            box-shadow: 0 6px 18px rgba(1,91,30,0.12);
        }
        .feature-icon {
            width: 64px; height: 64px;
            background: var(--primary-light);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 15px;
            font-size: 1.7rem;
            color: var(--primary);
            transition: all 0.3s;
        }
        .feature-card:hover .feature-icon { background: var(--primary); color: #fff; }
        .feature-card h5 {
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 8px;
            font-size: 1rem;
        }
        .feature-card p {
            color: var(--text-muted);
            font-size: 0.88rem;
            margin: 0;
        }

        /* ============================================
           FOOTER
        ============================================ */
        footer {
            background: var(--primary);
            color: rgba(255,255,255,0.85);
            padding: 45px 0 0;
            margin-top: 50px;
        }
        footer h5, footer h6 {
            color: #fff;
            font-weight: 700;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 16px;
        }
        footer p, footer li, footer a {
            color: rgba(255,255,255,0.78);
            font-size: 0.9rem;
            line-height: 1.9;
        }
        footer a:hover { color: #fff; }
        footer ul { list-style: none; padding: 0; }
        .footer-social a {
            display: inline-flex; align-items: center; justify-content: center;
            width: 36px; height: 36px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%; color: #fff !important;
            margin-right: 8px; font-size: 1rem;
            transition: all 0.25s;
        }
        .footer-social a:hover { background: var(--accent); transform: translateY(-3px); }
        .footer-bottom {
            background: var(--primary-dark);
            padding: 12px 0;
            text-align: center;
            font-size: 0.85rem;
            color: rgba(255,255,255,0.65);
            margin-top: 30px;
        }

        /* ============================================
           MISC
        ============================================ */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 5px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--accent); }

        /* Search result info */
        .alert { border-radius: 10px; border: none; font-weight: 600; font-size: 14px; }

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
            .navbar-search { max-width: 100%; margin: 8px 0; }
            .content-section { padding: 18px; }
            .category-card { padding: 12px 6px; }
            .category-card .cat-img-wrap { width: 44px; height: 44px; }
        }
    </style>
</head>
<body>

    <!-- Top Bar (desktop only) -->
    <div class="top-bar d-none d-md-block">
        <div class="container d-flex justify-content-between align-items-center">
            <span><i class="bi bi-telephone-fill me-1"></i> +62 85 955 202 267 &nbsp;|&nbsp; <i class="bi bi-envelope-fill me-1"></i> tigaayumart@gmail.com</span>
            <span>
                <a href="https://www.facebook.com/3aayumart" class="me-2"><i class="bi bi-facebook"></i></a>
                <a href="https://www.instagram.com/3aayumart12/"><i class="bi bi-instagram"></i></a>
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

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <!-- Search -->
                <div class="navbar-search mx-lg-3 my-2 my-lg-0">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Cari produk di sini..." id="searchInput">
                        <button class="btn" type="button" onclick="performSearch()">
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
                            <a class="btn-hero ms-1" href="{{ route('register') }}" style="padding:8px 20px;font-size:14px;">
                                <i class="bi bi-person-plus me-1"></i> Daftar
                            </a>
                        </li>
                    @else
                        <!-- Ticket -->
                        <li class="nav-item">
                            <a class="nav-icon-btn" href="{{ route('pelanggan.tickets.index') }}" title="Bantuan">
                                <i class="bi bi-headset"></i>
                                <span class="badge" id="ticket-count" style="display:none;">0</span>
                            </a>
                        </li>
                        <!-- Wishlist -->
                        <li class="nav-item">
                            <a class="nav-icon-btn" href="{{ route('pelanggan.wishlist') }}" title="Wishlist">
                                <i class="bi bi-heart"></i>
                                <span class="badge" id="wishlist-count" style="display:none;">0</span>
                            </a>
                        </li>
                        <!-- Cart -->
                        <li class="nav-item">
                            <a class="nav-icon-btn" href="{{ route('pelanggan.cart') }}" title="Keranjang">
                                <i class="bi bi-cart3"></i>
                                <span class="badge" id="cart-count" style="display:none;">0</span>
                            </a>
                        </li>
                        <!-- User -->
                        <li class="nav-item dropdown ms-1">
                            <a class="user-btn dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
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
                                        <i class="bi bi-star-fill" style="color:#f59e0b;"></i> Review Saya
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('membership') }}">
                                        <i class="bi bi-award"></i> Membership
                                    </a>
                                </li>
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

    <!-- Branch Location Bar -->
    <section class="branch-bar">
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
                            <strong>Pilih Cabang Terdekat Anda</strong>
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
                    <button id="detectLocationBtn" class="btn btn-sm btn-outline-primary" style="border-radius:100px;border-color:var(--primary);color:var(--primary);" title="Deteksi Lokasi Otomatis">
                        <i class="bi bi-crosshair"></i> Deteksi Lokasi
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== HERO SECTION ==================== -->
    <section class="hero-section">
        <img src="{{ asset('images/home.jpeg') }}" alt="AyuMart Banner" class="hero-bg">
        <div class="hero-overlay">
            <div class="container">
                <div class="hero-text">
                    <h1>Belanja Segar,<br>Hidup Sehat!</h1>
                    <p>Temukan produk segar dan berkualitas untuk kebutuhan keluarga Anda setiap hari.</p>
                    <a href="#products" class="btn-hero">
                        <i class="bi bi-bag-heart me-2"></i> Belanja Sekarang
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="container py-4">

        <!-- ==================== FEATURES BAR ==================== -->
        <div class="row g-3 mb-3">
            <div class="col-6 col-md-3">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-truck"></i></div>
                    <h5>Pengiriman Cepat</h5>
                    <p>Kurir ke rumah Anda dengan aman dan tepat waktu</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-shield-check"></i></div>
                    <h5>Produk Terjamin</h5>
                    <p>Kualitas produk selalu fresh dan terjamin kesegarannya</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-award"></i></div>
                    <h5>Program Member</h5>
                    <p>Kumpulkan poin & nikmati diskon eksklusif member</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-headset"></i></div>
                    <h5>Layanan 24/7</h5>
                    <p>Tim customer service siap membantu kapan saja</p>
                </div>
            </div>
        </div>

        <!-- ==================== CATEGORIES ==================== -->
        @if($categories && count($categories) > 0)
        <div class="content-section">
            <div class="section-header">
                <div class="section-header-left">
                    <div class="section-icon"><i class="bi bi-grid-3x3-gap-fill"></i></div>
                    <span class="section-title-text">Kategori</span>
                </div>
            </div>
            <div class="row g-2 g-md-3">
                <!-- All -->
                <div class="col-4 col-sm-3 col-md-2 col-lg-1" style="min-width:90px;">
                    <a href="{{ route('home', ['category' => 'all']) }}#products">
                        <div class="category-card {{ (!request('category') || request('category') == 'all') ? 'active-category' : '' }}">
                            <div class="cat-img-wrap">
                                <img src="{{ asset('images/categories/semua_kategori.png') }}" class="cat-image" alt="Semua Kategori">
                            </div>
                            <h6>Semua</h6>
                        </div>
                    </a>
                </div>
                @foreach($categories as $category)
                <div class="col-4 col-sm-3 col-md-2 col-lg-1" style="min-width:90px;">
                    <a href="{{ route('home', ['category' => $category->id_jenis]) }}#products">
                        <div class="category-card {{ request('category') == $category->id_jenis ? 'active-category' : '' }}">
                            @php
                                $catImages = [
                                    'Makanan Pokok'          => 'makanan_pokok.png',
                                    'Minuman'                => 'minuman.png',
                                    'Snack & Makanan Ringan' => 'snack.png',
                                    'Susu & Produk Olahan'   => 'makanan_pokok.png',
                                    'Buah & Sayur'           => 'buah_sayur.png',
                                    'Daging & Seafood'       => 'buah_sayur.png',
                                    'Bumbu & Penyedap'       => 'makanan_pokok.png',
                                    'Frozen Food'            => 'snack.png',
                                    'Perawatan Pribadi'      => 'minuman.png',
                                    'Peralatan Rumah Tangga' => 'makanan_pokok.png',
                                    'Ibu & Bayi'             => 'makanan_pokok.png',
                                    'Kesehatan'              => 'minuman.png',
                                ];
                                $imgFile = $catImages[$category->nama_jenis] ?? 'makanan_pokok.png';
                            @endphp
                            <div class="cat-img-wrap">
                                <img src="{{ asset('images/categories/' . $imgFile) }}" class="cat-image" alt="{{ $category->nama_jenis }}">
                            </div>
                            <h6>{{ Str::limit($category->nama_jenis, 14) }}</h6>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- ==================== PROMO SECTION ==================== -->
        <div class="content-section" id="promo">
            <div class="section-header">
                <div class="section-header-left">
                    <div class="section-icon" style="background:var(--accent);"><i class="bi bi-percent"></i></div>
                    <span class="section-title-text" style="color:var(--accent);">Diskon & Promo</span>
                </div>
                @if(isset($categoryId) && $categoryId)
                    <a href="{{ route('home') }}" class="btn-see-all">Lihat Semua</a>
                @endif
            </div>

            @if(isset($promoProducts) && count($promoProducts) > 0)
            <div class="row g-3">
                @foreach($promoProducts as $product)
                @php
                    $custTier = $customerTier ?? null;
                    $hargaDiskon = $product->getCurrentPrice($custTier);
                    $adaDiskon = $hargaDiskon < $product->harga_produk;
                    $pctDiskon = $adaDiskon ? round((($product->harga_produk - $hargaDiskon) / $product->harga_produk) * 100) : 0;
                    $isTier = ($product->discount_target === 'tier');
                    $stok = $product->stok_cabang ?? 0;
                @endphp
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="wrap-prodi h-100">
                        @if($adaDiskon)
                            <span class="badge-diskon">-{{ $pctDiskon }}%</span>
                        @endif
                        <a href="{{ route('product.show', $product->id_produk) }}">
                            <div class="product-img-wrap">
                                @if($product->foto_produk)
                                    <img src="{{ \App\Helpers\ImageHelper::getProductThumbnail($product->foto_produk, 200, 200) }}" alt="{{ $product->nama_produk }}">
                                @else
                                    <i class="bi bi-box-seam"></i>
                                @endif
                                @if($stok == 0)
                                    <div class="badge-stock-out"><span>Stok Habis</span></div>
                                @endif
                            </div>
                        </a>
                        <div class="prodi-body">
                            <a href="{{ route('product.show', $product->id_produk) }}">
                                <div class="prodi-name">{{ $product->nama_produk }}</div>
                            </a>
                            @if($adaDiskon)
                                <div class="prodi-price">Rp {{ number_format($hargaDiskon, 0, ',', '.') }}</div>
                                <div class="prodi-price-old">Rp {{ number_format($product->harga_produk, 0, ',', '.') }}</div>
                            @else
                                <div class="prodi-price">Rp {{ number_format($product->harga_produk, 0, ',', '.') }}</div>
                            @endif
                            <div class="prodi-stock-info">
                                @if($stok > 10)
                                    <span class="badge badge-available"><i class="bi bi-check-circle me-1"></i>Tersedia</span>
                                @elseif($stok > 0)
                                    <span class="badge badge-low"><i class="bi bi-exclamation-triangle me-1"></i>Sisa {{ $stok }}</span>
                                @else
                                    <span class="badge badge-out">Habis</span>
                                @endif
                            </div>
                            @guest
                                <a href="{{ route('login') }}" class="btn-login-to-buy"><i class="bi bi-cart-plus me-1"></i> Beli</a>
                            @else
                                @if($stok > 0)
                                    <button class="btn-tambah" onclick="addToCart({{ $product->id_produk }}, event)">
                                        <i class="bi bi-cart-plus me-1"></i> Tambah
                                    </button>
                                @else
                                    <button class="btn-tambah" disabled>Stok Habis</button>
                                @endif
                            @endguest
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-4">
                <i class="bi bi-tag" style="font-size:3rem;color:#ccc;"></i>
                <p class="text-muted mt-2">Belum ada produk promo saat ini</p>
                @if(isset($categoryId) && $categoryId)
                    <a href="{{ route('home') }}" class="btn btn-sm btn-outline-primary mt-1" style="border-radius:100px;">
                        <i class="bi bi-arrow-clockwise"></i> Lihat Semua Diskon
                    </a>
                @endif
            </div>
            @endif
        </div>

        <!-- ==================== ALL PRODUCTS ==================== -->
        <div class="content-section" id="products">
            <div class="section-header">
                <div class="section-header-left">
                    <div class="section-icon"><i class="bi bi-shop-window"></i></div>
                    <span class="section-title-text">Produk untuk Anda</span>
                </div>
            </div>

            <!-- Search Result Info -->
            <div id="searchResultInfo" class="alert alert-info mb-3" style="display:none;">
                <i class="bi bi-info-circle me-1"></i>
                <span id="searchResultText"></span>
            </div>

            <!-- No Product Found -->
            <div id="noProductFound" class="text-center py-5" style="display:none;">
                <i class="bi bi-search" style="font-size:4rem;color:#ccc;"></i>
                <h5 class="mt-3 text-muted">Produk Tidak Ditemukan</h5>
                <p class="text-muted">Coba gunakan kata kunci lain</p>
                <button class="btn btn-sm btn-primary" onclick="clearSearch()" style="border-radius:100px;">
                    <i class="bi bi-arrow-clockwise"></i> Lihat Semua Produk
                </button>
            </div>

            @if($products && count($products) > 0)
            <div class="row g-3" id="productContainer">
                @foreach($products as $product)
                @php
                    $custTier = $customerTier ?? null;
                    $prodHargaDiskon = $product->getCurrentPrice($custTier);
                    $prodAdaDiskon = $prodHargaDiskon < $product->harga_produk;
                    $prodPctDiskon = $prodAdaDiskon ? round((($product->harga_produk - $prodHargaDiskon) / $product->harga_produk) * 100) : 0;
                    $stok = $product->stok_cabang ?? 0;
                @endphp
                <div class="col-6 col-md-4 col-lg-2 product-item">
                    <div class="wrap-prodi h-100">
                        @if($prodAdaDiskon)
                            <span class="badge-diskon">-{{ $prodPctDiskon }}%</span>
                        @endif
                        <a href="{{ route('product.show', $product->id_produk) }}">
                            <div class="product-img-wrap">
                                @if($product->foto_produk)
                                    <img src="{{ \App\Helpers\ImageHelper::getProductThumbnail($product->foto_produk, 200, 200) }}" alt="{{ $product->nama_produk }}">
                                @else
                                    <i class="bi bi-box-seam"></i>
                                @endif
                                @if($stok == 0)
                                    <div class="badge-stock-out"><span>Stok Habis</span></div>
                                @endif
                            </div>
                        </a>
                        <div class="prodi-body">
                            <a href="{{ route('product.show', $product->id_produk) }}">
                                <div class="prodi-name">{{ $product->nama_produk }}</div>
                            </a>
                            @if($prodAdaDiskon)
                                <div class="prodi-price">Rp {{ number_format($prodHargaDiskon, 0, ',', '.') }}</div>
                                <div class="prodi-price-old">Rp {{ number_format($product->harga_produk, 0, ',', '.') }}</div>
                            @else
                                <div class="prodi-price" style="color:var(--primary);">Rp {{ number_format($product->harga_produk, 0, ',', '.') }}</div>
                            @endif
                            <div class="prodi-stock-info">
                                @if($stok > 10)
                                    <span class="badge badge-available"><i class="bi bi-check-circle me-1"></i>Stok: {{ $stok }}</span>
                                @elseif($stok > 0)
                                    <span class="badge badge-low"><i class="bi bi-exclamation-triangle me-1"></i>Sisa: {{ $stok }}</span>
                                @else
                                    <span class="badge badge-out">Stok Habis</span>
                                @endif
                            </div>
                            @guest
                                <a href="{{ route('login') }}" class="btn-login-to-buy"><i class="bi bi-cart-plus me-1"></i> Beli</a>
                            @else
                                @if($stok > 0)
                                    <button class="btn-tambah" onclick="addToCart({{ $product->id_produk }}, event)">
                                        <i class="bi bi-cart-plus me-1"></i> Tambah
                                    </button>
                                @else
                                    <button class="btn-tambah" disabled>Stok Habis</button>
                                @endif
                            @endguest
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            @if(!request('category') || request('category') == '')
            <div class="text-center mt-5">
                <a href="{{ route('home', ['category' => 'all']) }}#products" class="btn btn-primary px-4 py-2" style="border-radius: 100px; font-weight: 700; font-size: 14px; box-shadow: 0 4px 12px rgba(1, 91, 30, 0.2);">
                    Lihat Semua Produk <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            @endif

            @else
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size:4rem;color:#ccc;"></i>
                <h5 class="mt-3 text-muted">Belum ada produk tersedia</h5>
            </div>
            @endif
        </div>

    </div><!-- /container -->

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="{{ asset('images/logo.png') }}" alt="AyuMart" height="55" style="background:white;border-radius:50%;padding:2px;">
                        <div>
                            <h5 class="mb-0" style="font-family:'Barlow Condensed',sans-serif;font-size:1.5rem;">AYU MART</h5>
                            <small style="color:rgba(255,255,255,0.65);font-style:italic;">Always Fresh, Be Healthy</small>
                        </div>
                    </div>
                    <p>Supermarket online terpercaya pilihan keluarga Indonesia. Produk segar, harga terjangkau, pengiriman cepat.</p>
                </div>
                <div class="col-md-2 mb-4">
                    <h6>Layanan</h6>
                    <ul>
                        <li><a href="{{ route('pelanggan.tickets.index') }}"><i class="bi bi-chevron-right me-1" style="font-size:11px;"></i>Pusat Bantuan</a></li>
                        <li><a href="{{ route('pelanggan.orders') }}"><i class="bi bi-chevron-right me-1" style="font-size:11px;"></i>Pesanan Saya</a></li>
                        <li><a href="{{ route('pelanggan.profile') }}"><i class="bi bi-chevron-right me-1" style="font-size:11px;"></i>Profil Saya</a></li>
                        <li><a href="{{ route('membership') }}"><i class="bi bi-chevron-right me-1" style="font-size:11px;"></i>Membership</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6>Ikuti Kami</h6>
                    <div class="footer-social mb-3">
                        <a href="https://www.facebook.com/3aayumart" title="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="https://www.instagram.com/3aayumart12/" title="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="https://wa.me/6285955202267" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                    </div>
                    <p class="mb-1"><i class="bi bi-telephone-fill me-2"></i>+62 85 955 202 267</p>
                    <p><i class="bi bi-envelope-fill me-2"></i>tigaayumart@gmail.com</p>
                </div>
                <div class="col-md-3 mb-4">
                    <h6>Jam Operasional</h6>
                    <ul>
                        <li><i class="bi bi-clock me-2"></i>Senin – Jumat: 08.00 – 21.00</li>
                        <li><i class="bi bi-clock me-2"></i>Sabtu: 08.00 – 22.00</li>
                        <li><i class="bi bi-clock me-2"></i>Minggu: 09.00 – 21.00</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; {{ date('Y') }} AyuMart Supermarket. Hak Cipta Dilindungi.
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // ===================== NOTIFICATIONS =====================
        @if (session('success'))
            Swal.fire({ icon:'success', title:'Berhasil!', text:'{{ session('success') }}', confirmButtonColor:'#015b1e', timer:3000, timerProgressBar:true });
        @endif
        @if (session('error'))
            Swal.fire({ icon:'error', title:'Gagal!', text:'{{ session('error') }}', confirmButtonColor:'#015b1e' });
        @endif

        // ===================== SEARCH =====================
        function performSearch() {
            const searchInput = document.getElementById('searchInput');
            if (!searchInput) return;

            const term = searchInput.value.toLowerCase().trim();
            const items = document.querySelectorAll('.product-item');
            const container = document.getElementById('productContainer');
            const notFound = document.getElementById('noProductFound');
            const info = document.getElementById('searchResultInfo');
            const infoText = document.getElementById('searchResultText');

            let visible = 0;

            items.forEach(item => {
                const title = item.querySelector('.prodi-name')?.textContent.toLowerCase().trim() || '';
                const match = term === '' || title.includes(term);
                item.style.display = match ? '' : 'none';
                if (match) visible++;
            });

            if (term !== '') {
                setTimeout(() => {
                    document.getElementById('products')?.scrollIntoView({ behavior:'smooth', block:'start' });
                }, 100);

                if (visible === 0) {
                    if (container) container.style.display = 'none';
                    if (notFound) notFound.style.display = 'block';
                    if (info) info.style.display = 'none';
                } else {
                    if (container) container.style.display = '';
                    if (notFound) notFound.style.display = 'none';
                    if (info) info.style.display = 'block';
                    if (infoText) infoText.textContent = `Menampilkan ${visible} dari ${items.length} produk untuk "${term}"`;
                }
            } else {
                if (container) container.style.display = '';
                if (notFound) notFound.style.display = 'none';
                if (info) info.style.display = 'none';
            }
        }

        function clearSearch() {
            document.getElementById('searchInput').value = '';
            document.querySelectorAll('.product-item').forEach(i => i.style.display = '');
            const c = document.getElementById('productContainer');
            const n = document.getElementById('noProductFound');
            const i = document.getElementById('searchResultInfo');
            if (c) c.style.display = '';
            if (n) n.style.display = 'none';
            if (i) i.style.display = 'none';
            document.getElementById('products')?.scrollIntoView({ behavior:'smooth', block:'start' });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const inp = document.getElementById('searchInput');
            if (inp) {
                inp.addEventListener('keyup', e => {
                    if (e.key === 'Enter') {
                        performSearch();
                    } else {
                        clearTimeout(window._st);
                        window._st = setTimeout(performSearch, 350);
                    }
                });
            }
        });

        // ===================== ADD TO CART =====================
        function addToCart(productId, event) {
            if (event) { event.preventDefault(); event.stopPropagation(); }

            // Define Toast SweetAlert
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1800,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            const btn = event?.target.closest('button') || event?.target;
            let originalContent = '';
            if (btn) {
                originalContent = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            }

            fetch(`/cart/add/${productId}`, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ qty: 1 })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Toast.fire({
                        icon: 'success',
                        title: 'Produk ditambahkan ke keranjang'
                    });
                    
                    loadCounts();

                    if (btn) {
                        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Berhasil';
                        btn.classList.remove('btn-tambah');
                        btn.classList.add('btn-success', 'text-white');
                        setTimeout(() => {
                            btn.disabled = false;
                            btn.innerHTML = originalContent;
                            btn.classList.remove('btn-success', 'text-white');
                            btn.classList.add('btn-tambah');
                        }, 1500);
                    }
                } else {
                    Swal.fire({ icon:'error', title:'Gagal!', text: data.message || 'Terjadi kesalahan. Silakan coba lagi.', confirmButtonColor:'#015b1e' });
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = originalContent;
                    }
                }
            })
            .catch(() => {
                Swal.fire({ icon:'error', title:'Gagal!', text:'Terjadi kesalahan. Silakan coba lagi.', confirmButtonColor:'#015b1e' });
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalContent;
                }
            });
        }

        // ===================== COUNTS =====================
        @auth
        function loadCounts() {
            const h = { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' };
            
            const triggerBadgeAnimation = (el, newVal) => {
                const prevVal = el.textContent;
                el.textContent = newVal;
                el.style.display = newVal > 0 ? 'inline-block' : 'none';
                if (prevVal !== String(newVal)) {
                    el.classList.remove('badge-pulse');
                    void el.offsetWidth; // trigger reflow
                    el.classList.add('badge-pulse');
                }
            };

            ['cart','wishlist','ticket'].forEach(type => {
                const ep = type === 'ticket' ? '/api/tickets/count' : `/api/${type}/count`;
                fetch(ep, { headers: h })
                    .then(r => r.json())
                    .then(data => {
                        const count = data.count || 0;
                        const b = document.getElementById(`${type}-count`);
                        if (b) {
                            triggerBadgeAnimation(b, count);
                        }
                        if (type === 'cart') {
                            const fb = document.getElementById('floating-cart-count');
                            if (fb) {
                                triggerBadgeAnimation(fb, count);
                            }
                        }
                    }).catch(() => {});
            });
        }
        document.addEventListener('DOMContentLoaded', loadCounts);
        @endauth

        // ===================== BRANCH DETECTION =====================
        document.getElementById('detectLocationBtn')?.addEventListener('click', function() {
            if (!navigator.geolocation) {
                Swal.fire({ icon:'error', title:'Browser Tidak Mendukung', text:'Browser Anda tidak mendukung deteksi lokasi.', confirmButtonColor:'#015b1e' });
                return;
            }
            const btn = this;
            const originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mencari...';

            navigator.geolocation.getCurrentPosition(
                pos => {
                    fetch('/api/set-user-location', {
                        method:'POST',
                        headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content || '' },
                        body: JSON.stringify({ latitude: pos.coords.latitude, longitude: pos.coords.longitude })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success && data.branch) {
                            Swal.fire({
                                icon:'success', title:'Lokasi Ditemukan',
                                text:`Cabang terdekat: ${data.branch.nama_cabang} (${data.branch.distance} km)`,
                                confirmButtonColor:'#015b1e', timer:2000, timerProgressBar:true, showConfirmButton:false
                            }).then(() => window.location.reload());
                        } else {
                            Swal.fire({ icon:'info', title:'Informasi', text:'Cabang terdekat tidak ditemukan.', confirmButtonColor:'#015b1e' });
                            btn.disabled = false;
                            btn.innerHTML = originalHTML;
                        }
                    })
                    .catch(() => {
                        Swal.fire({ icon:'error', title:'Gagal', text:'Terjadi kesalahan saat mendeteksi lokasi.', confirmButtonColor:'#015b1e' });
                        btn.disabled = false;
                        btn.innerHTML = originalHTML;
                    });
                },
                () => {
                    Swal.fire({ icon:'warning', title:'Izin Diperlukan', text:'Izinkan akses lokasi untuk mendeteksi cabang terdekat.', confirmButtonColor:'#015b1e' });
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                }
            );
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

        document.getElementById('branchSelector')?.addEventListener('change', function() {
            const id = this.value;
            if (!id) return;
            fetch('/api/change-branch', {
                method:'POST',
                headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content || '' },
                body: JSON.stringify({ id_cabang: id })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon:'success', title:'Cabang Diubah',
                        text:`Sekarang memilih cabang: ${data.branch.nama_cabang}`,
                        confirmButtonColor:'#015b1e', timer:2000, timerProgressBar:true, showConfirmButton:false
                    }).then(() => window.location.reload());
                } else {
                    Swal.fire({ icon:'error', title:'Gagal', text:'Terjadi kesalahan saat mengubah cabang.', confirmButtonColor:'#015b1e' });
                }
            })
            .catch(() => Swal.fire({ icon:'error', title:'Gagal', text:'Terjadi kesalahan.', confirmButtonColor:'#015b1e' }));
        });
    </script>
</body>
</html>
