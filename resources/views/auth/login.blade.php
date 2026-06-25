<x-guest-layout>
<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - CV Mugijaya Logistics ERP</title>

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-primary: #0D1117;
            --bg-secondary: #161B22;
            --bg-card: #1C2333;
            --bg-input: #0D1117;
            --border-color: #30363D;
            --border-focus: #4B6EF5;
            --text-primary: #E6EDF3;
            --text-secondary: #8B949E;
            --text-muted: #656D76;
            --accent-blue: #4B6EF5;
            --accent-blue-hover: #3B5BDB;
            --accent-green: #16A34A;
            --accent-glow: rgba(75, 110, 245, 0.15);
            --gradient-brand: linear-gradient(135deg, #4B6EF5 0%, #7C3AED 100%);
        }

        html, body {
            height: 100%;
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: stretch;
        }

        /* ====== LEFT PANEL: Branding ====== */
        .login-brand-panel {
            width: 45%;
            background: var(--bg-secondary);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding: 4rem;
            position: relative;
            overflow: hidden;
        }

        .login-brand-panel::before {
            content: '';
            position: absolute;
            top: -120px;
            left: -120px;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(75,110,245,0.18) 0%, transparent 70%);
            pointer-events: none;
        }

        .login-brand-panel::after {
            content: '';
            position: absolute;
            bottom: -80px;
            right: -80px;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(124,58,237,0.13) 0%, transparent 70%);
            pointer-events: none;
        }

        .brand-logo-wrap {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 3.5rem;
        }

        .brand-icon {
            width: 48px;
            height: 48px;
            background: var(--gradient-brand);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: #fff;
            box-shadow: 0 4px 20px rgba(75,110,245,0.4);
        }

        .brand-name {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.3px;
        }

        .brand-name span {
            background: var(--gradient-brand);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-tagline {
            font-size: 2.2rem;
            font-weight: 800;
            line-height: 1.2;
            color: var(--text-primary);
            letter-spacing: -0.8px;
            margin-bottom: 1.25rem;
        }

        .brand-tagline .highlight {
            background: var(--gradient-brand);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-desc {
            font-size: 0.9rem;
            color: var(--text-secondary);
            line-height: 1.7;
            max-width: 380px;
            margin-bottom: 3rem;
        }

        .brand-features {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .feature-item .feature-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--gradient-brand);
            flex-shrink: 0;
        }

        .brand-footer-note {
            position: absolute;
            bottom: 2rem;
            left: 4rem;
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        /* ====== RIGHT PANEL: Form ====== */
        .login-form-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem 2rem;
            background: var(--bg-primary);
            position: relative;
        }

        .login-form-card {
            width: 100%;
            max-width: 420px;
        }

        .form-header {
            margin-bottom: 2rem;
        }

        .form-header h2 {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.4px;
            margin-bottom: 0.4rem;
        }

        .form-header p {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .session-alert {
            background: rgba(75,110,245,0.1);
            border: 1px solid rgba(75,110,245,0.3);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
            color: #A5B4FC;
            margin-bottom: 1.5rem;
        }

        .form-label-erp {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-secondary);
            letter-spacing: 0.3px;
            margin-bottom: 0.4rem;
            text-transform: uppercase;
        }

        .input-icon-wrap {
            position: relative;
        }

        .input-icon-wrap .input-icon {
            position: absolute;
            left: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1rem;
            pointer-events: none;
            transition: color 0.2s;
        }

        .input-erp {
            width: 100%;
            background: var(--bg-input);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            padding: 0.7rem 1rem 0.7rem 2.6rem;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .input-erp::placeholder {
            color: var(--text-muted);
        }

        .input-erp:focus {
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .input-erp:focus + .input-icon,
        .input-icon-wrap:focus-within .input-icon {
            color: var(--accent-blue);
        }

        .input-erp-password {
            padding-right: 2.8rem;
        }

        .toggle-password-btn {
            position: absolute;
            right: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 1rem;
            padding: 0;
            line-height: 1;
            transition: color 0.2s;
        }

        .toggle-password-btn:hover {
            color: var(--text-secondary);
        }

        .input-error-msg {
            font-size: 0.78rem;
            color: #F87171;
            margin-top: 0.3rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .form-check-erp {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-check-erp input[type="checkbox"] {
            width: 15px;
            height: 15px;
            border-radius: 4px;
            border: 1px solid var(--border-color);
            background: var(--bg-input);
            cursor: pointer;
            accent-color: var(--accent-blue);
        }

        .form-check-erp label {
            font-size: 0.84rem;
            color: var(--text-secondary);
            cursor: pointer;
            user-select: none;
        }

        .forgot-link {
            font-size: 0.84rem;
            color: var(--accent-blue);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .forgot-link:hover {
            color: #A5B4FC;
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            background: var(--gradient-brand);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 0.92rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            padding: 0.78rem 1.5rem;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.1s, box-shadow 0.2s;
            box-shadow: 0 4px 14px rgba(75,110,245,0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-login:hover {
            opacity: 0.9;
            box-shadow: 0 6px 20px rgba(75,110,245,0.5);
            transform: translateY(-1px);
        }

        .btn-login:active {
            transform: translateY(0);
            opacity: 1;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: var(--text-muted);
            font-size: 0.78rem;
            margin: 0.5rem 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border-color);
        }

        .system-info {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 2.5rem;
        }

        .system-info .pulse-dot-sm {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #16A34A;
            animation: pulse-green 2s infinite;
        }

        @keyframes pulse-green {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        .theme-btn {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 0.95rem;
            transition: background 0.2s, color 0.2s;
        }

        .theme-btn:hover {
            background: var(--bg-card);
            color: var(--text-primary);
        }

        /* Error state input */
        .input-erp.is-invalid {
            border-color: #F87171;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-brand-panel { display: none; }
            .login-form-panel { padding: 2rem 1.25rem; }
        }

        /* Light theme overrides */
        [data-bs-theme="light"] {
            --bg-primary: #F1F5F9;
            --bg-secondary: #FFFFFF;
            --bg-card: #FFFFFF;
            --bg-input: #FFFFFF;
            --border-color: #E2E8F0;
            --text-primary: #0F172A;
            --text-secondary: #475569;
            --text-muted: #94A3B8;
            --accent-glow: rgba(75,110,245,0.1);
        }
    </style>

    <script>
        // Prevent flash of wrong theme
        (function() {
            const t = localStorage.getItem('erp_theme') || 'dark';
            document.documentElement.setAttribute('data-bs-theme', t);
        })();
    </script>
</head>
<body>

<div class="login-wrapper">

    <!-- LEFT: Branding Panel -->
    <div class="login-brand-panel">
        <div class="brand-logo-wrap">
            <div class="brand-icon">
                <i class="bi bi-box-seam-fill"></i>
            </div>
            <div class="brand-name">Mugijaya <span>ERP</span></div>
        </div>

        <h1 class="brand-tagline">
            Kelola Logistik<br>
            <span class="highlight">Lebih Efisien</span><br>
            & Transparan
        </h1>

        <p class="brand-desc">
            Platform ERP terpadu untuk manajemen pergudangan, distribusi,
            dan pelacakan armada CV Mugijaya — semua dalam satu sistem.
        </p>

        <div class="brand-features">
            <div class="feature-item">
                <div class="feature-dot"></div>
                <span>Manajemen stok & mutasi real-time</span>
            </div>
            <div class="feature-item">
                <div class="feature-dot"></div>
                <span>Delivery Order & monitoring pengiriman</span>
            </div>
            <div class="feature-item">
                <div class="feature-dot"></div>
                <span>Audit log aktivitas seluruh divisi</span>
            </div>
            <div class="feature-item">
                <div class="feature-dot"></div>
                <span>Dashboard analitik & peringatan stok</span>
            </div>
        </div>

        <div class="brand-footer-note">
            © {{ date('Y') }} CV Mugijaya &mdash; All rights reserved.
        </div>
    </div>

    <!-- RIGHT: Login Form Panel -->
    <div class="login-form-panel">

        <!-- Theme Toggle -->
        <button class="theme-btn" id="theme-toggle-btn" title="Ganti tema">
            <i class="bi bi-moon" id="icon-moon"></i>
            <i class="bi bi-sun d-none" id="icon-sun"></i>
        </button>

        <div class="login-form-card">

            <div class="form-header">
                <h2>Masuk ke Sistem</h2>
                <p>Gunakan akun ERP Anda untuk melanjutkan.</p>
            </div>

            <!-- Session Status (password reset success, etc) -->
            @if (session('status'))
                <div class="session-alert">
                    <i class="bi bi-check-circle me-1"></i>
                    {{ session('status') }}
                </div>
            @endif

            <!-- Global Error Alert -->
            @if ($errors->any())
                <div style="background: rgba(248,113,113,0.1); border: 1px solid rgba(248,113,113,0.3); border-radius: 8px; padding: 0.75rem 1rem; font-size: 0.85rem; color: #FCA5A5; margin-bottom: 1.5rem;">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div style="margin-bottom: 1.25rem;">
                    <label for="email" class="form-label-erp">Alamat Email</label>
                    <div class="input-icon-wrap">
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="input-erp {{ $errors->has('email') ? 'is-invalid' : '' }}"
                            placeholder="admin@mugijaya.co.id"
                            required
                            autofocus
                            autocomplete="username"
                        >
                        <i class="bi bi-envelope input-icon"></i>
                    </div>
                    @error('email')
                        <div class="input-error-msg">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password -->
                <div style="margin-bottom: 1rem;">
                    <label for="password" class="form-label-erp">Password</label>
                    <div class="input-icon-wrap">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="input-erp input-erp-password {{ $errors->has('password') ? 'is-invalid' : '' }}"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        >
                        <i class="bi bi-lock input-icon"></i>
                        <button type="button" class="toggle-password-btn" id="toggle-password" tabindex="-1">
                            <i class="bi bi-eye" id="eye-icon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="input-error-msg">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Remember Me + Forgot Password -->
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 1.75rem;">
                    <div class="form-check-erp">
                        <input id="remember_me" type="checkbox" name="remember">
                        <label for="remember_me">Ingat saya</label>
                    </div>
                    @if (Route::has('password.request'))
                        <a class="forgot-link" href="{{ route('password.request') }}">
                            Lupa password?
                        </a>
                    @endif
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-login" id="login-btn">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Masuk ke Sistem
                </button>
            </form>

            <div class="divider" style="margin-top: 1.75rem;">Sistem Aktif</div>

            <div class="system-info">
                <div class="pulse-dot-sm"></div>
                <span>CV Mugijaya ERP &mdash; Laravel {{ app()->version() }}</span>
            </div>
        </div>
    </div>
</div>

<script>
    // Theme toggle
    const themeToggle = document.getElementById('theme-toggle-btn');
    const iconMoon = document.getElementById('icon-moon');
    const iconSun = document.getElementById('icon-sun');

    function applyLoginTheme(theme) {
        document.documentElement.setAttribute('data-bs-theme', theme);
        localStorage.setItem('erp_theme', theme);
        if (theme === 'dark') {
            iconMoon.classList.remove('d-none');
            iconSun.classList.add('d-none');
        } else {
            iconMoon.classList.add('d-none');
            iconSun.classList.remove('d-none');
        }
    }

    const currentTheme = localStorage.getItem('erp_theme') || 'dark';
    applyLoginTheme(currentTheme);

    themeToggle.addEventListener('click', () => {
        const cur = document.documentElement.getAttribute('data-bs-theme');
        applyLoginTheme(cur === 'dark' ? 'light' : 'dark');
    });

    // Password toggle
    const toggleBtn = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-icon');

    toggleBtn.addEventListener('click', () => {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        eyeIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
    });

    // Login button loading state
    const form = document.querySelector('form');
    const loginBtn = document.getElementById('login-btn');

    form.addEventListener('submit', () => {
        loginBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Memverifikasi...';
        loginBtn.disabled = true;
    });
</script>

</body>
</html>
</x-guest-layout>
