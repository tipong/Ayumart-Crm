<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak - 403</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .error-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
        }

        .error-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            overflow: hidden;
            text-align: center;
            padding: 50px 30px;
        }

        .error-icon {
            font-size: 6rem;
            color: #ef4444;
            margin-bottom: 20px;
        }

        .error-code {
            font-size: 4rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 10px;
        }

        .error-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 15px;
        }

        .error-message {
            font-size: 1.1rem;
            color: #64748b;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .btn-back {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 14px 30px;
            font-weight: 600;
            font-size: 1.05rem;
            color: white;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            margin: 5px;
        }

        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .btn-login {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            border: none;
            border-radius: 10px;
            padding: 14px 30px;
            font-weight: 600;
            font-size: 1.05rem;
            color: white;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            margin: 5px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(42, 82, 152, 0.4);
            color: white;
        }

        .help-text {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #e2e8f0;
        }

        .help-text p {
            color: #64748b;
            font-size: 0.95rem;
        }

        .help-text a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .help-text a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-container">
            <div class="error-card">
                <i class="bi bi-shield-exclamation error-icon"></i>
                <div class="error-code">403</div>
                <h1 class="error-title">Akses Ditolak</h1>
                <p class="error-message">
                    {{ $exception->getMessage() ?: 'Maaf, Anda tidak memiliki akses ke halaman ini.' }}
                    <br><br>
                    <strong>Pastikan Anda login dengan akun yang memiliki hak akses yang sesuai.</strong>
                </p>

                <div>
                    <a href="javascript:history.back()" class="btn-back">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    @auth
                        <a href="{{ route('home') }}" class="btn-back">
                            <i class="bi bi-house"></i> Beranda
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-login">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                    @endauth
                </div>

                <div class="help-text">
                    <p>
                        <i class="bi bi-info-circle"></i>
                        Jika Anda merasa ini adalah kesalahan, silakan hubungi administrator sistem.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
