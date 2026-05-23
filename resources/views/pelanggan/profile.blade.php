@extends('layouts.pelanggan')

@section('title', 'Profil Saya')

@push('styles')
    {{-- Load Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>

    <style>
        :root {
            --primary-color: #015b1e;
            --primary-hover: #013d14;
            --secondary-color: #027826;
            --text-dark: #1a1a1a;
            --text-muted: #777;
            --border-color: #e0e0e0;
            --bg-light: #f5f5f5;
            --success-color: #015b1e;
            --warning-color: #f59e0b;
            --info-color: #007bff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            color: var(--text-dark);
        }

        /* Navbar */
        .navbar-custom {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 1rem 0;
            box-shadow: 0 4px 20px rgba(238, 77, 45, 0.3);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.6rem;
            color: white !important;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .navbar-custom .nav-link {
            color: rgba(255, 255, 255, 0.95) !important;
            font-weight: 600;
            padding: 0.6rem 1.2rem;
            transition: all 0.3s;
            border-radius: 8px;
        }

        .navbar-custom .nav-link:hover {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .navbar-icons {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .nav-icon {
            position: relative;
            color: white;
            font-size: 1.4rem;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .nav-icon:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: scale(1.15);
        }

        .badge-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #fff;
            color: var(--primary-color);
            border-radius: 10px;
            padding: 2px 6px;
            font-size: 0.7rem;
            font-weight: 700;
            min-width: 18px;
            text-align: center;
        }

        .user-menu {
            position: relative;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            border: 2px solid white;
            cursor: pointer;
            object-fit: cover;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-radius: 8px;
            margin-top: 0.5rem;
        }

        .dropdown-item {
            padding: 0.7rem 1.2rem;
            transition: all 0.3s;
        }

        .dropdown-item:hover {
            background-color: #fff5f2;
            color: var(--primary-color);
        }

        /* Main Content */
        .profile-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }

        .page-header {
            background: linear-gradient(135deg, #f0fdf4 0%, #f5f9f2 100%);
            border-radius: 16px;
            padding: 2.5rem 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(1, 91, 30, 0.1);
            border-left: 5px solid var(--primary-color);
            border-right: 1px solid rgba(1, 91, 30, 0.1);
        }

        .page-header h2 {
            color: var(--primary-color);
            font-size: 2rem;
            font-weight: 800;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .page-header h2 i {
            color: var(--primary-color);
            font-size: 2.5rem;
            opacity: 0.9;
        }

        .page-header p {
            margin: 0.75rem 0 0 3.5rem;
            color: #555;
            font-size: 0.95rem;
            font-weight: 500;
        }

        /* Tab Navigation */
        .profile-tabs {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
            overflow: hidden;
            border: 1px solid rgba(1, 91, 30, 0.08);
        }



        .profile-card {
            background: white;
            border-radius: 12px;
            padding: 0;
            box-shadow: none;
            margin-bottom: 2rem;
            transition: all 0.3s;
            border: none;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0fdf4;
        }

        .card-title i {
            color: var(--primary-color);
            font-size: 1.3rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-label i {
            color: var(--primary-color);
            font-size: 1rem;
        }

        .form-control {
            border: 1.5px solid #ddd;
            border-radius: 8px;
            padding: 0.85rem 1rem;
            transition: all 0.3s;
            font-size: 0.95rem;
            background: #fafafa;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 4px rgba(1, 91, 30, 0.08);
        }

        .form-control:hover:not(:focus) {
            border-color: var(--primary-color);
            background: #fafafa;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 0.9rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(1, 91, 30, 0.2);
            cursor: pointer;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-hover), #016b24);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(1, 91, 30, 0.3);
        }

        .btn-outline-secondary {
            border: 2px solid #ddd;
            color: var(--text-dark);
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            background: white;
            cursor: pointer;
        }

        .btn-outline-secondary:hover {
            background: #f5f5f5;
            border-color: var(--primary-color);
            color: var(--primary-color);
            transform: translateY(-2px);
        }

        .membership-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.25rem;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.9rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }

        .membership-badge.gold {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: white;
        }

        .membership-badge.silver {
            background: linear-gradient(135deg, #e2e8f0, #94a3b8);
            color: white;
        }

        .membership-badge.bronze {
            background: linear-gradient(135deg, #fb923c, #ea580c);
            color: white;
        }

        .membership-badge.platinum {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
        }

        .membership-info-card {
            background: #f8f9fa;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1.25rem;
        }

        .membership-info-item {
            display: flex;
            align-items: center;
            padding: 0.85rem;
            background: white;
            border-radius: 8px;
            margin-bottom: 0.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }

        .membership-info-item:last-child {
            margin-bottom: 0;
        }

        .membership-info-item:hover {
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            transform: translateX(4px);
        }

        .membership-info-item i {
            font-size: 1.4rem;
            color: var(--primary-color);
            margin-right: 1rem;
            min-width: 35px;
            text-align: center;
        }

        .membership-info-item strong {
            color: var(--text-muted);
            display: block;
            font-size: 0.8rem;
            margin-bottom: 0.2rem;
            font-weight: 600;
        }

        .membership-info-item p {
            margin: 0;
            color: var(--text-dark);
            font-weight: 700;
            font-size: 1.05rem;
        }

        /* Member Card Styles */
        .member-card {
            background: linear-gradient(135deg, #015b1e 0%, #027826 100%);
            border-radius: 16px;
            padding: 2rem;
            color: white;
            box-shadow: 0 10px 30px rgba(1, 91, 30, 0.2);
            margin-bottom: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .member-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(-20px, -20px); }
        }

        .member-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .member-card-title {
            font-size: 0.9rem;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            opacity: 0.9;
        }

        .member-id {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 3px;
            margin-bottom: 0.5rem;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .member-name {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0;
            text-align: center;
            opacity: 0.95;
            position: relative;
            z-index: 1;
        }

        .qr-barcode-container {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 1rem;
            position: relative;
            z-index: 1;
        }

        .code-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
            border-bottom: 2px solid var(--border-color);
        }

        .code-tab {
            flex: 1;
            padding: 0.75rem;
            border: none;
            background: transparent;
            color: var(--text-muted);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border-bottom: 3px solid transparent;
        }

        .code-tab.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }

        .code-tab:hover:not(.active) {
            color: var(--text-dark);
        }

        .code-content {
            display: none;
            text-align: center;
            padding: 1rem 0;
        }

        .code-content.active {
            display: block;
        }

        #qrcode, #barcode {
            display: inline-block;
            margin: 0 auto;
        }

        #qrcode {
            line-height: 0;
        }

        #qrcode img {
            display: block;
            margin: 0 auto;
        }

        #barcode {
            max-width: 100%;
            height: auto;
        }

        .code-instructions {
            background: var(--bg-light);
            border-radius: 8px;
            padding: 0.75rem;
            margin-top: 1rem;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .btn-download {
            margin-top: 1rem;
            width: 100%;
            background: var(--primary-color);
            border: none;
            color: white;
            padding: 0.75rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-download:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            color: #166534;
            border-left: 4px solid #15803d;
        }

        .alert-danger {
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            color: #991b1b;
            border-left: 4px solid #dc2626;
        }

        .alert-info {
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            color: #1e40af;
            border-left: 4px solid #0284c7;
        }

        .section-divider {
            border: none;
            height: 2px;
            background: linear-gradient(to right, transparent, var(--primary-color), transparent);
            margin: 2rem 0;
        }

        .input-group-text {
            background: #f5f5f5;
            border: 1.5px solid #ddd;
            border-right: none;
            border-radius: 8px 0 0 8px;
            color: #999;
            font-weight: 600;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 8px 8px 0;
            background: #fafafa;
        }

        /* Password Toggle Styles */
        .password-toggle-wrapper {
            position: relative;
        }

        .password-toggle-wrapper .form-control {
            padding-right: 45px;
        }

        .password-toggle-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 5px 10px;
            transition: all 0.3s;
            z-index: 10;
        }

        .password-toggle-btn:hover {
            color: var(--primary-color);
        }

        .password-toggle-btn i {
            font-size: 1.2rem;
        }

        /* Form Section Styling */
        .form-section {
            background: linear-gradient(to right, #f9fdf7 0%, #ffffff 100%);
            border: 1px solid rgba(1, 91, 30, 0.1);
            border-radius: 12px;
            padding: 1.75rem;
            margin-bottom: 2rem;
        }

        .form-section:last-child {
            margin-bottom: 0;
        }

        .form-section .form-group {
            margin-bottom: 1.5rem;
        }

        .form-section .form-group:last-child {
            margin-bottom: 0;
        }

        /* Address Card Styling */
        .address-card {
            border: 1.5px solid #e0e0e0;
            border-radius: 12px;
            transition: all 0.3s ease;
            background: white;
        }

        .address-card:hover {
            box-shadow: 0 6px 20px rgba(1, 91, 30, 0.1);
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }

        .address-card .card-body {
            padding: 1.5rem;
        }

        .address-card .badge {
            background: linear-gradient(135deg, #15803d, #16a34a);
            font-size: 0.75rem;
            padding: 0.4rem 0.75rem;
        }

        /* Button Group Styling */
        .btn-group-spacing {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            justify-content: flex-end;
        }

        /* Divider */
        .form-divider {
            border-top: 2px solid #f0fdf4;
            margin: 2rem 0;
            position: relative;
        }

        .form-divider-label {
            position: absolute;
            left: 1rem;
            top: -12px;
            background: white;
            padding: 0 0.5rem;
            color: #999;
            font-size: 0.85rem;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .page-header {
                padding: 1.75rem 1.5rem;
            }

            .page-header h2 {
                font-size: 1.5rem;
            }

            .page-header h2 i {
                font-size: 1.75rem;
            }

            .page-header p {
                margin-left: 2.5rem;
                font-size: 0.85rem;
            }



            .form-section {
                padding: 1.25rem;
            }

            .form-label {
                font-size: 0.9rem;
            }

            .btn-group-spacing {
                flex-direction: column;
            }

            .btn-group-spacing .btn {
                width: 100%;
            }
        }

        /* Password Strength Indicator */
        .password-strength-meter {
            height: 6px;
            background: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 0.5rem;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
        }

        .password-strength-bar {
            height: 100%;
            width: 0%;
            border-radius: 10px;
            transition: all 0.3s ease;
            background: linear-gradient(90deg, #dc2626, #ea580c, #f59e0b, #15803d);
            background-size: 400% 100%;
        }

        .password-strength-bar.weak {
            width: 25%;
            background-color: #dc2626;
            box-shadow: 0 0 8px rgba(220, 38, 38, 0.4);
        }

        .password-strength-bar.fair {
            width: 50%;
            background-color: #f59e0b;
            box-shadow: 0 0 8px rgba(245, 158, 11, 0.4);
        }

        .password-strength-bar.good {
            width: 75%;
            background-color: #3b82f6;
            box-shadow: 0 0 8px rgba(59, 130, 246, 0.4);
        }

        .password-strength-bar.strong {
            width: 100%;
            background-color: #15803d;
            box-shadow: 0 0 8px rgba(21, 128, 61, 0.4);
        }

        .password-strength-text {
            font-size: 0.8rem;
            margin-top: 0.3rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .password-strength-text.weak {
            color: #dc2626;
        }

        .password-strength-text.fair {
            color: #f59e0b;
        }

        .password-strength-text.good {
            color: #3b82f6;
        }

        .password-strength-text.strong {
            color: #15803d;
        }

        /* Password Requirements */
        .password-requirements {
            background: #f9fdf7;
            border: 1px solid rgba(1, 91, 30, 0.15);
            border-radius: 12px;
            padding: 1.25rem;
            margin-top: 1rem;
            margin-bottom: 1rem;
        }

        .password-requirements h6 {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .requirement-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .requirement-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0;
            font-size: 0.85rem;
            color: #666;
            transition: all 0.3s;
        }

        .requirement-item.met {
            color: #15803d;
        }

        .requirement-item i {
            min-width: 18px;
            text-align: center;
            font-size: 1rem;
        }

        .requirement-item i.bi-circle {
            color: #ccc;
        }

        .requirement-item.met i.bi-check-circle-fill {
            color: #15803d;
        }

        /* Form Section Headers */
        .section-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid rgba(1, 91, 30, 0.1);
        }

        .section-header h5 {
            margin: 0;
            color: var(--primary-color);
            font-weight: 700;
            font-size: 1.1rem;
        }

        .section-header i {
            font-size: 1.3rem;
            color: var(--primary-color);
        }

        .section-header .badge {
            margin-left: auto;
            background: var(--primary-color);
            color: white;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        /* Personal Info Card */
        .personal-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .info-input-group {
            display: flex;
            flex-direction: column;
        }

        .info-input-group label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.95rem;
        }

        .info-input-group i {
            color: var(--primary-color);
            font-size: 1rem;
        }

        .info-input-group .form-control {
            transition: all 0.3s;
        }

        .info-input-group .form-control:focus {
            box-shadow: 0 0 0 4px rgba(1, 91, 30, 0.1);
            border-color: var(--primary-color);
        }

        /* Address Cards Enhancement */
        .address-card-wrapper {
            transition: all 0.3s ease;
        }

        .address-card-wrapper:hover {
            transform: translateY(-4px);
        }

        .address-card-header-enhanced {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .address-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 700;
            color: var(--primary-color);
            font-size: 1rem;
        }

        .address-badge-enhanced {
            background: linear-gradient(135deg, #15803d, #16a34a);
            color: white;
            padding: 0.35rem 0.85rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .address-info-enhanced {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-bottom: 1.25rem;
        }

        .address-info-row {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            font-size: 0.9rem;
            color: #555;
        }

        .address-info-row i {
            color: var(--primary-color);
            margin-top: 0.2rem;
            min-width: 20px;
        }

        .address-info-row strong {
            color: var(--text-dark);
            font-weight: 600;
        }

        .address-actions-enhanced {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            padding-top: 1rem;
            border-top: 1px solid #f0fdf4;
        }

        .address-btn {
            flex: 1;
            min-width: 120px;
            padding: 0.65rem 0.75rem;
            border-radius: 8px;
            border: 1.5px solid #ddd;
            background: white;
            color: var(--text-dark);
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            text-decoration: none;
        }

        .address-btn:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(1, 91, 30, 0.15);
        }

        .address-btn.primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-color: transparent;
        }

        .address-btn.primary:hover {
            box-shadow: 0 4px 12px rgba(1, 91, 30, 0.25);
        }

        .info-helper-text {
            font-size: 0.8rem;
            color: #888;
            margin-top: 0.35rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .info-helper-text i {
            color: #aaa;
            font-size: 0.9rem;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 2.5rem 1.5rem;
            background: linear-gradient(135deg, #f9fdf7 0%, #f0fdf4 100%);
            border-radius: 12px;
            border: 2px dashed rgba(1, 91, 30, 0.2);
        }

        .empty-state i {
            font-size: 3rem;
            color: var(--primary-color);
            opacity: 0.7;
            margin-bottom: 1rem;
            display: block;
        }

        .empty-state p {
            color: #666;
            font-size: 0.95rem;
            margin: 0.5rem 0;
        }

        .empty-state .btn {
            margin-top: 1rem;
        }

        @media (max-width: 768px) {
            .personal-info-grid {
                grid-template-columns: 1fr;
            }

            .address-actions-enhanced {
                flex-direction: column;
            }

            .address-btn {
                min-width: unset;
            }
        }

        @media (max-width: 576px) {
            .profile-container {
                padding: 0.75rem;
            }

            .page-header {
                padding: 1.25rem 1rem;
                margin-bottom: 1.5rem;
            }

            .page-header h2 {
                font-size: 1.25rem;
                gap: 0.5rem;
            }

            .page-header h2 i {
                font-size: 1.5rem;
            }

            .page-header p {
                margin-left: 2rem;
                font-size: 0.8rem;
            }

            .form-section {
                padding: 1rem;
                margin-bottom: 1.25rem;
            }

            .card-title {
                font-size: 1rem;
                margin-bottom: 1rem;
            }

            .row > .col-md-6 {
                margin-bottom: 1rem;
            }

            .alert {
                padding: 1rem;
                font-size: 0.9rem;
            }

            .section-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .section-header .badge {
                margin-left: 0;
                margin-top: 0.5rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="profile-container">
        <!-- Header -->
        <div class="page-header">
            <h2>
                <i class="bi bi-person-circle"></i>
                <span>Profil Saya</span>
            </h2>
            <p>Kelola informasi profil dan membership Anda</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Main Content Section -->
                <!-- Personal Info Section -->
                <div class="form-section">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <h5 style="color: var(--primary-color); font-weight: 700; margin-bottom: 1.5rem; font-size: 1.1rem;">
                                <i class="bi bi-person-check me-2"></i>Informasi Pribadi
                            </h5>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="bi bi-person"></i> Nama Lengkap
                                </label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', auth()->user()->name) }}"
                                       placeholder="Masukkan nama lengkap"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="bi bi-envelope"></i> Email
                                </label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', auth()->user()->email) }}"
                                       placeholder="email@example.com"
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="bi bi-telephone"></i> No. Telepon
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">+62</span>
                                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                           value="{{ old('phone', auth()->user()->phone) }}"
                                           placeholder="812xxxxxxxx">
                                </div>
                                @error('phone')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="btn-group-spacing">
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Security Section / Keamanan -->
                <div class="form-section">
                    <form action="{{ route('profile.update.password') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <h5 style="color: var(--primary-color); font-weight: 700; margin-bottom: 1.5rem; font-size: 1.1rem;">
                                <i class="bi bi-lock-fill me-2"></i>Ubah Password
                            </h5>
                            <div class="alert alert-info" style="margin-bottom: 1.5rem;">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Perhatian:</strong> Isi semua field untuk mengubah password Anda. Password minimal 8 karakter dan harus kombinasi huruf, angka, dan simbol.
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="bi bi-key"></i> Password Lama <span class="text-danger">*</span>
                                </label>
                                <div class="password-toggle-wrapper">
                                    <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror"
                                           placeholder="Masukkan password lama Anda"
                                           required>
                                    <button type="button" class="password-toggle-btn" onclick="togglePassword('current_password')">
                                        <i class="bi bi-eye" id="current_password-icon"></i>
                                    </button>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="bi bi-shield-lock"></i> Password Baru <span class="text-danger">*</span>
                                </label>
                                <div class="password-toggle-wrapper">
                                    <input type="password" name="password" id="new_password" class="form-control @error('password') is-invalid @enderror"
                                           placeholder="Masukkan password baru (min 8 karakter)"
                                           required>
                                    <button type="button" class="password-toggle-btn" onclick="togglePassword('new_password')">
                                        <i class="bi bi-eye" id="new_password-icon"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="bi bi-shield-check"></i> Konfirmasi Password <span class="text-danger">*</span>
                                </label>
                                <div class="password-toggle-wrapper">
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                                           placeholder="Ulangi password baru"
                                           required>
                                    <button type="button" class="password-toggle-btn" onclick="togglePassword('password_confirmation')">
                                        <i class="bi bi-eye" id="password_confirmation-icon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="btn-group-spacing">
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Update Password
                            </button>
                        </div>
                    </form>
                </div>
                <!-- Saved Addresses Section -->
                <div class="form-section">
                    <div class="mb-3">
                        <h5 style="color: var(--primary-color); font-weight: 700; margin-bottom: 1.5rem; font-size: 1.1rem; display: flex; justify-content: space-between; align-items: center;">
                            <span><i class="bi bi-pin-map me-2"></i>Daftar Alamat Tersimpan</span>
                            <a href="{{ route('pelanggan.addresses.create') }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-plus-lg me-1"></i> Tambah
                            </a>
                        </h5>
                    </div>

                    @if($addresses && count($addresses) > 0)
                        <div class="row g-3">
                            @foreach($addresses as $address)
                                <div class="col-md-6">
                                    <div class="address-card" style="border-left: 4px solid {{ $address->is_default ? 'var(--primary-color)' : '#e0e0e0' }}">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <h6 class="card-title mb-0" style="color: var(--primary-color); font-size: 1rem;">
                                                    <i class="bi bi-tag me-1"></i>{{ $address->label }}
                                                </h6>
                                                @if($address->is_default)
                                                    <span class="badge">
                                                        <i class="bi bi-check-circle me-1"></i>Utama
                                                    </span>
                                                @endif
                                            </div>

                                            <p class="mb-2" style="font-size: 0.9rem; color: #555;">
                                                <i class="bi bi-person me-2" style="color: var(--primary-color);"></i>
                                                <strong>{{ $address->nama_penerima }}</strong>
                                                <br>
                                                <i class="bi bi-telephone me-2" style="color: var(--primary-color); margin-left: 1.2rem;"></i>
                                                {{ $address->no_telp_penerima }}
                                            </p>

                                            <p class="mb-2" style="font-size: 0.9rem; color: #555;">
                                                <i class="bi bi-geo-alt me-2" style="color: var(--primary-color);"></i>
                                                {{ $address->alamat_lengkap }}
                                            </p>

                                            <p class="mb-3" style="font-size: 0.9rem; color: #555;">
                                                <i class="bi bi-building me-2" style="color: var(--primary-color);"></i>
                                                {{ $address->kecamatan }}, {{ $address->kota }}
                                                <br>
                                                <i class="bi bi-mailbox me-2" style="color: var(--primary-color); margin-left: 1.2rem;"></i>
                                                Kode Pos: {{ $address->kode_pos }}
                                            </p>

                                            <div class="d-flex gap-2 flex-wrap">
                                                @if(!$address->is_default)
                                                    <a href="{{ route('pelanggan.addresses.set-default', $address->id) }}" class="btn btn-sm btn-outline-primary" style="border-color: var(--primary-color); color: var(--primary-color);">
                                                        <i class="bi bi-star me-1"></i>Jadikan Utama
                                                    </a>
                                                @endif
                                                <a href="{{ route('pelanggan.addresses.edit', $address->id) }}" class="btn btn-sm btn-outline-warning">
                                                    <i class="bi bi-pencil me-1"></i>Edit
                                                </a>
                                                <form action="{{ route('pelanggan.addresses.destroy', $address->id) }}" method="POST" style="display: inline;"
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus alamat ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash me-1"></i>Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info" style="border-radius: 12px; text-align: center; padding: 2rem;">
                            <i class="bi bi-info-circle me-2" style="font-size: 1.5rem;"></i>
                            <strong>Belum ada alamat tersimpan</strong>
                            <p style="margin-top: 0.75rem; margin-bottom: 1rem; color: inherit;">
                                Tambahkan alamat pengiriman untuk mempermudah checkout di masa depan
                            </p>
                            <a href="{{ route('pelanggan.addresses.create') }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-plus-lg me-1"></i>Tambah Alamat Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
@endsection

@push('scripts')

<script>
    // Load cart and wishlist counts
    function loadCounts() {
        fetch('/api/cart/count')
            .then(response => response.json())
            .then(data => {
                const cartBadge = document.getElementById('cart-count');
                if (cartBadge) {
                    cartBadge.textContent = data.count || 0;
                }
            })
            .catch(error => console.error('Error loading cart count:', error));

        fetch('/api/wishlist/count')
            .then(response => response.json())
            .then(data => {
                const wishlistBadge = document.getElementById('wishlist-count');
                if (wishlistBadge) {
                    wishlistBadge.textContent = data.count || 0;
                }
            })
            .catch(error => console.error('Error loading wishlist count:', error));
    }

    // Toggle Password Visibility
    function togglePassword(fieldId) {
        const passwordField = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '-icon');

        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }

    // Load counts on page load
    loadCounts();
</script>

{{-- Include Address Modals --}}
@include('pelanggan.partials.edit-address-modal')

{{-- Load Leaflet.js for Interactive Maps --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>

{{-- Load Custom Map Picker --}}
<script src="{{ asset('js/map-picker.js') }}"></script>

{{-- Load Checkout Address Management JS (contains editAddress logic) --}}
<script src="{{ asset('js/checkout-address.js') }}"></script>

@endpush
