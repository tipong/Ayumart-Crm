@extends('layouts.pelanggan')

@section('title', 'Profil Saya')

@push('styles')
    {{-- Load Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
     
    <style>
        :root {
            --primary-color: #3F4F44;
            --primary-hover: #2E3A31;
            --secondary-color: #556B58;
            --text-dark: #333;
            --text-muted: #666;
            --border-color: #e5e5e5;
            --bg-light: #f8f9fa;
            --success-color: #3F4F44;
            --warning-color: #f59e0b;
            --info-color: #3b82f6;
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
            background: white;
            border-radius: 12px;
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-left: 4px solid var(--primary-color);
        }

        .page-header h2 {
            color: var(--text-dark);
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .page-header h2 i {
            color: var(--primary-color);
            font-size: 2rem;
        }

        .page-header p {
            margin: 0.5rem 0 0 0;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        /* Tab Navigation */
        .profile-tabs {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
            overflow: hidden;
            border: 1px solid rgba(63, 79, 68, 0.1);
        }

        .nav-tabs {
            border: none;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
            background: linear-gradient(to bottom, #fafafa, #ffffff);
        }

        .nav-tabs .nav-item {
            flex: 1;
            min-width: 150px;
        }

        .nav-tabs .nav-link {
            border: none;
            border-bottom: 4px solid transparent;
            border-right: 1px solid rgba(0,0,0,0.05);
            background: transparent;
            color: var(--text-muted);
            padding: 1.5rem 1.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .nav-tabs .nav-item:last-child .nav-link {
            border-right: none;
        }

        .nav-tabs .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            opacity: 0;
            transition: opacity 0.4s ease;
            z-index: 0;
        }

        .nav-tabs .nav-link:hover {
            color: var(--primary-color);
            background: linear-gradient(to bottom, rgba(63, 79, 68, 0.05), rgba(63, 79, 68, 0.02));
            transform: translateY(-2px);
        }

        .nav-tabs .nav-link.active {
            color: white;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-bottom-color: transparent;
            box-shadow: 0 4px 12px rgba(63, 79, 68, 0.3);
            transform: translateY(-2px);
        }

        .nav-tabs .nav-link.active::before {
            opacity: 1;
        }

        .nav-tabs .nav-link i,
        .nav-tabs .nav-link span {
            position: relative;
            z-index: 1;
        }

        .nav-tabs .nav-link i {
            font-size: 1.3rem;
            transition: transform 0.3s ease;
        }

        .nav-tabs .nav-link:hover i {
            transform: scale(1.15);
        }

        .nav-tabs .nav-link.active i {
            transform: scale(1.1);
        }

        /* Tab Content */
        .tab-content {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
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

        .profile-card:hover {
            box-shadow: none;
            transform: none;
        }

        .card-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--bg-light);
        }

        .card-title i {
            color: var(--primary-color);
            font-size: 1.5rem;
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
            font-size: 1.1rem;
        }

        .form-control {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: all 0.3s;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(238, 77, 45, 0.1);
            transform: none;
        }

        .form-control:hover:not(:focus) {
            border-color: #ccc;
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
            padding: 0.85rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(238, 77, 45, 0.3);
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(238, 77, 45, 0.4);
        }

        .btn-outline-secondary {
            border: 1px solid var(--border-color);
            color: var(--text-dark);
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            background: white;
        }

        .btn-outline-secondary:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            padding: 2rem;
            color: white;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
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
            padding: 1rem 1.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .alert-success {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #065f46;
        }

        .alert-danger {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #991b1b;
        }

        .section-divider {
            border: none;
            height: 2px;
            background: linear-gradient(to right, transparent, var(--primary-color), transparent);
            margin: 2rem 0;
        }

        .input-group-text {
            background: var(--bg-light);
            border: 2px solid var(--border-color);
            border-right: none;
            border-radius: 10px 0 0 10px;
            color: var(--text-muted);
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
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

        /* Responsive */
        @media (max-width: 768px) {
            .navbar-icons {
                gap: 1rem;
            }

            .nav-icon {
                font-size: 1.1rem;
            }

            .page-header h2 {
                font-size: 1.5rem;
            }

            .nav-tabs {
                flex-direction: column;
            }

            .nav-tabs .nav-item {
                width: 100%;
            }

            .nav-tabs .nav-link {
                font-size: 0.9rem;
                padding: 1.25rem 1.5rem;
                justify-content: flex-start;
                border-right: none !important;
                border-bottom: 1px solid rgba(0,0,0,0.05);
            }

            .nav-tabs .nav-link::after {
                content: '';
                position: absolute;
                right: 1.5rem;
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background: white;
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .nav-tabs .nav-link.active::after {
                opacity: 1;
            }

            .nav-tabs .nav-link span {
                display: inline;
            }

            .nav-tabs .nav-link i {
                font-size: 1.3rem;
            }

            .tab-content {
                padding: 1.5rem;
            }

            .member-card {
                padding: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .profile-container {
                padding: 1rem;
            }

            .page-header {
                padding: 1rem;
            }

            .nav-tabs .nav-item {
                min-width: auto;
            }

            .nav-tabs .nav-link {
                padding: 1rem 1.25rem;
                font-size: 0.85rem;
            }

            .nav-tabs .nav-link i {
                font-size: 1.2rem;
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

        <!-- Tab Navigation -->
        <div class="profile-tabs">
            <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active bg-danger" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">
                        <i class="bi bi-person-vcard"></i>
                        <span>Informasi Pribadi</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link bg-danger" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab">
                        <i class="bi bi-shield-lock"></i>
                        <span>Keamanan</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link bg-danger" id="membership-tab" data-bs-toggle="tab" data-bs-target="#membership" type="button" role="tab">
                        <i class="bi bi-award"></i>
                        <span>Membership</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link bg-danger" id="member-card-tab" data-bs-toggle="tab" data-bs-target="#member-card" type="button" role="tab">
                        <i class="bi bi-credit-card-2-front"></i>
                        <span>Kartu Member</span>
                    </button>
                </li>
            </ul>
        </div>

        <!-- Tab Content -->
        <div class="tab-content" id="profileTabContent">
        <!-- Tab Content -->
        <div class="tab-content" id="profileTabContent">
            <!-- Informasi Pribadi Tab -->
            <div class="tab-pane fade show active" id="info" role="tabpanel">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

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
                                <i class="bi bi-phone"></i> No. Telepon
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

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>

                <!-- Saved Addresses Section -->
                <div class="mt-5 pt-4 border-top">
                    <h5 class="mb-4">
                        <i class="bi bi-pin-map"></i> Daftar Alamat Tersimpan
                        <a href="{{ route('pelanggan.addresses.create') }}" class="btn btn-sm btn-success float-end">
                            <i class="bi bi-plus-lg"></i> Tambah Alamat
                        </a>
                    </h5>

                    @if($addresses && count($addresses) > 0)
                        <div class="row">
                            @foreach($addresses as $address)
                                <div class="col-md-6 mb-3">
                                    <div class="card address-card h-100" style="border-left: 4px solid {{ $address->is_default ? 'var(--primary-color)' : '#e5e5e5' }}">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0">
                                                    <i class="bi bi-tag"></i> {{ $address->label }}
                                                </h6>
                                                @if($address->is_default)
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle"></i> Utama
                                                    </span>
                                                @endif
                                            </div>

                                            <p class="mb-2 text-muted">
                                                <i class="bi bi-person"></i> <strong>{{ $address->nama_penerima }}</strong>
                                                <br>
                                                <i class="bi bi-telephone"></i> {{ $address->no_telp_penerima }}
                                            </p>

                                            <p class="mb-2 text-muted">
                                                <i class="bi bi-geo-alt"></i> {{ $address->alamat_lengkap }}
                                            </p>

                                            <p class="mb-3 text-muted">
                                                <i class="bi bi-building"></i> {{ $address->kecamatan }}, {{ $address->kota }}<br>
                                                <i class="bi bi-mailbox"></i> Kode Pos: {{ $address->kode_pos }}
                                            </p>

                                            <div class="d-flex gap-2">
                                                @if(!$address->is_default)
                                                    <a href="{{ route('pelanggan.addresses.set-default', $address->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-star"></i> Jadikan Utama
                                                    </a>
                                                @endif
                                                <a href="{{ route('pelanggan.addresses.edit', $address->id) }}" class="btn btn-sm btn-outline-warning">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                                <form action="{{ route('pelanggan.addresses.destroy', $address->id) }}" method="POST" style="display: inline;"
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus alamat ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Belum ada alamat tersimpan.</strong>
                            <a href="{{ route('pelanggan.addresses.create') }}">Tambah alamat baru</a> untuk memudahkan checkout.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Keamanan Tab -->
            <div class="tab-pane fade" id="security" role="tabpanel">
                <form action="{{ route('profile.update.password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Info:</strong> Isi semua field untuk mengubah password Anda
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="bi bi-key"></i> Password Lama <span class="text-danger">*</span>
                            </label>
                            <div class="password-toggle-wrapper">
                                <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror"
                                       placeholder="Masukkan password lama"
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
                                <i class="bi bi-key-fill"></i> Password Baru <span class="text-danger">*</span>
                            </label>
                            <div class="password-toggle-wrapper">
                                <input type="password" name="password" id="new_password" class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Minimal 8 karakter"
                                       required>
                                <button type="button" class="password-toggle-btn" onclick="togglePassword('new_password')">
                                    <i class="bi bi-eye" id="new_password-icon"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Password minimal 8 karakter</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="bi bi-key-fill"></i> Konfirmasi Password Baru <span class="text-danger">*</span>
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

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-shield-check"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>

            <!-- Membership Tab -->
            <!-- Membership Tab -->
            <div class="tab-pane fade" id="membership" role="tabpanel">
                @if(auth()->user()->membership && auth()->user()->membership->isValid())
                    <div class="text-center mb-4">
                        <span class="membership-badge {{ strtolower(auth()->user()->membership->tier) }}">
                            <i class="bi bi-star-fill"></i>
                            {{ strtoupper(auth()->user()->membership->tier) }} MEMBER
                        </span>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="membership-info-card">
                                <div class="membership-info-item">
                                    <i class="bi bi-percent"></i>
                                    <div>
                                        <strong>Diskon Member</strong>
                                        <p>{{ auth()->user()->membership->discount_percentage }}%</p>
                                    </div>
                                </div>

                                <div class="membership-info-item">
                                    <i class="bi bi-star"></i>
                                    <div>
                                        <strong>Total Poin</strong>
                                        <p>{{ number_format(auth()->user()->membership->points, 0, ',', '.') }} poin</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="membership-info-card">
                                <div class="membership-info-item">
                                    <i class="bi bi-calendar-check"></i>
                                    <div>
                                        <strong>Bergabung Sejak</strong>
                                        <p>{{ auth()->user()->membership->created_at->format('d M Y') }}</p>
                                    </div>
                                </div>

                                <div class="membership-info-item">
                                    <i class="bi bi-calendar-event"></i>
                                    <div>
                                        <strong>Berlaku Hingga</strong>
                                        <p>{{ auth()->user()->membership->valid_until ? auth()->user()->membership->valid_until->format('d M Y') : '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @php
                        $nextTier = auth()->user()->membership->getNextTierInfo();
                    @endphp

                    @if($nextTier['next_tier'] != 'Maximum')
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Info:</strong> Kumpulkan <strong>{{ number_format($nextTier['points_needed'], 0, ',', '.') }}</strong> poin lagi untuk naik ke tier <strong>{{ $nextTier['next_tier'] }}</strong>!
                        </div>
                    @else
                        <div class="alert alert-success">
                            <i class="bi bi-trophy-fill me-2"></i>
                            <strong>Selamat!</strong> Anda sudah mencapai tier tertinggi!
                        </div>
                    @endif

                    <div class="alert alert-success">
                        <i class="bi bi-gift me-2"></i>
                        <strong>Cara Mendapatkan Poin:</strong> Dapatkan 1 poin dari setiap Rp 20.000 belanja!
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-4 text-center mb-3">
                            <div class="card border-0 shadow-sm p-3">
                                <i class="bi bi-trophy text-warning" style="font-size: 3rem;"></i>
                                <h6 class="mt-2 mb-0">Bronze</h6>
                                <small class="text-muted">0-100 poin</small>
                                <p class="mb-0 mt-1"><strong>5% diskon</strong></p>
                            </div>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <div class="card border-0 shadow-sm p-3">
                                <i class="bi bi-trophy text-secondary" style="font-size: 3rem;"></i>
                                <h6 class="mt-2 mb-0">Silver</h6>
                                <small class="text-muted">101-250 poin</small>
                                <p class="mb-0 mt-1"><strong>10% diskon</strong></p>
                            </div>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <div class="card border-0 shadow-sm p-3">
                                <i class="bi bi-trophy text-warning" style="font-size: 3rem;"></i>
                                <h6 class="mt-2 mb-0">Gold</h6>
                                <small class="text-muted">251-400 poin</small>
                                <p class="mb-0 mt-1"><strong>15% diskon</strong></p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 offset-md-4 text-center mb-3">
                            <div class="card border-0 shadow-sm p-3 border-primary">
                                <i class="bi bi-trophy text-primary" style="font-size: 3rem;"></i>
                                <h6 class="mt-2 mb-0">Platinum</h6>
                                <small class="text-muted">401+ poin</small>
                                <p class="mb-0 mt-1"><strong>20% diskon</strong></p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-emoji-frown" style="font-size: 5rem; color: var(--text-muted);"></i>
                        <h4 class="mt-3 mb-2">Belum Memiliki Membership</h4>
                        <p class="text-muted mb-4">Lakukan transaksi pertama untuk mendapatkan membership dan nikmati berbagai keuntungan!</p>
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="bi bi-shop"></i> Belanja Sekarang
                        </a>
                    </div>
                @endif
            </div>

            <!-- Member Card Tab -->
            <!-- Member Card Tab -->
            <div class="tab-pane fade" id="member-card" role="tabpanel">
                @if(auth()->user()->membership && auth()->user()->membership->isValid())
                    <div class="row justify-content-center">
                        <div class="col-lg-6">
                            <div class="member-card">
                                <div class="member-card-header">
                                    <span class="member-card-title">Ayu Mart Member</span>
                                    <span class="member-card-title">{{ strtoupper(auth()->user()->membership->tier) }}</span>
                                </div>

                                <div class="member-id">
                                    {{ 'AYU-' . str_pad(auth()->user()->id_user, 6, '0', STR_PAD_LEFT) }}
                                </div>

                                <div class="member-name">
                                    {{ auth()->user()->name }}
                                </div>

                                <div class="qr-barcode-container">
                                    <div class="code-tabs">
                                        <button class="code-tab active" onclick="switchTab('qr')">
                                            <i class="bi bi-qr-code"></i> QR Code
                                        </button>
                                        <button class="code-tab" onclick="switchTab('barcode')">
                                            <i class="bi bi-upc-scan"></i> Barcode
                                        </button>
                                    </div>

                                    <!-- QR Code Tab -->
                                    <div id="qr-tab" class="code-content active">
                                        <div id="qrcode" style="min-height: 200px; display: flex; align-items: center; justify-content: center;">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                        <div class="text-center mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="generateCodes()">
                                                <i class="bi bi-arrow-clockwise"></i> Refresh QR Code
                                            </button>
                                        </div>
                                        <div class="code-instructions">
                                            <i class="bi bi-info-circle"></i>
                                            Tunjukkan QR Code ini di kasir untuk mendapatkan poin & diskon
                                        </div>
                                        <button class="btn-download" onclick="downloadQRCode()">
                                            <i class="bi bi-download"></i> Download QR Code
                                        </button>
                                    </div>

                                    <!-- Barcode Tab -->
                                    <div id="barcode-tab" class="code-content">
                                        <div style="min-height: 150px; display: flex; align-items: center; justify-content: center;">
                                            <svg id="barcode"></svg>
                                        </div>
                                        <div class="text-center mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="generateCodes()">
                                                <i class="bi bi-arrow-clockwise"></i> Refresh Barcode
                                            </button>
                                        </div>
                                        <div class="code-instructions">
                                            <i class="bi bi-info-circle"></i>
                                            Scan barcode ini di kasir untuk mendapatkan poin & diskon
                                        </div>
                                        <button class="btn-download" onclick="downloadBarcode()">
                                            <i class="bi bi-download"></i> Download Barcode
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-warning mt-3">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Penting:</strong> Jangan bagikan kode member Anda kepada orang lain.
                                Kode ini bersifat pribadi dan hanya untuk digunakan oleh Anda.
                            </div>

                            <div class="alert alert-info mt-3">
                                <i class="bi bi-lightbulb me-2"></i>
                                <strong>Cara Menggunakan:</strong>
                                <ol class="mb-0 mt-2 ps-3">
                                    <li>Tunjukkan kartu member digital ini di kasir Ayu Mart</li>
                                    <li>Kasir akan scan QR Code atau Barcode Anda</li>
                                    <li>Dapatkan diskon {{ auth()->user()->membership->discount_percentage }}% dan poin otomatis!</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-credit-card-2-front" style="font-size: 5rem; color: var(--text-muted);"></i>
                        <h4 class="mt-3 mb-2">Kartu Member Belum Tersedia</h4>
                        <p class="text-muted mb-4">Lakukan transaksi pertama untuk mendapatkan kartu member digital!</p>
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="bi bi-shop"></i> Belanja Sekarang
                        </a>
                    </div>
                @endif
            </div>
        </div>
@endsection

@push('scripts')
<!-- QR Code Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" crossorigin="anonymous"></script>
<!-- Barcode Library -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js" crossorigin="anonymous"></script>

<script>
    console.log('=== Scripts Loading ===');

    // Check if libraries are loaded with retry
    function checkLibraries(attempt = 1, maxAttempts = 5) {
        return new Promise((resolve, reject) => {
            if (typeof QRCode !== 'undefined' && typeof JsBarcode !== 'undefined') {
                console.log('✓ All libraries loaded successfully on attempt', attempt);
                resolve(true);
            } else if (attempt < maxAttempts) {
                console.log(`⏳ Waiting for libraries... (attempt ${attempt}/${maxAttempts})`);
                setTimeout(() => {
                    checkLibraries(attempt + 1, maxAttempts).then(resolve).catch(reject);
                }, 500);
            } else {
                console.error('✗ Libraries not loaded after', maxAttempts, 'attempts');
                console.error('QRCode available:', typeof QRCode !== 'undefined');
                console.error('JsBarcode available:', typeof JsBarcode !== 'undefined');
                reject(new Error('Libraries failed to load'));
            }
        });
    }

    // Member Code
    const memberCode = 'AYU-{{ str_pad(auth()->user()->id_user, 6, "0", STR_PAD_LEFT) }}';
    const memberName = '{{ auth()->user()->name }}';
    const memberTier = '{{ auth()->user()->membership ? strtoupper(auth()->user()->membership->tier) : "" }}';

    // Generate QR Code and Barcode
    @if(auth()->user()->membership && auth()->user()->membership->isValid())
    console.log('=== Membership detected, preparing to generate codes ===');

    // Wait for DOM and libraries
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeCodes);
    } else {
        initializeCodes();
    }

    function initializeCodes() {
        console.log('=== Member Card Generator Started ===');
        console.log('Member Code:', memberCode);
        console.log('Member Name:', memberName);
        console.log('Member Tier:', memberTier);

        // Check if we're on the member card tab
        const memberCardTab = document.getElementById('member-card-tab');
        if (memberCardTab) {
            memberCardTab.addEventListener('shown.bs.tab', function (e) {
                console.log('Member card tab shown, generating codes...');
                generateCodes();
            });
        }

        // Also try to generate on page load if tab is already active
        setTimeout(() => {
            const memberCardContent = document.getElementById('member-card');
            if (memberCardContent && memberCardContent.classList.contains('active')) {
                console.log('Member card tab already active, generating codes...');
                generateCodes();
            }
        }, 500);
    }

    function generateCodes() {
        checkLibraries()
            .then(() => {
                console.log('📱 Starting code generation...');
                generateQRCode();
                generateBarcode();
            })
            .catch(error => {
                console.error('Failed to load libraries:', error);
                showError('qrcode', 'Library belum dimuat. Silakan refresh halaman.');
            });
    }

    function generateQRCode() {
        try {
            const qrcodeContainer = document.getElementById("qrcode");
            if (!qrcodeContainer) {
                console.error('❌ QR Code container not found!');
                return;
            }

            console.log('🔄 Generating QR Code...');

            // Clear container
            qrcodeContainer.innerHTML = '';
            qrcodeContainer.style.minHeight = 'auto';
            qrcodeContainer.style.display = 'block';
            qrcodeContainer.style.textAlign = 'center';

            // Check if QRCode library is available
            if (typeof QRCode === 'undefined') {
                console.warn('⚠️ QRCode library not available, using API fallback');
                // Use API fallback
                const img = document.createElement('img');
                img.src = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(memberCode)}`;
                img.alt = 'QR Code';
                img.style.maxWidth = '200px';
                img.style.height = 'auto';
                img.onerror = function() {
                    qrcodeContainer.innerHTML = '<div class="alert alert-danger small">Gagal memuat QR Code</div>';
                };
                qrcodeContainer.appendChild(img);
                console.log('✅ QR Code loaded via API');
                return;
            }

            // Generate QR Code using library
            new QRCode(qrcodeContainer, {
                text: memberCode,
                width: 200,
                height: 200,
                colorDark: "#333333",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });

            console.log('✅ QR Code generated successfully!');
        } catch (error) {
            console.error('❌ Error generating QR Code:', error);
            showError('qrcode', 'Gagal membuat QR Code: ' + error.message);
        }
    }

    function generateBarcode() {
        try {
            const barcodeElement = document.getElementById("barcode");
            if (!barcodeElement) {
                console.error('❌ Barcode element not found!');
                return;
            }

            console.log('🔄 Generating Barcode...');

            // Generate Barcode
            JsBarcode("#barcode", memberCode, {
                format: "CODE128",
                width: 2,
                height: 80,
                displayValue: true,
                fontSize: 16,
                margin: 10,
                background: "#ffffff",
                lineColor: "#000000"
            });

            console.log('✅ Barcode generated successfully!');
        } catch (error) {
            console.error('❌ Error generating Barcode:', error);
            showError('barcode', 'Gagal membuat Barcode: ' + error.message);
        }
    }

    function showError(elementId, message) {
        const element = document.getElementById(elementId);
        if (element) {
            if (element.tagName === 'svg') {
                element.parentElement.innerHTML = '<div class="alert alert-danger small"><i class="bi bi-x-circle"></i> ' + message + '</div>';
            } else {
                element.innerHTML = '<div class="alert alert-danger small"><i class="bi bi-x-circle"></i> ' + message + '</div>';
            }
        }
    }
    @else
    console.log('ℹ️ No active membership found');
    @endif

    // Switch between QR Code and Barcode tabs
    function switchTab(tab) {
        // Remove active class from all tabs
        document.querySelectorAll('.code-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.code-content').forEach(c => c.classList.remove('active'));

        // Add active class to selected tab
        if (tab === 'qr') {
            document.querySelectorAll('.code-tab')[0].classList.add('active');
            document.getElementById('qr-tab').classList.add('active');
        } else {
            document.querySelectorAll('.code-tab')[1].classList.add('active');
            document.getElementById('barcode-tab').classList.add('active');
        }
    }

    // Download QR Code
    function downloadQRCode() {
        const qrCanvas = document.querySelector('#qrcode canvas');
        if (!qrCanvas) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'QR Code belum di-generate!'
            });
            return;
        }

        // Create a new canvas with member info
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');

        // Set canvas size
        canvas.width = 400;
        canvas.height = 500;

        // Background
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // Add gradient header
        const gradient = ctx.createLinearGradient(0, 0, canvas.width, 0);
        gradient.addColorStop(0, '#667eea');
        gradient.addColorStop(1, '#764ba2');
        ctx.fillStyle = gradient;
        ctx.fillRect(0, 0, canvas.width, 80);

        // Title
        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 24px Arial';
        ctx.textAlign = 'center';
        ctx.fillText('Ayu Mart Member Card', canvas.width / 2, 35);
        ctx.font = 'bold 16px Arial';
        ctx.fillText(memberTier, canvas.width / 2, 60);

        // Member info
        ctx.fillStyle = '#333333';
        ctx.font = 'bold 18px Arial';
        ctx.fillText(memberName, canvas.width / 2, 120);
        ctx.font = '16px Arial';
        ctx.fillText(memberCode, canvas.width / 2, 145);

        // Draw QR Code
        ctx.drawImage(qrCanvas, 100, 170, 200, 200);

        // Footer text
        ctx.font = '12px Arial';
        ctx.fillStyle = '#666666';
        ctx.fillText('Tunjukkan kode ini di kasir', canvas.width / 2, 400);
        ctx.fillText('untuk mendapatkan poin & diskon', canvas.width / 2, 420);

        // Download
        const link = document.createElement('a');
        link.download = `Ayu Mart_QR_${memberCode}.png`;
        link.href = canvas.toDataURL();
        link.click();

        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'QR Code berhasil diunduh',
            timer: 2000,
            showConfirmButton: false
        });
    }

    // Download Barcode
    function downloadBarcode() {
        const barcodeSvg = document.querySelector('#barcode');
        if (!barcodeSvg) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Barcode belum di-generate!'
            });
            return;
        }

        // Create canvas
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');

        canvas.width = 500;
        canvas.height = 400;

        // Background
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // Add gradient header
        const gradient = ctx.createLinearGradient(0, 0, canvas.width, 0);
        gradient.addColorStop(0, '#667eea');
        gradient.addColorStop(1, '#764ba2');
        ctx.fillStyle = gradient;
        ctx.fillRect(0, 0, canvas.width, 80);

        // Title
        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 24px Arial';
        ctx.textAlign = 'center';
        ctx.fillText('Ayu Mart Member Card', canvas.width / 2, 35);
        ctx.font = 'bold 16px Arial';
        ctx.fillText(memberTier, canvas.width / 2, 60);

        // Member info
        ctx.fillStyle = '#333333';
        ctx.font = 'bold 18px Arial';
        ctx.fillText(memberName, canvas.width / 2, 120);

        // Convert SVG to image and draw
        const svgData = new XMLSerializer().serializeToString(barcodeSvg);
        const img = new Image();
        const svgBlob = new Blob([svgData], {type: 'image/svg+xml;charset=utf-8'});
        const url = URL.createObjectURL(svgBlob);

        img.onload = function() {
            ctx.drawImage(img, 50, 150, 400, 150);

            // Footer text
            ctx.font = '12px Arial';
            ctx.fillStyle = '#666666';
            ctx.fillText('Scan barcode ini di kasir', canvas.width / 2, 330);
            ctx.fillText('untuk mendapatkan poin & diskon', canvas.width / 2, 350);

            // Download
            const link = document.createElement('a');
            link.download = `Ayu Mart_Barcode_${memberCode}.png`;
            link.href = canvas.toDataURL();
            link.click();

            URL.revokeObjectURL(url);

            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Barcode berhasil diunduh',
                timer: 2000,
                showConfirmButton: false
            });
        };

        img.src = url;
    }

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
