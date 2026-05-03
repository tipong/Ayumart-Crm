<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Staff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #059669 0%, #03563c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            max-width: 450px;
            margin: 50px auto;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #059669 0%, #059669 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .login-header i {
            font-size: 3.5rem;
            margin-bottom: 15px;
        }

        .login-header h3 {
            font-weight: 700;
            margin-bottom: 5px;
        }

        .login-header p {
            margin: 0;
            opacity: 0.9;
        }

        .login-body {
            padding: 40px 30px;
        }

        .form-label {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border-radius: 10px;
            padding: 12px 18px;
            border: 2px solid #e2e8f0;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: #059669;
            box-shadow: 0 0 0 0.2rem rgba(42, 82, 152, 0.15);
        }

        .btn-login {
            background: linear-gradient(135deg, #059669 0%, #059669 100%);
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-weight: 600;
            font-size: 1.05rem;
            color: white;
            width: 100%;
            transition: all 0.3s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(42, 82, 152, 0.4);
            color: white;
        }

        .role-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin: 5px;
        }

        .badge-owner {
            background: #fef3c7;
            color: #059669;
        }

        .badge-admin {
            background: #dbeafe;
            color: #059669;
        }

        .badge-cs {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-kurir {
            background: #e0e7ff;
            color: #059669;
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .back-home {
            text-align: center;
            margin-top: 20px;
        }

        .back-home a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .back-home a:hover {
            text-decoration: underline;
        }

        .customer-login-link {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            margin-top: 20px;
        }

        .customer-login-link a {
            color: white;
            text-decoration: none;
            font-weight: 600;
        }

        .customer-login-link a:hover {
            text-decoration: underline;
        }

        .info-box {
            background: #f1f5f9;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .info-box h6 {
            color: #1e293b;
            font-weight: 600;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <i class="bi bi-shield-lock"></i>
                    <h3>Login Staff</h3>
                    <p>Akses untuk Admin, Owner, CS, dan Kurir</p>
                </div>

                <div class="login-body">
                    <form method="POST" action="{{ route('staff.login.submit') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="bi bi-envelope"></i> Email Staff
                            </label>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   placeholder="staff@company.com"
                                   required
                                   autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="bi bi-lock"></i> Password
                            </label>
                            <div class="input-group">
                                <input type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       id="password"
                                       name="password"
                                       placeholder="Masukkan password"
                                       required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye" id="toggleIcon"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Ingat Saya
                            </label>
                        </div>

                        <button type="submit" class="btn btn-login">
                            <i class="bi bi-box-arrow-in-right"></i> Masuk
                        </button>
                    </form>
                </div>
            </div>

            {{-- <div class="back-home">
                <a href="{{ route('home') }}">
                    <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                </a>
            </div> --}}
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // SweetAlert2 untuk error dan success messages
        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal',
                html: `
                    <ul style="text-align: left; list-style: none; padding: 0;">
                        @foreach ($errors->all() as $error)
                            <li><i class="bi bi-x-circle"></i> {{ $error }}</li>
                        @endforeach
                    </ul>
                `,
                confirmButtonColor: '#2a5298',
                confirmButtonText: 'OK'
            });
        @endif

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#2a5298',
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#2a5298'
            });
        @endif

        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');

        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            if (type === 'password') {
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            } else {
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            }
        });
    </script>
</body>
</html>
