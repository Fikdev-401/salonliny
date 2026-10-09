<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LINY Salon Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --liny-black: #0f0f0f;
            --liny-dark: #1a1a1a;
            --liny-soft: #2c2c2c;
            --liny-gold: #c9a961;
            --liny-gold-dark: #b08d4a;
            --liny-gold-soft: #e2c98a;
            --liny-cream: #f5efe6;
        }

        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--liny-black) 0%, var(--liny-soft) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Decorative gold radials */
        body::before {
            content: '';
            position: absolute;
            top: -20%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(201, 169, 97, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        body::after {
            content: '';
            position: absolute;
            bottom: -20%;
            left: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(201, 169, 97, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        /* Split layout for desktop, stacked for mobile */
        .login-wrapper {
            display: flex;
            width: 100%;
            max-width: 980px;
            min-height: 600px;
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(201, 169, 97, 0.18);
            position: relative;
            z-index: 1;
        }

        /* LEFT panel — branding */
        .login-brand {
            flex: 1 1 50%;
            background: linear-gradient(160deg, var(--liny-black) 0%, var(--liny-dark) 70%, #232323 100%);
            color: #fff;
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
            border-right: 1px solid rgba(201, 169, 97, 0.25);
            overflow: hidden;
        }
        .login-brand::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 20% 20%, rgba(201, 169, 97, 0.18), transparent 45%),
                radial-gradient(circle at 80% 80%, rgba(201, 169, 97, 0.08), transparent 45%);
            pointer-events: none;
        }

        .brand-logo {
            width: 160px;
            height: 160px;
            object-fit: cover;
            border-radius: 50%;
            background: #fff;
            padding: 10px;
            border: 2px solid var(--liny-gold);
            box-shadow: 0 12px 30px rgba(201, 169, 97, 0.35);
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .brand-name {
            font-family: 'Cormorant Garamond', serif;
            font-weight: 600;
            font-size: 2.6rem;
            letter-spacing: 8px;
            color: var(--liny-gold-soft);
            margin: 0;
            position: relative;
            z-index: 1;
        }

        .brand-tagline {
            font-size: 0.85rem;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 0.5rem;
            margin-bottom: 0;
            position: relative;
            z-index: 1;
        }

        .brand-divider {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            margin-top: 1rem;
            color: var(--liny-gold);
            position: relative;
            z-index: 1;
        }
        .brand-divider::before,
        .brand-divider::after {
            content: '';
            width: 40px;
            height: 1px;
            background: var(--liny-gold);
        }

        .brand-foot {
            margin-top: 2.5rem;
            position: relative;
            z-index: 1;
        }
        .brand-foot p {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.1rem;
            font-style: italic;
            color: rgba(255, 255, 255, 0.75);
            margin: 0;
            line-height: 1.6;
            max-width: 320px;
        }

        /* RIGHT panel — form */
        .login-form-panel {
            flex: 1 1 50%;
            padding: 3rem 2.75rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 0; /* allow flex shrink */
        }

        .login-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--liny-dark);
            margin-bottom: 0.25rem;
            letter-spacing: 1px;
        }
        .login-subtitle {
            color: #8a7f6c;
            font-size: 0.88rem;
            margin-bottom: 2rem;
        }

        .form-floating > .form-control {
            border: 1.5px solid #e8e2d5;
            border-radius: 10px;
            background-color: #fbf8f2;
            color: var(--liny-dark);
            font-size: 0.95rem;
            transition: all 0.25s ease;
            height: calc(3.2rem + 2px);
        }
        .form-floating > .form-control:focus {
            border-color: var(--liny-gold);
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(201, 169, 97, 0.12);
        }
        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            color: var(--liny-gold-dark);
        }
        .form-floating > label {
            color: #8a7f6c;
            font-weight: 400;
            padding-left: 1rem;
        }
        .form-floating > label i {
            margin-right: 6px;
            color: var(--liny-gold-dark);
        }

        .btn-login {
            background: linear-gradient(180deg, var(--liny-gold) 0%, var(--liny-gold-dark) 100%);
            border: none;
            color: var(--liny-black);
            padding: 0.85rem;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            border-radius: 10px;
            box-shadow: 0 6px 18px rgba(176, 141, 74, 0.35);
            transition: all 0.25s ease;
            font-size: 0.9rem;
        }
        .btn-login:hover {
            background: linear-gradient(180deg, var(--liny-gold-dark) 0%, var(--liny-gold) 100%);
            color: var(--liny-black);
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(176, 141, 74, 0.45);
        }
        .btn-login:active { transform: translateY(0); }

        .login-divider {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #b8ad97;
            font-size: 0.7rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin: 1.75rem 0 1rem;
        }
        .login-divider::before,
        .login-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, #e0d8c2, transparent);
        }

        .default-creds {
            text-align: center;
            color: #8a7f6c;
            font-size: 0.8rem;
        }
        .default-creds p {
            margin-bottom: 0.6rem;
            letter-spacing: 1px;
        }
        .default-creds code {
            background: var(--liny-cream);
            color: var(--liny-dark);
            padding: 3px 9px;
            border-radius: 5px;
            border: 1px solid #e0d8c2;
            font-size: 0.78rem;
            margin: 0 2px;
        }

        .alert-danger {
            border: 1px solid #f1c2c2;
            background-color: #fdf2f2;
            color: #a13c3c;
            border-radius: 10px;
            font-size: 0.88rem;
        }

        /* Tablet — shrink padding, keep split */
        @media (max-width: 820px) {
            .login-wrapper {
                max-width: 720px;
                min-height: auto;
            }
            .login-brand { padding: 2.25rem 1.75rem; }
            .login-form-panel { padding: 2.25rem 1.75rem; }
            .brand-logo { width: 120px; height: 120px; }
            .brand-name { font-size: 2rem; letter-spacing: 6px; }
        }

        /* Mobile — stack vertically, brand on top */
        @media (max-width: 640px) {
            body { padding: 0.5rem; }
            .login-wrapper {
                flex-direction: column;
                border-radius: 16px;
                min-height: auto;
            }
            .login-brand {
                flex: none;
                padding: 2rem 1.5rem 1.5rem;
                border-right: none;
                border-bottom: 2px solid var(--liny-gold);
            }
            .brand-logo {
                width: 90px;
                height: 90px;
                margin-bottom: 0.75rem;
            }
            .brand-name {
                font-size: 1.7rem;
                letter-spacing: 5px;
            }
            .brand-tagline { font-size: 0.75rem; letter-spacing: 3px; }
            .brand-foot { display: none; }
            .brand-divider { margin-top: 0.75rem; }

            .login-form-panel {
                padding: 1.75rem 1.5rem 2rem;
            }
            .login-title { font-size: 1.4rem; }
            .form-floating > .form-control {
                font-size: 1rem; /* prevent iOS zoom */
                height: calc(3.3rem + 2px);
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <!-- LEFT: branding panel -->
        <div class="login-brand">
            <img src="{{ asset('assets/images/logo.jpeg') }}" alt="LINY Salon" class="brand-logo">
            <h1 class="brand-name">LINY</h1>
            <p class="brand-tagline">hair and beauty</p>
            <div class="brand-divider"><i class="bi bi-scissors"></i></div>
            <div class="brand-foot">
                <p>"Where elegance meets every strand — your beauty, our craft."</p>
            </div>
        </div>

        <!-- RIGHT: form panel -->
        <div class="login-form-panel">
            <h2 class="login-title">Welcome Back</h2>
            <p class="login-subtitle">Sign in to manage your salon operations</p>

            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" required autofocus>
                    <label for="username"><i class="bi bi-person"></i>Username</label>
                </div>
                <div class="form-floating mb-4">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    <label for="password"><i class="bi bi-lock"></i>Password</label>
                </div>
                <button type="submit" class="btn btn-login w-100">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                </button>
            </form>

            <div class="login-divider">Default Login</div>
            <div class="default-creds">
                <code>superadmin</code>
                <code>admin</code>
                <code>kasir</code>
                <code>pimpinan</code>
                <p class="mt-2 mb-0">password: <code>password</code></p>
            </div>
        </div>
    </div>
</body>
</html>
