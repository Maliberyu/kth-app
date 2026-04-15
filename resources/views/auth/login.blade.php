<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — KTH Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #0b1f12;
        }

        /* LEFT PANEL */
        .left-panel {
            width: 55%;
            background: linear-gradient(145deg, #0f2419 0%, #1a4a28 60%, #0f2419 100%);
            display: flex; flex-direction: column;
            justify-content: center; align-items: flex-start;
            padding: 60px;
            position: relative; overflow: hidden;
        }
        .left-panel::before {
            content: '';
            position: absolute; inset: 0;
            background: radial-gradient(ellipse at 30% 50%, rgba(26,127,75,.25) 0%, transparent 70%);
        }
        .left-panel::after {
            content: '';
            position: absolute; top: -100px; right: -100px;
            width: 400px; height: 400px; border-radius: 50%;
            border: 1px solid rgba(255,255,255,.04);
        }
        .circles {
            position: absolute; bottom: -80px; left: -80px;
        }
        .circles span {
            display: block; border-radius: 50%;
            border: 1px solid rgba(255,255,255,.05);
            position: absolute;
        }
        .circles span:nth-child(1) { width: 200px; height: 200px; top: 0; left: 0; }
        .circles span:nth-child(2) { width: 350px; height: 350px; top: -75px; left: -75px; }
        .circles span:nth-child(3) { width: 500px; height: 500px; top: -150px; left: -150px; }

        .brand { position: relative; z-index: 2; margin-bottom: 48px; }
        .brand-logo {
            display: flex; align-items: center; gap: 12px; margin-bottom: 8px;
        }
        .brand-icon {
            width: 48px; height: 48px; background: #1a7f4b;
            border-radius: 12px; display: flex; align-items: center;
            justify-content: center; font-size: 22px;
        }
        .brand-name { color: #fff; font-size: 28px; font-weight: 800; letter-spacing: -.5px; }
        .brand-name span { color: #f0a500; }
        .brand-tagline { color: rgba(255,255,255,.4); font-size: 13px; margin-left: 60px; }

        .hero-text { position: relative; z-index: 2; }
        .hero-text h2 {
            color: #fff; font-size: 36px; font-weight: 800;
            line-height: 1.2; letter-spacing: -.5px; margin-bottom: 16px;
        }
        .hero-text h2 em { color: #f0a500; font-style: normal; }
        .hero-text p { color: rgba(255,255,255,.5); font-size: 15px; line-height: 1.7; max-width: 400px; }

        .features { position: relative; z-index: 2; margin-top: 40px; display: flex; flex-direction: column; gap: 14px; }
        .feature-item { display: flex; align-items: center; gap: 12px; }
        .feature-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: #1a7f4b; flex-shrink: 0;
        }
        .feature-item span { color: rgba(255,255,255,.6); font-size: 13.5px; }

        /* RIGHT PANEL */
        .right-panel {
            flex: 1;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            padding: 48px 60px;
        }
        .login-box { width: 100%; max-width: 380px; }
        .login-box h3 {
            font-size: 24px; font-weight: 800; color: #0f2419;
            margin-bottom: 6px; letter-spacing: -.3px;
        }
        .login-box p { color: #6b7a8d; font-size: 14px; margin-bottom: 32px; }

        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block; font-size: 13px; font-weight: 600;
            color: #374151; margin-bottom: 7px;
        }
        .input-wrap { position: relative; }
        .input-wrap i {
            position: absolute; left: 14px; top: 50%;
            transform: translateY(-50%);
            color: #adb5bd; font-size: 14px;
        }
        .form-control {
            width: 100%; padding: 11px 14px 11px 40px;
            border: 1.5px solid #e2e8f0; border-radius: 10px;
            font-size: 14px; font-family: inherit; color: #1e2a35;
            background: #fafafa; transition: all .2s;
        }
        .form-control:focus {
            outline: none; border-color: #1a7f4b;
            background: #fff; box-shadow: 0 0 0 4px rgba(26,127,75,.08);
        }
        .form-control.is-invalid { border-color: #dc3545; }

        .error-msg { color: #dc3545; font-size: 12px; margin-top: 5px; }

        .remember-row {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 24px;
        }
        .remember-row label {
            display: flex; align-items: center; gap: 8px;
            font-size: 13px; color: #6b7a8d; cursor: pointer;
        }
        .remember-row a { font-size: 13px; color: #1a7f4b; text-decoration: none; font-weight: 600; }
        .remember-row a:hover { text-decoration: underline; }

        .btn-login {
            width: 100%; padding: 13px;
            background: #1a7f4b; color: #fff;
            border: none; border-radius: 10px;
            font-size: 15px; font-weight: 700; font-family: inherit;
            cursor: pointer; transition: all .2s;
            letter-spacing: .2px;
        }
        .btn-login:hover { background: #145f38; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(26,127,75,.3); }
        .btn-login:active { transform: translateY(0); }

        .error-alert {
            background: #fff5f5; border: 1px solid #fed7d7;
            color: #c53030; padding: 12px 16px; border-radius: 10px;
            font-size: 13px; margin-bottom: 20px;
            display: flex; align-items: center; gap: 8px;
        }

        @media (max-width: 768px) {
            .left-panel { display: none; }
            .right-panel { padding: 32px 24px; }
        }
    </style>
</head>
<body>

<div class="left-panel">
    <div class="circles">
        <span></span><span></span><span></span>
    </div>

    <div class="brand">
        <div class="brand-logo">
            <div class="brand-icon">🌿</div>
            <div class="brand-name">KTH <span>App</span></div>
        </div>
        <div class="brand-tagline">Sistem Manajemen Kelompok Tani Hutan</div>
    </div>

    <div class="hero-text">
        <h2>Kelola <em>Produksi</em><br>Getah dengan<br>Mudah & Efisien</h2>
        <p>Platform terintegrasi untuk manajemen penyadap, produksi getah, pengiriman, dan penjualan KTH Anda.</p>
    </div>

    <div class="features">
        <div class="feature-item">
            <div class="feature-dot"></div>
            <span>Monitoring produksi getah real-time</span>
        </div>
        <div class="feature-item">
            <div class="feature-dot"></div>
            <span>Manajemen penyadap & blok terintegrasi</span>
        </div>
        <div class="feature-item">
            <div class="feature-dot"></div>
            <span>Laporan penjualan & inventaris otomatis</span>
        </div>
        <div class="feature-item">
            <div class="feature-dot"></div>
            <span>Mapping blok dengan peta interaktif</span>
        </div>
    </div>
</div>

<div class="right-panel">
    <div class="login-box">
        <h3>Selamat Datang</h3>
        <p>Masuk ke akun KTH App Anda</p>

        @if($errors->any())
            <div class="error-alert">
                <span>⚠</span> {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <div class="input-wrap">
                    <i class="fas fa-envelope"></i>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                        placeholder="contoh@email.com"
                        required autofocus
                    >
                </div>
                @error('email')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="input-wrap">
                    <i class="fas fa-lock"></i>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                        placeholder="••••••••"
                        required
                    >
                </div>
                @error('password')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            <div class="remember-row">
                <label>
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Ingat saya
                </label>
                @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}">Lupa password?</a>
                @endif
            </div>

            <button type="submit" class="btn-login">
                Masuk ke Aplikasi
            </button>
        </form>
    </div>
</div>

</body>
</html>