<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Scholarship System</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #eab308 0%, #ca8a04 100%);
            --navy-blue: #090a0f;
            --soft-slate: #1c1c1e;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at 10% 20%, rgb(9, 10, 15) 0%, rgb(18, 18, 20) 90%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            color: #cbd5e1;
        }

        .login-card {
            background: rgba(24, 24, 27, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1.5rem;
            border: 1px solid rgba(234, 179, 8, 0.2);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.6), 0 10px 10px -5px rgba(0, 0, 0, 0.6);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .login-header {
            padding: 3rem 2.5rem 1.5rem 2.5rem;
            text-align: center;
        }

        .login-logo {
            font-size: 3.5rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
            display: inline-block;
        }

        .login-body {
            padding: 0 2.5rem 3rem 2.5rem;
        }

        .form-control {
            background-color: #121214;
            border: 1px solid #27272a;
            color: #ffffff;
        }

        .form-control:focus {
            background-color: #121214;
            color: #ffffff;
            border-color: #eab308;
            box-shadow: 0 0 0 0.25rem rgba(234, 179, 8, 0.15);
        }

        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            color: #eab308;
        }

        .form-floating > label {
            color: #94a3b8;
        }

        .btn-gradient {
            background: var(--primary-gradient);
            border: none;
            color: #090a0f;
            font-weight: 700;
            padding: 0.8rem;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(234, 179, 8, 0.3);
        }

        .btn-gradient:hover {
            opacity: 0.95;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(234, 179, 8, 0.4);
            color: #090a0f;
            background: linear-gradient(135deg, #facc15 0%, #ca8a04 100%);
        }

        .btn-gradient:active {
            transform: translateY(1px);
        }

        .text-muted {
            color: #cbd5e1 !important; /* Lighter color for readability */
        }

        .text-warning {
            color: #eab308 !important;
        }

        .text-warning:hover {
            color: #facc15 !important;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <div class="login-logo"><i class="bi bi-mortarboard-fill"></i></div>
            <h4 class="fw-bold text-white m-0">Scholarship System</h4>
            <p class="text-muted small mt-1">Layanan Administrasi & Beasiswa Kemahasiswaan</p>
        </div>

        <div class="login-body">
            @if (isset($errors) && $errors->any())
                <div class="alert alert-danger border-0 bg-danger-subtle text-danger-emphasis p-3 rounded-3 small mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ url('/login') }}" method="POST">
                @csrf
                <!-- Email Input -->
                <div class="form-floating mb-3">
                    <input type="email" name="email" class="form-control" id="floatingEmail" placeholder="name@example.com" value="{{ old('email') }}" required>
                    <label for="floatingEmail"><i class="bi bi-envelope me-2"></i>Email Address</label>
                </div>

                <!-- Password Input -->
                <div class="form-floating mb-4">
                    <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password" required>
                    <label for="floatingPassword"><i class="bi bi-lock me-2"></i>Password</label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-gradient w-100 py-3">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Sign In
                </button>

                <!-- Back to Landing Page Link -->
                <div class="text-center mt-4">
                    <a href="{{ url('/') }}" class="text-decoration-none small text-warning" style="font-weight: 600;">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Halaman Utama
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
