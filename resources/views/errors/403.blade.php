<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak | CV Mugijaya ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0D1117;
            --bg-secondary: #161B22;
            --border-color: #30363D;
            --text-primary: #E6EDF3;
            --text-secondary: #8B949E;
            --gradient-brand: linear-gradient(135deg, #4B6EF5 0%, #7C3AED 100%);
        }
        html, body {
            height: 100%;
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
        }
        .error-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .error-card {
            max-width: 520px;
            width: 100%;
            text-align: center;
        }
        .error-code-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(220,38,38,0.12);
            border: 1px solid rgba(220,38,38,0.3);
            color: #F87171;
            border-radius: 50px;
            padding: 0.4rem 1.2rem;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 1.5rem;
        }
        .error-icon-wrap {
            width: 100px;
            height: 100px;
            background: rgba(220,38,38,0.1);
            border: 1px solid rgba(220,38,38,0.25);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: #F87171;
            margin: 0 auto 1.5rem;
        }
        .error-title {
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 0.75rem;
        }
        .error-desc {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.7;
            margin-bottom: 2rem;
        }
        .divider-line {
            height: 1px;
            background: var(--border-color);
            margin: 2rem 0;
        }
        .btn-home {
            background: var(--gradient-brand);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.65rem 1.5rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: opacity 0.2s, transform 0.1s;
        }
        .btn-home:hover {
            opacity: 0.9;
            color: #fff;
            transform: translateY(-1px);
        }
        .btn-back {
            background: transparent;
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 0.65rem 1.5rem;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.2s;
        }
        .btn-back:hover {
            color: var(--text-primary);
            border-color: #4B6EF5;
        }
        .brand-logo-sm {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-secondary);
            text-decoration: none;
        }
        .brand-icon-sm {
            width: 28px;
            height: 28px;
            background: var(--gradient-brand);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 0.8rem;
        }
    </style>
    <script>
        (function() {
            const t = localStorage.getItem('erp_theme') || 'dark';
            document.documentElement.setAttribute('data-bs-theme', t);
        })();
    </script>
</head>
<body>
<div class="error-wrapper">
    <div class="error-card">
        {{-- Brand --}}
        <div class="mb-4">
            <a href="{{ route('dashboard') }}" class="brand-logo-sm">
                <div class="brand-icon-sm"><i class="bi bi-box-seam-fill"></i></div>
                Mugijaya ERP
            </a>
        </div>

        {{-- Error Badge --}}
        <div class="error-code-badge">
            <i class="bi bi-shield-x"></i>
            ERROR 403 — AKSES DITOLAK
        </div>

        {{-- Icon --}}
        <div class="error-icon-wrap">
            <i class="bi bi-lock-fill"></i>
        </div>

        {{-- Title --}}
        <h1 class="error-title">Anda tidak memiliki<br>akses ke halaman ini</h1>

        {{-- Description --}}
        <p class="error-desc">
            Halaman yang Anda coba akses memerlukan izin khusus yang tidak dimiliki oleh role Anda saat ini.
            Hubungi Administrator jika Anda merasa ini adalah kesalahan.
        </p>

        @auth
        <div style="background: rgba(75,110,245,0.08); border: 1px solid rgba(75,110,245,0.2); border-radius: 8px; padding: 0.75rem 1rem; margin-bottom: 1.5rem; font-size: 0.82rem; color: #93C5FD;">
            <i class="bi bi-person-badge me-1"></i>
            Anda login sebagai <strong>{{ Auth::user()->name }}</strong>
            @php $role = Auth::user()->roles->first()?->name; @endphp
            @if($role)
            dengan role <strong>{{ $role }}</strong>
            @endif
        </div>
        @endauth

        <div class="divider-line"></div>

        {{-- Actions --}}
        <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap">
            <a href="javascript:history.back()" class="btn-back">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('dashboard') }}" class="btn-home">
                <i class="bi bi-grid-1x2-fill"></i> Ke Dashboard
            </a>
        </div>
    </div>
</div>
</body>
</html>
