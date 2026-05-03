<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar - AyuMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #3F4F44;
            --primary-hover: #d73211;
            --secondary-color: #3F4F44;
            --text-dark: #333;
            --text-muted: #666;
            --border-color: #e5e5e5;
            --bg-light: #f5f5f5;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            max-width: 1100px;
            width: 100%;
            display: flex;
            flex-direction: row;
        }

        /* Left Side - Welcome Graphics */
        .auth-left {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 60px 40px;
            color: white;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .auth-left::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            top: -100px;
            left: -100px;
        }

        .auth-left::after {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            bottom: -50px;
            right: -50px;
        }

        .welcome-content {
            position: relative;
            z-index: 1;
        }

        .welcome-icon {
            font-size: 6rem;
            margin-bottom: 2rem;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        .welcome-content h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .welcome-content p {
            font-size: 1.1rem;
            opacity: 0.95;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .feature-list {
            text-align: left;
            margin-top: 2rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
            font-size: 0.95rem;
        }

        .feature-item i {
            font-size: 1.5rem;
        }

        /* Right Side - Register Form */
        .auth-right {
            padding: 60px 50px;
            flex: 1;
            overflow-y: auto;
            max-height: 90vh;
            background: white;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 2rem;
        }

        .brand-logo i {
            font-size: 2.5rem;
            color: var(--primary-color);
        }

        .brand-logo h2 {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-dark);
            margin: 0;
        }

        .auth-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 10px;
        }

        .auth-subtitle {
            color: var(--text-muted);
            margin-bottom: 30px;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-dark);
            font-weight: 500;
            font-size: 0.9rem;
        }

        .form-label i {
            margin-right: 5px;
            color: var(--primary-color);
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s;
            background: #fafafa;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(238, 77, 45, 0.1);
            background: white;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
            background: #fff5f5;
        }

        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 5px;
        }

        /* Password Toggle Styles */
        .password-wrapper {
            position: relative;
        }

        .password-wrapper .form-control {
            padding-right: 45px;
        }

        .password-toggle-btn {
            position: absolute;
            right: 12px;
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

        .btn-primary {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
            box-shadow: 0 4px 12px rgba(238, 77, 45, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(238, 77, 45, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-primary i {
            margin-right: 8px;
        }

        .auth-links {
            margin-top: 20px;
            text-align: center;
        }

        .auth-links a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .auth-links a:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            transition: all 0.3s;
            font-size: 0.9rem;
            margin-top: 2rem;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: white;
            color: white;
            transform: translateX(-5px);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .required {
            color: #dc3545;
            margin-left: 3px;
        }

        /* Password strength indicator */
        .password-strength {
            margin-top: 8px;
            font-size: 0.85rem;
        }

        .password-strength.weak { color: #ef4444; }
        .password-strength.medium { color: #f59e0b; }
        .password-strength.strong { color: #10b981; }

        @media (max-width: 992px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .auth-container {
                max-width: 900px;
            }
        }

        @media (max-width: 768px) {
            .auth-container {
                flex-direction: column;
            }

            .auth-left {
                padding: 40px 20px;
                flex: none;
            }

            .welcome-content h1 {
                font-size: 2rem;
            }

            .welcome-icon {
                font-size: 4rem;
            }

            .auth-right {
                padding: 40px 20px;
                max-height: none;
            }

            .brand-logo h2 {
                font-size: 1.5rem;
            }
        }

        /* Custom Scrollbar */
        .auth-right::-webkit-scrollbar {
            width: 8px;
        }

        .auth-right::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .auth-right::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 4px;
        }

        .auth-right::-webkit-scrollbar-thumb:hover {
            background: var(--primary-hover);
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <!-- Left Side - Branding & Welcome -->
        <div class="auth-left">
            <div class="welcome-content">
                <div class="welcome-icon">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <h1>Bergabunglah!</h1>
                <p>Daftar sekarang dan nikmati pengalaman berbelanja yang lebih mudah dan menyenangkan di Ayu Mart</p>

                <div class="feature-list">
                    <div class="feature-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Proses pendaftaran cepat & mudah</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-shield-check"></i>
                        <span>Data aman dan terenkripsi</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-gift"></i>
                        <span>Dapatkan penawaran eksklusif</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-truck"></i>
                        <span>Gratis ongkir untuk member baru</span>
                    </div>
                </div>

                <a href="{{ route('home') }}" class="btn-back">
                    <i class="bi bi-arrow-left"></i>
                    Kembali ke Beranda
                </a>
            </div>
        </div>

        <!-- Right Side - Register Form -->
        <div class="auth-right">
            <div class="brand-logo">
                {{-- <i class="bi bi-shop-window"></i> --}}
                <h2><img src="{{ asset('images/logo.png') }}" alt="AyuMart" height="36" width="36" class="d-inline-block align-text-top me-2">Ayu Mart</h2>
            </div>

            <h2 class="auth-title">Buat Akun Baru</h2>
            <p class="auth-subtitle">Isi form di bawah untuk mendaftar sebagai member Ayu Mart</p>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Nama Lengkap -->
                <div class="form-group">
                    <label for="name" class="form-label">
                        <i class="bi bi-person"></i> Nama Lengkap<span class="required">*</span>
                    </label>
                    <input
                        id="name"
                        type="text"
                        class="form-control @error('name') is-invalid @enderror"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Masukkan nama lengkap Anda"
                        required
                        autocomplete="name"
                        autofocus
                    >
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Email & Nomor Telepon -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope"></i> Email<span class="required">*</span>
                        </label>
                        <input
                            id="email"
                            type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="contoh@email.com"
                            required
                            autocomplete="email"
                        >
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="telepon" class="form-label">
                            <i class="bi bi-telephone"></i> Nomor Telepon<span class="required">*</span>
                        </label>
                        <input
                            id="telepon"
                            type="tel"
                            class="form-control @error('telepon') is-invalid @enderror"
                            name="telepon"
                            value="{{ old('telepon') }}"
                            placeholder="08xxxxxxxxxx"
                            required
                        >
                        @error('telepon')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <!-- Alamat Lengkap -->
                <div class="form-group">
                    <label for="alamat" class="form-label">
                        <i class="bi bi-geo-alt"></i> Alamat Lengkap<span class="required">*</span>
                    </label>
                    <textarea
                        id="alamat"
                        class="form-control @error('alamat') is-invalid @enderror"
                        name="alamat"
                        rows="3"
                        placeholder="Masukkan alamat lengkap Anda (Jalan, Nomor, RT/RW, Kelurahan, Kecamatan)"
                        required
                    >{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Password & Konfirmasi Password -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock"></i> Password<span class="required">*</span>
                        </label>
                        <div class="password-wrapper">
                            <input
                                id="password"
                                type="password"
                                class="form-control @error('password') is-invalid @enderror"
                                name="password"
                                placeholder="Min. 8 karakter"
                                required
                                autocomplete="new-password"
                            >
                            <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('password')">
                                <i class="bi bi-eye" id="password-icon"></i>
                            </button>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password-confirm" class="form-label">
                            <i class="bi bi-lock-fill"></i> Konfirmasi Password<span class="required">*</span>
                        </label>
                        <div class="password-wrapper">
                            <input
                                id="password-confirm"
                                type="password"
                                class="form-control"
                                name="password_confirmation"
                                placeholder="Ulangi password"
                                required
                                autocomplete="new-password"
                            >
                            <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('password-confirm')">
                                <i class="bi bi-eye" id="password-confirm-icon"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-person-plus"></i> Daftar Sekarang
                </button>

                <!-- Links -->
                <div class="auth-links">
                    Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle Password Visibility
        function togglePasswordVisibility(fieldId) {
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

        // Password strength indicator with visual feedback
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('password-confirm');

        passwordInput.addEventListener('input', function(e) {
            const password = e.target.value;
            const minLength = password.length >= 8;
            const hasNumber = /\d/.test(password);
            const hasLetter = /[a-zA-Z]/.test(password);

            let strength = 0;
            if (minLength) strength++;
            if (hasNumber) strength++;
            if (hasLetter) strength++;

            if (password.length > 0) {
                if (strength === 3) {
                    e.target.style.borderColor = '#10b981';
                } else if (strength === 2) {
                    e.target.style.borderColor = '#f59e0b';
                } else {
                    e.target.style.borderColor = '#ef4444';
                }
            } else {
                e.target.style.borderColor = '#e5e5e5';
            }

            // Revalidate confirmation if it has value
            if (confirmPasswordInput.value) {
                confirmPasswordInput.dispatchEvent(new Event('input'));
            }
        });

        // Password confirmation match with visual feedback
        confirmPasswordInput.addEventListener('input', function(e) {
            const password = passwordInput.value;
            const confirmPassword = e.target.value;

            if (confirmPassword.length > 0) {
                if (password === confirmPassword && password.length >= 8) {
                    e.target.style.borderColor = '#10b981';
                } else {
                    e.target.style.borderColor = '#ef4444';
                }
            } else {
                e.target.style.borderColor = '#e5e5e5';
            }
        });

        // Phone number validation (Indonesian format)
        const teleponInput = document.getElementById('telepon');
        teleponInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^0-9]/g, '');
            e.target.value = value;

            // Validate Indonesian phone number format
            if (value.length > 0) {
                if (value.startsWith('08') && value.length >= 10 && value.length <= 13) {
                    e.target.style.borderColor = '#10b981';
                } else {
                    e.target.style.borderColor = '#ef4444';
                }
            } else {
                e.target.style.borderColor = '#e5e5e5';
            }
        });

        // Form validation before submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            const telepon = teleponInput.value;

            if (password !== confirmPassword) {
                e.preventDefault();
                confirmPasswordInput.focus();
                confirmPasswordInput.style.borderColor = '#ef4444';
                alert('Password dan konfirmasi password tidak cocok!');
                return false;
            }

            if (password.length < 8) {
                e.preventDefault();
                passwordInput.focus();
                passwordInput.style.borderColor = '#ef4444';
                alert('Password minimal 8 karakter!');
                return false;
            }

            if (!telepon.startsWith('08') || telepon.length < 10) {
                e.preventDefault();
                teleponInput.focus();
                teleponInput.style.borderColor = '#ef4444';
                alert('Nomor telepon harus diawali dengan 08 dan minimal 10 digit!');
                return false;
            }
        });
    </script>
</body>
</html>
