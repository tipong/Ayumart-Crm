<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - AyuMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #3F4F44;
            --primary-hover: #d73211;
            --secondary-color: #3F4F44;
            --text-dark: #333;
            --text-muted: #666;
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
            max-width: 1000px;
            width: 100%;
            display: flex;
            flex-direction: row;
        }

        /* Left Side - Welcome Image/Graphics */
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

        /* Right Side - Login Form */
        .auth-right {
            padding: 60px 50px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
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

        .brand-logo span {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .auth-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .auth-subtitle {
            color: var(--text-muted);
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
            font-weight: 600;
            font-size: 0.9rem;
        }

        .form-label i {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }

        .form-control {
            width: 100%;
            padding: 0.85rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(238, 77, 45, 0.1);
            transform: translateY(-2px);
        }

        .form-control::placeholder {
            color: #cbd5e1;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 0.5rem;
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

        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            margin-right: 0.5rem;
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .form-check-label {
            color: var(--text-muted);
            font-size: 0.9rem;
            cursor: pointer;
        }

        .btn-primary {
            width: 100%;
            padding: 0.95rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(238, 77, 45, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(238, 77, 45, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .auth-links {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.9rem;
        }

        .auth-links a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .auth-links a:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }

        .divider {
            margin: 1.5rem 0;
            text-align: center;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e2e8f0;
        }

        .divider span {
            background: white;
            padding: 0 1rem;
            color: var(--text-muted);
            font-size: 0.85rem;
            position: relative;
            z-index: 1;
        }

        .btn-back {
            position: absolute;
            top: 2rem;
            right: 2rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: white;
            text-decoration: none;
            padding: 0.65rem 1.25rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 25px;
            transition: all 0.3s;
            font-size: 0.9rem;
            font-weight: 600;
            z-index: 2;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: white;
            color: white;
            transform: translateX(-5px);
        }

        @media (max-width: 768px) {
            .auth-container {
                flex-direction: column;
                border-radius: 15px;
            }

            .auth-left {
                padding: 40px 20px;
            }

            .welcome-icon {
                font-size: 4rem;
            }

            .welcome-content h1 {
                font-size: 2rem;
            }

            .feature-list {
                display: none;
            }

            .auth-right {
                padding: 40px 20px;
            }

            .btn-back {
                top: 1rem;
                right: 1rem;
                padding: 0.5rem 1rem;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <!-- Left Side - Branding -->
        <div class="auth-left">
            <i class="bi bi-shop-window"></i>
            <h1 id="brandName">Ayu Mart</h1>
            <p id="brandTagline">Belanja kebutuhan harian Anda dengan mudah dan cepat</p>
            <a href="{{ route('home') }}" class="btn-back">
                <i class="bi bi-arrow-left"></i>
                Kembali ke Beranda
            </a>
        </div>

        <!-- Right Side - Login Form -->
        <div class="auth-right">

            <h2 class="auth-title">Selamat Datang!</h2>
            <p class="auth-subtitle">Silakan login untuk melanjutkan</p>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope"></i> Email
                    </label>
                    <input
                        id="email"
                        type="email"
                        class="form-control @error('email') is-invalid @enderror"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Masukkan email Anda"
                        required
                        autocomplete="email"
                        autofocus
                    >
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock"></i> Password
                    </label>
                    <div class="password-wrapper">
                        <input
                            id="password"
                            type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            name="password"
                            placeholder="Masukkan password Anda"
                            required
                            autocomplete="current-password"
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

                <!-- Remember Me -->
                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="remember"
                        id="remember"
                        {{ old('remember') ? 'checked' : '' }}
                    >
                    <label class="form-check-label" for="remember">
                        Ingat Saya
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-box-arrow-in-right"></i> Masuk
                </button>

                <!-- Links -->
                <div class="auth-links" id="authLinks">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}">Lupa Password?</a>
                        <span class="mx-2">|</span>
                    @endif
                    <a href="{{ route('register') }}" id="registerLink">Belum punya akun? Daftar</a>
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

        function switchLoginType(type) {
            const body = document.getElementById('loginBody');
            const brandName = document.getElementById('brandName');
            const brandTagline = document.getElementById('brandTagline');
            const registerLink = document.getElementById('registerLink');
            const buttons = document.querySelectorAll('.login-type-btn');

            // Update active button
            buttons.forEach(btn => btn.classList.remove('active'));
            event.target.closest('.login-type-btn').classList.add('active');

            if (type === 'customer') {
                body.className = 'customer-login';
                brandName.textContent = 'AyuMart';
                brandTagline.textContent = 'Belanja kebutuhan harian Anda dengan mudah dan cepat';
                registerLink.style.display = 'inline';
            } else {
                body.className = 'admin-login';
                brandName.textContent = 'AyuMart Admin';
                brandTagline.textContent = 'Sistem manajemen untuk staff dan administrator';
                registerLink.style.display = 'none';
            }
        }

        // Check if there's an error and switch to admin view if needed
        @if($errors->any() && old('email'))
            // Try to detect if email looks like staff email
            const email = '{{ old('email') }}';
            if (email.includes('@admin') || email.includes('@staff') || email.includes('@cs') || email.includes('@kurir') || email.includes('@owner')) {
                switchLoginType('admin');
                document.querySelectorAll('.login-type-btn')[1].classList.add('active');
                document.querySelectorAll('.login-type-btn')[0].classList.remove('active');
            }
        @endif
    </script>
</body>
</html>
