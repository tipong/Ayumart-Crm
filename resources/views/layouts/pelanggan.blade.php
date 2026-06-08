<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AyuMart - Supermarket Online')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:ital,wght@0,300;0,400;0,600;0,700;0,800;1,400&family=Barlow+Condensed:wght@400;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    @stack('styles')

    <style>
        /* ============================================================
           DESIGN TOKENS
        ============================================================ */
        :root {
            --primary:          #015b1e;
            --primary-dark:     #013d14;
            --primary-light:    #e8f5e9;
            --primary-mid:      #c8e6c9;
            --accent:           #e7482e;
            --accent-dark:      #c43520;
            --accent-light:     #fef3f1;
            --yellow:           #f59e0b;
            --yellow-light:     #fffde7;
            --text-dark:        #1a1a1a;
            --text-mid:         #444;
            --text-muted:       #777;
            --border:           #e0e0e0;
            --white:            #ffffff;
            --body-bg:          #f0f4f1;
            --card-bg:          #ffffff;
            --card-shadow:      0 2px 12px rgba(0,0,0,0.07);
            --card-shadow-hover:0 8px 28px rgba(1,91,30,0.14);
            --radius:           12px;
            --radius-lg:        16px;
            --transition:       all 0.28s ease;
        }

        /* ============================================================
           RESET & BASE
        ============================================================ */
        *, *::before, *::after { box-sizing: border-box; }
        * { margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Source Sans Pro', -apple-system, sans-serif;
            background-color: var(--body-bg);
            color: var(--text-dark);
            font-size: 15px;
            line-height: 1.6;
        }

        a { text-decoration: none !important; color: inherit; }

        img { max-width: 100%; }

        /* ============================================================
           TOP INFO BAR
        ============================================================ */
        .top-bar {
            background: var(--primary);
            color: rgba(255,255,255,0.88);
            font-size: 13px;
            padding: 6px 0;
        }
        .top-bar a { color: rgba(255,255,255,0.88); transition: color 0.2s; }
        .top-bar a:hover { color: #fff; }
        .top-bar .top-bar-item {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        /* ============================================================
           NAVBAR
        ============================================================ */
        .navbar {
            background: #fff !important;
            border-bottom: 2px solid var(--primary);
            padding: 0.55rem 0;
            box-shadow: 0 3px 14px rgba(0,0,0,0.09);
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: 'Barlow Condensed', sans-serif;
            font-weight: 800;
            font-size: 1.65rem;
            color: var(--primary) !important;
            letter-spacing: 0.5px;
        }
        .navbar-brand img { height: 44px; width: auto; }
        .navbar-brand .brand-name { line-height: 1; }
        .navbar-brand .brand-tagline {
            font-size: 0.65rem;
            font-weight: 400;
            font-family: 'Source Sans Pro', sans-serif;
            color: var(--text-muted);
            letter-spacing: 0.5px;
            display: block;
            font-style: italic;
        }

        /* Nav links */
        .navbar .nav-link {
            color: var(--text-dark) !important;
            font-weight: 600;
            font-size: 14px;
            padding: 0.5rem 0.9rem !important;
            transition: var(--transition);
            border-radius: 8px;
        }
        .navbar .nav-link:hover,
        .navbar .nav-link.active {
            color: var(--white) !important;
            background-color: var(--primary);
        }

        /* Search bar */
        .nav-search-wrap { max-width: 460px; flex: 1; }
        .nav-search-wrap .input-group { border-radius: 100px; overflow: hidden; }
        .nav-search-wrap input {
            border: 2px solid var(--border);
            border-right: none;
            border-radius: 100px 0 0 100px !important;
            padding: 8px 20px;
            font-size: 14px;
            font-family: 'Source Sans Pro', sans-serif;
            background: #fafafa;
            transition: var(--transition);
        }
        .nav-search-wrap input:focus {
            border-color: var(--primary);
            background: #fff;
            box-shadow: none;
        }
        .nav-search-wrap button {
            background: var(--primary);
            border: 2px solid var(--primary);
            color: #fff;
            padding: 8px 20px;
            border-radius: 0 100px 100px 0 !important;
            transition: var(--transition);
        }
        .nav-search-wrap button:hover { background: var(--primary-dark); }

        /* Nav icon buttons (cart, wishlist, ticket, notification) */
        .nav-icon-btn {
            position: relative;
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--text-mid) !important;
            border-radius: 50%;
            transition: var(--transition);
            font-size: 1.25rem;
        }
        .nav-icon-btn:hover {
            background: var(--primary-light);
            color: var(--primary) !important;
        }
        .nav-icon-btn .badge {
            position: absolute;
            top: 2px; right: 2px;
            font-size: 0.58rem;
            min-width: 17px;
            padding: 2px 5px;
            border-radius: 100px;
            background: var(--accent) !important;
            border: 2px solid #fff;
        }

        /* Notification Dropdown */
        .notif-dropdown {
            width: 360px;
            max-width: 92vw;
            padding: 0;
            border-radius: 14px;
            border: 1px solid var(--border);
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            overflow: hidden;
        }
        .notif-header {
            background: linear-gradient(135deg, var(--primary), #027826);
            color: #fff;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .notif-header h6 { margin: 0; font-weight: 700; font-size: 0.95rem; }
        .notif-mark-all {
            font-size: 12px;
            color: rgba(255,255,255,0.85);
            background: rgba(255,255,255,0.15);
            border: none;
            border-radius: 100px;
            padding: 4px 12px;
            cursor: pointer;
            transition: var(--transition);
        }
        .notif-mark-all:hover { background: rgba(255,255,255,0.3); color: #fff; }
        .notif-list {
            max-height: 360px;
            overflow-y: auto;
        }
        .notif-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 13px 18px;
            border-bottom: 1px solid #f0f4f1;
            transition: background 0.18s;
            text-decoration: none !important;
            color: var(--text-dark);
        }
        .notif-item:hover { background: #f7fdf9; }
        .notif-item.unread { background: #f0fdf4; border-left: 3px solid var(--primary); padding-left: 15px; }
        .notif-icon {
            width: 38px; height: 38px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            font-size: 1rem;
        }
        .notif-icon.shipping   { background: #dbeafe; color: #1e40af; }
        .notif-icon.payment    { background: #dcfce7; color: var(--primary); }
        .notif-icon.expired    { background: #fee2e2; color: #dc2626; }
        .notif-icon.cancellation { background: #fef9c3; color: #854d0e; }
        .notif-icon.ticket     { background: #e0f2fe; color: #0369a1; }
        .notif-icon.order      { background: var(--primary-light); color: var(--primary); }
        .notif-body { flex: 1; min-width: 0; }
        .notif-title { font-weight: 700; font-size: 13px; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .notif-msg  { font-size: 12px; color: var(--text-muted); line-height: 1.4; }
        .notif-time { font-size: 11px; color: var(--text-muted); margin-top: 4px; }
        .notif-empty {
            padding: 32px 20px;
            text-align: center;
            color: var(--text-muted);
        }
        .notif-empty i { font-size: 2.5rem; display: block; margin-bottom: 10px; opacity: 0.4; }
        .notif-footer {
            padding: 12px 18px;
            text-align: center;
            border-top: 1px solid var(--border);
            background: #fafff8;
        }
        .notif-footer a {
            font-size: 13px;
            font-weight: 700;
            color: var(--primary);
        }
        .notif-footer a:hover { color: var(--accent); }

        /* Bell animation when there are notifications */
        @keyframes bellRing {
            0%, 100% { transform: rotate(0); }
            10%, 30%, 50% { transform: rotate(-12deg); }
            20%, 40%      { transform: rotate(12deg); }
        }
        .bell-animate { animation: bellRing 1.2s ease; }

        /* User pill button */
        .user-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--primary);
            color: #fff !important;
            border-radius: 100px;
            padding: 6px 18px;
            font-weight: 700;
            font-size: 14px;
            transition: var(--transition);
            border: 2px solid var(--primary);
        }
        .user-pill:hover { background: var(--primary-dark); border-color: var(--primary-dark); }
        .user-pill .user-av {
            width: 26px; height: 26px;
            border-radius: 50%;
            background: rgba(255,255,255,0.25);
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 0.72rem;
        }

        /* Dropdown */
        .dropdown-menu {
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: 0 8px 28px rgba(0,0,0,0.13);
            padding: 8px 0;
            margin-top: 10px !important;
        }
        .dropdown-item {
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-mid);
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .dropdown-item i { color: var(--primary); width: 18px; text-align: center; }
        .dropdown-item:hover { background: var(--primary-light); color: var(--primary); }
        .dropdown-item.logout-item i { color: var(--accent); }
        .dropdown-item.logout-item:hover { background: var(--accent-light); color: var(--accent); }
        .dropdown-divider { border-color: var(--border); margin: 4px 0; }

        .navbar-toggler { border: 2px solid var(--primary); border-radius: 8px; padding: 5px 9px; }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%23015b1e' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2.5' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* ============================================================
           MAIN CONTENT
        ============================================================ */
        .main-content {
            min-height: calc(100vh - 260px);
            padding-bottom: 2rem;
        }

        /* ============================================================
           BREADCRUMB
        ============================================================ */
        .breadcrumb-bar {
            background: #fff;
            border-bottom: 1px solid var(--border);
            padding: 9px 0;
            font-size: 13px;
        }
        .breadcrumb {
            margin: 0;
            padding: 0;
            background: none;
        }
        .breadcrumb-item + .breadcrumb-item::before {
            content: "›";
            font-weight: 700;
            color: var(--text-muted);
        }
        .breadcrumb-item a { color: var(--primary); font-weight: 600; }
        .breadcrumb-item.active { color: var(--text-muted); font-weight: 600; }

        /* ============================================================
           PAGE HEADER (untuk halaman-halaman pelanggan)
        ============================================================ */
        .page-hero {
            background: linear-gradient(135deg, var(--primary) 0%, #027826 100%);
            color: #fff;
            padding: 24px 0;
            margin-bottom: 24px;
        }
        .page-hero h1 {
            font-family: 'Barlow Condensed', sans-serif;
            font-weight: 800;
            font-size: 1.9rem;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .page-hero .hero-icon {
            width: 52px; height: 52px;
            background: rgba(255,255,255,0.15);
            border-radius: var(--radius);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem;
            backdrop-filter: blur(8px);
        }
        .page-hero p { color: rgba(255,255,255,0.82); margin: 6px 0 0; font-size: 0.9rem; }

        /* ============================================================
           CARD STYLES
        ============================================================ */
        .ay-card {
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            overflow: hidden;
        }
        .ay-card:hover { box-shadow: var(--card-shadow-hover); }

        .ay-card-header {
            background: var(--primary);
            color: #fff;
            padding: 14px 20px;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 0.3px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .ay-card-header i { font-size: 1.1rem; }

        .ay-card-body { padding: 20px; }

        /* ============================================================
           BADGES & STATUS
        ============================================================ */
        .badge {
            padding: 0.38rem 0.85rem;
            border-radius: 100px;
            font-weight: 700;
            font-size: 0.78rem;
            letter-spacing: 0.2px;
        }
        .badge-pending  { background: #fff3cd; color: #856404; }
        .badge-process  { background: #cff4fc; color: #055160; }
        .badge-ready    { background: #d1f0e0; color: #0f5132; }
        .badge-success  { background: var(--primary-light); color: var(--primary); }
        .badge-cancel   { background: #f8d7da; color: #842029; }
        .badge-danger   { background: var(--accent-light); color: var(--accent); }
        .badge-member   { background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; }

        /* ============================================================
           BUTTONS
        ============================================================ */
        .btn {
            font-family: 'Source Sans Pro', sans-serif;
            font-weight: 700;
            transition: var(--transition);
            border-radius: 100px;
            font-size: 14px;
            padding: 0.55rem 1.4rem;
        }
        .btn-primary {
            background: var(--primary) !important;
            border-color: var(--primary) !important;
            color: #fff !important;
        }
        .btn-primary:hover {
            background: var(--accent) !important;
            border-color: var(--accent) !important;
            transform: translateY(-2px);
            box-shadow: 0 5px 16px rgba(231,72,46,0.35);
        }
        .btn-outline-primary {
            border: 2px solid var(--primary) !important;
            color: var(--primary) !important;
            background: transparent !important;
        }
        .btn-outline-primary:hover {
            background: var(--primary) !important;
            color: #fff !important;
            transform: translateY(-2px);
        }
        .btn-accent {
            background: var(--accent) !important;
            border-color: var(--accent) !important;
            color: #fff !important;
        }
        .btn-accent:hover {
            background: var(--accent-dark) !important;
            transform: translateY(-2px);
            box-shadow: 0 5px 16px rgba(231,72,46,0.35);
        }
        .btn-sm { padding: 0.38rem 1rem; font-size: 13px; }
        .btn-lg { padding: 0.75rem 2rem; font-size: 1rem; }

        /* ============================================================
           FORM ELEMENTS
        ============================================================ */
        .form-control, .form-select {
            border: 1.5px solid var(--border);
            border-radius: 10px;
            padding: 0.6rem 1rem;
            font-family: 'Source Sans Pro', sans-serif;
            font-size: 14px;
            color: var(--text-dark);
            transition: var(--transition);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(1,91,30,0.1);
        }
        .form-label {
            font-weight: 700;
            font-size: 13px;
            color: var(--text-mid);
            margin-bottom: 6px;
        }

        /* ============================================================
           ALERTS
        ============================================================ */
        .alert {
            border-radius: var(--radius);
            border: none;
            padding: 14px 18px;
            font-weight: 600;
            font-size: 14px;
        }
        .alert-success { background: var(--primary-light); color: var(--primary); }
        .alert-danger  { background: var(--accent-light); color: var(--accent-dark); }
        .alert-info    { background: #dbeafe; color: #1e40af; }
        .alert-warning { background: var(--yellow-light); color: #92400e; }

        /* ============================================================
           TABLES
        ============================================================ */
        .table {
            font-size: 14px;
            border-collapse: separate;
            border-spacing: 0;
        }
        .table thead th {
            background: var(--primary-light);
            color: var(--primary);
            font-weight: 700;
            border-bottom: 2px solid var(--primary-mid);
            padding: 12px 16px;
            font-size: 13px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            vertical-align: middle;
            white-space: nowrap;
        }
        .table tbody tr { transition: background 0.18s; }
        .table tbody tr:hover { background: #fafff8; }
        .table tbody td {
            padding: 12px 16px;
            vertical-align: middle;
            border-bottom: 1px solid #f0f0f0;
            color: var(--text-mid);
        }

        /* ============================================================
           PAGINATION
        ============================================================ */
        .pagination { gap: 4px; }
        .page-link {
            border-radius: 8px !important;
            border: 1.5px solid var(--border) !important;
            color: var(--primary);
            font-weight: 700;
            font-size: 13px;
            padding: 6px 12px;
            transition: var(--transition);
        }
        .page-link:hover { background: var(--primary); color: #fff; border-color: var(--primary) !important; }
        .page-item.active .page-link { background: var(--primary); border-color: var(--primary) !important; }
        .page-item.disabled .page-link { color: var(--text-muted); }

        /* ============================================================
           FLOATING QUICK ACCESS (bottom mobile)
        ============================================================ */
        .quick-nav {
            display: none;
        }

        @media (max-width: 768px) {
            .quick-nav {
                display: flex;
                position: fixed;
                bottom: 0; left: 0; right: 0;
                background: #fff;
                border-top: 1.5px solid var(--border);
                box-shadow: 0 -4px 20px rgba(0,0,0,0.12);
                z-index: 1040;
                justify-content: space-around;
                align-items: center;
                padding: 6px 0 env(safe-area-inset-bottom, 8px);
            }
            .quick-nav-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 3px;
                padding: 6px 12px;
                border-radius: 10px;
                transition: var(--transition);
                position: relative;
                text-decoration: none !important;
                color: var(--text-muted);
                font-size: 11px;
                font-weight: 700;
                min-width: 54px;
            }
            .quick-nav-item i { font-size: 1.3rem; }
            .quick-nav-item.active { color: var(--primary); }
            .quick-nav-item:hover { color: var(--primary); background: var(--primary-light); }
            .quick-nav-item .qn-badge {
                position: absolute;
                top: 4px; right: 8px;
                background: var(--accent);
                color: #fff;
                font-size: 0.55rem;
                min-width: 15px;
                height: 15px;
                border-radius: 100px;
                display: flex; align-items: center; justify-content: center;
                font-weight: 800;
            }
            .main-content { padding-bottom: 80px; }
            footer { margin-bottom: 60px; }
        }

        /* ============================================================
           SCROLL TO TOP BUTTON
        ============================================================ */
        .scroll-top-btn {
            position: fixed;
            bottom: 24px;
            right: 20px;
            width: 44px; height: 44px;
            background: var(--primary);
            color: #fff;
            border-radius: 50%;
            border: none;
            box-shadow: 0 4px 16px rgba(1,91,30,0.35);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
            z-index: 1035;
            opacity: 0;
            transform: translateY(20px);
            transition: var(--transition);
            cursor: pointer;
        }
        .scroll-top-btn.show { opacity: 1; transform: translateY(0); }
        .scroll-top-btn:hover { background: var(--accent); transform: translateY(-3px); }

        @media (max-width: 768px) {
            .scroll-top-btn { bottom: 80px; right: 16px; }
        }

        /* ============================================================
           TOAST NOTIFICATION
        ============================================================ */
        .toast-container {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 1060;
        }
        .ay-toast {
            background: #fff;
            border-radius: var(--radius);
            border-left: 4px solid var(--primary);
            box-shadow: 0 6px 24px rgba(0,0,0,0.14);
            padding: 14px 18px;
            min-width: 280px;
            max-width: 360px;
            animation: slideInToast 0.35s ease forwards;
            font-size: 14px;
            font-weight: 600;
        }
        .ay-toast.toast-error { border-left-color: var(--accent); }
        .ay-toast.toast-warning { border-left-color: var(--yellow); }
        @keyframes slideInToast {
            from { opacity: 0; transform: translateX(30px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        /* ============================================================
           FOOTER
        ============================================================ */
        footer {
            background: var(--primary);
            color: rgba(255,255,255,0.82);
            padding: 44px 0 0;
            margin-top: 50px;
        }
        footer h5, footer h6 {
            color: #fff;
            font-weight: 800;
            font-size: 0.92rem;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            margin-bottom: 16px;
        }
        footer p, footer li {
            color: rgba(255,255,255,0.75);
            font-size: 0.88rem;
            line-height: 2;
        }
        footer a { color: rgba(255,255,255,0.75); }
        footer a:hover { color: #fff; }
        footer ul { list-style: none; padding: 0; }
        footer li { transition: var(--transition); }
        footer li:hover { padding-left: 4px; }
        footer li::before { content: "›"; margin-right: 6px; font-weight: 700; }

        .footer-social a {
            display: inline-flex;
            align-items: center; justify-content: center;
            width: 36px; height: 36px;
            background: rgba(255,255,255,0.13);
            border-radius: 50%;
            color: #fff !important;
            margin-right: 8px;
            font-size: 1rem;
            transition: var(--transition);
        }
        .footer-social a:hover { background: var(--accent); transform: translateY(-3px) scale(1.1); }

        .footer-bottom {
            background: rgba(0,0,0,0.18);
            padding: 13px 0;
            text-align: center;
            font-size: 0.82rem;
            color: rgba(255,255,255,0.6);
            margin-top: 32px;
        }

        /* ============================================================
           UTILITIES
        ============================================================ */
        .section-title {
            font-family: 'Barlow Condensed', sans-serif;
            font-weight: 800;
            font-size: 1.45rem;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .divider-primary {
            border: none;
            height: 2px;
            background: linear-gradient(to right, var(--primary), transparent);
            margin: 12px 0 20px;
        }

        .text-primary-ay { color: var(--primary) !important; }
        .text-accent    { color: var(--accent) !important; }
        .bg-primary-light { background: var(--primary-light) !important; }

        /* Loading overlay */
        .loading-overlay {
            position: fixed; inset: 0;
            background: rgba(255,255,255,0.72);
            backdrop-filter: blur(4px);
            display: flex; align-items: center; justify-content: center;
            z-index: 9999;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s;
        }
        .loading-overlay.show { opacity: 1; pointer-events: all; }
        .spinner-ring {
            width: 50px; height: 50px;
            border: 4px solid var(--primary-mid);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.75s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f0f0f0; }
        ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--accent); }

        /* Smooth focus ring */
        :focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 3px;
        }

        /* ============================================================
           RESPONSIVE
        ============================================================ */
        @media (max-width: 992px) {
            .navbar .nav-link { padding: 0.45rem 0.7rem !important; font-size: 13px; }
        }
        @media (max-width: 768px) {
            .navbar-brand { font-size: 1.3rem; }
            .navbar-brand img { height: 38px; }
            .page-hero { padding: 18px 0; }
            .page-hero h1 { font-size: 1.5rem; }
            .ay-card-body { padding: 16px; }
        }
        @media (max-width: 576px) {
            .btn { font-size: 13px; padding: 0.5rem 1.1rem; }
        }
    </style>
</head>
<body>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="text-center">
        <div class="spinner-ring mb-3"></div>
        <p style="color:var(--primary);font-weight:700;font-size:14px;">Memuat...</p>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<!-- ===================== TOP INFO BAR ===================== -->
<div class="top-bar d-none d-md-block">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <span class="top-bar-item"><i class="bi bi-telephone-fill"></i> +62 85 955 202 267</span>
            <span class="top-bar-item"><i class="bi bi-envelope-fill"></i> tigaayumart@gmail.com</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="top-bar-item" style="font-size:12px;"><i class="bi bi-clock"></i> Buka 08.00–22.00</span>
            <span style="color:rgba(255,255,255,0.3)">|</span>
            <a href="https://www.facebook.com/3aayumart" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
            <a href="https://www.instagram.com/3aayumart12/" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
            <a href="https://wa.me/6285955202267" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
        </div>
    </div>
</div>

<!-- ===================== NAVBAR ===================== -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('images/logo.png') }}" alt="AyuMart Logo">
            <div>
                <span class="brand-name">Ayu Mart</span>
                <span class="brand-tagline">Always Fresh, Be Healthy</span>
            </div>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMain">
            <!-- Search -->
            <div class="nav-search-wrap mx-lg-3 my-2 my-lg-0">
                <div class="input-group">
                    <input type="text" class="form-control" id="searchInput" placeholder="Cari produk…" aria-label="Cari produk">
                    <button class="btn" type="button" onclick="doSearch()">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>

            <!-- Right icons + user -->
            <ul class="navbar-nav align-items-center gap-1 ms-auto">
                <!-- Nav links (desktop) -->
                <li class="nav-item d-none d-xl-block">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="bi bi-house-door me-1"></i> Beranda
                    </a>
                </li>
                <li class="nav-item d-none d-xl-block">
                    <a class="nav-link {{ request()->routeIs('pelanggan.orders') ? 'active' : '' }}" href="{{ route('pelanggan.orders') }}">
                        <i class="bi bi-bag-check me-1"></i> Pesanan
                    </a>
                </li>

                <!-- Notification Bell -->
                <!-- <li class="nav-item dropdown">
                    <a class="nav-icon-btn" href="#" id="notifBell" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false" title="Notifikasi" aria-label="Notifikasi">
                        <i class="bi bi-bell" id="bellIcon"></i>
                        <span class="badge" id="notif-count" style="display:none;">0</span>
                    </a>
                    <div class="dropdown-menu notif-dropdown dropdown-menu-end" aria-labelledby="notifBell">
                        <div class="notif-header">
                            <h6><i class="bi bi-bell-fill me-2"></i>Notifikasi</h6>
                            <button class="notif-mark-all" onclick="markAllNotifsRead()" type="button">Tandai semua dibaca</button>
                        </div>
                        <div class="notif-list" id="notifList">
                            <div class="notif-empty">
                                <i class="bi bi-bell-slash"></i>
                                <div>Memuat notifikasi...</div>
                            </div>
                        </div>
                        <div class="notif-footer">
                            <a href="{{ route('pelanggan.notifications') }}"><i class="bi bi-list-ul me-1"></i>Lihat Semua Notifikasi</a>
                        </div>
                    </div>
                </li> -->

                <!-- Ticket -->
                <li class="nav-item">
                    <a class="nav-icon-btn" href="{{ route('pelanggan.tickets.index') }}" title="Bantuan" aria-label="Bantuan">
                        <i class="bi bi-headset"></i>
                        <span class="badge" id="ticket-count" style="display:none;">0</span>
                    </a>
                </li>

                <!-- Wishlist -->
                <li class="nav-item">
                    <a class="nav-icon-btn" href="{{ route('pelanggan.wishlist') }}" title="Wishlist" aria-label="Wishlist">
                        <i class="bi bi-heart"></i>
                        <span class="badge" id="wishlist-count" style="display:none;">0</span>
                    </a>
                </li>

                <!-- Cart -->
                <li class="nav-item">
                    <a class="nav-icon-btn" href="{{ route('pelanggan.cart') }}" title="Keranjang" aria-label="Keranjang">
                        <i class="bi bi-cart3"></i>
                        <span class="badge" id="cart-count" style="display:none;">0</span>
                    </a>
                </li>

                <!-- User dropdown -->
                <li class="nav-item dropdown ms-2">
                    <a class="user-pill dropdown-toggle" href="#" id="userDrop" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menu pengguna">
                        <span class="user-av">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        <span class="d-none d-lg-inline">{{ Str::limit(auth()->user()->name, 14) }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDrop">
                        <li class="px-3 py-2" style="font-size:12px;color:var(--text-muted);">
                            Masuk sebagai
                            <div style="font-weight:700;color:var(--text-dark);font-size:14px;">{{ auth()->user()->name }}</div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('pelanggan.profile') }}"><i class="bi bi-person-circle"></i> Profil Saya</a></li>
                        <li><a class="dropdown-item" href="{{ route('pelanggan.orders') }}"><i class="bi bi-bag-check"></i> Pesanan Saya</a></li>
                        <li><a class="dropdown-item" href="{{ route('pelanggan.notifications') }}"><i class="bi bi-bell"></i> Notifikasi <span id="dropdown-notif-badge" class="badge ms-1" style="background:var(--accent);display:none;">0</span></a></li>
                        <li><a class="dropdown-item" href="{{ route('pelanggan.reviews.index') }}"><i class="bi bi-star-fill" style="color:#f59e0b;"></i> Review Saya</a></li>
                        <li><a class="dropdown-item" href="{{ route('membership') }}"><i class="bi bi-award"></i> Membership</a></li>
                        <li><a class="dropdown-item" href="{{ route('pelanggan.wishlist') }}"><i class="bi bi-heart"></i> Wishlist</a></li>
                        <li><a class="dropdown-item" href="{{ route('pelanggan.tickets.index') }}"><i class="bi bi-headset"></i> Pusat Bantuan</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item logout-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right"></i> Keluar
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- ===================== MAIN CONTENT ===================== -->
<div class="main-content">
    @yield('content')
</div>

<!-- ===================== FOOTER ===================== -->
<footer>
    <div class="container">
        <div class="row g-4">
            <!-- Brand -->
            <div class="col-md-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="{{ asset('images/logo.png') }}" alt="AyuMart" height="54"
                         style="background:white;border-radius:50%;padding:3px;box-shadow:0 2px 8px rgba(0,0,0,0.15);">
                    <div>
                        <div style="font-family:'Barlow Condensed',sans-serif;font-weight:800;font-size:1.4rem;color:#fff;">AYU MART</div>
                        <div style="font-size:12px;color:rgba(255,255,255,0.65);font-style:italic;">Always Fresh, Be Healthy</div>
                    </div>
                </div>
                <p>Supermarket online terpercaya pilihan keluarga Indonesia. Produk segar, harga terbaik, pengiriman cepat.</p>
                <div class="footer-social mt-3">
                    <a href="https://www.facebook.com/3aayumart" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="https://www.instagram.com/3aayumart12/" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="https://wa.me/6285955202267" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                    <a href="https://www.tiktok.com" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
                </div>
            </div>

            <!-- Layanan -->
            <div class="col-6 col-md-2">
                <h6>Layanan</h6>
                <ul>
                    <li><a href="{{ route('home') }}">Belanja</a></li>
                    <li><a href="{{ route('pelanggan.orders') }}">Pesanan Saya</a></li>
                    <li><a href="{{ route('pelanggan.cart') }}">Keranjang</a></li>
                    <li><a href="{{ route('pelanggan.wishlist') }}">Wishlist</a></li>
                    <li><a href="{{ route('membership') }}">Membership</a></li>
                </ul>
            </div>

            <!-- Bantuan -->
            <div class="col-6 col-md-2">
                <h6>Bantuan</h6>
                <ul>
                    <li><a href="{{ route('pelanggan.tickets.index') }}">Pusat Bantuan</a></li>
                    <li><a href="{{ route('pelanggan.profile') }}">Profil Saya</a></li>
                    <li><a href="{{ route('pelanggan.reviews.index') }}">Review Saya</a></li>
                </ul>
            </div>

            <!-- Kontak -->
            <div class="col-md-4">
                <h6>Hubungi Kami</h6>
                <ul>
                    <li style="list-style:none;"><li class="d-flex align-items-start gap-2 mb-1" style="list-style:none;">
                        <i class="bi bi-telephone-fill mt-1" style="color:rgba(255,255,255,0.6);"></i>
                        <span>+62 85 955 202 267</span>
                    </li></li>
                    <li class="d-flex align-items-start gap-2 mb-1" style="list-style:none;">
                        <i class="bi bi-envelope-fill mt-1" style="color:rgba(255,255,255,0.6);"></i>
                        <span>tigaayumart@gmail.com</span>
                    </li>
                    <li class="d-flex align-items-start gap-2 mb-1" style="list-style:none;">
                        <i class="bi bi-geo-alt-fill mt-1" style="color:rgba(255,255,255,0.6);"></i>
                        <span>Bali, Indonesia</span>
                    </li>
                </ul>
                <div style="background:rgba(255,255,255,0.1);border-radius:10px;padding:12px;margin-top:12px;">
                    <div style="font-size:12px;color:rgba(255,255,255,0.7);font-weight:700;margin-bottom:4px;">Jam Operasional</div>
                    <div style="font-size:13px;">Senin–Sabtu: 08.00 – 22.00</div>
                    <div style="font-size:13px;">Minggu: 09.00 – 21.00</div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            &copy; {{ date('Y') }} <strong style="color:rgba(255,255,255,0.85);">AyuMart Supermarket</strong>. Hak Cipta Dilindungi.
        </div>
    </div>
</footer>

<!-- ===================== MOBILE BOTTOM NAV ===================== -->
<nav class="quick-nav" aria-label="Navigasi cepat">
    <a class="quick-nav-item {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}" aria-label="Beranda">
        <i class="bi {{ request()->routeIs('home') ? 'bi-house-fill' : 'bi-house' }}"></i>
        Beranda
    </a>
    <a class="quick-nav-item {{ request()->routeIs('pelanggan.orders') ? 'active' : '' }}" href="{{ route('pelanggan.orders') }}" aria-label="Pesanan">
        <i class="bi {{ request()->routeIs('pelanggan.orders') ? 'bi-bag-check-fill' : 'bi-bag-check' }}"></i>
        Pesanan
    </a>
    <a class="quick-nav-item {{ request()->routeIs('pelanggan.cart') ? 'active' : '' }}" href="{{ route('pelanggan.cart') }}" aria-label="Keranjang">
        <i class="bi {{ request()->routeIs('pelanggan.cart') ? 'bi-cart-fill' : 'bi-cart3' }}"></i>
        <span class="qn-badge" id="qn-cart" style="display:none;">0</span>
        Keranjang
    </a>
    <a class="quick-nav-item {{ request()->routeIs('pelanggan.wishlist') ? 'active' : '' }}" href="{{ route('pelanggan.wishlist') }}" aria-label="Wishlist">
        <i class="bi {{ request()->routeIs('pelanggan.wishlist') ? 'bi-heart-fill' : 'bi-heart' }}"></i>
        Wishlist
    </a>
    <a class="quick-nav-item {{ request()->routeIs('pelanggan.notifications') ? 'active' : '' }}" href="{{ route('pelanggan.notifications') }}" aria-label="Notifikasi" style="position:relative;">
        <i class="bi {{ request()->routeIs('pelanggan.notifications') ? 'bi-bell-fill' : 'bi-bell' }}"></i>
        <span class="qn-badge" id="qn-notif" style="display:none;">0</span>
        Notif
    </a>
    <a class="quick-nav-item {{ request()->routeIs('pelanggan.profile') ? 'active' : '' }}" href="{{ route('pelanggan.profile') }}" aria-label="Akun">
        <i class="bi {{ request()->routeIs('pelanggan.profile') ? 'bi-person-fill' : 'bi-person' }}"></i>
        Akun
    </a>
</nav>

<!-- Scroll to Top -->
<button class="scroll-top-btn" id="scrollTopBtn" aria-label="Kembali ke atas">
    <i class="bi bi-arrow-up"></i>
</button>

<!-- Logout Form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>

<!-- ===================== SCRIPTS ===================== -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    /* ---- SWEETALERT NOTIFICATIONS ---- */
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ addslashes(session('success')) }}',
            confirmButtonColor: '#015b1e',
            timer: 3500,
            timerProgressBar: true,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    @endif
    @if (session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '{{ addslashes(session('error')) }}',
            confirmButtonColor: '#015b1e'
        });
    @endif

    /* ---- SEARCH ---- */
    function doSearch() {
        const val = document.getElementById('searchInput')?.value?.trim();
        if (val) window.location.href = '{{ route("home") }}?search=' + encodeURIComponent(val);
        else window.location.href = '{{ route("home") }}';
    }
    document.getElementById('searchInput')?.addEventListener('keydown', e => {
        if (e.key === 'Enter') doSearch();
    });

    /* ---- SCROLL TO TOP ---- */
    const scrollBtn = document.getElementById('scrollTopBtn');
    window.addEventListener('scroll', () => {
        scrollBtn?.classList.toggle('show', window.scrollY > 400);
    });
    scrollBtn?.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

    /* ---- LOADING OVERLAY ---- */
    function showLoading() { document.getElementById('loadingOverlay')?.classList.add('show'); }
    function hideLoading() { document.getElementById('loadingOverlay')?.classList.remove('show'); }
    // Hide after page loads
    window.addEventListener('load', hideLoading);

    /* ---- BADGE COUNTS ---- */
    function loadCounts() {
        const h = { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' };
        [
            { url: '/api/cart/count',     id: 'cart-count',     qn: 'qn-cart' },
            { url: '/api/wishlist/count', id: 'wishlist-count', qn: null      },
            { url: '/api/tickets/count',  id: 'ticket-count',   qn: null      }
        ].forEach(({ url, id, qn }) => {
            fetch(url, { headers: h })
                .then(r => r.json())
                .then(d => {
                    const n = d.count || 0;
                    const b = document.getElementById(id);
                    if (b) { b.textContent = n; b.style.display = n > 0 ? 'inline-flex' : 'none'; }
                    if (qn) {
                        const q = document.getElementById(qn);
                        if (q) { q.textContent = n; q.style.display = n > 0 ? 'flex' : 'none'; }
                    }
                }).catch(() => {});
        });
    }

    /* ---- NOTIFICATION SYSTEM ---- */
    const NOTIF_COLORS = {
        shipping: 'shipping', payment: 'payment', expired: 'expired',
        cancellation: 'cancellation', ticket: 'ticket', order: 'order'
    };

    function loadNotifications() {
        const h = {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'Accept': 'application/json'
        };
        fetch('/api/notifications', { headers: h })
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                const list    = document.getElementById('notifList');
                const badge   = document.getElementById('notif-count');
                const qnBadge = document.getElementById('qn-notif');
                const ddBadge = document.getElementById('dropdown-notif-badge');
                const total   = data.total || 0;

                // Update badges
                [badge, qnBadge, ddBadge].forEach(el => {
                    if (!el) return;
                    el.textContent = total;
                    el.style.display = total > 0 ? (el === badge || el === ddBadge ? 'inline-flex' : 'flex') : 'none';
                });

                // Animate bell if there are notifications
                const bellIcon = document.getElementById('bellIcon');
                if (bellIcon && total > 0) {
                    bellIcon.classList.remove('bell-animate');
                    void bellIcon.offsetWidth; // force reflow
                    bellIcon.classList.add('bell-animate');
                }

                // Render dropdown list (max 5)
                if (!list) return;
                if (!data.notifications || data.notifications.length === 0) {
                    list.innerHTML = `<div class="notif-empty"><i class="bi bi-bell-slash"></i><div>Tidak ada notifikasi</div></div>`;
                    return;
                }

                list.innerHTML = data.notifications.slice(0, 5).map(n => `
                    <a href="${n.url}" class="notif-item unread" onclick="markNotifRead('${n.id}')">
                        <div class="notif-icon ${NOTIF_COLORS[n.type] || 'order'}">
                            <i class="bi ${n.icon || 'bi-bell'}"></i>
                        </div>
                        <div class="notif-body">
                            <div class="notif-title">${n.title}</div>
                            <div class="notif-msg">${n.message}</div>
                            <div class="notif-time"><i class="bi bi-clock me-1"></i>${n.time}</div>
                        </div>
                    </a>
                `).join('');
            })
            .catch(() => {
                const list = document.getElementById('notifList');
                if (list) list.innerHTML = `<div class="notif-empty"><i class="bi bi-wifi-off"></i><div>Gagal memuat notifikasi</div></div>`;
            });
    }

    function markNotifRead(id) {
        const h = {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'Content-Type': 'application/json'
        };
        fetch(`/api/notifications/${id}/read`, { method: 'POST', headers: h }).catch(() => {});
    }

    function markAllNotifsRead() {
        const h = {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'Content-Type': 'application/json'
        };
        fetch('/api/notifications/read-all', { method: 'POST', headers: h })
            .then(() => loadNotifications())
            .catch(() => {});
    }

    // Load notifications when dropdown is opened
    document.getElementById('notifBell')?.addEventListener('show.bs.dropdown', loadNotifications);

    document.addEventListener('DOMContentLoaded', () => {
        loadCounts();
        loadNotifications();
        // Poll every 60 seconds
        setInterval(loadNotifications, 60000);
    });

    /* ---- GLOBAL ADD TO CART (used across pages) ---- */
    function addToCartGlobal(productId) {
        return fetch(`/cart/add/${productId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({ quantity: 1 })
        }).then(r => r.json()).then(d => { loadCounts(); return d; });
    }

    /* ---- ACTIVE NAV LINK HIGHLIGHT ---- */
    document.querySelectorAll('.navbar .nav-link').forEach(link => {
        if (link.href === window.location.href) link.classList.add('active');
    });
</script>

@stack('scripts')
</body>
</html>
