<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CV Mugijaya Logistics ERP</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script>
        (function () {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>
</head>
<body class="login-bg">
    <div class="login-card">
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center bg-light-dark p-3 rounded-circle mb-3" style="border: 1px solid var(--border-color)">
                <i class="bi bi-box-seam-fill text-indigo" style="font-size: 2.5rem; line-height: 1;"></i>
            </div>
            <h3 class="fw-bold mb-1">CV Mugijaya</h3>
            <p class="text-muted text-sm">Sistem Manajemen Distribusi & Gudang</p>
        </div>

        <div class="alert alert-info text-xs border-light-dark bg-light-dark text-secondary mb-4" role="alert">
            <div class="fw-semibold text-primary mb-1"><i class="bi bi-info-circle-fill me-1"></i>Akun Demo Terdaftar:</div>
            <ul class="m-0 ps-3">
                <li>Super Admin: <code>budi.admin@mugijaya.com</code></li>
                <li>Gudang Admin: <code>siti.warehouse@mugijaya.com</code></li>
                <li>(Password bebas/apa saja)</li>
            </ul>
        </div>

        <form id="login-form" method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="form-floating mb-3">
                <input type="email" name="email" class="form-control" id="email" placeholder="name@example.com" value="budi.admin@mugijaya.com" required>
                <label for="email">Alamat Email</label>
            </div>
            <div class="form-floating mb-4">
                <input type="password" name="password" class="form-control" id="password" placeholder="Password" value="password123" required>
                <label for="password">Kata Sandi</label>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember-me" name="remember" checked>
                    <label class="form-check-label text-sm text-secondary" for="remember-me">
                        Ingat Saya
                    </label>
                </div>
                <a href="#" class="text-sm text-indigo text-decoration-none fw-semibold">Lupa Password?</a>
            </div>

            <button type="submit" class="btn btn-lg btn-primary-gradient w-100 py-2.5">
                Masuk Sistem <i class="bi bi-box-arrow-in-right ms-1"></i>
            </button>
        </form>

        <div class="text-center mt-4 text-xs text-muted">
            &copy; 2026 CV Mugijaya. All rights reserved.
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Mock Database -->
    <script src="{{ asset('js/mock-data.js') }}"></script>
    <script>
        document.getElementById('login-form').addEventListener('submit', function(e) {
            // Check if we are running in frontend-only mock mode (which is always true since no DB setup yet)
            // Intercepting form submit so it doesn't trigger database error in Laravel
            e.preventDefault();
            const email = document.getElementById('email').value.trim();
            
            // Find user in db
            const users = db.getData('users');
            const foundUser = users.find(u => u.email.toLowerCase() === email.toLowerCase());
            
            if (foundUser && foundUser.status === "Active") {
                const avatarMap = {
                    "Super Admin": "https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80",
                    "Warehouse Admin": "https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=150&q=80",
                    "Courier Admin": "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=150&q=80",
                    "Driver": "https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=150&q=80",
                    "Validator": "https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=150&q=80"
                };
                
                const userSession = {
                    name: foundUser.name,
                    role: foundUser.role,
                    email: foundUser.email,
                    avatar: avatarMap[foundUser.role] || "https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=150&q=80"
                };
                
                sessionStorage.setItem('erp_user', JSON.stringify(userSession));
                
                // Log the successful login
                db.logAction(foundUser.name, "User Access", "Login", `Admin ${foundUser.email} login ke sistem ERP`, "Success");
                
                window.location.href = "{{ route('dashboard') }}";
            } else {
                alert('Akun tidak terdaftar atau tidak aktif. Coba gunakan budi.admin@mugijaya.com');
            }
        });
    </script>
</body>
</html>
