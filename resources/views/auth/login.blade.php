<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wing POS - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --glass-bg: rgba(255, 255, 255, 0.75);
            --glass-border: rgba(255, 255, 255, 0.4);
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        }
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: url('/images/auth-bg.png') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(2px);
            z-index: 0;
        }

        .login-container {
            z-index: 1;
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .login-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .login-header {
            background: var(--primary-gradient);
            color: white;
            text-align: center;
            padding: 40px 30px;
            border-radius: 0 0 24px 24px;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.2);
        }

        .login-header i {
            font-size: 2.5rem;
            display: block;
            margin-bottom: 15px;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
        }

        .login-header h3 {
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .card-body {
            padding: 40px 35px !important;
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            font-size: 0.875rem;
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 12px 16px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            background: rgba(255, 255, 255, 0.8);
        }

        .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            background: #fff;
        }

        .btn-login {
            background: var(--primary-gradient);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 700;
            font-size: 1rem;
            color: white;
            margin-top: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(79, 70, 229, 0.4);
            filter: brightness(1.1);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .form-check-input:checked {
            background-color: #6366f1;
            border-color: #6366f1;
        }

        .text-muted {
            color: #6b7280 !important;
        }

        .register-link {
            color: #4f46e5;
            text-decoration: none;
            font-weight: 700;
            transition: color 0.2s;
        }

        .register-link:hover {
            color: #4338ca;
            text-decoration: underline;
        }

        .alert {
            border-radius: 12px;
            font-size: 0.875rem;
            border: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="card login-card">
            <div class="login-header">
                <i class="bi bi-shop"></i>
                <h3 class="mb-0">Wing POS</h3>
                <p class="mb-0 mt-2 opacity-75">Secure Retail Gateway</p>
            </div>
            
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <div class="d-flex">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <div>{{ session('success') }}</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div class="d-flex">
                            <i class="bi bi-exclamation-circle-fill me-2"></i>
                            <div>
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="login_role" class="form-label">Login As</label>
                        <select id="login_role" name="login_role" class="form-control">
                            <option value="super_admin" selected>Super Admin</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               placeholder="name@company.com" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <label for="password" class="form-label">Password</label>
                        </div>
                        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror"
                               placeholder="••••••••" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4 d-flex justify-content-between">
                        <div class="form-check">
                            <input type="checkbox" id="remember" name="remember" class="form-check-input">
                            <label class="form-check-label text-muted small" for="remember">
                                Keep me signed in
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-login w-100">
                        Sign In <i class="bi bi-chevron-right ms-2 small"></i>
                    </button>
                </form>

                <div class="mt-5 text-center">
                    <p class="text-muted small mb-0">
                        New team member? 
                        <a href="{{ route('register') }}" class="register-link">Create account</a>
                    </p>
                </div>
            </div>
        </div>

        <div class="text-center mt-4 text-dark opacity-50">
            <p class="small">&copy; {{ date('Y') }} Wing POS Retail Division</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
